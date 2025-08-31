<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Question;
use Illuminate\Http\Request;

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
}
