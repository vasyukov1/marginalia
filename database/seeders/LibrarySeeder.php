<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        $books = json_decode(file_get_contents(__DIR__.'/library-books.json'), true, flags: JSON_THROW_ON_ERROR);
        $authors = collect(json_decode(file_get_contents(__DIR__.'/library-authors.json'), true, flags: JSON_THROW_ON_ERROR))->keyBy('name');
        $ownerId = User::where('email', 'admin@example.com')->value('id');

        DB::transaction(function () use ($books, $authors, $ownerId): void {
            foreach ($books as $row) {
                $author = Author::firstOrCreate(
                    ['name_key' => Str::lower(Str::squish($row['author']))],
                    ['name' => $row['author'], 'name_en' => $authors[$row['author']]['name_en'],
                        'country' => $row['country'], 'created_by_user_id' => $ownerId],
                );

                $book = Book::firstOrCreate(
                    ['author_id' => $author->id, 'title_key' => Str::lower(Str::squish($row['title']))],
                    ['title' => $row['title'], 'title_en' => $row['title_en'], 'pages' => $row['pages'],
                        'publication_year' => $row['publication_year'], 'genre' => $row['genre'],
                        'created_by_user_id' => $ownerId],
                );

                // Дополняем только отсутствующий перевод; пользовательские правки сохраняем.
                if (! $book->title_en && $row['title_en']) {
                    $book->update(['title_en' => $row['title_en']]);
                }
            }
        });

        $this->call(CatalogTranslationSeeder::class);
    }
}
