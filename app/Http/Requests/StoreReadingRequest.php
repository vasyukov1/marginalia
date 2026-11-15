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
        foreach (['title', 'author_name'] as $field) {
            if (is_string($this->input($field))) {
                $this->merge([$field => Str::squish($this->input($field))]);
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
            'required' => 'Заполните поле «:attribute».',
            'string' => 'Поле «:attribute» должно содержать текст.',
            'integer' => 'Поле «:attribute» должно содержать целое число.',
            'max' => 'Поле «:attribute»: допустимый максимум — :max.',
            'min' => 'Поле «:attribute»: допустимый минимум — :min.',
            'exists' => 'Выберите книгу из каталога.',
            'in' => 'Выберите один из предложенных вариантов.',
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'mode' => 'Способ добавления', 'book_id' => 'Книга', 'title' => 'Название',
            'author_name' => 'Автор', 'country' => 'Страна автора', 'genre' => 'Жанр',
            'pages' => 'Страницы', 'publication_year' => 'Год издания',
            'period' => 'Когда прочитана', 'rating' => 'Оценка', 'notes' => 'Заметка',
        ];
    }
}
