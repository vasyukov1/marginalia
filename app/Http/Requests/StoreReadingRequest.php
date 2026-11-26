<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreReadingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        foreach (['title', 'title_en', 'author_name', 'author_name_en'] as $field) {
            if (is_string($this->input($field))) {
                $value = Str::squish($this->input($field));
                $this->merge([$field => $value === '' ? null : $value]);
            }
        }
    }

    /** @return array<string, array<mixed>> */
    public function rules(): array
    {
        return [
            'mode' => ['required', Rule::in(['existing', 'new'])],
            'book_id' => ['exclude_unless:mode,existing', 'required', 'integer', 'exists:books,id'],
            'title' => ['exclude_unless:mode,new', 'required', 'string', 'max:255'],
            'author_name' => ['exclude_unless:mode,new', 'required', 'string', 'max:255'],
            'title_en' => ['exclude_unless:mode,new', 'nullable', 'string', 'max:255'],
            'author_name_en' => ['exclude_unless:mode,new', 'nullable', 'string', 'max:255'],
            'country' => ['exclude_unless:mode,new', 'nullable', 'string', 'max:100'],
            'genre' => ['exclude_unless:mode,new', 'nullable', 'string', 'max:100'],
            'pages' => ['exclude_unless:mode,new', 'nullable', 'integer', 'min:1', 'max:100000'],
            'publication_year' => ['exclude_unless:mode,new', 'nullable', 'integer', 'min:1', 'max:'.(now()->year + 1)],
            'period' => ['nullable', 'string', 'max:100'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:10'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'required' => __('Заполните поле «:attribute».'),
            'string' => __('Поле «:attribute» должно содержать текст.'),
            'integer' => __('Поле «:attribute» должно содержать целое число.'),
            'max' => __('Поле «:attribute»: допустимый максимум — :max.'),
            'min' => __('Поле «:attribute»: допустимый минимум — :min.'),
            'exists' => __('Выберите книгу из библиотеки.'),
            'in' => __('Выберите один из предложенных вариантов.'),
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'title_en' => __('Название на английском'), 'author_name_en' => __('Автор на английском'),
            'mode' => __('Способ добавления'), 'book_id' => __('Книга'), 'title' => __('Название'),
            'author_name' => __('Автор'), 'country' => __('Страна автора'), 'genre' => __('Жанр'),
            'pages' => __('Страницы'), 'publication_year' => __('Год издания'),
            'period' => __('Когда прочитана'), 'rating' => __('Оценка'), 'notes' => __('Заметка'),
        ];
    }
}
