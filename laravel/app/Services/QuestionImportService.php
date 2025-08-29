<?php

namespace App\Services;

use App\Models\Answer;
use App\Models\AnswerTranslation;
use App\Models\MediaContent;
use App\Models\Question;
use App\Models\QuestionTranslation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class QuestionImportService
{
    public function parseJson(string $jsonContent): array
    {
        $data = json_decode($jsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            throw new \InvalidArgumentException('Invalid JSON payload.');
        }
        return $data;
    }

    /**
     * Import questions payload into database.
     *
     * @param array $questionsPayload Array of questions as provided in JSON
     * @param string $locale Two-letter locale code for translations, e.g. 'cs'
     * @return array{questions_created:int,questions_updated:int,answers_created:int,answers_updated:int}
     */
    public function importQuestions(array $questionsPayload, string $locale = 'cs'): array
    {
        $stats = [
            'questions_created' => 0,
            'questions_updated' => 0,
            'answers_created' => 0,
            'answers_updated' => 0,
        ];

        foreach ($questionsPayload as $questionItem) {
            DB::transaction(function () use (&$stats, $questionItem, $locale) {
                $questionCode = $this->stringOrNull($questionItem['questionCode'] ?? null);
                $externalId = $this->intOrNull($questionItem['id'] ?? null);

                $questionAttributes = [
                    'question_code' => $questionCode,
                ];

                $questionValues = [
                    'external_id' => $externalId,
                    'template_id' => $this->intOrNull($questionItem['questionTemplateId'] ?? null),
                    'points_count' => $this->intOrNull($questionItem['pointsCount'] ?? null),
                    'valid_from' => $this->dateOrNull($questionItem['validFrom'] ?? null),
                    'valid_to' => $this->dateOrNull($questionItem['validTo'] ?? null),
                    'type' => 'multiple_choice',
                    'is_active' => true,
                ];

                [$question, $wasRecentlyCreated] = $this->updateOrCreateWithFlag(Question::class, $questionAttributes, $questionValues);
                $wasRecentlyCreated ? $stats['questions_created']++ : $stats['questions_updated']++;

                // Question translation
                $translationValues = [
                    'text' => $this->stringOrNull($questionItem['questionText'] ?? null),
                    'explanation' => $this->stringOrNull($questionItem['explanationNote'] ?? null),
                ];
                $this->updateOrCreateWithFlag(
                    QuestionTranslation::class,
                    ['question_id' => $question->id, 'locale' => $locale],
                    $translationValues
                );

                // Media (optional)
                if (!empty($questionItem['mediaContent'])) {
                    $mediaContentId = $this->upsertMedia($question, $questionItem['mediaContent']);
                    if ($mediaContentId) {
                        $question->update(['media_content_id' => $mediaContentId]);
                    }
                }

                // Answers
                $answers = is_array($questionItem['questionAnswers'] ?? null) ? $questionItem['questionAnswers'] : [];
                foreach ($answers as $answerItem) {
                    $answerExternalId = $this->intOrNull($answerItem['id'] ?? null);
                    $answerAttributes = $answerExternalId !== null
                        ? ['external_id' => $answerExternalId]
                        : ['question_id' => $question->id, 'text' => $this->stringOrNull($answerItem['answerText'] ?? null)];

                    $answerValues = [
                        'question_id' => $question->id,
                        'text' => $this->stringOrNull($answerItem['answerText'] ?? null),
                        'is_correct' => (bool)($answerItem['isCorrect'] ?? false),
                        'order' => $this->intOrNull($answerItem['sortOrderNumber'] ?? null),
                    ];

                    [$answer, $answerCreated] = $this->updateOrCreateWithFlag(Answer::class, $answerAttributes, $answerValues);
                    $answerCreated ? $stats['answers_created']++ : $stats['answers_updated']++;

                    // Answer translation to keep i18n ready
                    $this->updateOrCreateWithFlag(
                        AnswerTranslation::class,
                        ['answer_id' => $answer->id, 'locale' => $locale],
                        ['text' => $this->stringOrNull($answerItem['answerText'] ?? null)]
                    );

                    if (!empty($answerItem['mediaContent'])) {
                        $mediaContentId = $this->upsertMedia($answer, $answerItem['mediaContent']);
                        if ($mediaContentId) {
                            $answer->update(['media_content_id' => $mediaContentId]);
                        }
                    }
                }
            });
        }

        return $stats;
    }

    private function updateOrCreateWithFlag(string $modelClass, array $attributes, array $values): array
    {
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = $modelClass::query()->where($attributes)->first();
        if ($model === null) {
            /** @var \Illuminate\Database\Eloquent\Model $created */
            $created = $modelClass::query()->create(array_merge($attributes, $values));
            return [$created, true];
        }
        $model->fill($values);
        $model->save();
        return [$model, false];
    }

    private function dateOrNull(?string $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }
        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function intOrNull($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }
        return is_numeric($value) ? (int) $value : null;
    }

    private function stringOrNull($value): ?string
    {
        if ($value === null) {
            return null;
        }
        $str = trim((string) $value);
        return $str === '' ? null : $str;
    }

    private function upsertMedia($model, $media): ?int
    {
        $mediaUrl = null;
        $mediaType = null;

        if (is_array($media)) {
            $fullMediaUrl = $this->stringOrNull($media['mediaUrl'] ?? $media['url'] ?? null);
            $mediaUrl = $fullMediaUrl ? basename($fullMediaUrl) : null;
            $rawMediaType = $this->stringOrNull($media['mediaFormatCode'] ?? $media['mediaType'] ?? null);
            
            // Normalizuj mediaType na obecné kategorie
            $mediaType = $this->normalizeMediaType($rawMediaType);
        } elseif (is_string($media)) {
            $mediaUrl = basename($this->stringOrNull($media));
            $mediaType = 'file';
        }

        if ($mediaUrl === null) {
            return null;
        }

        // Zajisti, že mediaType není null
        if ($mediaType === null) {
            $mediaType = 'unknown';
        }

        /** @var \Illuminate\Database\Eloquent\Model $model */
        /** @var \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\Relation $relation */
        $relation = $model->mediaContents();

        /** @var MediaContent $existing */
        $existing = $relation->where('media_url', $mediaUrl)->first();
        if ($existing) {
            $existing->fill([
                'media_type' => $mediaType,
            ])->save();
            return $existing->id;
        }

        $newMedia = $relation->create([
            'media_url' => $mediaUrl,
            'media_type' => $mediaType,
        ]);

        return $newMedia->id;
    }

    private function normalizeMediaType(?string $rawType): string
    {
        if ($rawType === null) {
            return 'unknown';
        }

        $type = strtolower(trim($rawType));

        // Video formáty
        if (str_contains($type, 'video') || str_contains($type, 'mp4') || str_contains($type, 'avi') || 
            str_contains($type, 'mov') || str_contains($type, 'wmv') || str_contains($type, 'flv')) {
            return 'video';
        }

        // Obrázkové formáty
        if (str_contains($type, 'image') || str_contains($type, 'jpg') || str_contains($type, 'jpeg') || 
            str_contains($type, 'png') || str_contains($type, 'gif') || str_contains($type, 'bmp') || 
            str_contains($type, 'webp') || str_contains($type, 'svg')) {
            return 'image';
        }

        // Audio formáty
        if (str_contains($type, 'audio') || str_contains($type, 'mp3') || str_contains($type, 'wav') || 
            str_contains($type, 'ogg') || str_contains($type, 'aac')) {
            return 'audio';
        }

        // Dokumenty
        if (str_contains($type, 'pdf') || str_contains($type, 'doc') || str_contains($type, 'txt')) {
            return 'document';
        }

        return 'unknown';
    }
}

