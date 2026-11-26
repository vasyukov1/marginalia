<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\User;
use Database\Seeders\AuthorPortraitSeeder;
use Database\Seeders\LibrarySeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

it('imports the library without duplicates or personal reading records', function () {
    $user = User::factory()->create(['email' => 'admin@example.com']);
    $author = Author::factory()->create(['name' => 'Джордж Оруэлл', 'name_en' => 'George Orwell']);
    $book = Book::factory()->for($author)->create(['title' => '1984', 'pages' => 999]);

    $this->seed(LibrarySeeder::class);
    $this->seed(LibrarySeeder::class);

    $this->assertDatabaseCount('books', 213);
    $this->assertDatabaseCount('authors', 72);
    $this->assertDatabaseCount('readings', 0);
    $this->assertDatabaseHas('books', ['id' => $book->id, 'pages' => 999]);
    $this->assertDatabaseHas('books', ['title' => 'Дюна', 'created_by_user_id' => $user->id]);
    $this->assertDatabaseHas('books', ['title' => 'Дао-Дэ цзин', 'publication_year' => null]);
});

it('imports credited portraits once without replacing uploaded avatars', function () {
    Storage::fake('public');
    $photo = UploadedFile::fake()->image('portrait.jpg');
    Http::fake(['*' => Http::response(file_get_contents($photo->path()))]);
    $author = Author::factory()->create(['name' => 'Жюль Верн']);
    $custom = Author::factory()->create(['name' => 'Джек Лондон', 'avatar_path' => 'authors/custom.jpg']);

    $this->seed(AuthorPortraitSeeder::class);
    $this->seed(AuthorPortraitSeeder::class);

    $author->refresh();
    expect($author->avatar_path)->not->toBeNull();
    expect($author->avatar_credit)->not->toBeEmpty();
    expect($author->avatar_source_url)->toStartWith('https://commons.wikimedia.org/');
    expect($custom->fresh()->avatar_path)->toBe('authors/custom.jpg');
    Storage::disk('public')->assertExists($author->avatar_path);
    Http::assertSentCount(1);
});
