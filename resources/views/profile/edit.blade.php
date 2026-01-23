<x-app-layout>
    @section('title', 'Perfil - Biblioteca Digital')
    <style>
        nav[x-data] { display: none !important; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        [x-cloak] { display: none !important; }
    </style>

    <div x-data="{
            darkMode: localStorage.getItem('theme') === 'dark',

            toggleTheme() {
                this.darkMode = !this.darkMode;
                localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
                if (this.darkMode) document.documentElement.classList.add('dark');
                else document.documentElement.classList.remove('dark');
            },

            init() {
                if (this.darkMode) document.documentElement.classList.add('dark');
            }
        }"
        x-init="init()"
        :class="{ 'dark': darkMode }"
        class="bg-gray-50 dark:bg-gray-900 min-h-screen font-sans transition-colors duration-300 flex">

        <!-- Sidebar (Coluna Esquerda) -->
        @include('dashboard.partials.sidebar')

        <!-- Conteúdo Principal (Coluna Central) -->
        <div class="flex-1 max-w-5xl mx-auto p-6">
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center">
                        <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ substr(Auth::user()->name, 0, 2) }}</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Meu Perfil</h1>
                        <p class="text-gray-500 dark:text-gray-400">Gerencie suas informações pessoais</p>
                    </div>
                </div>
            </div>

            <!-- Formulários -->
            <div class="space-y-6">
                <!-- Informações do Perfil -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Informações Pessoais</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Atualize seu nome e email</p>
                        </div>
                    </div>
                    <div class="max-w-2xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <!-- Senha -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Senha</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Altere sua senha de acesso</p>
                        </div>
                    </div>
                    <div class="max-w-2xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <!-- Excluir Conta -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-red-500">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Excluir Conta</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Remova permanentemente sua conta e dados</p>
                        </div>
                    </div>
                    <div class="max-w-2xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

