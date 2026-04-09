<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //return false;
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $product = $this->route('product'); //-->return Product model

        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'condition_id' => 'sometimes|exists:conditions,id',
            'category_id' => 'sometimes|exists:categories,id',
            'images' => 'sometimes|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            
            'main_image_id' => [
                'sometimes',
                'nullable',
                Rule::exists('images', 'id')->where(function ($q) use ($product) {
                    $q->where('product_id', $product->id);
                })
            ],

            'delete_image_ids' => 'sometimes|array',
            'delete_image_ids.*' => [
                'integer',
                Rule::exists('images', 'id')->where(function ($q) use ($product) {
                    $q->where('product_id', $product->id);
                })
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422));
    }
}
