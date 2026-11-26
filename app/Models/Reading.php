<?php

namespace App\Models;

use Database\Factories\ReadingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reading extends Model
{
    /** @use HasFactory<ReadingFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = ['book_id', 'period', 'rating', 'notes'];

    public const SEASONS = ['winter' => 'Зима', 'spring' => 'Весна', 'summer' => 'Лето', 'autumn' => 'Осень'];

    /** @return array{year: ?int, season: ?string} */
    public function periodParts(): array
    {
        $period = mb_strtolower(trim($this->period ?? ''));
        $unknown = ['year' => null, 'season' => null];
        if (! preg_match('/^(?:(зима|весна|лето|осень|winter|spring|summer|autumn|fall)\s+)?([12]\d{3})(?:\s*[-–—]\s*(\d{2}|[12]\d{3}))?$/u', $period, $matches)) {
            return $unknown;
        }

        $season = match ($matches[1]) {
            'зима', 'winter' => 'winter',
            'весна', 'spring' => 'spring',
            'лето', 'summer' => 'summer',
            'осень', 'autumn', 'fall' => 'autumn',
            default => null,
        };
        $year = (int) $matches[2];
        if (isset($matches[3])) {
            $endYear = strlen($matches[3]) === 2 ? intdiv($year, 100) * 100 + (int) $matches[3] : (int) $matches[3];
            if ($endYear < $year && strlen($matches[3]) === 2) {
                $endYear += 100;
            }
            if ($season !== 'winter' || $endYear !== $year + 1) {
                return $unknown;
            }
            // Зима 2017–18 относится к 2018 году, в котором она закончилась.
            $year = $endYear;
        }

        return ['year' => $year, 'season' => $season];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
