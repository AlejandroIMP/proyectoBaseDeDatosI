<?php

namespace App\Http\Controllers\Api;

use App\Models\PetTreatment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PetTreatmentController extends ResourceController
{
    /** @var class-string<PetTreatment> */
    protected string $model = PetTreatment::class;

    /** @var list<string> */
    protected array $with = ['pet', 'treatment'];

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'pet_id' => ['required', 'integer', 'exists:pets,id'],
            'treatment_id' => ['required', 'integer', 'exists:treatments,id'],
            'instructions' => ['required', 'string'],
            'treated_at' => ['required', 'date'],
        ];
    }
}
