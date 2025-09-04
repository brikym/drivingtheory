<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::with(['translations', 'author'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title_cs' => 'required|string|max:255',
            'content_cs' => 'required|string',
            'title_en' => 'required|string|max:255',
            'content_en' => 'required|string',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $news = News::create([
            'author_id' => Auth::id(),
            'is_published' => $request->boolean('is_published'),
            'published_at' => $request->boolean('is_published') ? ($request->published_at ?: now()) : null,
        ]);

        // Create Czech translation
        $news->translations()->create([
            'locale' => 'cs',
            'title' => $request->title_cs,
            'content' => $request->content_cs,
            'slug' => Str::slug($request->title_cs),
        ]);

        // Create English translation
        $news->translations()->create([
            'locale' => 'en',
            'title' => $request->title_en,
            'content' => $request->content_en,
            'slug' => Str::slug($request->title_en),
        ]);

        return redirect()->route('admin.news.index')->with('success', 'Novinka byla úspěšně vytvořena.');
    }

    public function edit(News $news)
    {
        $news->load('translations');
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $request->validate([
            'title_cs' => 'required|string|max:255',
            'content_cs' => 'required|string',
            'title_en' => 'required|string|max:255',
            'content_en' => 'required|string',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $news->update([
            'is_published' => $request->boolean('is_published'),
            'published_at' => $request->boolean('is_published') ? ($request->published_at ?: now()) : null,
        ]);

        // Update Czech translation
        $news->translations()->updateOrCreate(
            ['locale' => 'cs'],
            [
                'title' => $request->title_cs,
                'content' => $request->content_cs,
                'slug' => Str::slug($request->title_cs),
            ]
        );

        // Update English translation
        $news->translations()->updateOrCreate(
            ['locale' => 'en'],
            [
                'title' => $request->title_en,
                'content' => $request->content_en,
                'slug' => Str::slug($request->title_en),
            ]
        );

        return redirect()->route('admin.news.index')->with('success', 'Novinka byla úspěšně aktualizována.');
    }

    public function destroy(News $news)
    {
        $news->delete();
        return redirect()->route('admin.news.index')->with('success', 'Novinka byla úspěšně smazána.');
    }
}
