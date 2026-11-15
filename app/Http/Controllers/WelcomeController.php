<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    public function __invoke(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'author' => ['nullable', 'integer', 'exists:authors,id'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        $search = Str::lower(Str::squish($filters['q'] ?? ''));
        $books = Book::with('author');

        if ($search !== '') {
            $books->where(function (Builder $query) use ($search) {
                $query->where('title_key', 'like', '%'.$search.'%')
                    ->orWhereHas('author', fn (Builder $authors) => $authors->where('name_key', 'like', '%'.$search.'%'));
            });
        }

        if (! empty($filters['author'])) {
            $books->where('author_id', $filters['author']);
        }

        return view('welcome', [
            'books' => $books->orderBy('title_key')->orderBy('id')->paginate(12)->withQueryString(),
            'authors' => Author::orderBy('name_key')->get(),
            'filters' => $filters,
            'bookCount' => Book::count(),
        ]);
    }
}
