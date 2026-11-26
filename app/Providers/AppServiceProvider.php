<?php

namespace App\Providers;

use App\Models\Author;
use App\Models\Book;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('update-author', fn (User $user, Author $author): bool => $author->created_by_user_id === $user->id);
        Gate::define('update-book', fn (User $user, Book $book): bool => $book->created_by_user_id === $user->id);
    }
}
