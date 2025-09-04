<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// News routes
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{id}', [NewsController::class, 'show'])->name('news.show');

// Social login routes
Route::get('/auth/{provider}/redirect', [App\Http\Controllers\Auth\SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [App\Http\Controllers\Auth\SocialAuthController::class, 'callback'])->name('social.callback');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Test routes (pouze pro user a admin, ne pro demo)
    Route::middleware('role:user')->group(function () {
        Route::get('/test', [QuestionController::class, 'test'])->name('test.index');
        Route::post('/test/start', [QuestionController::class, 'startTest'])->name('test.start');
        Route::get('/test/question', [QuestionController::class, 'showQuestion'])->name('test.question');
        Route::post('/test/answer', [QuestionController::class, 'submitAnswer'])->name('test.answer');
        Route::post('/test/finish', [QuestionController::class, 'finishTest'])->name('test.finish');
        Route::get('/test/result', [QuestionController::class, 'showResult'])->name('test.result');
        Route::get('/test/history', [QuestionController::class, 'testHistory'])->name('test.history');
        Route::get('/test/result/{test}', [QuestionController::class, 'showTestResult'])->name('test.result.show');
        Route::post('/test/repeat/{test}', [QuestionController::class, 'repeatTest'])->name('test.repeat');
        Route::delete('/test/cancel/{test}', [QuestionController::class, 'cancelTest'])->name('test.cancel');
        Route::delete('/test/delete/{test}', [QuestionController::class, 'deleteTest'])->name('test.delete');
        Route::post('/test/auto-complete/{test}', [QuestionController::class, 'autoCompleteTest'])->name('test.auto-complete');
    });
    
    // Language switching
    Route::post('/language/switch', [App\Http\Controllers\LanguageController::class, 'switch'])->name('language.switch');
    
    // Questions routes
    Route::get('/questions', [QuestionController::class, 'index'])->name('questions.index');
    Route::get('/questions/category/{category}', [QuestionController::class, 'showCategory'])->name('questions.category');
    
    // Admin routes
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('admin.users');
        Route::patch('/users/{user}/role', [App\Http\Controllers\AdminController::class, 'updateUserRole'])->name('admin.users.update-role');
        Route::get('/tests', [App\Http\Controllers\AdminController::class, 'tests'])->name('admin.tests');
        
        Route::get('/import', [ImportController::class, 'index'])->name('admin.import.index');
        Route::post('/import', [ImportController::class, 'store'])->name('admin.import.store');
        
        // News management routes
        Route::get('/news', [AdminNewsController::class, 'index'])->name('admin.news.index');
        Route::get('/news/create', [AdminNewsController::class, 'create'])->name('admin.news.create');
        Route::post('/news', [AdminNewsController::class, 'store'])->name('admin.news.store');
        Route::get('/news/{news}/edit', [AdminNewsController::class, 'edit'])->name('admin.news.edit');
        Route::put('/news/{news}', [AdminNewsController::class, 'update'])->name('admin.news.update');
        Route::delete('/news/{news}', [AdminNewsController::class, 'destroy'])->name('admin.news.destroy');
    });
});

require __DIR__.'/auth.php';
