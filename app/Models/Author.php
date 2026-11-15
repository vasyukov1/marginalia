<?php

namespace App\Models;

use Database\Factories\AuthorFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Author extends Model
{
    /** @use HasFactory<AuthorFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = ['name', 'country'];

    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn (string $value): array => [
                'name' => Str::squish($value),
                'name_key' => Str::lower(Str::squish($value)),
            ],
        );
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    protected function flag(): Attribute
    {
        return Attribute::make(get: fn (): string => match ($this->country) {
            'Англия' => "🏴\u{E0067}\u{E0062}\u{E0065}\u{E006E}\u{E0067}\u{E007F}",
            'Великобритания' => '🇬🇧',
            'США' => '🇺🇸',
            'Франция' => '🇫🇷',
            'Италия' => '🇮🇹',
            'Ирландия' => '🇮🇪',
            'Россия' => '🇷🇺',
            'СССР' => '',
            'Беларусь' => '🇧🇾',
            'Украина' => '🇺🇦',
            'Германия' => '🇩🇪',
            'Япония' => '🇯🇵',
            'Испания' => '🇪🇸',
            'Канада' => '🇨🇦',
            default => '',
        });
    }
}
