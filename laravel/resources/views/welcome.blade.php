@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-16">
    <div class="text-center">
        <h1 class="text-5xl font-bold text-gray-900 mb-6">
            Vítejte v Autoškole E-testy
        </h1>
        
        <p class="text-xl text-gray-600 mb-12 max-w-3xl mx-auto">
            Komplexní systém pro přípravu na zkoušky z autoškoly. Procházejte otázky podle kategorií a připravte se na úspěšné složení zkoušky.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('questions.index') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-8 rounded-lg text-lg transition-colors duration-200">
                Začít procházet otázky
            </a>
            
            <a href="{{ route('admin.import.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-4 px-8 rounded-lg text-lg transition-colors duration-200">
                Administrace
            </a>
        </div>
    </div>
</div>
@endsection
