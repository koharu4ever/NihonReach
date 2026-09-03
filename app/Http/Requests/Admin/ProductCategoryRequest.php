<?php

namespace App\Http\Requests\Admin;

use App\Models\ProductCategory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->is_admin === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $category = $this->route('product_category');

        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => [
                'required',
                'alpha_dash:ascii',
                'max:100',
                Rule::unique('product_categories', 'slug')
                    ->ignore($category instanceof ProductCategory ? $category->id : null),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'translations' => ['required', 'array:zh'],
            'translations.zh' => ['required', 'array:name,description'],
            'translations.zh.name' => ['required', 'string', 'max:100'],
            'translations.zh.description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
        ];
    }
}
