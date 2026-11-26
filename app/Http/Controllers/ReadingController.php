<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReadingRequest;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ReadingController extends Controller
{
    public function create(): View
    {
        $books = Book::with('author')->get()->sortBy('localized_title')->values();
        $authors = Author::all()->sortBy('localized_name')->values();

        return view('readings.create', [
            'authors' => $authors,
            'bookSuggestions' => $books->map(fn (Book $book): array => [
                'id' => $book->id,
                'label' => $book->localized_title,
                'search' => $book->title.' '.$book->title_en,
                'author_id' => $book->author_id,
                'author' => $book->author->localized_name,
                'author_search' => $book->author->name.' '.$book->author->name_en,
            ]),
            'authorSuggestions' => $authors->map(fn (Author $author): array => [
                'id' => $author->id,
                'label' => $author->localized_name,
                'search' => $author->name.' '.$author->name_en,
            ]),
        ]);
    }

    public function store(StoreReadingRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $reading = DB::transaction(function () use ($request, $data) {
            if ($data['mode'] === 'existing') {
                $book = Book::findOrFail($data['book_id']);
            } else {
                $author = Author::firstOrCreate(
                    ['name_key' => Str::lower($data['author_name'])],
                    ['name' => $data['author_name'], 'country' => $data['country'] ?? null],
                );
                $book = $author->books()->firstOrCreate(
                    ['title_key' => Str::lower($data['title'])],
                    [
                        'title' => $data['title'],
                        'pages' => $data['pages'] ?? null,
                        'publication_year' => $data['publication_year'] ?? null,
                        'genre' => $data['genre'] ?? null,
                        'created_by_user_id' => $request->user()->id,
                    ],
                );
            }

            return $request->user()->readings()->firstOrCreate(
                ['book_id' => $book->id],
                ['period' => $data['period'] ?? null, 'rating' => $data['rating'] ?? null, 'notes' => $data['notes'] ?? null],
            );
        });

        return to_route('dashboard')->with('status', $reading->wasRecentlyCreated
            ? __('Книга добавлена в прочитанные.')
            : __('Эта книга уже есть в ваших прочитанных.'));
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $data = $request->validate([
            'period' => ['nullable', 'string', 'max:100'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:10'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ], [
            'rating.integer' => __('Оценка должна быть целым числом от 1 до 10.'),
            'rating.min' => __('Оценка должна быть от 1 до 10.'),
            'rating.max' => __('Оценка должна быть от 1 до 10.'),
            'period.max' => __('Период чтения — не более 100 символов.'),
            'notes.max' => __('Заметка — не более 2000 символов.'),
        ]);

        $request->user()->readings()->updateOrCreate(['book_id' => $book->id], $data);

        return to_route('books.show', $book)->with('status', __('Запись о чтении сохранена.'));
    }

    public function destroy(Request $request, Book $book): RedirectResponse
    {
        $request->user()->readings()->where('book_id', $book->id)->delete();

        return to_route('books.show', $book)->with('status', __('Книга убрана из прочитанных.'));
    }
}
