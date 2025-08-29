<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ImportController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->group(function () {
    Route::get('/import', [ImportController::class, 'index'])->name('admin.import.index');
    Route::post('/import', [ImportController::class, 'store'])->name('admin.import.store');
});
