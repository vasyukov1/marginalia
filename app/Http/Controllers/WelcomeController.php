<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookTableRequest;
use App\Models\Author;
use App\Models\Book;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    public function __invoke(BookTableRequest $request): View
    {
        return view('welcome', [
            'books' => Book::forTable($request->validated())->get(),
            'authors' => Author::orderBy('name_key')->get(),
            'genres' => Book::whereNotNull('genre')->distinct()->orderBy('genre')->pluck('genre'),
            'filters' => $request->validated(),
        ]);
    }
}
