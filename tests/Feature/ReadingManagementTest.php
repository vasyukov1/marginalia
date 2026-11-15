<?php

use App\Models\Book;
use App\Models\Reading;
use App\Models\User;

it('requires login to save a reading', function () {
    $book = Book::factory()->create();

    $this->put('/books/'.$book->id.'/reading', ['rating' => 8])->assertRedirect('/login');
    $this->assertDatabaseCount('readings', 0);
});

it('saves and updates only the signed in readers record', function () {
    $user = User::factory()->create();
    $other = Reading::factory()->create(['rating' => 2]);
    $url = '/books/'.$other->book_id.'/reading';

    $this->actingAs($user)->put($url, ['rating' => 8, 'period' => 'Лето 2020', 'user_id' => $other->user_id])
        ->assertRedirect('/books/'.$other->book_id);
    $this->assertDatabaseHas('readings', ['user_id' => $user->id, 'book_id' => $other->book_id, 'rating' => 8, 'period' => 'Лето 2020']);

    $this->put($url, ['rating' => 10])->assertRedirect('/books/'.$other->book_id);
    $this->assertDatabaseCount('readings', 2);
    $this->assertDatabaseHas('readings', ['user_id' => $user->id, 'rating' => 10]);
    expect($other->fresh()->rating)->toBe(2);
});

it('allows a read book without an invented rating or date', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $this->actingAs($user)->put('/books/'.$book->id.'/reading', [])->assertRedirect('/books/'.$book->id);
    $this->assertDatabaseHas('readings', ['user_id' => $user->id, 'book_id' => $book->id, 'rating' => null, 'period' => null]);
});

it('rejects invalid reading data', function (array $data, string $field) {
    $book = Book::factory()->create();

    $this->actingAs(User::factory()->create())->putJson('/books/'.$book->id.'/reading', $data)
        ->assertUnprocessable()->assertJsonValidationErrors($field);
    $this->assertDatabaseCount('readings', 0);
})->with([
    'too low' => [['rating' => 0], 'rating'],
    'too high' => [['rating' => 11], 'rating'],
    'fraction' => [['rating' => 1.5], 'rating'],
    'array period' => [['period' => ['bad']], 'period'],
    'long period' => [['period' => str_repeat('a', 101)], 'period'],
    'long notes' => [['notes' => str_repeat('a', 2001)], 'notes'],
]);

it('removes only the users reading while keeping the shared book', function () {
    $other = Reading::factory()->create();
    $own = Reading::factory()->for($other->book)->create();

    $this->actingAs($own->user)->delete('/books/'.$other->book_id.'/reading')->assertRedirect('/books/'.$other->book_id);

    $this->assertModelMissing($own);
    $this->assertModelExists($other);
    $this->assertModelExists($other->book);
});

it('shows only the current readers form values and escapes notes', function () {
    $reading = Reading::factory()->create(['notes' => '<script>alert(1)</script>']);
    Reading::factory()->for($reading->book)->create(['notes' => 'Someone elses notes']);

    $this->actingAs($reading->user)->get('/books/'.$reading->book_id)->assertOk()
        ->assertSee('<script>alert(1)</script>')->assertDontSee('<script>alert(1)</script>', false)
        ->assertDontSee('Someone elses notes');
});
