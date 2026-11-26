<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\User;

it('updates author names without disconnecting their books', function () {
    $user = User::factory()->create();
    $author = Author::factory()->create(['created_by_user_id' => $user->id]);
    $book = Book::factory()->for($author)->create();

    $this->actingAs($user)->get(route('authors.edit', $author))->assertOk()->assertSee($author->name);
    $this->patch(route('authors.update', $author), [
        'name' => '  Новый   Автор ', 'name_en' => 'New Author', 'country' => 'Франция',
        'created_by_user_id' => 999,
    ])->assertRedirect(route('authors.show', $author));

    $this->assertDatabaseHas('authors', [
        'id' => $author->id, 'name' => 'Новый Автор', 'name_key' => 'новый автор',
        'name_en' => 'New Author', 'country' => 'Франция', 'created_by_user_id' => $user->id,
    ]);
    $this->assertDatabaseHas('books', ['id' => $book->id, 'author_id' => $author->id]);
});

it('protects author editing from guests and other users', function () {
    $owner = User::factory()->create();
    $author = Author::factory()->create(['created_by_user_id' => $owner->id]);
    $this->get(route('authors.edit', $author))->assertRedirect(route('login'));
    $this->patch(route('authors.update', $author), [])->assertRedirect(route('login'));

    $this->actingAs(User::factory()->create())->get(route('authors.edit', $author))->assertForbidden();
    $this->patch(route('authors.update', $author), ['name' => 'Changed'])->assertForbidden();
    $this->assertDatabaseHas('authors', ['id' => $author->id, 'name' => $author->name]);
});

it('rejects a duplicate normalized author name', function () {
    $user = User::factory()->create();
    $author = Author::factory()->create(['created_by_user_id' => $user->id]);
    Author::factory()->create(['name' => 'Другой Автор']);

    $this->actingAs($user)->patch(route('authors.update', $author), ['name' => ' ДРУГОЙ   АВТОР '])
        ->assertSessionHasErrors('name');

    $this->assertDatabaseHas('authors', ['id' => $author->id, 'name' => $author->name]);
});
