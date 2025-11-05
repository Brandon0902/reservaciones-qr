<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExtraServiceRequest extends FormRequest
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
}
