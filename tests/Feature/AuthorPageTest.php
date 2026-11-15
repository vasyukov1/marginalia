<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\Reading;
use App\Models\User;

it('shows an authors books and country to guests without personal statistics', function () {
    $author = Author::factory()->create(['name' => 'Жюль Верн', 'country' => 'Франция']);
    $book = Book::factory()->for($author)->create(['title' => 'Вокруг света']);
    Book::factory()->create(['title' => 'Другой автор']);
    Reading::factory()->for($book)->create(['period' => 'Private period']);

    $this->get('/authors/'.$author->id)->assertOk()->assertSee('Жюль Верн')->assertSee('🇫🇷')
        ->assertSee('Вокруг света')->assertDontSee('Другой автор')->assertDontSee('Private period')
        ->assertDontSee('Средняя оценка');
});

it('shows only the current users ratings and statistics for an author', function () {
    $user = User::factory()->create();
    $author = Author::factory()->create();
    $book = Book::factory()->for($author)->create(['pages' => 123]);
    Book::factory()->for($author)->create();
    Reading::factory()->for($book)->for($user)->create(['rating' => 7, 'period' => 'Мой период']);
    Reading::factory()->for($book)->create(['rating' => 1, 'period' => 'Чужой период']);
    Reading::factory()->for($user)->create(['rating' => 10]);

    $this->actingAs($user)->get('/authors/'.$author->id)->assertOk()
        ->assertSee('Мой период')->assertDontSee('Чужой период')
        ->assertViewHas('stats', ['books' => 1, 'authors' => 1, 'pages' => 123, 'rating' => 7.0]);
});

it('lists authors with personal reading counts', function () {
    $user = User::factory()->create();
    $author = Author::factory()->create();
    $book = Book::factory()->for($author)->create();
    Reading::factory()->for($user)->for($book)->create(['rating' => 9]);
    Reading::factory()->for($book)->create(['rating' => 1]);

    $this->actingAs($user)->get('/authors')->assertOk()
        ->assertViewHas('authors', fn ($authors) => $authors->find($author->id)->readings_count === 1
            && (float) $authors->find($author->id)->readings_avg_rating === 9.0);
});

it('returns not found for a missing author', function () {
    $this->get('/authors/9999')->assertNotFound();
});
