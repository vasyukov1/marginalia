<?php

return [
    'required' => 'Поле :attribute обязательно.',
    'string' => 'Поле :attribute должно содержать текст.',
    'email' => 'Введите корректный email.',
    'unique' => 'Такое значение поля :attribute уже используется.',
    'confirmed' => 'Подтверждение поля :attribute не совпадает.',
    'current_password' => 'Неверный текущий пароль.',
    'lowercase' => 'Поле :attribute должно быть в нижнем регистре.',
    'integer' => 'Поле :attribute должно содержать целое число.',
    'exists' => 'Выбранное значение поля :attribute не найдено.',
    'in' => 'Выберите допустимое значение поля :attribute.',
    'min' => [
        'string' => 'Поле :attribute должно содержать не менее :min символов.',
        'numeric' => 'Поле :attribute должно быть не меньше :min.',
    ],
    'max' => [
        'string' => 'Поле :attribute должно содержать не более :max символов.',
        'numeric' => 'Поле :attribute должно быть не больше :max.',
    ],
    'attributes' => [
        'name' => 'Имя', 'email' => 'Email', 'password' => 'Пароль',
        'current_password' => 'Текущий пароль', 'password_confirmation' => 'Подтверждение пароля',
        'locale' => 'Язык', 'q' => 'Поиск', 'author' => 'Автор', 'country' => 'Страна',
        'genre' => 'Жанр', 'sort' => 'Сортировка', 'direction' => 'Направление сортировки',
        'period' => 'Период чтения', 'rating' => 'Оценка', 'notes' => 'Заметка',
    ],
];
