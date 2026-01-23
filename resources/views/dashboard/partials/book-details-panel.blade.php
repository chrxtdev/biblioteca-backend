<!-- Painel de Estatísticas (Coluna Direita) -->
<div class="w-80 bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-6">

    @if(!request()->routeIs('favorites.index'))
    <!-- Estatísticas da Biblioteca -->
    <div>
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Estatísticas da Biblioteca</h3>
        <div class="space-y-3">
            <div class="flex justify-between items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <span class="text-blue-700 dark:text-blue-300 font-medium">Total de Livros</span>
                <span class="text-blue-900 dark:text-blue-100 font-bold">{{ $books->count() }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                <span class="text-green-700 dark:text-green-300 font-medium">Livros Novos</span>
                <span class="text-green-900 dark:text-green-100 font-bold">{{ $newBooks->count() }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                <span class="text-purple-700 dark:text-purple-300 font-medium">Meus Envios</span>
                <span class="text-purple-900 dark:text-purple-100 font-bold">{{ $myBooks->count() }}</span>
            </div>
        </div>
    </div>
    @endif

    <!-- Categorias Populares -->
    <div>
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Cursos por Categoria</h3>
        <div class="space-y-2">
            <a href="{{ request()->routeIs('favorites.index') ? route('favorites.index', ['course' => 'Engenharia Civil']) : route('aluno', ['course' => 'Engenharia Civil']) }}" class="block p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <div class="flex items-center gap-2"><div class="w-3 h-3 bg-blue-500 rounded-full"></div><span class="text-sm text-gray-700 dark:text-gray-300">Engenharia Civil</span></div>
            </a>
            <a href="{{ request()->routeIs('favorites.index') ? route('favorites.index', ['course' => 'Direito']) : route('aluno', ['course' => 'Direito']) }}" class="block p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <div class="flex items-center gap-2"><div class="w-3 h-3 bg-green-500 rounded-full"></div><span class="text-sm text-gray-700 dark:text-gray-300">Direito</span></div>
            </a>
            <a href="{{ request()->routeIs('favorites.index') ? route('favorites.index', ['course' => 'Administração']) : route('aluno', ['course' => 'Administração']) }}" class="block p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <div class="flex items-center gap-2"><div class="w-3 h-3 bg-purple-500 rounded-full"></div><span class="text-sm text-gray-700 dark:text-gray-300">Administração</span></div>
            </a>
            <a href="{{ request()->routeIs('favorites.index') ? route('favorites.index', ['course' => 'Psicologia']) : route('aluno', ['course' => 'Psicologia']) }}" class="block p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <div class="flex items-center gap-2"><div class="w-3 h-3 bg-yellow-500 rounded-full"></div><span class="text-sm text-gray-700 dark:text-gray-300">Psicologia</span></div>
            </a>
            <a href="{{ request()->routeIs('favorites.index') ? route('favorites.index', ['course' => 'Serviço Social']) : route('aluno', ['course' => 'Serviço Social']) }}" class="block p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <div class="flex items-center gap-2"><div class="w-3 h-3 bg-pink-500 rounded-full"></div><span class="text-sm text-gray-700 dark:text-gray-300">Serviço Social</span></div>
            </a>
            <a href="{{ request()->routeIs('favorites.index') ? route('favorites.index', ['course' => 'Fisioterapia']) : route('aluno', ['course' => 'Fisioterapia']) }}" class="block p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <div class="flex items-center gap-2"><div class="w-3 h-3 bg-teal-500 rounded-full"></div><span class="text-sm text-gray-700 dark:text-gray-300">Fisioterapia</span></div>
            </a>
            <a href="{{ request()->routeIs('favorites.index') ? route('favorites.index', ['course' => 'Enfermagem']) : route('aluno', ['course' => 'Enfermagem']) }}" class="block p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <div class="flex items-center gap-2"><div class="w-3 h-3 bg-sky-500 rounded-full"></div><span class="text-sm text-gray-700 dark:text-gray-300">Enfermagem</span></div>
            </a>
            <a href="{{ request()->routeIs('favorites.index') ? route('favorites.index', ['course' => 'Outros']) : route('aluno', ['course' => 'Outros']) }}" class="block p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <div class="flex items-center gap-2"><div class="w-3 h-3 bg-gray-500 rounded-full"></div><span class="text-sm text-gray-700 dark:text-gray-300">Geral/Outros</span></div>
            </a>
        </div>
    </div>
</div>
