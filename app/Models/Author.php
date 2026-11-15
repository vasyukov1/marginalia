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
}
