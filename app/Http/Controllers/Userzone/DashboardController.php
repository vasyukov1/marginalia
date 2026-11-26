<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookTableRequest;
use App\Models\Book;
use App\Models\Reading;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(BookTableRequest $request): View
    {
        $user = $request->user();
        $library = Book::whereHas('readings', fn (Builder $readings) => $readings->where('user_id', $user->id));
        $allBooks = (clone $library)->with('author')->get();
        $filters = $request->validated();
        $readings = $user->readings()->with('book.author')->get();
        $periodReadings = $readings->filter(function (Reading $reading) use ($filters): bool {
            $period = $reading->periodParts();

            return (empty($filters['reading_year']) || (string) ($period['year'] ?? 'unknown') === $filters['reading_year'])
                && (empty($filters['reading_season']) || ($period['season'] ?? 'unknown') === $filters['reading_season']);
        });
        $library->whereIn('id', $periodReadings->pluck('book_id'));

        $yearGroups = $readings->groupBy(fn (Reading $reading) => $reading->periodParts()['year'] ?? 'unknown')->sortKeysDesc();
        $seasonGroups = $readings->groupBy(function (Reading $reading): string {
            $period = $reading->periodParts();

            return ($period['year'] ?? __('Год не указан')).' / '.__(Reading::SEASONS[$period['season']] ?? 'Сезон не указан');
        })->sortBy(function (Collection $group): int {
            $period = $group->first()->periodParts();
            $seasonOrder = ['winter' => 1, 'spring' => 2, 'summer' => 3, 'autumn' => 4];

            // Сначала новые годы, внутри года — от зимы до осени.
            return (3000 - ($period['year'] ?? 0)) * 10 + ($seasonOrder[$period['season']] ?? 5);
        });
        $countryGroups = $periodReadings->groupBy(fn (Reading $reading) => $reading->book->author->country ?: __('Страна не указана'));

        return view('userzone.dashboard', [
            'user' => $user,
            'books' => $library->forTable($request->validated(), $user->id)->get(),
            'authors' => $allBooks->pluck('author')->unique('id')->sortBy('localized_name'),
            'genres' => $allBooks->pluck('genre')->filter()->unique()->sort(),
            'filters' => $filters,
            'stats' => $user->readingStats(),
            'readingYears' => $yearGroups->keys()->reject(fn ($year) => $year === 'unknown'),
            'seasons' => Reading::SEASONS,
            'yearStats' => $this->summarize($yearGroups),
            'seasonStats' => $this->summarize($seasonGroups),
            'countryStats' => $this->summarize($countryGroups)->sortByDesc('books'),
        ]);
    }

    /** @return Collection<string|int, array{books: int, pages: int, rating: ?float}> */
    private function summarize(Collection $groups): Collection
    {
        return $groups->map(function (Collection $readings): array {
            $rating = $readings->avg('rating');

            return [
                'books' => $readings->count(),
                'pages' => (int) $readings->sum('book.pages'),
                'rating' => $rating === null ? null : round($rating, 1),
            ];
        });
    }
}
