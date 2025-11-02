<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // middleware 'admin.only' ya valida
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id ?? null;

        return [
            'full_name' => ['required','string','max:150'],
            'email'     => ['required','email','max:150',"unique:users,email,{$userId}"],
            'phone'     => ['nullable','string','max:30'],
            'role'      => ['required','in:admin,validator,customer'],
            'password'  => ['nullable', Password::defaults(), 'confirmed'],
        ];
    }
}
