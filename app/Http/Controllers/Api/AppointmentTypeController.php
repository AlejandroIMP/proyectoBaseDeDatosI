<?php

namespace App\Http\Controllers\Api;

use App\Models\AppointmentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AppointmentTypeController extends ResourceController
{
    /** @var class-string<AppointmentType> */
    protected string $model = AppointmentType::class;

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'name' => ['required', 'string', 'max:50', Rule::unique('appointment_types', 'name')->ignore($record)],
        ];
    }
}
