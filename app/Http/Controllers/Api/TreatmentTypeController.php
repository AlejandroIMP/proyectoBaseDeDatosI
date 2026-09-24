<?php

namespace App\Http\Controllers\Api;

use App\Models\TreatmentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class TreatmentTypeController extends ResourceController
{
    /** @var class-string<TreatmentType> */
    protected string $model = TreatmentType::class;

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
        ];
    }
}
