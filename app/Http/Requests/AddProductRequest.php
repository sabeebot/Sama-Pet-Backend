<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddProductRequest extends FormRequest
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
        return[];
        // return [
        //     //product rules
        //     'name' => 'required|string',
        //     'old_price' => 'required|numeric',
        //     'new_price' => 'numeric',
        //     'percentage' => 'numeric',
        //     'quantity' => 'required|numeric',
        //     'images' => 'required|string',
        //     'description' => 'required|string',
        //     'contact_number' => 'required|string',
        //     'pet_type' => 'required|string', 
        // ];
    }
}
