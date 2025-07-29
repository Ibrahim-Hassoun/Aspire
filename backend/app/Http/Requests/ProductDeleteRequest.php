<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductDeleteRequest extends FormRequest
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
            'ids' => 'required|array|min:1|max:50',
            'ids.*' => 'required|integer|exists:products,id',
            'force' => 'nullable|boolean',
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
            'ids.required' => 'At least one product ID is required',
            'ids.array' => 'Product IDs must be provided as an array',
            'ids.min' => 'At least one product ID is required',
            'ids.max' => 'Cannot delete more than 50 products at once',
            'ids.*.required' => 'Each product ID is required',
            'ids.*.integer' => 'Product IDs must be valid integers',
            'ids.*.exists' => 'One or more products do not exist',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'ids' => 'product IDs',
            'ids.*' => 'product ID',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Additional validation logic can go here
            $ids = $this->get('ids', []);
            
            // Check for duplicate IDs
            if (count($ids) !== count(array_unique($ids))) {
                $validator->errors()->add('ids', 'Duplicate product IDs are not allowed');
            }
        });
    }
}
