<?php

namespace App\Http\Requests;

use App\Models\Book;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

class UpdateBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update-book', $this->route('book'));
    }

    protected function prepareForValidation(): void
    {
        foreach (['title', 'title_en', 'genre'] as $field) {
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
            'title' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'author_id' => ['required', 'integer', 'exists:authors,id'],
            'pages' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'publication_year' => ['nullable', 'integer', 'min:1', 'max:'.(now()->year + 1)],
            'genre' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:10000'],
        ];
    }

    /** @return array<callable> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            // У одного автора не должно быть двух книг с одинаковым названием.
            $duplicate = Book::where('author_id', $this->integer('author_id'))
                ->where('title_key', Str::lower($this->input('title')))
                ->where('id', '!=', $this->route('book')->id)
                ->exists();

            if ($duplicate) {
                $validator->errors()->add('title', __('У этого автора уже есть книга с таким названием.'));
            }
        }];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'title' => __('Название'), 'title_en' => __('Название на английском'),
            'author_id' => __('Автор'), 'pages' => __('Страницы'),
            'publication_year' => __('Год издания'), 'genre' => __('Жанр'),
            'description' => __('Описание'),
        ];
    }
}
