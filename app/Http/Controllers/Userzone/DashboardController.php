<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookTableRequest;
use App\Models\Book;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(BookTableRequest $request): View
    {
        $user = $request->user();
        $library = Book::whereHas('readings', fn (Builder $readings) => $readings->where('user_id', $user->id));
        $allBooks = (clone $library)->with('author')->get();

        return view('userzone.dashboard', [
            'user' => $user,
            'books' => $library->forTable($request->validated(), $user->id)->get(),
            'authors' => $allBooks->pluck('author')->unique('id')->sortBy('localized_name'),
            'genres' => $allBooks->pluck('genre')->filter()->unique()->sort(),
            'filters' => $request->validated(),
            'stats' => $user->readingStats(),
        ]);
    }
}
