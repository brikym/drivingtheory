<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Autoškola E-testy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="text-xl font-bold text-gray-800">
                    Autoškola E-testy
                </div>
                
                <ul class="flex space-x-6">
                    <li>
                        <a href="{{ route('questions.index') }}" 
                           class="text-gray-600 hover:text-gray-900 transition-colors duration-200">
                            Procházení otázek
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.import.index') }}" 
                           class="text-gray-600 hover:text-gray-900 transition-colors duration-200">
                            Import otázek
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>
</body>
</html>


