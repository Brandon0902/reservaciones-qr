<?php

// app/Http/Requests/Api/Auth/LoginRequestApi.php
namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequestApi extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'email'    => ['required','string','email','max:255'],
            'password' => ['required','string','min:6','max:100'],
            'device'   => ['nullable','string','max:100'],
        ];
    }
}
