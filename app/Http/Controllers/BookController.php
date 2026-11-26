<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBookRequest;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    public function show(Request $request, Book $book): View
    {
        return view('books.show', [
            'book' => $book->load('author'),
            'reading' => $request->user()?->readings()->where('book_id', $book->id)->first(),
        ]);
    }

    public function edit(Book $book): View
    {
        return view('books.edit', [
            'book' => $book,
            'authors' => Author::all()->sortBy('localized_name'),
        ]);
    }

    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
    {
        $book->update($request->validated());

        return to_route('books.show', $book)->with('status', __('Книга обновлена.'));
    }
}
