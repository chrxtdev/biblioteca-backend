<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="appState" x-init="init()" @keydown.window="handleKeydown($event)">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <meta name="description" content="@yield('meta_description', 'Acesso à Biblioteca Digital da Unicentro. Explore nosso acervo.')">
    <meta property="og:title" content="@yield('title', config('app.name'))">
    <meta property="og:description" content="@yield('meta_description', 'Acesso à Biblioteca Digital da Unicentro.')">
    <meta property="og:image" content="{{ asset('images/unicentro-logo-new.png') }}">
    <meta property="twitter:card" content="summary_large_image">
    <link rel="canonical" href="{{ url()->current() }}" />

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts & Styles -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        /* Anti-flicker: sidebar sizing and child visibility via CSS */
        #app-sidebar { width: 16rem; }
        html.sidebar-closed #app-sidebar { width: 5rem; }
        html.sidebar-closed .sb-expanded { display: none; }
        html.sidebar-closed .sb-link { justify-content: center; gap: 0; }
        html.sidebar-closed .sb-footer-btns { flex-direction: column; align-items: center; }
        html.sidebar-closed .sb-btn { width: 2.5rem; height: 2.5rem; flex: none; }
        html.sidebar-closed .sb-btn-form { flex: none; }
        html.sidebar-closed .sb-toggle { position: static; }
        html.sidebar-closed .sb-header { justify-content: center; }
        /* Dark mode icon toggle via CSS (no Alpine needed) */
        html.dark .icon-light { display: none; }
        html:not(.dark) .icon-dark { display: none; }
    </style>

    <!-- PDF.js via CDN - versão 2.16.105 estável -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script>
        // Configurar o worker do PDF.js
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
        window.pdfjsLib = pdfjsLib;
    </script>


    <script>
        // Evita o "flash" do modo claro/escuro (FOUC)
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        // Anti-Flicker Sidebar (Executa antes do body renderizar)
        // Se estiver em mobile, sempre começa fechada (padrão)
        // Se desktop, respeita o localStorage ou abre por padrão
        const isMobile = window.innerWidth <= 1024;
        const storedSidebar = localStorage.getItem('sidebarOpen');
        const shouldBeOpen = storedSidebar !== null ? storedSidebar === 'true' : !isMobile;
        
        if (!shouldBeOpen) {
            document.documentElement.classList.add('sidebar-closed');
        } else {
            document.documentElement.classList.remove('sidebar-closed');
        }
    </script>
</head>
<body class="font-sans antialiased"
      data-flash-message="{{ session('status') === 'livro-enviado' ? 'Livro enviado com sucesso! 📚' : '' }}"
      data-flash-type="success">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <main>
            {{ $slot }}
        </main>
    </div>

    <!-- Toast Component -->
    <div x-show="toast.show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         class="fixed bottom-4 right-4 z-50 flex items-center w-full max-w-xs p-4 space-x-4 text-gray-500 bg-white rounded-lg shadow-xl dark:text-gray-400 dark:bg-gray-800 border border-gray-100 dark:border-gray-700"
         role="alert"
         x-cloak>
        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg"
             :class="toast.type === 'success' ? 'text-green-500 bg-green-100 dark:bg-green-800 dark:text-green-200' : 'text-red-500 bg-red-100 dark:bg-red-800 dark:text-red-200'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="toast.type === 'success'"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="toast.type !== 'success'"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </div>
        <div class="text-sm font-normal" x-text="toast.message"></div>
        <button @click="toast.show = false" type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700">
            <span class="sr-only">Close</span>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <script type="module">
        // Estado do PDF fora do Alpine para evitar Proxy wrapping
        window._pdfState = {
            doc: null,
            numPages: 0,
            renderedPages: []
        };

        // Watch viewMode changes to trigger re-render
        // Watch viewMode changes to trigger re-render
        document.addEventListener('alpine:initialized', () => {
            // Logic moved to app-state.js
        });
    </script>
</body>
</html>
