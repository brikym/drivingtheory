
---

## 1. Vytvoření nového Laravel projektu

Pokud ho ještě nemáš:

```bash
composer create-project laravel/laravel .
php artisan serve
```

---

## 2. Vytvoření základního routingu a controlleru

Chceme sekci administrace s položkou **Import otázek**.

### Route (routes/web.php)

```php
use App\Http\Controllers\Admin\ImportController;

Route::prefix('admin')->group(function () {
    Route::get('/import', [ImportController::class, 'index'])->name('admin.import.index');
    Route::post('/import', [ImportController::class, 'store'])->name('admin.import.store');
});
```

---

## 3. Controller

Vytvoříme controller:

```bash
php artisan make:controller Admin/ImportController
```

### app/Http/Controllers/Admin/ImportController.php

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    // Zobrazení formuláře
    public function index()
    {
        return view('admin.import.index');
    }

    // Zpracování nahraného souboru
    public function store(Request $request)
    {
        $request->validate([
            'questions_file' => 'required|file|mimes:json,txt',
        ]);

        $file = $request->file('questions_file');
        $jsonContent = file_get_contents($file->getRealPath());

        // Zkusíme dekódovat JSON
        $questions = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->with('error', 'Neplatný JSON soubor.');
        }

        // Zatím jen vypíšeme počet otázek
        return back()->with('success', 'Soubor načten. Počet otázek: ' . count($questions));
    }
}
```

---

## 4. Blade šablona

Vytvoříme složku `resources/views/admin/import/index.blade.php`.

```blade
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Import otázek</h1>

        @if(session('success'))
            <div style="color: green">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div style="color: red">{{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.import.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label for="questions_file">Vyber JSON soubor:</label>
            <input type="file" name="questions_file" id="questions_file" required>
            <button type="submit">Nahrát</button>
        </form>
    </div>
@endsection
```

---

## 5. Layout (kostra aplikace)

Vytvoříme `resources/views/layouts/app.blade.php`.

```blade
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Autoškola E-testy</title>
</head>
<body>
    <nav>
        <ul>
            <li><a href="{{ route('admin.import.index') }}">Import otázek</a></li>
        </ul>
    </nav>

    <main>
        @yield('content')
    </main>
</body>
</html>
```

---
