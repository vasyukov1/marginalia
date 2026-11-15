<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\Reading;
use App\Models\User;

it('requires login to view a personal library', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

it('shows only the current readers books and statistics', function () {
    $user = User::factory()->create();
    $author = Author::factory()->create();
    $first = Book::factory()->for($author)->create(['title' => 'Моя первая книга', 'pages' => 100]);
    $second = Book::factory()->for($author)->create(['title' => 'Моя вторая книга', 'pages' => 200]);
    Reading::factory()->for($user)->for($first)->create(['rating' => 8, 'period' => 'Лето 2019']);
    Reading::factory()->for($user)->for($second)->create(['rating' => null]);
    Reading::factory()->for($first)->create(['rating' => 2, 'period' => 'Чужой период']);
    Reading::factory()->for(Book::factory()->create(['title' => 'Чужая книга']))->create();

    $this->actingAs($user)->get('/dashboard')->assertOk()
        ->assertSee('Моя первая книга')->assertSee('Моя вторая книга')->assertSee('Лето 2019')
        ->assertDontSee('Чужая книга')->assertDontSee('Чужой период')
        ->assertViewHas('stats', ['books' => 2, 'authors' => 1, 'pages' => 300, 'rating' => 8.0]);
});

it('keeps overall personal statistics when the table is filtered', function () {
    $user = User::factory()->create();
    Reading::factory()->for($user)->count(2)->create();

    $this->actingAs($user)->get('/dashboard?q=NoMatches')->assertOk()
        ->assertViewHas('books', fn ($books) => $books->isEmpty())
        ->assertViewHas('stats', fn ($stats) => $stats['books'] === 2);
});

it('handles a new users empty library', function () {
    $this->actingAs(User::factory()->create())->get('/dashboard')->assertOk()
        ->assertViewHas('stats', ['books' => 0, 'authors' => 0, 'pages' => 0, 'rating' => null]);
});

it('sorts personal ratings using only the current users readings', function () {
    $user = User::factory()->create();
    $first = Reading::factory()->for($user)->create(['rating' => 3]);
    $last = Reading::factory()->for($user)->create(['rating' => 9]);
    Reading::factory()->for($first->book)->create(['rating' => 10]);

    $this->actingAs($user)->get('/dashboard?sort=rating&direction=desc')->assertOk()
        ->assertViewHas('books', fn ($books) => $books->modelKeys() === [$last->book_id, $first->book_id]);
});
