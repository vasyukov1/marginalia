<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Reading;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reading>
 */
class ReadingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'period' => '2026',
            'rating' => null,
            'notes' => null,
        ];
    }
}
