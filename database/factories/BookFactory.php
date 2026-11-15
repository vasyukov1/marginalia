<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'author_id' => Author::factory(),
            'pages' => fake()->numberBetween(80, 900),
            'genre' => 'Роман',
            'isbn' => fake()->unique()->isbn13(),
            'description' => fake()->paragraph(),
            'publication_year' => fake()->numberBetween(1450, (int) date('Y')),
            'created_by_user_id' => User::factory(),
        ];
    }
}
