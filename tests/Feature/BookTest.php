<?php

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

it('creates a catalog book for the user who submitted it', function () {
    $user = User::factory()->create();
    $book = Book::factory()->for($user, 'createdBy')->create([
        'title' => 'The Left Hand of Darkness',
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

it('seeds a repeatable catalog and a demo readers library', function () {
    $this->seed();
    $this->seed();

    $this->assertDatabaseCount('books', 20);
    $this->assertDatabaseHas('authors', ['name' => 'Жюль Верн']);
    $this->assertDatabaseHas('users', ['email' => 'admin@example.com']);
    $this->assertDatabaseCount('readings', 20);
    $this->assertDatabaseHas('readings', ['period' => 'Лето 2016']);
    expect(Hash::check('password', User::where('email', 'admin@example.com')->firstOrFail()->password))->toBeTrue();
});
