<?php

namespace App\Models;

use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'title_en',
        'author_id',
        'pages',
        'genre',
        'isbn',
        'description',
        'publication_year',
        'created_by_user_id',
    ];

    protected function title(): Attribute
    {
        return Attribute::make(
            set: fn (string $value): array => [
                'title' => Str::squish($value),
                'title_key' => Str::lower(Str::squish($value)),
            ],
        );
    }

    /** Название для интерфейса; исходное title остаётся неизменным. */
    protected function localizedTitle(): Attribute
    {
        return Attribute::make(
            get: fn (): string => app()->getLocale() === 'en' && $this->title_en
                ? $this->title_en
                : $this->title,
        );
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function readings(): HasMany
    {
        return $this->hasMany(Reading::class);
    }

    /** @param array{q?: ?string, author?: mixed, country?: ?string, genre?: ?string, sort?: ?string, direction?: ?string} $filters */
    #[Scope]
    protected function forTable(Builder $query, array $filters, ?int $userId = null): void
    {
        $query->with('author');
        if ($userId !== null) {
            $query->with(['readings' => fn (HasMany $readings) => $readings->where('user_id', $userId)]);
        }

        $searchTerm = Str::squish($filters['q'] ?? '');
        $search = Str::lower($searchTerm);
        if ($search !== '') {
            $query->where(function (Builder $books) use ($search, $searchTerm) {
                $books->where('title_key', 'like', '%'.$search.'%')
                    ->orWhereLike('title_en', '%'.$search.'%')
                    ->orWhereHas('author', fn (Builder $authors) => $authors
                        ->where('name_key', 'like', '%'.$search.'%')
                        ->orWhereLike('name_en', '%'.$search.'%')
                        ->orWhere('country', 'like', '%'.$searchTerm.'%'));

                if (ctype_digit($search)) {
                    $books->orWhere('publication_year', (int) $search);
                }
            });
        }
        if (! empty($filters['author'])) {
            $query->where('author_id', $filters['author']);
        }
        if (! empty($filters['country'])) {
            $query->whereHas('author', fn (Builder $authors) => $authors->where('country', $filters['country']));
        }
        if (! empty($filters['genre'])) {
            $query->where('genre', $filters['genre']);
        }

        $sort = $filters['sort'] ?? 'title';
        $direction = ($filters['direction'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
        $columns = ['title' => 'title_key', 'genre' => 'genre', 'year' => 'publication_year', 'pages' => 'pages'];

        if (in_array($sort, ['author', 'country'], true)) {
            $column = $sort === 'author' ? 'name_key' : 'country';
            $authorName = app()->getLocale() === 'en' && $sort === 'author'
                ? Author::selectRaw('LOWER(COALESCE(name_en, name_key))')
                : Author::select($column);
            $query->orderBy($authorName->whereColumn('authors.id', 'books.author_id'), $direction);
        } elseif (in_array($sort, ['period', 'rating'], true) && $userId !== null) {
            $query->orderBy(Reading::select($sort)->whereColumn('readings.book_id', 'books.id')->where('user_id', $userId), $direction);
        } elseif ($sort === 'title' && app()->getLocale() === 'en') {
            $query->orderByRaw('LOWER(COALESCE(title_en, title_key)) '.$direction);
        } else {
            $query->orderBy($columns[$sort] ?? 'title_key', $direction);
        }
        $query->orderBy('books.id');
    }
}
