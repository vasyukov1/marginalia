<?php

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\QueryException;

it('creates a catalog book for the user who submitted it', function () {
    $user = User::factory()->create();
    $book = Book::factory()->for($user, 'createdBy')->create([
        'title' => 'The Left Hand of Darkness',
        'author_name' => 'Ursula K. Le Guin',
        'isbn' => '9780441478125',
    ]);

    $this->assertModelExists($book);
    expect($book->createdBy->is($user))->toBeTrue();
});

it('lists only the books submitted by a user', function () {
    $user = User::factory()->create();
    $submittedBooks = Book::factory()->count(2)->for($user, 'createdBy')->create();
    Book::factory()->create();

    expect($user->submittedBooks()->pluck('books.id')->all())
        ->toEqualCanonicalizing($submittedBooks->modelKeys());
});

it('rejects a second catalog book with the same ISBN', function () {
    Book::factory()->create(['isbn' => '9780441478125']);

    expect(fn () => Book::factory()->create(['isbn' => '9780441478125']))
        ->toThrow(QueryException::class);
    $this->assertDatabaseCount('books', 1);
});

it('seeds books for local development and assessment', function () {
    $this->seed();

    $this->assertDatabaseCount('books', 20);
});
