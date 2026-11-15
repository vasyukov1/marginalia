<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\User;

it('switches language and remembers it across pages', function () {
    $this->get('/')->assertSee('lang="ru"', false);
    $this->from('/')->post('/language', ['locale' => 'en'])->assertRedirect('/')->assertSessionHas('locale', 'en');
    $this->get('/')->assertSee('lang="en"', false)->assertSee('Add a read book')->assertDontSee('Добавить прочитанную книгу');
    $this->get('/register')->assertSee('Create account');
    $this->get('/books')->assertSee('All authors');
    $this->actingAs(User::factory()->create())->get('/readings/create')->assertSee('From the catalog');
    $this->post('/language', ['locale' => 'ru'])->assertSessionHas('locale', 'ru');
    $this->get('/')->assertSee('Добавить прочитанную книгу');
});

it('rejects unsupported languages', function () {
    $this->withSession(['locale' => 'en'])->post('/language', ['locale' => '../bad'])
        ->assertSessionHasErrors('locale')->assertSessionHas('locale', 'en');
});

it('translates validation and authentication errors', function () {
    $this->withSession(['locale' => 'ru'])->post('/login', ['email' => 'reader@example.com', 'password' => 'bad'])
        ->assertSessionHasErrors(['email' => 'Неверный email или пароль.']);
    $this->post('/register', [])->assertSessionHasErrors(['name' => 'Поле Имя обязательно.']);
    $this->withSession(['locale' => 'en'])->actingAs(User::factory()->create())->post('/readings', ['mode' => 'new'])
        ->assertSessionHasErrors(['title' => 'Fill in the “Title” field.']);
});

it('keeps translated country filters tied to the original catalog data', function () {
    $author = Author::factory()->create(['country' => 'Франция']);
    $book = Book::factory()->for($author)->create(['title' => 'Книга', 'genre' => 'Роман']);
    $this->withSession(['locale' => 'en'])->get('/books?'.http_build_query(['country' => 'Франция']))
        ->assertOk()->assertSee('France')->assertSee('Novel')->assertSee('Книга')
        ->assertViewHas('books', fn ($books) => $books->modelKeys() === [$book->id]);
});

it('renders settings and password recovery in both languages', function () {
    $this->withSession(['locale' => 'ru'])->get('/forgot-password')->assertOk()->assertSee('Отправить ссылку');
    $this->withSession(['locale' => 'en'])->get('/forgot-password')->assertOk()->assertSee('Email Password Reset Link');
    $this->actingAs(User::factory()->create())->withSession(['locale' => 'ru'])->get('/profile')
        ->assertOk()->assertSee('Данные профиля')->assertSee('Текущий пароль')->assertDontSee('Profile Information');
    $this->withSession(['locale' => 'en'])->get('/profile')->assertOk()->assertSee('Profile Information')->assertSee('Current Password');
});
