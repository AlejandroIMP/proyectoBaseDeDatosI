<?php

namespace App\Http\Controllers\Api;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AppointmentController extends ResourceController
{
    /** @var class-string<Appointment> */
    protected string $model = Appointment::class;

    /** @var list<string> */
    protected array $with = ['pet', 'appointmentType', 'person', 'user'];

    protected function defaults(Request $request): array
    {
        return ['user_id' => $request->user()?->id];
    }

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'pet_id' => ['required', 'integer', 'exists:pets,id'],
            'appointment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:pending,completed,missed'],
            'appointment_type_id' => ['required', 'integer', 'exists:appointment_types,id'],
            'person_id' => ['required', 'integer', 'exists:people,id'],
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
        ];
    }
}
