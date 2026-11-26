<?php

namespace App\Http\Requests;

use App\Models\Author;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

class UpdateAuthorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update-author', $this->route('author'));
    }

    protected function prepareForValidation(): void
    {
        foreach (['name', 'name_en', 'country'] as $field) {
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
            'name' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:100'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=4000,max_height=4000', 'prohibited_if:remove_avatar,1'],
            'remove_avatar' => ['sometimes', 'boolean'],
        ];
    }

    /** @return array<callable> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $duplicate = Author::where('name_key', Str::lower($this->input('name')))
                ->where('id', '!=', $this->route('author')->id)->exists();
            if ($duplicate) {
                $validator->errors()->add('name', __('Автор с таким именем уже есть в каталоге.'));
            }
        }];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['name' => __('Имя'), 'name_en' => __('Автор на английском'), 'country' => __('Страна'), 'avatar' => __('Аватарка'), 'remove_avatar' => __('Удалить аватарку')];
    }
}
