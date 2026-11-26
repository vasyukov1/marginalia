<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookTableRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Models\Author;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

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

        return view('authors.index', ['authors' => $authors->get()->sortBy('localized_name')->values()]);
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

    public function edit(Author $author): View
    {
        return view('authors.edit', ['author' => $author]);
    }

    public function update(UpdateAuthorRequest $request, Author $author): RedirectResponse
    {
        $oldPath = $author->avatar_path;
        $newPath = null;
        $author->fill($request->safe()->only(['name', 'name_en', 'country']));

        if ($request->hasFile('avatar')) {
            $newPath = $request->file('avatar')->store('authors', 'public');
            if ($newPath === false) {
                throw ValidationException::withMessages(['avatar' => __('Не удалось сохранить аватарку. Попробуйте ещё раз.')]);
            }
            $author->avatar_path = $newPath;
        } elseif ($request->boolean('remove_avatar')) {
            $author->avatar_path = null;
        }

        try {
            $author->save();
        } catch (Throwable $exception) {
            if ($newPath !== null) {
                Storage::disk('public')->delete($newPath);
            }
            throw $exception;
        }

        // Старую картинку удаляем только после успешного сохранения записи.
        if ($oldPath !== null && $oldPath !== $author->avatar_path) {
            Storage::disk('public')->delete($oldPath);
        }

        return to_route('authors.show', $author)->with('status', __('Данные автора обновлены.'));
    }
}
