<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class StoreReservationRequest extends FormRequest
{
    /** Primer día válido = hoy + 8 */
    public const MIN_DAYS_AHEAD = 8;

    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Normaliza el campo date y loguea (si APP_DEBUG=true):
     * - Elimina caracteres invisibles (p{C}) y separadores (p{Z}) Unicode
     * - Extrae el primer patrón YYYY-MM-DD
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('date')) {
            $raw = (string) $this->input('date');

            if (config('app.debug')) {
                Log::debug('SRR.prepare: raw date', [
                    'raw'     => $raw,
                    'len'     => strlen($raw),
                    'bin2hex' => bin2hex($raw),
                ]);
            }

            // Quita TODOS los caracteres de control (p{C}) y separadores (p{Z})
            $san = preg_replace('/[\p{C}\p{Z}]+/u', '', $raw);

            // Extrae YYYY-MM-DD si existe
            if (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $san, $m)) {
                $san = "{$m[1]}-{$m[2]}-{$m[3]}";
            }

            if (config('app.debug')) {
                Log::debug('SRR.prepare: sanitized date', [
                    'san'     => $san,
                    'len'     => strlen($san),
                    'bin2hex' => bin2hex($san),
                ]);
            }

            $this->merge(['date' => $san]);
        }
    }

    public function rules(): array
    {
        return [
            'event_name'       => ['required','string','max:120'],

            // Formato se valida aquí; anticipación en withValidator.
            'date'             => ['bail','required','date_format:Y-m-d'],

            'shift'            => ['required','in:day,night'],

            // Horas NO se comparan entre sí (el nocturno cruza de día). El controlador fija horas por turno.
            'start_time'       => ['nullable','date_format:H:i'],
            'end_time'         => ['nullable','date_format:H:i'],

            'headcount'        => ['required','integer','min:1','max:1000'],
            'discount_amount'  => ['nullable','numeric','min:0'],

            // En el controlador se convierte a enum; aquí permitimos solo los valores conocidos si viene.
            'source'           => ['nullable','in:in_person,phone,whatsapp,web,other'],
            'notes'            => ['nullable','string','max:1000'],

            'extras'           => ['nullable','array'],
            'extras.*.id'      => ['required','integer','exists:extra_services,id'],
            'extras.*.qty'     => ['nullable','integer','min:1'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $dateStr = (string) $this->input('date');

            if ($v->errors()->has('date') || $dateStr === '') {
                return;
            }

            try {
                $tz     = config('app.timezone');
                $parsed = Carbon::createFromFormat('Y-m-d', $dateStr, $tz);
                if ($parsed === false) {
                    $v->errors()->add('date', 'Fecha inválida (usa el selector de calendario).');
                    return;
                }

                $date  = $parsed->copy()->startOfDay();
                $today = Carbon::today($tz);
                $min   = $today->copy()->addDays(self::MIN_DAYS_AHEAD)->startOfDay(); // hoy + 8

                // Anticipación mínima (permitimos igual a min)
                if ($date->lt($min)) {
                    $v->errors()->add(
                        'date',
                        "La fecha debe reservarse con al menos 8 días de anticipación. Primer día disponible: {$min->format('d/m/Y')}."
                    );
                    return;
                }

                // ⚠️ Importante:
                // NO se valida ocupación aquí porque depende del TURNO elegido.
                // Esa verificación se hace en el controlador (por turno: day/night).

            } catch (\Throwable $e) {
                if (config('app.debug')) {
                    Log::error('SRR.validate: exception parsing date', [
                        'date' => $dateStr,
                        'err'  => $e->getMessage(),
                    ]);
                }
                $v->errors()->add('date', 'Fecha inválida (usa el selector de calendario).');
            }
        });
    }

    public function messages(): array
    {
        return [
            'event_name.required'  => 'Ingresa el nombre del evento.',
            'date.required'        => 'Selecciona una fecha.',
            'date.date_format'     => 'La fecha debe tener el formato YYYY-MM-DD.',
            'shift.required'       => 'Selecciona un horario.',
            'shift.in'             => 'Horario inválido.',
            'headcount.required'   => 'Indica el número de personas.',
            'headcount.integer'    => 'El número de personas debe ser entero.',
            'headcount.min'        => 'El número de personas debe ser al menos 1.',
            'discount_amount.numeric' => 'El descuento debe ser un número.',
            'discount_amount.min'  => 'El descuento no puede ser negativo.',
            'extras.*.id.exists'   => 'Alguno de los servicios extra no existe.',
        ];
    }
}
