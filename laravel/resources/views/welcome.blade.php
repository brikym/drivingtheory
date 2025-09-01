@extends('layouts.guest')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    {{-- Hero sekce --}}
    <div class="container mx-auto px-4 py-16">
        <div class="text-center">
            <h1 class="text-6xl font-bold text-gray-900 mb-6">
                Autoškola E-testy
            </h1>
            
            <p class="text-2xl text-gray-600 mb-8 max-w-4xl mx-auto">
                Nejlepší způsob přípravy na řidičské zkoušky pro automobily a motocykly
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-16">
                @auth
                    <a href="{{ route('test.index') }}" 
                       class="bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-8 rounded-lg text-lg transition-colors duration-200">
                        Začít test
                    </a>
                    
                    <a href="{{ route('questions.index') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-8 rounded-lg text-lg transition-colors duration-200">
                        Procházet otázky
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-8 rounded-lg text-lg transition-colors duration-200">
                        Přihlásit se
                    </a>
                    
                    <a href="{{ route('register') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-8 rounded-lg text-lg transition-colors duration-200">
                        Registrovat se
                    </a>
                @endauth
            </div>
        </div>
    </div>

    {{-- Funkce --}}
    <div class="bg-white py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center text-gray-900 mb-12">
                Co nabízíme?
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Oficiální testy</h3>
                    <p class="text-gray-600">
                        Testujte se v podmínkách skutečné zkoušky s časovým limitem 30 minut a oficiálním hodnocením.
                    </p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Procházení otázek</h3>
                    <p class="text-gray-600">
                        Studujte otázky podle kategorií, vyhledávejte konkrétní témata a připravte se důkladně.
                    </p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Automobily i motocykly</h3>
                    <p class="text-gray-600">
                        Podporujeme přípravu na zkoušky pro skupinu B (automobily) i skupinu A (motocykly).
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistiky --}}
    <div class="bg-gray-50 py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center text-gray-900 mb-12">
                Proč si vybrat naše testy?
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-4xl font-bold text-blue-600 mb-2">1000+</div>
                    <div class="text-gray-600">Otázek v databázi</div>
                </div>
                
                <div class="text-center">
                    <div class="text-4xl font-bold text-green-600 mb-2">95%</div>
                    <div class="text-gray-600">Úspěšnost našich studentů</div>
                </div>
                
                <div class="text-center">
                    <div class="text-4xl font-bold text-purple-600 mb-2">30 min</div>
                    <div class="text-gray-600">Časový limit jako na zkoušce</div>
                </div>
                
                <div class="text-center">
                    <div class="text-4xl font-bold text-orange-600 mb-2">24/7</div>
                    <div class="text-gray-600">Dostupnost testů</div>
                </div>
            </div>
        </div>
    </div>

    {{-- CTA sekce --}}
    <div class="bg-blue-600 py-16">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold text-white mb-6">
                Připravte se na úspěch
            </h2>
            
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                Neztrácejte čas a začněte se připravovat ještě dnes. Registrace je zdarma a získáte okamžitý přístup ke všem funkcím.
            </p>
            
            @guest
                <a href="{{ route('register') }}" 
                   class="bg-white hover:bg-gray-100 text-blue-600 font-bold py-4 px-8 rounded-lg text-lg transition-colors duration-200">
                    Začít zdarma
                </a>
            @endguest
        </div>
    </div>
</div>
@endsection
