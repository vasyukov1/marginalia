<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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

it('uploads and replaces an author avatar', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $author = Author::factory()->create(['created_by_user_id' => $user->id]);

    $this->actingAs($user)->post(route('authors.update', $author), [
        '_method' => 'PATCH', 'name' => $author->name,
        'avatar' => UploadedFile::fake()->image('portrait.jpg'),
    ])->assertRedirect(route('authors.show', $author));

    $oldPath = $author->fresh()->avatar_path;
    expect($oldPath)->not->toBeNull();
    Storage::disk('public')->assertExists($oldPath);
    $this->get(route('authors.show', $author))->assertOk()->assertSee($author->fresh()->avatar_url);

    $this->post(route('authors.update', $author), [
        '_method' => 'PATCH', 'name' => $author->name,
        'avatar' => UploadedFile::fake()->image('replacement.png'),
    ])->assertRedirect(route('authors.show', $author));

    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($author->fresh()->avatar_path);
});

it('keeps an avatar during text edits and removes it only when requested', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $author = Author::factory()->create(['created_by_user_id' => $user->id, 'avatar_path' => 'authors/old.jpg']);
    Storage::disk('public')->put('authors/old.jpg', 'old image');

    $this->actingAs($user)->patch(route('authors.update', $author), ['name' => $author->name])->assertRedirect();
    expect($author->fresh()->avatar_path)->toBe('authors/old.jpg');
    Storage::disk('public')->assertExists('authors/old.jpg');

    $this->patch(route('authors.update', $author), ['name' => $author->name, 'remove_avatar' => '1'])->assertRedirect();
    expect($author->fresh()->avatar_path)->toBeNull();
    Storage::disk('public')->assertMissing('authors/old.jpg');
});

it('rejects invalid avatar files without saving them', function (string $kind) {
    Storage::fake('public');
    $user = User::factory()->create();
    $author = Author::factory()->create(['created_by_user_id' => $user->id]);
    $file = match ($kind) {
        'document' => UploadedFile::fake()->create('document.pdf', 10, 'application/pdf'),
        'large' => UploadedFile::fake()->image('large.jpg')->size(2049),
        'svg' => UploadedFile::fake()->createWithContent('image.svg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>'),
    };

    $this->actingAs($user)->post(route('authors.update', $author), [
        '_method' => 'PATCH', 'name' => 'Changed', 'avatar' => $file,
    ])->assertSessionHasErrors('avatar');

    $this->assertDatabaseHas('authors', ['id' => $author->id, 'name' => $author->name, 'avatar_path' => null]);
    Storage::disk('public')->assertDirectoryEmpty('authors');
})->with(['document', 'large', 'svg']);
