<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\QuestionController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/questions', [QuestionController::class, 'index'])->name('questions.index');
Route::get('/questions/category/{category}', [QuestionController::class, 'showCategory'])->name('questions.category');

Route::prefix('admin')->group(function () {
    Route::get('/import', [ImportController::class, 'index'])->name('admin.import.index');
    Route::post('/import', [ImportController::class, 'store'])->name('admin.import.store');
});
