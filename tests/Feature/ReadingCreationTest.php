<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\Reading;
use App\Models\User;

it('requires authentication to add books', function () {
    $this->get('/readings/create')->assertRedirect('/login');
    $this->post('/readings', ['mode' => 'new'])->assertRedirect('/login');
    $this->assertDatabaseCount('books', 0);
});

it('adds a new book to the shared catalog and only the current readers library', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($user)->post('/readings', [
        'mode' => 'new', 'title' => '  Новая   книга ', 'author_name' => ' Новый  автор ',
        'country' => 'Франция', 'pages' => 120, 'publication_year' => 2020,
        'genre' => 'Роман', 'rating' => 9, 'period' => 'Осень 2026',
        'user_id' => $other->id, 'created_by_user_id' => $other->id,
    ])->assertRedirect('/dashboard');

    $book = Book::sole();
    expect($book->title)->toBe('Новая книга')
        ->and($book->author->name)->toBe('Новый автор')
        ->and($book->created_by_user_id)->toBe($user->id);
    $this->assertDatabaseHas('readings', ['user_id' => $user->id, 'book_id' => $book->id, 'rating' => 9]);
    $this->actingAs($other)->get('/')->assertSee('Новая книга');
    $this->get('/readings/create')->assertOk()->assertSee('Новая книга');
    $this->post('/readings', ['mode' => 'existing', 'book_id' => $book->id, 'rating' => 7])
        ->assertRedirect('/dashboard');
    $this->assertDatabaseCount('books', 1);
    $this->assertDatabaseCount('authors', 1);
    $this->assertDatabaseCount('readings', 2);
    $this->assertDatabaseHas('readings', ['user_id' => $other->id, 'book_id' => $book->id, 'rating' => 7]);
});

it('reuses normalized authors and books without changing shared data or existing readings', function () {
    $author = Author::factory()->create(['name' => 'Новый Автор', 'country' => 'Россия']);
    $book = Book::factory()->for($author)->create(['title' => 'Новая Книга', 'pages' => 200]);
    $reading = Reading::factory()->for($book)->create(['rating' => 8, 'notes' => 'Моя заметка']);

    $this->actingAs($reading->user)->post('/readings', [
        'mode' => 'new', 'title' => ' новая   книга ', 'author_name' => ' новый  автор ',
        'country' => 'США', 'pages' => 15, 'rating' => 1,
    ])->assertRedirect('/dashboard');

    $this->assertDatabaseCount('books', 1);
    $this->assertDatabaseCount('authors', 1);
    $this->assertDatabaseCount('readings', 1);
    expect($book->fresh()->pages)->toBe(200)
        ->and($author->fresh()->country)->toBe('Россия')
        ->and($reading->fresh()->rating)->toBe(8)
        ->and($reading->fresh()->notes)->toBe('Моя заметка');

    $this->post('/readings', ['mode' => 'new', 'title' => 'Другая книга', 'author_name' => 'НОВЫЙ АВТОР'])
        ->assertRedirect('/dashboard');
    $this->assertDatabaseCount('authors', 1);
    $this->assertDatabaseCount('books', 2);
});

it('ignores new book fields when choosing an existing book', function () {
    $book = Book::factory()->create();
    $this->actingAs(User::factory()->create())->post('/readings', [
        'mode' => 'existing', 'book_id' => $book->id, 'title' => ['invalid'],
        'author_name' => 'Лишний автор', 'pages' => -1,
    ])->assertRedirect('/dashboard');
    $this->assertDatabaseCount('books', 1);
    $this->assertDatabaseCount('authors', 1);
    $this->assertDatabaseHas('readings', ['book_id' => $book->id, 'rating' => null]);
});

it('rejects invalid additions before creating any records', function (array $data, string $field) {
    $this->actingAs(User::factory()->create())->postJson('/readings', $data)
        ->assertUnprocessable()->assertJsonValidationErrors($field);
    $this->assertDatabaseCount('books', 0);
    $this->assertDatabaseCount('authors', 0);
    $this->assertDatabaseCount('readings', 0);
})->with([
    'unknown mode' => [['mode' => 'bad'], 'mode'],
    'missing book' => [['mode' => 'existing', 'book_id' => 999], 'book_id'],
    'missing title' => [['mode' => 'new', 'author_name' => 'Автор'], 'title'],
    'blank author' => [['mode' => 'new', 'title' => 'Книга', 'author_name' => '   '], 'author_name'],
    'invalid pages' => [['mode' => 'new', 'title' => 'Книга', 'author_name' => 'Автор', 'pages' => -1], 'pages'],
    'invalid year' => [['mode' => 'new', 'title' => 'Книга', 'author_name' => 'Автор', 'publication_year' => 'bad'], 'publication_year'],
    'invalid rating' => [['mode' => 'new', 'title' => 'Книга', 'author_name' => 'Автор', 'rating' => 11], 'rating'],
]);
