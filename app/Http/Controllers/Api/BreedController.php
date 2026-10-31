<?php

namespace App\Http\Controllers\Api;

use App\Models\Breed;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BreedController extends ResourceController
{
    /** @var class-string<Breed> */
    protected string $model = Breed::class;

    /** @var list<string> */
    protected array $with = ['species'];

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'species_id' => ['required', 'integer', 'exists:species,id'],
            'name' => ['required', 'string', 'max:50', Rule::unique('breeds', 'name')->ignore($record)->where('species_id', $request->integer('species_id') ?: $record?->getAttribute('species_id'))],
            'description' => ['required', 'string', 'max:50'],
        ];
    }
}
