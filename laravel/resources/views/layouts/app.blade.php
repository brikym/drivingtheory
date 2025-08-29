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


