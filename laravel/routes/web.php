<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\Admin\ImportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Social login routes
Route::get('/auth/{provider}/redirect', [App\Http\Controllers\Auth\SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [App\Http\Controllers\Auth\SocialAuthController::class, 'callback'])->name('social.callback');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Test routes
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
    
    // Questions routes
    Route::get('/questions', [QuestionController::class, 'index'])->name('questions.index');
    Route::get('/questions/category/{category}', [QuestionController::class, 'showCategory'])->name('questions.category');
    
    // Admin routes
    Route::prefix('admin')->group(function () {
        Route::get('/import', [ImportController::class, 'index'])->name('admin.import.index');
        Route::post('/import', [ImportController::class, 'store'])->name('admin.import.store');
    });
});

require __DIR__.'/auth.php';
