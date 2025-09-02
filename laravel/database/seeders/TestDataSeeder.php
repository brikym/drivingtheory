<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Test;
use App\Models\TestAnswer;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Category;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $userId = 2;
        
        // Zkontrolovat, zda uživatel existuje
        if (!\App\Models\User::find($userId)) {
            $this->command->error("Uživatel s ID {$userId} neexistuje!");
            return;
        }

        // Získat všechny otázky pro generování testů
        $allQuestions = Question::with(['answers', 'categories'])->get();
        
        if ($allQuestions->isEmpty()) {
            $this->command->error("Žádné otázky nenalezeny! Nejdříve spusťte CategorySeeder a QuestionImportService.");
            return;
        }

        $this->command->info("Generuji 20 testů pro uživatele ID {$userId}...");

        // Generovat 10 úspěšných testů
        for ($i = 1; $i <= 10; $i++) {
            $this->generateTest($userId, $allQuestions, true, $i);
        }

        // Generovat 10 neúspěšných testů
        for ($i = 1; $i <= 10; $i++) {
            $this->generateTest($userId, $allQuestions, false, $i + 10);
        }

        $this->command->info("Úspěšně vygenerováno 20 testů!");
    }

    private function generateTest($userId, $allQuestions, $shouldPass, $testNumber)
    {
        // Náhodně vybrat typ vozidla
        $vehicleTypes = ['automobil', 'motocykl'];
        $vehicleType = $vehicleTypes[array_rand($vehicleTypes)];

        // Definovat konfiguraci testů podle typu vozidla
        $testConfig = $this->getTestConfig($vehicleType);
        
        // Vybrat otázky podle konfigurace
        $selectedQuestions = $this->selectQuestionsForTest($allQuestions, $testConfig);
        
        if ($selectedQuestions->isEmpty()) {
            $this->command->warn("Nepodařilo se vybrat otázky pro test {$testNumber}");
            return;
        }

        // Vytvořit test
        $test = Test::create([
            'user_id' => $userId,
            'vehicle_type' => $vehicleType,
            'status' => 'completed',
            'started_at' => Carbon::now()->subDays(rand(1, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
            'completed_at' => null, // Nastavíme později
            'time_limit_minutes' => 30,
            'total_questions' => $selectedQuestions->count(),
            'total_points' => $selectedQuestions->sum('points'),
            'earned_points' => 0, // Nastavíme později
            'percentage' => 0, // Nastavíme později
            'passed' => false, // Nastavíme později
            'time_expired' => false,
        ]);

        $totalEarnedPoints = 0;
        $answeredQuestions = 0;

        // Generovat odpovědi
        foreach ($selectedQuestions as $question) {
            $answers = $question->answers;
            if ($answers->isEmpty()) {
                continue;
            }

            // Najít správnou odpověď
            $correctAnswer = $answers->where('is_correct', true)->first();
            
            if (!$correctAnswer) {
                continue;
            }

            // Rozhodnout, zda odpovědět správně nebo špatně
            $shouldAnswerCorrectly = $this->shouldAnswerCorrectly($shouldPass, $answeredQuestions, $selectedQuestions->count());
            
            $selectedAnswer = $shouldAnswerCorrectly ? $correctAnswer : $answers->where('is_correct', false)->random();
            
            if (!$selectedAnswer) {
                $selectedAnswer = $answers->random();
            }

            $isCorrect = $selectedAnswer->is_correct;
            $pointsEarned = $isCorrect ? $question->points : 0;
            $totalEarnedPoints += $pointsEarned;
            $answeredQuestions++;

            // Vytvořit odpověď
            TestAnswer::create([
                'test_id' => $test->id,
                'question_id' => $question->id,
                'selected_answer_id' => $selectedAnswer->id,
                'is_correct' => $isCorrect,
                'points_earned' => $pointsEarned,
                'points_possible' => $question->points,
                'answered_at' => $test->started_at->addMinutes(rand(1, 25)),
            ]);
        }

        // Vypočítat výsledky
        $percentage = $test->total_points > 0 ? ($totalEarnedPoints / $test->total_points) * 100 : 0;
        $passed = $percentage >= 86; // 86% = 43 bodů z 50

        // Aktualizovat test s výsledky
        $test->update([
            'completed_at' => $test->started_at->addMinutes(rand(15, 30)),
            'earned_points' => $totalEarnedPoints,
            'percentage' => round($percentage, 2),
            'passed' => $passed,
        ]);

        $status = $passed ? 'úspěšný' : 'neúspěšný';
        $this->command->info("Test {$testNumber} ({$vehicleType}): {$totalEarnedPoints}/{$test->total_points} bodů ({$percentage}%) - {$status}");
    }

    private function getTestConfig($vehicleType)
    {
        // Stejná konfigurace jako v QuestionController
        return [
            'Pravidla provozu na pozemních komunikacích' => ['count' => 10, 'points' => 2],
            'Dopravní značky' => ['count' => 3, 'points' => 1],
            'Zásady bezpečné jízdy' => ['count' => 4, 'points' => 2],
            'Dopravní situace' => ['count' => 3, 'points' => 4],
            'Předpisy o podmínkách provozu vozidel' => ['count' => 2, 'points' => 1],
            'Předpisy související s provozem' => ['count' => 2, 'points' => 2],
            'Zdravotnická příprava' => ['count' => 1, 'points' => 1],
        ];
    }

    private function selectQuestionsForTest($allQuestions, $testConfig)
    {
        $selectedQuestions = collect();

        foreach ($testConfig as $categoryName => $config) {
            // Najít kategorii
            $category = Category::whereHas('translations', function($query) use ($categoryName) {
                $query->where('name', $categoryName);
            })->first();

            if (!$category) {
                $this->command->warn("Kategorie '{$categoryName}' nenalezena");
                continue;
            }

            // Najít otázky v této kategorii
            $categoryQuestions = $allQuestions->filter(function($question) use ($category) {
                return $question->categories->contains($category);
            });

            if ($categoryQuestions->isEmpty()) {
                $this->command->warn("Žádné otázky v kategorii '{$categoryName}'");
                continue;
            }

            // Vybrat náhodné otázky z kategorie
            $questionsToSelect = min($config['count'], $categoryQuestions->count());
            $selectedCategoryQuestions = $categoryQuestions->random($questionsToSelect);

            // Nastavit body pro otázky
            foreach ($selectedCategoryQuestions as $question) {
                $question->points = $config['points'];
            }

            $selectedQuestions = $selectedQuestions->merge($selectedCategoryQuestions);
            
            $this->command->info("Kategorie '{$categoryName}': vybráno {$questionsToSelect} otázek z {$categoryQuestions->count()} dostupných");
        }

        // Pokud nemáme dostatek otázek, doplníme náhodnými otázkami
        if ($selectedQuestions->count() < 20) {
            $remainingQuestions = $allQuestions->diff($selectedQuestions);
            $neededQuestions = 25 - $selectedQuestions->count();
            
            if ($remainingQuestions->count() > 0) {
                $additionalQuestions = $remainingQuestions->random(min($neededQuestions, $remainingQuestions->count()));
                
                // Nastavit body pro doplňkové otázky (výchozí 2 body)
                foreach ($additionalQuestions as $question) {
                    $question->points = 2;
                }
                
                $selectedQuestions = $selectedQuestions->merge($additionalQuestions);
                $this->command->info("Doplněno {$additionalQuestions->count()} náhodných otázek");
            }
        }

        return $selectedQuestions;
    }

    private function shouldAnswerCorrectly($shouldPass, $currentQuestion, $totalQuestions)
    {
        if ($shouldPass) {
            // Pro úspěšné testy: odpovědět správně v 90-100% případů
            $correctRate = 0.9 + (rand(0, 10) / 100); // 90-100%
        } else {
            // Pro neúspěšné testy: odpovědět správně v 60-85% případů
            $correctRate = 0.6 + (rand(0, 25) / 100); // 60-85%
        }

        // Přidat malou náhodnost
        $randomFactor = rand(0, 100) / 100;
        
        return $randomFactor < $correctRate;
    }
}