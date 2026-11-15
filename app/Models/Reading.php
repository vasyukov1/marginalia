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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
