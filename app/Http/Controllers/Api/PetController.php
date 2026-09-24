<?php

namespace App\Http\Controllers\Api;

use App\Models\Pet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PetController extends ResourceController
{
    /** @var class-string<Pet> */
    protected string $model = Pet::class;

    /** @var list<string> */
    protected array $with = ['species', 'breed', 'color', 'petStatus', 'shelter'];

    protected function rules(Request $request, ?Model $record): array
    {
        $speciesId = $request->integer('species_id') ?: ($record instanceof Pet ? $record->species_id : null);

        return [
            'name' => ['required', 'string', 'max:50'],
            'species_id' => ['required', 'integer', 'exists:species,id'],
            'breed_id' => ['required', 'integer', Rule::exists('breeds', 'id')->where('species_id', $speciesId)],
            'sex' => ['required', 'in:M,F'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'estimated_age' => ['nullable', 'integer', 'min:0', 'max:100'],
            'color_id' => ['required', 'integer', 'exists:colors,id'],
            'weight_kg' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'notes' => ['nullable', 'string'],
            'pet_status_id' => ['required', 'integer', 'exists:pet_statuses,id'],
            'registered_at' => ['required', 'date'],
            'status' => ['sometimes', 'in:active,inactive'],
            'shelter_id' => ['required', 'integer', 'exists:shelters,id'],
        ];
    }
}
