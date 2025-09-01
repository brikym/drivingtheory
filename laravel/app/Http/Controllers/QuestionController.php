<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Question;
use App\Models\Test;
use App\Models\TestAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $locale = 'cs'; // Prozatím jen čeština
        
        $query = Category::where('is_active', true)
            ->with(['translations' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            }]);

        // Globální fulltextové vyhledávání
        if ($search && strlen($search) >= 3) {
            $query->whereHas('questions.translations', function ($q) use ($search, $locale) {
                $q->where('locale', $locale)
                  ->where(function ($subQuery) use ($search) {
                      $subQuery->where('text', 'LIKE', "%{$search}%")
                               ->orWhere('explanation', 'LIKE', "%{$search}%");
                  });
            })->orWhereHas('questions.answers.translations', function ($q) use ($search, $locale) {
                $q->where('locale', $locale)
                  ->where('text', 'LIKE', "%{$search}%");
            });
        }

        $categories = $query->get();

        // Přidat dynamický počet otázek pro každou kategorii
        $categories->each(function ($category) use ($search, $locale) {
            $questionQuery = $category->questions()->where('is_active', true);
            
            // Pokud se vyhledává, přidej filtrování
            if ($search && strlen($search) >= 3) {
                $questionQuery->where(function ($mainQuery) use ($search, $locale) {
                    $mainQuery->whereHas('translations', function ($q) use ($search, $locale) {
                        $q->where('locale', $locale)
                          ->where(function ($subQuery) use ($search) {
                              $subQuery->where('text', 'LIKE', "%{$search}%")
                                       ->orWhere('explanation', 'LIKE', "%{$search}%");
                          });
                    })->orWhereHas('answers.translations', function ($q) use ($search, $locale) {
                        $q->where('locale', $locale)
                          ->where('text', 'LIKE', "%{$search}%");
                    });
                });
            }
            
            $category->filtered_questions_count = $questionQuery->count();
        });

        return view('questions.index', compact('categories', 'search'));
    }

    public function showCategory(Category $category, Request $request)
    {
        $search = $request->get('search');
        $locale = 'cs'; // Prozatím jen čeština
        
        $query = $category->questions()
            ->where('is_active', true)
            ->with(['translations' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            }, 'answers.translations' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            }, 'answers.mediaContent', 'mediaContent']);

        // Fulltextové vyhledávání - filtruje pouze otázky obsahující hledaný text
        if ($search && strlen($search) >= 3) {
            $query->where(function ($mainQuery) use ($search, $locale) {
                // Hledá v textu otázky nebo vysvětlení
                $mainQuery->whereHas('translations', function ($q) use ($search, $locale) {
                    $q->where('locale', $locale)
                      ->where(function ($subQuery) use ($search) {
                          $subQuery->where('text', 'LIKE', "%{$search}%")
                                   ->orWhere('explanation', 'LIKE', "%{$search}%");
                      });
                })
                // NEBO hledá v odpovědích
                ->orWhereHas('answers.translations', function ($q) use ($search, $locale) {
                    $q->where('locale', $locale)
                      ->where('text', 'LIKE', "%{$search}%");
                });
            });
        }

        $questions = $query->paginate(10)->appends(request()->query());

        return view('questions.category', compact('category', 'questions', 'search'));
    }

    public function test()
    {
        return view('test.index');
    }

    public function startTest(Request $request)
    {
        $vehicleType = $request->get('vehicle_type');
        
        if (!in_array($vehicleType, ['automobil', 'motocykl'])) {
            return redirect()->route('test.index')->with('error', 'Neplatný typ vozidla');
        }

        // Zkontrolovat, zda uživatel nemá aktivní test
        $activeTest = Auth::user()->getActiveTest();
        if ($activeTest) {
            return redirect()->route('test.question')->with('info', 'Máte aktivní test. Pokračujete v něm.');
        }

        // Definice testů podle typu vozidla
        $testConfig = $this->getTestConfig($vehicleType);
        
        // Vybrat otázky podle konfigurace
        $selectedQuestions = $this->selectQuestionsForTest($testConfig);
        
        // Vytvořit test v databázi
        $test = Test::create([
            'user_id' => Auth::id(),
            'vehicle_type' => $vehicleType,
            'status' => 'in_progress',
            'started_at' => now(),
            'time_limit_minutes' => 30,
            'total_questions' => count($selectedQuestions),
            'total_points' => array_sum(array_map(function($q) { return $q['points']; }, $selectedQuestions)),
        ]);

        // Uložit otázky do session pro rychlý přístup
        session([
            'test_id' => $test->id,
            'test_questions' => $selectedQuestions,
            'test_current_question' => 0,
        ]);

        return redirect()->route('test.question');
    }

    private function getTestConfig($vehicleType)
    {
        // Konfigurace je stejná pro oba typy vozidel
        return [
            'pravidla_provozu' => ['count' => 10, 'points' => 2, 'categories' => [1]],
            'dopravni_znacky' => ['count' => 3, 'points' => 1, 'categories' => [5]],
            'zasady_bezpecne_jizdy' => ['count' => 4, 'points' => 2, 'categories' => [2, 3, 4]],
            'dopravni_situace' => ['count' => 3, 'points' => 4, 'categories' => [6]],
            'podminky_provozu' => ['count' => 2, 'points' => 1, 'categories' => [7]],
            'souvisejici_predpisy' => ['count' => 2, 'points' => 2, 'categories' => [8]],
            'zdravotnicka_priprava' => ['count' => 1, 'points' => 1, 'categories' => [9]]
        ];
    }

    private function selectQuestionsForTest($testConfig)
    {
        $selectedQuestions = [];
        
        foreach ($testConfig as $section => $config) {
            $questions = Question::where('is_active', true)
                ->whereHas('categories', function ($query) use ($config) {
                    $query->whereIn('categories.id', $config['categories']);
                })
            ->with(['translations' => function ($query) {
                $query->where('locale', 'cs');
            }, 'answers.translations' => function ($query) {
                $query->where('locale', 'cs');
            }, 'answers.mediaContent', 'mediaContent'])
                ->inRandomOrder()
                ->limit($config['count'])
            ->get();

            foreach ($questions as $question) {
                $selectedQuestions[] = [
                    'id' => $question->id,
                    'question_code' => $question->question_code,
                    'text' => $question->translations->first()?->text,
                    'explanation' => $question->translations->first()?->explanation,
                    'points' => $config['points'],
                    'section' => $section,
                    'answers' => $question->answers->map(function ($answer) {
                        return [
                            'id' => $answer->id,
                            'text' => $answer->translations->first()?->text,
                            'is_correct' => $answer->is_correct,
                            'media_content' => $answer->mediaContent
                        ];
                    }),
                    'media_content' => $question->mediaContent
                ];
            }
        }

        // Zamíchat otázky
        shuffle($selectedQuestions);
        
        return $selectedQuestions;
    }

    public function showQuestion(Request $request)
    {
        // Zkusit najít aktivní test z databáze nebo session
        $test = $this->getCurrentTest();
        
        if (!$test) {
            return redirect()->route('test.index')->with('error', 'Žádný aktivní test nenalezen');
        }

        // Kontrola časového limitu
        if ($test->isTimeExpired()) {
            $test->update([
                'status' => 'expired',
                'time_expired' => true,
                'completed_at' => now()
            ]);
            return redirect()->route('test.result')->with('time_expired', true);
        }

        // Čas se počítá od started_at, není potřeba aktualizovat

        $questions = session('test_questions');
        
        // Pokud nejsou otázky v session, načteme je z databáze
        if (!$questions) {
            $questions = $this->loadQuestionsFromTestAnswers($test);
            session(['test_questions' => $questions]);
        }



        // Získat číslo otázky z parametru nebo použít aktuální
        $questionNumber = $request->get('q', 1);
        $currentQuestionIndex = max(0, min($questionNumber - 1, count($questions) - 1));
        
        // Aktualizovat aktuální otázku v session
        session(['test_current_question' => $currentQuestionIndex]);

        $currentQuestion = $questions[$currentQuestionIndex];
        $remainingTime = $test->getRemainingTime();
        
        // Zkontrolovat, zda je aktuální otázka zodpovězena
        $selectedAnswer = $test->testAnswers()->where('question_id', $currentQuestion['id'])->first();
        $selectedAnswerId = $selectedAnswer ? $selectedAnswer->selected_answer_id : null;
        
        return view('test.question', compact('currentQuestion', 'currentQuestionIndex', 'questions', 'remainingTime', 'test', 'selectedAnswerId'));
    }

    public function submitAnswer(Request $request)
    {
        $test = $this->getCurrentTest();
        
        if (!$test) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Žádný aktivní test nenalezen'], 400);
            }
            return redirect()->route('test.index')->with('error', 'Žádný aktivní test nenalezen');
        }

        // Kontrola časového limitu
        if ($test->isTimeExpired()) {
            $test->update([
                'status' => 'expired',
                'time_expired' => true,
                'completed_at' => now()
            ]);
            if ($request->expectsJson()) {
                return response()->json(['time_expired' => true], 400);
            }
            return redirect()->route('test.result')->with('time_expired', true);
        }

        $questions = session('test_questions');
        
        // Pokud nejsou otázky v session, načteme je z databáze
        if (!$questions) {
            $questions = $this->loadQuestionsFromTestAnswers($test);
            session(['test_questions' => $questions]);
        }

        $questionId = $request->get('question_id');
        $selectedAnswerId = $request->get('answer_id');
        
        // Najít aktuální otázku
        $currentQuestion = collect($questions)->firstWhere('id', $questionId);
        if (!$currentQuestion) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Otázka nenalezena'], 400);
            }
            return redirect()->route('test.question')->with('error', 'Otázka nenalezena');
        }
        
        // Najít správnou odpověď
        $correctAnswer = collect($currentQuestion['answers'])->firstWhere('is_correct', true);
        $isCorrect = $correctAnswer && $correctAnswer['id'] == $selectedAnswerId;
        
        // Zkontrolovat, zda už není odpověď uložena
        $existingAnswer = $test->testAnswers()->where('question_id', $questionId)->first();
        if ($existingAnswer) {
            // Aktualizovat existující odpověď
            $existingAnswer->update([
                'selected_answer_id' => $selectedAnswerId,
                'is_correct' => $isCorrect,
                'points_earned' => $isCorrect ? $currentQuestion['points'] : 0,
                'answered_at' => now(),
            ]);
        } else {
            // Vytvořit novou odpověď
            TestAnswer::create([
                'test_id' => $test->id,
                'question_id' => $questionId,
                'selected_answer_id' => $selectedAnswerId,
                'is_correct' => $isCorrect,
                'points_earned' => $isCorrect ? $currentQuestion['points'] : 0,
                'points_possible' => $currentQuestion['points'],
                'answered_at' => now(),
            ]);
        }
        
        // Aktualizovat skóre v testu
        $newScore = $test->testAnswers()->sum('points_earned');
        $test->update(['earned_points' => $newScore]);
        
        if ($request->expectsJson()) {
            $totalQuestions = count($questions);
            $answeredQuestions = $test->testAnswers()->count();
            
            return response()->json([
                'success' => true, 
                'message' => 'Odpověď byla uložena',
                'answered_questions' => $answeredQuestions,
                'total_questions' => $totalQuestions,
                'all_answered' => $answeredQuestions >= $totalQuestions
            ]);
        }
        
        return redirect()->route('test.question');
    }

    public function finishTest()
    {
        $test = $this->getCurrentTest();
        
        if (!$test) {
            return redirect()->route('test.index')->with('error', 'Žádný aktivní test nenalezen');
        }

        $questions = session('test_questions');
        if (!$questions) {
            $questions = $this->loadQuestionsFromTestAnswers($test);
        }

        $totalQuestions = count($questions);
        $answeredQuestions = $test->testAnswers()->count();
        
        // Zkontrolovat, zda jsou všechny otázky zodpovězeny
        if ($answeredQuestions < $totalQuestions) {
            return redirect()->route('test.question')->with('error', 'Nejsou zodpovězeny všechny otázky');
        }

        // Dokončit test
        $this->completeTest($test);
        return redirect()->route('test.result');
    }

    public function showResult()
    {
        // Zkusit najít test z session nebo databáze
        $testId = session('test_id');
        if ($testId) {
            $test = Test::where('id', $testId)
                ->where('user_id', Auth::id())
                ->whereIn('status', ['completed', 'expired'])
                ->first();
        } else {
            $test = null;
        }
        
        if (!$test) {
            return redirect()->route('test.index')->with('error', 'Žádný dokončený test nenalezen');
        }

        $questions = session('test_questions');
        $testAnswers = $test->testAnswers()->with([
            'question.translations',
            'question.mediaContent',
            'question.mediaContents',
            'selectedAnswer.translations',
            'selectedAnswer.mediaContent',
            'selectedAnswer.mediaContents'
        ])->get();
        
        $timeExpired = $test->time_expired;

        return view('test.result', compact(
            'test',
            'questions', 
            'testAnswers', 
            'timeExpired'
        ));
    }

    private function getCurrentTest(): ?Test
    {
        // Zkusit najít test z session
        $testId = session('test_id');
        if ($testId) {
            $test = Test::find($testId);
            if ($test && $test->user_id === Auth::id() && $test->isInProgress()) {
                return $test;
            }
        }

        // Zkusit najít aktivní test z databáze
        return Auth::user()->getActiveTest();
    }

    private function completeTest(Test $test): void
    {
        $earnedPoints = $test->testAnswers()->sum('points_earned');
        $percentage = $test->total_points > 0 ? ($earnedPoints / $test->total_points) * 100 : 0;
        $passed = $percentage >= 86; // 86% = 43 bodů z 50

        $test->update([
            'status' => 'completed',
            'earned_points' => $earnedPoints,
            'percentage' => $percentage,
            'passed' => $passed,
            'completed_at' => now()
        ]);

        // Vyčistit session, ale zachovat test_id pro zobrazení výsledků
        session()->forget(['test_questions', 'test_current_question']);
    }

    public function testHistory(Request $request)
    {
        $tests = Auth::user()->tests()
            ->whereIn('status', ['completed', 'expired', 'cancelled'])
            ->orderBy('completed_at', 'desc')
            ->paginate(10);

        // Příprava dat pro graf - seřadit od nejstaršího po nejnovější
        $allTests = Auth::user()->tests()
            ->whereIn('status', ['completed', 'expired', 'cancelled'])
            ->orderBy('completed_at', 'asc') // Od nejstaršího po nejnovější
            ->get();

        $chartData = $allTests->map(function($test, $index) {
            return [
                'testNumber' => $index + 1,
                'points' => $test->earned_points ?? 0,
                'date' => $test->completed_at ? $test->completed_at->format('d.m.Y') : 'N/A',
                'datetime' => $test->completed_at ? $test->completed_at->format('d.m.Y H:i') : 'N/A',
                'status' => $test->status,
                'vehicleType' => $test->vehicle_type
            ];
        });

        return view('test.history', compact('tests', 'chartData'));
    }

    public function repeatTest(Test $test)
    {
        // Ověřit, že test patří aktuálnímu uživateli
        if ($test->user_id !== Auth::id()) {
            return redirect()->route('test.history')->with('error', 'Nemáte oprávnění k tomuto testu');
        }

        // Zkontrolovat, zda uživatel nemá aktivní test
        $activeTest = Auth::user()->getActiveTest();
        if ($activeTest) {
            return redirect()->route('test.question')->with('info', 'Máte aktivní test. Dokončete ho před zahájením nového.');
        }

        // Vytvořit nový test se stejnou konfigurací
        $newTest = Test::create([
            'user_id' => Auth::id(),
            'vehicle_type' => $test->vehicle_type,
            'status' => 'in_progress',
            'started_at' => now(),
            'time_limit_minutes' => 30,
            'total_questions' => $test->total_questions,
            'total_points' => $test->total_points,
        ]);

        // Vybrat nové otázky podle stejné konfigurace
        $testConfig = $this->getTestConfig($test->vehicle_type);
        $selectedQuestions = $this->selectQuestionsForTest($testConfig);

        // Uložit otázky do session
        session([
            'test_id' => $newTest->id,
            'test_questions' => $selectedQuestions,
            'test_current_question' => 0,
        ]);

        return redirect()->route('test.question')->with('success', 'Test byl úspěšně spuštěn');
    }

    public function showTestResult(Test $test)
    {
        // Ověřit, že test patří aktuálnímu uživateli
        if ($test->user_id !== Auth::id()) {
            return redirect()->route('test.history')->with('error', 'Nemáte oprávnění k tomuto testu');
        }

        // Ověřit, že test je dokončený (ne zrušený)
        if (!$test->isCompleted() && !$test->isExpired()) {
            return redirect()->route('test.history')->with('error', 'Test ještě není dokončen');
        }

        // Zrušené testy nemají výsledky
        if ($test->status === 'cancelled') {
            return redirect()->route('test.history')->with('error', 'Zrušené testy nemají výsledky');
        }

        $testAnswers = $test->testAnswers()->with([
            'question.translations', 
            'question.mediaContent',
            'question.mediaContents',
            'selectedAnswer.translations',
            'selectedAnswer.mediaContent',
            'selectedAnswer.mediaContents'
        ])->get();
        $timeExpired = $test->time_expired;

        return view('test.result', compact('test', 'testAnswers', 'timeExpired'));
    }

    public function cancelTest(Test $test)
    {
        // Ověřit, že test patří aktuálnímu uživateli
        if ($test->user_id !== Auth::id()) {
            return redirect()->route('test.index')->with('error', 'Nemáte oprávnění k tomuto testu');
        }

        // Ověřit, že test je aktivní
        if (!$test->isInProgress()) {
            return redirect()->route('test.index')->with('error', 'Test nelze zrušit - není aktivní');
        }

        // Označit test jako zrušený místo smazání
        $test->update([
            'status' => 'cancelled',
            'completed_at' => now(),
            'earned_points' => 0,
            'percentage' => 0,
            'passed' => false,
        ]);

        // Vyčistit session
        session()->forget(['test_id', 'test_questions', 'test_current_question']);

        return redirect()->route('test.index')->with('success', 'Test byl úspěšně zrušen');
    }

    public function deleteTest(Test $test)
    {
        // Ověřit, že test patří aktuálnímu uživateli
        if ($test->user_id !== Auth::id()) {
            return redirect()->route('test.history')->with('error', 'Nemáte oprávnění k tomuto testu');
        }

        // Ověřit, že test není aktivní
        if ($test->isInProgress()) {
            return redirect()->route('test.history')->with('error', 'Nelze smazat aktivní test');
        }

        // Smazat test (cascade delete smaže i test_answers)
        $test->delete();

        return redirect()->route('test.history')->with('success', 'Test byl úspěšně smazán');
    }

    private function loadQuestionsFromTestAnswers(Test $test): array
    {
        $testAnswers = $test->testAnswers()
            ->with([
                'question.translations',
                'question.answers.translations',
                'question.mediaContent',
                'question.mediaContents'
            ])
            ->orderBy('created_at')
            ->get();

        $questions = [];
        
        foreach ($testAnswers as $testAnswer) {
            $question = $testAnswer->question;
            
            // Načíst odpovědi pro otázku
            $answers = $question->answers()
                ->with(['translations', 'mediaContent', 'mediaContents'])
                ->get()
                ->map(function ($answer) {
                    return [
                        'id' => $answer->id,
                        'text' => $answer->translations->first()->text ?? 'N/A',
                        'is_correct' => $answer->is_correct,
                        'media_content' => $answer->mediaContent,
                        'media_contents' => $answer->mediaContents,
                    ];
                })
                ->toArray();

            $questions[] = [
                'id' => $question->id,
                'question_code' => $question->question_code,
                'text' => $question->translations->first()->text ?? 'N/A',
                'points' => $testAnswer->points_possible,
                'answers' => $answers,
                'media_content' => $question->mediaContent,
                'media_contents' => $question->mediaContents,
            ];
        }

        return $questions;
    }
}
