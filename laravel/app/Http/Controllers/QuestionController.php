<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->with(['translations' => function ($query) {
                $query->where('locale', 'cs');
            }])
            ->get();

        return view('questions.index', compact('categories'));
    }

    public function showCategory(Category $category)
    {
        $questions = $category->questions()
            ->where('is_active', true)
            ->with(['translations' => function ($query) {
                $query->where('locale', 'cs');
            }, 'answers.translations' => function ($query) {
                $query->where('locale', 'cs');
            }, 'answers.mediaContent', 'mediaContent'])
            ->get();

        return view('questions.category', compact('category', 'questions'));
    }
}
