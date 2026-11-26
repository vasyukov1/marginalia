<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\Reading;
use App\Models\User;

it('lets the creator edit book details without changing reading records', function () {
    $user = User::factory()->create();
    $book = Book::factory()->for($user, 'createdBy')->create();
    $author = Author::factory()->create();
    $reading = Reading::factory()->for($book)->create(['rating' => 8]);

    $this->actingAs($user)->get(route('books.edit', $book))->assertOk()->assertSee($book->title);
    $this->patch(route('books.update', $book), [
        'title' => '  Новое   название ', 'title_en' => 'New title', 'author_id' => $author->id,
        'pages' => 320, 'publication_year' => 1999, 'genre' => 'Роман', 'description' => 'Описание',
        'created_by_user_id' => $reading->user_id,
    ])->assertRedirect(route('books.show', $book));

    $this->assertDatabaseHas('books', [
        'id' => $book->id, 'title' => 'Новое название', 'title_key' => 'новое название',
        'title_en' => 'New title', 'author_id' => $author->id, 'pages' => 320,
        'publication_year' => 1999, 'genre' => 'Роман', 'description' => 'Описание',
        'created_by_user_id' => $user->id,
    ]);
    $this->assertDatabaseHas('readings', ['id' => $reading->id, 'book_id' => $book->id, 'rating' => 8]);
});

it('requires login and rejects editing another users book', function () {
    $book = Book::factory()->create();
    $this->get(route('books.edit', $book))->assertRedirect(route('login'));
    $this->patch(route('books.update', $book), [])->assertRedirect(route('login'));

    $this->actingAs(User::factory()->create());
    $this->get(route('books.edit', $book))->assertForbidden();
    $this->patch(route('books.update', $book), ['title' => 'Changed'])->assertForbidden();
    $this->assertDatabaseHas('books', ['id' => $book->id, 'title' => $book->title]);
});

it('rejects a duplicate normalized title for the same author', function () {
    $user = User::factory()->create();
    $book = Book::factory()->for($user, 'createdBy')->create();
    Book::factory()->for($book->author)->create(['title' => 'Другая книга']);

    $this->actingAs($user)->patch(route('books.update', $book), [
        'title' => ' ДРУГАЯ   КНИГА ', 'author_id' => $book->author_id,
    ])->assertSessionHasErrors('title');

    $this->assertDatabaseHas('books', ['id' => $book->id, 'title' => $book->title]);
});

it('accepts an unchanged title and clears an optional translation', function () {
    $user = User::factory()->create();
    $book = Book::factory()->for($user, 'createdBy')->create(['title_en' => 'Old translation']);

    $this->actingAs($user)->patch(route('books.update', $book), [
        'title' => $book->title, 'title_en' => '   ', 'author_id' => $book->author_id,
    ])->assertRedirect(route('books.show', $book));

    $this->assertDatabaseHas('books', ['id' => $book->id, 'title_en' => null]);
});

it('rejects invalid book details without saving changes', function () {
    $user = User::factory()->create();
    $book = Book::factory()->for($user, 'createdBy')->create();

    $this->actingAs($user)->patch(route('books.update', $book), [
        'title' => ' ', 'author_id' => 999999, 'pages' => -1, 'publication_year' => 'bad',
    ])->assertSessionHasErrors(['title', 'author_id', 'pages', 'publication_year']);

    $this->assertDatabaseHas('books', ['id' => $book->id, 'title' => $book->title, 'pages' => $book->pages]);
});
