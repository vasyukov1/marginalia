<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\Reading;

it('shows books and authors to guests', function () {
    $author = Author::factory()->create(['name' => 'Жюль Верн']);
    Book::factory()->for($author)->create(['title' => 'Вокруг света за 80 дней']);

    $this->get('/')->assertOk()->assertSee('Вокруг света за 80 дней')->assertSee('Жюль Верн');
});

it('searches titles and author names regardless of Cyrillic case', function (string $query) {
    $author = Author::factory()->create(['name' => 'Жюль Верн']);
    Book::factory()->for($author)->create(['title' => 'Вокруг света']);
    Book::factory()->create(['title' => 'Другой роман']);

    $this->get('/?'.http_build_query(['q' => $query]))
        ->assertOk()->assertSee('Вокруг света')->assertDontSee('Другой роман');
})->with(['  ВОКРУГ  ', 'ВЕРН']);

it('combines search with the selected author', function () {
    $author = Author::factory()->create();
    $book = Book::factory()->for($author)->create(['title' => 'Дом']);
    Book::factory()->create(['title' => 'Дом']);
    Book::factory()->for($author)->create(['title' => 'Море']);

    $this->get('/?'.http_build_query(['q' => 'Дом', 'author' => $author->id]))
        ->assertOk()->assertViewHas('books', fn ($books) => $books->modelKeys() === [$book->id]);
});

it('paginates the catalog and keeps the filter', function () {
    Book::factory()->count(13)->create(['title' => 'Дом']);
    Book::factory()->create(['title' => 'Море']);

    $this->get('/?'.http_build_query(['q' => 'Дом', 'page' => 2]))->assertOk()
        ->assertViewHas('books', fn ($books) => $books->count() === 1 && $books->total() === 13)
        ->assertSee('q=%D0%94%D0%BE%D0%BC', false);
});

it('shows an empty state when nothing matches', function () {
    $this->get('/?q=Несуществующая')->assertOk()->assertSee('Книги не найдены');
});

it('rejects malformed catalog filters', function (array $query, string $field) {
    $this->getJson('/?'.http_build_query($query))->assertUnprocessable()->assertJsonValidationErrors($field);
})->with([
    'array search' => [['q' => ['unexpected']], 'q'],
    'long search' => [['q' => str_repeat('a', 121)], 'q'],
    'invalid author' => [['author' => 'invalid'], 'author'],
    'missing author' => [['author' => 9999], 'author'],
    'invalid page' => [['page' => -1], 'page'],
]);

it('shows book details without exposing personal notes', function () {
    $reading = Reading::factory()->create(['notes' => 'Private reading notes']);

    $this->get('/books/'.$reading->book_id)->assertOk()
        ->assertSee($reading->book->title)->assertDontSee('Private reading notes');
});

it('escapes catalog text', function () {
    Book::factory()->create(['title' => '<script>alert(1)</script>']);

    $this->get('/')->assertOk()->assertSee('<script>alert(1)</script>')
        ->assertDontSee('<script>alert(1)</script>', false);
});

it('returns not found for a missing book', function () {
    $this->get('/books/9999')->assertNotFound();
});
