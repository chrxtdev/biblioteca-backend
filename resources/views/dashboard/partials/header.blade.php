{{-- CABEÇALHO --}}
<div class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-40 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            {{-- Logo --}}
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/unicentroma-logo.png') }}" alt="Logo" class="h-12 w-auto object-contain">
                <div class="hidden md:block">
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white leading-none">Biblioteca Digital</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Acervo Universitário</p>
                </div>
            </div>

            {{-- Ações (Dark Mode + Botões) --}}
            <div class="flex items-center gap-4">
                {{-- Botão Dark Mode --}}
                <button @click="toggleTheme()" class="p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition">
                    <svg x-show="!darkMode" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    <svg x-show="darkMode" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </button>

                {{-- Botão Enviar Livro (Abre Modal) --}}
                <button @click="showCreate = true" 
                   class="flex items-center gap-2 bg-indigo-600 text-white px-5 py-2.5 rounded-full font-semibold text-sm hover:bg-indigo-700 transition shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span class="hidden sm:inline">Enviar Livro</span>
                </button>
                
                <div class="h-8 w-px bg-gray-300 dark:bg-gray-600 mx-2"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 font-medium text-sm transition">
                        Sair
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>