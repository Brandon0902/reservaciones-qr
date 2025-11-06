<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        return [
            'event_name'   => ['required','string','max:120'],
            'date'         => ['required','date'],
            'shift'        => ['required','in:day,night'],
            'start_time'   => ['required','date_format:H:i'],
            'end_time'     => ['required','date_format:H:i','after:start_time'],
            'headcount'    => ['required','integer','min:1','max:1000'],
            'status'       => ['required','in:pending,confirmed,canceled,checked_in,completed'],
            'discount_amount' => ['nullable','numeric','min:0'],
            'source'       => ['required','in:in_person,phone,whatsapp,web,other'],
            'notes'        => ['nullable','string','max:500'],
            'extras'       => ['array'],
            'extras.*.id'  => ['required','exists:extra_services,id'],
            'extras.*.qty' => ['nullable','integer','min:1'],
        ];
    }
}
