<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Ya protegemos por middleware 'admin.only' en las rutas
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required','string','max:150'],
            'email'     => ['required','email','max:150','unique:users,email'],
            'phone'     => ['nullable','string','max:30'],
            'role'      => ['required','in:admin,validator,customer'],
            'password'  => ['required', Password::defaults(), 'confirmed'],
        ];
    }
}
