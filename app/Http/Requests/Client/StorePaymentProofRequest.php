<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\PaymentMethod;

class StorePaymentProofRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'method'  => ['required', Rule::enum(PaymentMethod::class)],
            'receipt' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB
            'notes'   => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'method.required'  => 'Selecciona el método de pago.',
            'receipt.required' => 'Debes adjuntar el comprobante.',
            'receipt.mimes'    => 'Formato no válido. Sube PDF/JPG/PNG.',
        ];
    }
}
