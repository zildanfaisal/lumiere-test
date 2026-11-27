<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'pembeli';
    }

    public function rules(): array
    {
        return ['variant_id' => ['required', 'exists:product_variants,id'], 'quantity' => ['required', 'integer', 'min:1', 'max:99']];
    }
}
