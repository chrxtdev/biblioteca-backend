<x-app-layout>
    <div class="flex bg-gray-50 dark:bg-gray-900">

        <!-- Sidebar -->
        @include('dashboard.partials.sidebar')

        <!-- Conteúdo Principal ou Favoritos -->
        <div class="flex-1 overflow-y-auto">
            @if(request()->routeIs('favorites.index'))
                @include('dashboard.favorites', [
                    'books' => $books,
                    'readingProgress' => $readingProgress,
                    'favoriteBookIds' => $favoriteBookIds ?? []
                ])
            @else
                @include('dashboard.partials.main-content', [
                    'books' => $books,
                    'newBooks' => $newBooks,
                    'myBooks' => $myBooks,
                    'search' => $search,
                    'course' => $course,
                    'readingProgress' => $readingProgress,
                    'favoriteBookIds' => $favoriteBookIds ?? []
                ])
            @endif
        </div>

        <!-- Coluna Direita -->
        @if(!request()->routeIs('profile.edit'))
            @include('dashboard.partials.book-details-panel')
        @endif

        <!-- Modais -->
        @include('dashboard.partials.modal-create')
        @include('dashboard.partials.modal-reader')
        @include('dashboard.partials.modal-submissions')

    </div>
</x-app-layout>
