<x-app-layout>
    @section('title')
        @if(request()->routeIs('favorites.index'))
            Favoritos - Biblioteca Digital
        @elseif(request()->routeIs('profile.edit'))
             Perfil -Biblioteca Digital 
        @else
            Início - Biblioteca Digital
        @endif
    @endsection

    <div class="flex bg-gray-50 dark:bg-gray-900 min-h-screen overflow-x-hidden">

        <!-- Sidebar -->
        @include('dashboard.partials.sidebar')

        <!-- Conteúdo Principal ou Favoritos -->
        <div class="flex-1 min-w-0 overflow-y-auto">
            @if(request()->routeIs('favorites.index'))
                @include('dashboard.favorites', [
                    'books' => $books,
                    'readingProgress' => $readingProgress,
                    'favoriteBookIds' => $favoriteBookIds ?? []
                ])
            @else
                @include('dashboard.partials.main-content', [
                    'books' => $books,
                    'booksByCourse' => $booksByCourse ?? collect(),
                    'newBooks' => $newBooks,
                    'myBooks' => $myBooks,
                    'search' => $search,
                    'course' => $course,
                    'readingProgress' => $readingProgress,
                    'favoriteBookIds' => $favoriteBookIds ?? []
                ])
            @endif
        </div>
        
        <!-- Coluna Direita (Sticky) -->
        @if(!request()->routeIs('profile.edit') && !request()->routeIs('favorites.index'))
            <div class="hidden lg:block w-72 flex-shrink-0 p-4">
                @include('dashboard.partials.book-details-panel')
            </div>
        @endif

        <!-- Modais -->
        @include('dashboard.partials.modal-create')
        @include('dashboard.partials.modal-reader')
        @include('dashboard.partials.modal-submissions')

    </div>
</x-app-layout>
