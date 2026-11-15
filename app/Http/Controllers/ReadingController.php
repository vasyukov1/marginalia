<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReadingController extends Controller
{
    public function update(Request $request, Book $book): RedirectResponse
    {
        $data = $request->validate([
            'period' => ['nullable', 'string', 'max:100'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:10'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ], [
            'rating.integer' => 'Оценка должна быть целым числом от 1 до 10.',
            'rating.min' => 'Оценка должна быть от 1 до 10.',
            'rating.max' => 'Оценка должна быть от 1 до 10.',
            'period.max' => 'Период чтения — не более 100 символов.',
            'notes.max' => 'Заметка — не более 2000 символов.',
        ]);

        $request->user()->readings()->updateOrCreate(['book_id' => $book->id], $data);

        return to_route('books.show', $book)->with('status', 'Запись о чтении сохранена.');
    }

    public function destroy(Request $request, Book $book): RedirectResponse
    {
        $request->user()->readings()->where('book_id', $book->id)->delete();

        return to_route('books.show', $book)->with('status', 'Книга убрана из прочитанных.');
    }
}
