<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="description" content="@yield('meta_description', 'Acesso à Biblioteca Digital da Unicentro. Login, cadastro e recuperação de senha.')">
    <meta property="og:title" content="@yield('title', config('app.name'))">
    <meta property="og:description" content="@yield('meta_description', 'Acesso à Biblioteca Digital da Unicentro.')">
    <meta property="og:image" content="{{ asset('images/unicentro-logo-new.png') }}">
    <meta property="twitter:card" content="summary_large_image">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full font-sans antialiased text-gray-900">
    <div class="min-h-screen flex">
        <!-- Lado Esquerdo - Formulário -->
        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24 bg-white w-full lg:w-1/2 z-10">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <!-- Logo Mobile -->
                <div class="lg:hidden text-center mb-10">
                    <a href="/">
                        <img class="h-16 w-auto mx-auto" src="{{ asset('images/unicentro-logo-new.png') }}" alt="Unicentro">
                    </a>
                </div>

                <!-- Conteúdo do Form -->
                {{ $slot }}
                
            </div>
        </div>

        <!-- Lado Direito - Imagem (Apenas Desktop) -->
        <div class="hidden lg:block relative w-0 flex-1">
            <img class="absolute inset-0 h-full w-full object-cover" src="{{ asset('images/auth-hero.png') }}" alt="Campus Unicentro" width="1920" height="1080">
            
            <!-- Overlay Gradiente com cores da marca -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/80 via-blue-800/60 to-teal-700/50 mix-blend-multiply"></div>
            
            <!-- Texto/Logo sobre a imagem -->
            <div class="absolute inset-0 flex flex-col justify-center items-center text-white p-12 text-center pointer-events-none">
                <img class="h-32 w-auto mb-8 drop-shadow-lg" src="{{ asset('images/unicentro-logo-new.png') }}" alt="Unicentro" width="128" height="128">
                <h2 class="text-4xl font-bold mb-4 drop-shadow-md">Bem-vindo à Biblioteca Digital</h2>
                <p class="text-lg text-blue-100 max-w-lg drop-shadow">
                    Acesso ilimitado ao conhecimento. Explore, pesquise e aprenda com nosso vasto acervo digital.
                </p>
            </div>
        </div>
    </div>
    
    <script type="module">
        // Start Alpine manualmente para este layout, já que removemos do app.js global
        // para evitar conflito com o dashboard que tem seu próprio setup.
        if (window.Alpine) {
            Alpine.start();
        }
    </script>
</body>
</html>
