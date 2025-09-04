<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::published()
            ->ordered()
            ->with(['translations' => function ($query) {
                $query->where('locale', app()->getLocale());
            }])
            ->paginate(10);

        return view('news.index', compact('news'));
    }

    public function show($id)
    {
        $news = News::published()
            ->with(['translations' => function ($query) {
                $query->where('locale', app()->getLocale());
            }])
            ->findOrFail($id);

        return view('news.show', compact('news'));
    }
}
