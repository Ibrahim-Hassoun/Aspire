<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductsListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'status' => 'nullable|in:in_stock,low_stock,ordered,discontinued',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gte:min_price',
            'sort_by' => 'nullable|in:name,price,quantity,created_at,updated_at',
            'sort_order' => 'nullable|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'low_stock_threshold' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'status.in' => 'Status must be one of: in_stock, low_stock, ordered, discontinued',
            'min_price.numeric' => 'Minimum price must be a valid number',
            'max_price.gte' => 'Maximum price must be greater than or equal to minimum price',
            'sort_by.in' => 'Sort by must be one of: name, price, quantity, created_at, updated_at',
            'sort_order.in' => 'Sort order must be either asc or desc',
            'per_page.max' => 'Items per page cannot exceed 100',
        ];
    }

    /**
     * Get the validated data from the request with default values.
     */
    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();
        
        return array_merge([
            'search' => null,
            'category' => null,
            'status' => null,
            'min_price' => null,
            'max_price' => null,
            'sort_by' => 'created_at',
            'sort_order' => 'desc',
            'per_page' => 15,
            'page' => 1,
            'low_stock_threshold' => null,
        ], $validated);
    }
}
