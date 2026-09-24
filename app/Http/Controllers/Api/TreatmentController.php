<?php

namespace App\Http\Controllers\Api;

use App\Models\Treatment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class TreatmentController extends ResourceController
{
    /** @var class-string<Treatment> */
    protected string $model = Treatment::class;

    /** @var list<string> */
    protected array $with = ['treatmentType'];

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'requires_veterinarian' => ['required', 'boolean'],
            'cost' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'treatment_type_id' => ['required', 'integer', 'exists:treatment_types,id'],
        ];
    }
}
