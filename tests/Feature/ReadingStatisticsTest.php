<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\Reading;
use App\Models\User;

it('groups personal readings and filters books and countries by period', function () {
    $user = User::factory()->create();
    $author = Author::factory()->create(['country' => 'Англия']);
    $winter = Book::factory()->for($author)->create(['pages' => 100]);
    $summer = Book::factory()->for($author)->create(['pages' => 200]);
    Reading::factory()->for($user)->for($winter)->create(['period' => 'Зима 2017-18', 'rating' => 8]);
    Reading::factory()->for($user)->for($summer)->create(['period' => 'Summer 2018', 'rating' => null]);
    Reading::factory()->for($user)->create(['period' => null]);
    Reading::factory()->for($winter)->create(['period' => 'Зима 2018', 'rating' => 2]);

    $response = $this->actingAs($user)->get('/dashboard?reading_year=2018&reading_season=winter');

    $response->assertOk()->assertViewHas('books', fn ($books) => $books->modelKeys() === [$winter->id]);
    expect($response->viewData('yearStats')[2018])->toBe(['books' => 2, 'pages' => 300, 'rating' => 8.0]);
    expect($response->viewData('yearStats')['unknown']['books'])->toBe(1);
    expect($response->viewData('seasonStats')->keys()->take(2)->all())->toBe(['2018 / Зима', '2018 / Лето']);
    expect($response->viewData('countryStats')['Англия'])->toBe(['books' => 1, 'pages' => 100, 'rating' => 8.0]);

    $this->get('/dashboard?reading_year=unknown')->assertOk()
        ->assertViewHas('books', fn ($books) => $books->count() === 1);
    $this->get('/dashboard?reading_year=2020')->assertOk()
        ->assertViewHas('books', fn ($books) => $books->isEmpty())
        ->assertViewHas('countryStats', fn ($rows) => $rows->isEmpty());
});

it('understands existing period formats without guessing ambiguous dates', function (?string $period, ?int $year, ?string $season) {
    $reading = Reading::factory()->make(['period' => $period]);

    expect($reading->periodParts())->toBe(['year' => $year, 'season' => $season]);
})->with([
    ['Лето 2016', 2016, 'summer'],
    ['autumn 2026', 2026, 'autumn'],
    [' WINTER 2017–2018 ', 2018, 'winter'],
    ['Зима 1999-00', 2000, 'winter'],
    ['2026', 2026, null],
    ['2017-18', null, null],
    ['Зима 2017-20', null, null],
    ['когда-то', null, null],
    [null, null, null],
]);

it('rejects invalid statistics filters', function () {
    $this->actingAs(User::factory()->create())
        ->getJson('/dashboard?reading_year=oops&reading_season=monsoon')
        ->assertUnprocessable()->assertJsonValidationErrors(['reading_year', 'reading_season']);
});

it('shows empty statistics and translated labels', function () {
    $this->actingAs(User::factory()->create())->withSession(['locale' => 'en'])
        ->get('/dashboard')->assertOk()->assertSee('Reading year')
        ->assertViewHas('yearStats', fn ($rows) => $rows->isEmpty());
});
