<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookTableRequest;
use App\Models\Author;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthorController extends Controller
{
    public function index(Request $request): View
    {
        $authors = Author::withCount('books')->orderBy('name_key');
        if ($request->user() !== null) {
            $ownReadings = fn (Builder $readings) => $readings->where('user_id', $request->user()->id);
            $authors->withCount(['readings' => $ownReadings])
                ->withAvg(['readings' => $ownReadings], 'rating');
        }

        return view('authors.index', ['authors' => $authors->get()]);
    }

    public function show(BookTableRequest $request, Author $author): View
    {
        return view('authors.show', [
            'author' => $author,
            'books' => $author->books()->forTable($request->validated(), $request->user()?->id)->get(),
            'authors' => collect([$author]),
            'genres' => $author->books()->whereNotNull('genre')->distinct()->orderBy('genre')->pluck('genre'),
            'filters' => $request->validated(),
            'stats' => $request->user()?->readingStats($author),
        ]);
    }
}
