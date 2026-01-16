<!-- Widgets (Coluna Direita) -->
<div class="w-80 p-6 space-y-6">

    <!-- Estatísticas Rápidas -->
    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-sm p-6 text-white">
        <h3 class="font-bold mb-4">Estatísticas da Biblioteca</h3>
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-sm opacity-90">Total de Livros</span>
                <span class="font-bold">
                    {{ isset($books) ? $books->count() : \App\Models\Book::where('is_verified', true)->count() }}
                </span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm opacity-90">Usuários Ativos</span>
                <span class="font-bold">4</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm opacity-90">Downloads Hoje</span>
                <span class="font-bold">56</span>
            </div>
        </div>
    </div>

    <!-- Categorias Populares -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h3 class="font-bold text-gray-900 dark:text-white mb-4">Categorias Populares</h3>
        <div class="flex flex-wrap gap-2">
            <span
                class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-xs rounded-full font-medium">
                Engenharia Civil
            </span>
            <span
                class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs rounded-full font-medium">
                Direito
            </span>
            <span
                class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs rounded-full font-medium">
                Enfermagem
            </span>
            <span
                class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 text-xs rounded-full font-medium">
                Administração
            </span>
            <span
                class="px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-xs rounded-full font-medium">
                Psicologia
            </span>
            <span
                class="px-3 py-1 bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300 text-xs rounded-full font-medium">
                Serviço Social
            </span>
            <span
                class="px-3 py-1 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 text-xs rounded-full font-medium">
                Geral/Outros
            </span>
            <span
                class="px-3 py-1 bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300 text-xs rounded-full font-medium">
                Fisioterapia
            </span>
        </div>
    </div>
</div>