<?php

use App\Models\Author;
use App\Models\Book;
use Illuminate\Database\QueryException;

it('shares one author between several books', function () {
    $author = Author::factory()->create();
    $books = Book::factory()->count(2)->for($author)->create();

    expect($author->books->modelKeys())->toEqualCanonicalizing($books->modelKeys());
    expect($books->first()->author->is($author))->toBeTrue();
});

it('rejects repeated author names regardless of case and spacing', function () {
    Author::factory()->create(['name' => 'Жюль Верн']);

    expect(fn () => Author::factory()->create(['name' => '  ЖЮЛЬ   ВЕРН  ']))
        ->toThrow(QueryException::class);
});

it('rejects repeated titles for the same author', function () {
    $author = Author::factory()->create();
    Book::factory()->for($author)->create(['title' => 'Вокруг света']);

    expect(fn () => Book::factory()->for($author)->create(['title' => '  ВОКРУГ   СВЕТА  ']))
        ->toThrow(QueryException::class);
});

it('allows different authors to use the same title', function () {
    Book::factory()->create(['title' => 'Дом']);
    $book = Book::factory()->create(['title' => 'Дом']);

    $this->assertModelExists($book);
    $this->assertDatabaseCount('books', 2);
});
