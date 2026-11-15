<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\Reading;

it('shows books and authors to guests', function () {
    $author = Author::factory()->create(['name' => 'Жюль Верн']);
    Book::factory()->for($author)->create(['title' => 'Вокруг света за 80 дней']);

    $this->get('/books')->assertOk()->assertSee('Вокруг света за 80 дней')->assertSee('Жюль Верн');
});

it('searches titles and author names regardless of Cyrillic case', function (string $query) {
    $author = Author::factory()->create(['name' => 'Жюль Верн']);
    Book::factory()->for($author)->create(['title' => 'Вокруг света']);
    Book::factory()->create(['title' => 'Другой роман']);

    $this->get('/books?'.http_build_query(['q' => $query]))
        ->assertOk()->assertSee('Вокруг света')->assertDontSee('Другой роман');
})->with(['  ВОКРУГ  ', 'ВЕРН']);

it('combines search with the selected author', function () {
    $author = Author::factory()->create();
    $book = Book::factory()->for($author)->create(['title' => 'Дом']);
    Book::factory()->create(['title' => 'Дом']);
    Book::factory()->for($author)->create(['title' => 'Море']);

    $this->get('/books?'.http_build_query(['q' => 'Дом', 'author' => $author->id]))
        ->assertOk()->assertViewHas('books', fn ($books) => $books->modelKeys() === [$book->id]);
});

it('shows the entire filtered catalog without pagination', function () {
    Book::factory()->count(25)->create(['title' => 'Дом']);
    Book::factory()->create(['title' => 'Море']);

    $this->get('/books?'.http_build_query(['q' => 'Дом']))->assertOk()
        ->assertViewHas('books', fn ($books) => $books->count() === 25)
        ->assertDontSee('Далее →')->assertDontSee('← Назад');
});

it('shows an empty state when nothing matches', function () {
    $this->get('/books?q=Несуществующая')->assertOk()->assertSee('Книги не найдены');
});

it('rejects malformed catalog filters', function (array $query, string $field) {
    $this->getJson('/books?'.http_build_query($query))->assertUnprocessable()->assertJsonValidationErrors($field);
})->with([
    'array search' => [['q' => ['unexpected']], 'q'],
    'long search' => [['q' => str_repeat('a', 121)], 'q'],
    'invalid author' => [['author' => 'invalid'], 'author'],
    'missing author' => [['author' => 9999], 'author'],
    'invalid sort' => [['sort' => 'password'], 'sort'],
    'invalid direction' => [['direction' => 'DROP TABLE books'], 'direction'],
    'array country' => [['country' => ['invalid']], 'country'],
]);

it('shows book details without exposing personal notes', function () {
    $reading = Reading::factory()->create(['notes' => 'Private reading notes']);

    $this->get('/books/'.$reading->book_id)->assertOk()
        ->assertSee($reading->book->title)->assertDontSee('Private reading notes');
});

it('escapes catalog text', function () {
    Book::factory()->create(['title' => '<script>alert(1)</script>']);

    $this->get('/books')->assertOk()->assertSee('<script>alert(1)</script>')
        ->assertDontSee('<script>alert(1)</script>', false);
});

it('returns not found for a missing book', function () {
    $this->get('/books/9999')->assertNotFound();
});

it('filters by country and genre together', function () {
    $author = Author::factory()->create(['country' => 'Франция']);
    $book = Book::factory()->for($author)->create(['genre' => 'Приключения']);
    Book::factory()->for($author)->create(['genre' => 'Драма']);
    Book::factory()->for(Author::factory()->create(['country' => 'США']))->create(['genre' => 'Приключения']);

    $this->get('/books?'.http_build_query(['country' => 'Франция', 'genre' => 'Приключения']))
        ->assertOk()->assertSee('🇫🇷')
        ->assertViewHas('books', fn ($books) => $books->modelKeys() === [$book->id]);
});

it('sorts table columns in both directions', function (string $sort) {
    $firstAuthor = Author::factory()->create(['name' => 'Александр', 'country' => 'Англия']);
    $lastAuthor = Author::factory()->create(['name' => 'Ян', 'country' => 'Япония']);
    $first = Book::factory()->for($firstAuthor)->create(['title' => 'Азбука', 'pages' => 9, 'publication_year' => 1890, 'genre' => 'Драма']);
    $last = Book::factory()->for($lastAuthor)->create(['title' => 'Яблоко', 'pages' => 100, 'publication_year' => 2000, 'genre' => 'Фантастика']);

    $this->get('/books?sort='.$sort.'&direction=asc')->assertOk()
        ->assertViewHas('books', fn ($books) => $books->modelKeys() === [$first->id, $last->id]);
    $this->get('/books?sort='.$sort.'&direction=desc')->assertOk()
        ->assertViewHas('books', fn ($books) => $books->modelKeys() === [$last->id, $first->id]);
})->with(['title', 'author', 'country', 'genre', 'year', 'pages']);

it('keeps the home page free of the book table', function () {
    Book::factory()->create(['title' => 'Catalog only title']);
    $this->get('/')->assertOk()->assertSee('Добавить прочитанную книгу')
        ->assertSee(route('books.index'))->assertDontSee('Catalog only title')
        ->assertDontSee('<table', false);
});
