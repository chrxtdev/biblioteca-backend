<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $user = Auth::user();

        if ($user) {
            $view->with('readingProgress', $user->readingProgress);
            $view->with('favoriteBookIds', $user->favoriteBooks()->pluck('books.id')->toArray());
            
            // Injeta o total de livros (cacheado) para o widget lateral
            $view->with('totalBooksCount', app(\App\Services\BookService::class)->countVerifiedBooks());

            $view->with('myBooks', $user->books()->latest()->paginate(5, ['*'], 'my_page'));
        }
    }
}
