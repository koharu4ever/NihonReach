<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
        $product = $this->route('product');
        $productId = $product instanceof Product ? $product->id : null;

        return [
            'product_category_id' => ['required', 'integer', 'exists:product_categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'slug' => [
                'required',
                'alpha_dash:ascii',
                'max:160',
                Rule::unique('products', 'slug')->ignore($productId),
            ],
            'sku' => [
                'required',
                'string',
                'max:80',
                'regex:/^[A-Z0-9][A-Z0-9._-]*$/',
                Rule::unique('products', 'sku')->ignore($productId),
            ],
            'summary' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:10000'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'specifications' => ['nullable', 'array', 'max:12'],
            'specifications.*.label' => ['required', 'string', 'max:50'],
            'specifications.*.value' => ['required', 'string', 'max:100'],
            'translations' => ['required', 'array:zh'],
            'translations.zh' => [
                'required',
                'array:name,summary,description,specifications',
            ],
            'translations.zh.name' => ['required', 'string', 'max:150'],
            'translations.zh.summary' => ['required', 'string', 'max:255'],
            'translations.zh.description' => ['required', 'string', 'max:10000'],
            'translations.zh.specifications' => ['nullable', 'array', 'max:12'],
            'translations.zh.specifications.*.label' => ['required', 'string', 'max:50'],
            'translations.zh.specifications.*.value' => ['required', 'string', 'max:100'],
            'is_featured' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
        ];
    }
}
