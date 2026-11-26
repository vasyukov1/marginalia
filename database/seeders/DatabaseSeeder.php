<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Reading;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            User::factory()->raw(['name' => 'Читатель', 'email' => 'admin@example.com', 'password' => 'password']),
        );

        foreach ($this->books() as [$title, $authorName, $period, $pages, $country, $year, $genre]) {
            $author = Author::firstOrCreate(
                ['name_key' => Str::lower(Str::squish($authorName))],
                Author::factory()->raw(['name' => $authorName, 'country' => $country, 'created_by_user_id' => $user->id]),
            );

            $book = Book::firstOrCreate(
                ['author_id' => $author->id, 'title_key' => Str::lower(Str::squish($title))],
                Book::factory()->raw([
                    'title' => $title,
                    'author_id' => $author->id,
                    'created_by_user_id' => $user->id,
                    'pages' => $pages,
                    'publication_year' => $year,
                    'genre' => $genre,
                    'isbn' => null,
                    'description' => null,
                ]),
            );

            Reading::firstOrCreate(
                ['user_id' => $user->id, 'book_id' => $book->id],
                Reading::factory()->raw([
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                    'period' => $period,
                ]),
            );
        }

        $this->call(LibrarySeeder::class);
    }

    /**
     * Sample from the Books sheet; periods and page counts are kept as recorded.
     *
     * @return list<array{string, string, string, int, string, int, string}>
     */
    private function books(): array
    {
        return [
            ['Гарри Поттер и философский камень', 'Джоан Роулинг', 'Лето 2016', 396, 'Англия', 1997, 'Фантастика'],
            ['Гарри Поттер и Тайная комната', 'Джоан Роулинг', 'Лето 2017', 471, 'Англия', 1998, 'Фантастика'],
            ['Гарри Поттер и узник Азкабана', 'Джоан Роулинг', 'Весна 2017', 507, 'Англия', 1999, 'Фантастика'],
            ['Гарри Поттер и Кубок огня', 'Джоан Роулинг', 'Весна 2017', 665, 'Англия', 2000, 'Фантастика'],
            ['Гарри Поттер и Орден Феникса', 'Джоан Роулинг', 'Зима 2017-18', 824, 'Англия', 2003, 'Фантастика'],
            ['Гарри Поттер и Принц-полукровка', 'Джоан Роулинг', 'Весна 2018', 665, 'Англия', 2005, 'Фантастика'],
            ['Гарри Поттер и Дары смерти', 'Джоан Роулинг', 'Лето 2018', 634, 'Англия', 2007, 'Фантастика'],
            ['Всадник без головы', 'Майн Рид', 'Лето 2018', 455, 'Англия', 1865, 'Приключения'],
            ['Охотники за жирафами', 'Майн Рид', 'Лето 2019', 209, 'Англия', 1867, 'Приключения'],
            ['Оцеола, вождь семинолов', 'Майн Рид', 'Лето 2019', 366, 'Англия', 1859, 'Приключения'],
            ['Вокруг света за 80 дней', 'Жюль Верн', 'Лето 2019', 180, 'Франция', 1873, 'Приключения'],
            ['Жак Депар', 'Майн Рид', 'Лето 2019', 23, 'Англия', 1861, 'Приключения'],
            ['Жизнь у индейцев', 'Майн Рид', 'Лето 2019', 27, 'Англия', 1861, 'Приключения'],
            ['Отважная охотница', 'Майн Рид', 'Лето 2019', 247, 'Англия', 1861, 'Приключения'],
            ['Охотник на бобров', 'Майн Рид', 'Лето 2019', 51, 'Англия', 1867, 'Приключения'],
            ['Рассказы', 'Майн Рид', 'Лето 2019', 9, 'Англия', 1880, 'Приключения'],
            ['Мартин Иден', 'Джек Лондон', 'Лето 2019', 281, 'США', 1909, 'Драма'],
            ['Одиссея капитана Блада', 'Рафаэль Сабатини', 'Лето 2019', 323, 'Италия', 1922, 'Морские приключения'],
            ['В дебрях Южной Африки, или Приключения Бура и его семьи', 'Майн Рид', 'Лето 2019', 208, 'Англия', 1855, 'Приключения'],
            ['Хроника капитана Блада', 'Рафаэль Сабатини', 'Осень 2019', 225, 'Италия', 1931, 'Морские приключения'],
        ];
    }
}
