<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $authors = [
            'Джоан Роулинг' => 'J. K. Rowling',
            'Майн Рид' => 'Mayne Reid',
            'Жюль Верн' => 'Jules Verne',
            'Джек Лондон' => 'Jack London',
            'Рафаэль Сабатини' => 'Rafael Sabatini',
        ];

        $books = [
            'Джоан Роулинг' => [
                'Гарри Поттер и философский камень' => "Harry Potter and the Philosopher's Stone",
                'Гарри Поттер и Тайная комната' => 'Harry Potter and the Chamber of Secrets',
                'Гарри Поттер и узник Азкабана' => 'Harry Potter and the Prisoner of Azkaban',
                'Гарри Поттер и Кубок огня' => 'Harry Potter and the Goblet of Fire',
                'Гарри Поттер и Орден Феникса' => 'Harry Potter and the Order of the Phoenix',
                'Гарри Поттер и Принц-полукровка' => 'Harry Potter and the Half-Blood Prince',
                'Гарри Поттер и Дары смерти' => 'Harry Potter and the Deathly Hallows',
            ],
            'Майн Рид' => [
                'Всадник без головы' => 'The Headless Horseman',
                'Охотники за жирафами' => 'The Giraffe Hunters',
                'Оцеола, вождь семинолов' => 'Osceola the Seminole',
                'Жак Депар' => 'Jacques Depard',
                'Жизнь у индейцев' => 'Life Among the Indians',
                'Отважная охотница' => 'The Brave Huntress',
                'Охотник на бобров' => 'The Beaver Hunter',
                'Рассказы' => 'Short Stories',
                'В дебрях Южной Африки, или Приключения Бура и его семьи' => 'The Bush Boys',
            ],
            'Жюль Верн' => ['Вокруг света за 80 дней' => 'Around the World in Eighty Days'],
            'Джек Лондон' => ['Мартин Иден' => 'Martin Eden'],
            'Рафаэль Сабатини' => [
                'Одиссея капитана Блада' => 'Captain Blood',
                'Хроника капитана Блада' => 'Captain Blood Returns',
            ],
        ];

        foreach ($authors as $name => $translation) {
            DB::table('authors')->where('name', $name)->whereNull('name_en')->update(['name_en' => $translation]);
        }

        // Обновляем только известные книги и не заменяем уже введённые переводы.
        foreach ($books as $authorName => $titles) {
            $authorId = DB::table('authors')->where('name', $authorName)->value('id');
            foreach ($titles as $title => $translation) {
                DB::table('books')->where('author_id', $authorId)->where('title', $title)
                    ->whereNull('title_en')->update(['title_en' => $translation]);
            }
        }
    }
}
