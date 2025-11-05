<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreExtraServiceRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        return [
            'name'        => ['required','string','max:150'],
            'description' => ['nullable','string','max:2000'],
            'day_price'   => ['required','numeric','min:0','max:99999999.99'],
            'night_price' => ['required','numeric','min:0','max:99999999.99'],
        ];
    }

    public function messages(): array
    {
        return [
            'day_price.required'   => 'La tarifa de día es obligatoria.',
            'night_price.required' => 'La tarifa de noche es obligatoria.',
        ];
    }
}
