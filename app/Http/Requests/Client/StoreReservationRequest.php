<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Reservation;
use App\Enums\ReservationStatus;

class StoreReservationRequest extends FormRequest
{
    /** Primer día válido = hoy + 8 */
    public const MIN_DAYS_AHEAD = 8;

    public static function blockingStatuses(): array
    {
        // ✅ usar los cases reales del enum
        return [
            ReservationStatus::PENDING,
            ReservationStatus::CONFIRMED,
            ReservationStatus::CHECKED_IN,
            ReservationStatus::COMPLETED,
        ];
    }

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
            'event_name'        => ['required','string','max:120'],

            // Validamos formato y reglas en withValidator para evitar choques por caracteres invisibles
            'date'              => ['bail','required'],

            'shift'             => ['required','in:day,night'],
            'start_time'        => ['required','date_format:H:i'],
            'end_time'          => ['required','date_format:H:i','after:start_time'],
            'headcount'         => ['required','integer','min:1','max:1000'],
            'discount_amount'   => ['nullable','numeric','min:0'],
            'source'            => ['required','in:in_person,phone,whatsapp,web,other'],
            'notes'             => ['nullable','string','max:500'],

            'extras'            => ['array'],
            'extras.*.id'       => ['required','exists:extra_services,id'],
            'extras.*.qty'      => ['nullable','integer','min:1'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $dateStr = (string) $this->input('date');

            if (config('app.debug')) {
                Log::debug('SRR.validate: incoming date (before parse)', [
                    'date'     => $dateStr,
                    'len'      => strlen($dateStr),
                    'bin2hex'  => bin2hex($dateStr),
                ]);
            }

            if ($v->errors()->has('date') || $dateStr === '') {
                return;
            }

            try {
                $parsed = Carbon::createFromFormat('Y-m-d', $dateStr, config('app.timezone'));
                if ($parsed === false) {
                    if (config('app.debug')) {
                        Log::debug('SRR.validate: Carbon returned false', ['date' => $dateStr]);
                    }
                    $v->errors()->add('date', 'Fecha inválida (use el selector de calendario).');
                    return;
                }

                if (config('app.debug')) {
                    Log::debug('SRR.validate: parsed ok', [
                        'parsed' => $parsed->toDateString(),
                    ]);
                }

                $date  = $parsed->copy()->startOfDay();
                $today = Carbon::today(config('app.timezone'));
                $min   = $today->copy()->addDays(self::MIN_DAYS_AHEAD); // hoy + 8

                // 1) Anticipación mínima (permitimos igual a min)
                if ($date->lt($min)) {
                    if (config('app.debug')) {
                        Log::debug('SRR.validate: fails minDaysAhead', [
                            'date' => $date->toDateString(),
                            'min'  => $min->toDateString(),
                        ]);
                    }
                    $v->errors()->add(
                        'date',
                        "La fecha debe reservarse con al menos 7 días de anticipación. Primer día disponible: {$min->format('d/m/Y')}."
                    );
                    return;
                }

                // 2) Bloqueo por otra reserva
                $exists = Reservation::query()
                    ->whereDate('date', $date->toDateString())
                    ->whereIn('status', self::blockingStatuses())
                    ->exists();

                if ($exists) {
                    if (config('app.debug')) {
                        Log::debug('SRR.validate: busy date', ['date' => $date->toDateString()]);
                    }
                    $v->errors()->add('date', 'La fecha seleccionada ya está ocupada.');
                }
            } catch (\Throwable $e) {
                if (config('app.debug')) {
                    Log::error('SRR.validate: exception parsing date', [
                        'date' => $dateStr,
                        'err'  => $e->getMessage(),
                    ]);
                }
                $v->errors()->add('date', 'Fecha inválida (use el selector de calendario).');
            }
        });
    }

    public function messages(): array
    {
        return [
            'date.required'      => 'Selecciona una fecha.',
            'extras.*.id.exists' => 'Alguno de los servicios extra no existe.',
        ];
    }
}
