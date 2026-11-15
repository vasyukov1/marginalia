<?php

use App\Models\Book;
use App\Models\Reading;
use App\Models\User;
use Illuminate\Database\QueryException;

it('keeps each readers notes on a shared book separate', function () {
    $book = Book::factory()->create();
    $reader = User::factory()->create();
    $reading = Reading::factory()->for($reader)->for($book)->create(['period' => 'Лето 2016']);
    Reading::factory()->for($book)->create();

    expect($reader->readings->modelKeys())->toBe([$reading->id]);
    expect($reading->book->is($book))->toBeTrue();
    expect($reading->user->is($reader))->toBeTrue();
    expect($reading->period)->toBe('Лето 2016');
    expect($book->readings)->toHaveCount(2);
    $this->assertDatabaseCount('books', 1);
});

it('allows a book only once in a readers library', function () {
    $reading = Reading::factory()->create();

    expect(fn () => Reading::factory()->for($reading->user)->for($reading->book)->create())
        ->toThrow(QueryException::class);
});

it('removes personal readings but preserves the catalog when a reader is deleted', function () {
    $reading = Reading::factory()->create();
    $book = $reading->book;

    $reading->user->delete();

    $this->assertModelMissing($reading);
    $this->assertModelExists($book);
});
