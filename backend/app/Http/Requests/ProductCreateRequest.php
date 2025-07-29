<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductCreateRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:products,name',
            'quantity' => 'required|integer|min:0',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0|max:99999999.99',
            'status' => 'nullable|in:in_stock,low_stock,ordered,discontinued',
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
            'name.required' => 'Product name is required',
            'name.unique' => 'A product with this name already exists',
            'name.max' => 'Product name cannot exceed 255 characters',
            'quantity.required' => 'Product quantity is required',
            'quantity.integer' => 'Quantity must be a valid number',
            'quantity.min' => 'Quantity cannot be negative',
            'category.max' => 'Category cannot exceed 255 characters',
            'description.max' => 'Description cannot exceed 1000 characters',
            'price.required' => 'Product price is required',
            'price.numeric' => 'Price must be a valid number',
            'price.min' => 'Price cannot be negative',
            'price.max' => 'Price cannot exceed 99,999,999.99',
            'status.in' => 'Status must be one of: in_stock, low_stock, ordered, discontinued',
        ];
    }

    /**
     * Get the validated data with default values applied.
     */
    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();
        
        // Set default status based on quantity if not provided
        if (!isset($validated['status'])) {
            $validated['status'] = $validated['quantity'] > 10 ? 'in_stock' : 'low_stock';
        }
        
        // Set default category if not provided
        if (!isset($validated['category'])) {
            $validated['category'] = 'General';
        }
        
        return $validated;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Trim whitespace from string fields
        $this->merge([
            'name' => $this->name ? trim($this->name) : $this->name,
            'category' => $this->category ? trim($this->category) : $this->category,
            'description' => $this->description ? trim($this->description) : $this->description,
        ]);
    }
}
