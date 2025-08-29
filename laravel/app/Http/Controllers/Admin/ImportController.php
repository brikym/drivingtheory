<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportQuestionsRequest;
use App\Services\QuestionImportService;

class ImportController extends Controller
{
    public function __construct(private QuestionImportService $questionImportService)
    {
    }

    public function index()
    {
        return view('admin.import.index');
    }

    public function store(ImportQuestionsRequest $request)
    {
        $file = $request->file('questions_file');
        $jsonContent = file_get_contents($file->getRealPath());
        try {
            $questions = $this->questionImportService->parseJson($jsonContent);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', 'Neplatný JSON soubor.');
        }

        $stats = $this->questionImportService->importQuestions($questions, 'cs');

        return back()->with('success', 'Import hotov. Otázky: +'.$stats['questions_created'].' / ~'.$stats['questions_updated'].', Odpovědi: +'.$stats['answers_created'].' / ~'.$stats['answers_updated']);
    }
}


