<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<mixed>> */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:120'],
            'author' => ['nullable', 'integer', 'exists:authors,id'],
            'country' => ['nullable', 'string', 'max:100'],
            'genre' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', Rule::in(['title', 'author', 'country', 'genre', 'year', 'pages', 'period', 'rating'])],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
            'reading_year' => ['nullable', 'regex:/^(?:[12][0-9]{3}|unknown)$/'],
            'reading_season' => ['nullable', Rule::in(['winter', 'spring', 'summer', 'autumn', 'unknown'])],
        ];
    }
}
