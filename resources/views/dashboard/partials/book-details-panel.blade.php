<!-- Painel de Estatísticas (Coluna Direita) -->
<div class="w-80 bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-6">
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

    <!-- Categorias Populares -->
    <div>
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Categorias Populares</h3>
        <div class="space-y-2">
            <div class="flex items-center justify-between p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg cursor-pointer transition-colors">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Engenharia Civil</span>
                </div>
                <span class="text-xs text-gray-500 dark:text-gray-400">45 livros</span>
            </div>
            <div class="flex items-center justify-between p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg cursor-pointer transition-colors">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Direito</span>
                </div>
                <span class="text-xs text-gray-500 dark:text-gray-400">32 livros</span>
            </div>
            <div class="flex items-center justify-between p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg cursor-pointer transition-colors">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Administração</span>
                </div>
                <span class="text-xs text-gray-500 dark:text-gray-400">28 livros</span>
            </div>
            <div class="flex items-center justify-between p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg cursor-pointer transition-colors">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Psicologia</span>
                </div>
                <span class="text-xs text-gray-500 dark:text-gray-400">19 livros</span>
            </div>
        </div>
    </div>
</div>
