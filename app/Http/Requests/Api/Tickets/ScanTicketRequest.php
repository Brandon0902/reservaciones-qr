<?php

namespace App\Http\Requests\Api\Tickets;

use Illuminate\Foundation\Http\FormRequest;

class ScanTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Protegido por auth:sanctum + ability:validator en la ruta
        return true;
    }

    public function rules(): array
    {
        return [
            // Tus tokens son UUID (Str::uuid). Si prefieres no forzar uuid, usa: ['required','string','max:100']
            'token' => ['required', 'uuid'],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => 'Falta el token del boleto.',
            'token.uuid'     => 'El token no tiene un formato válido.',
        ];
    }
}
