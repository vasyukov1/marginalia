<?php

namespace App\Http\Controllers;

use App\Models\Book;
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
}
