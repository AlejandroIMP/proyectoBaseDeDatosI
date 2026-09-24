<?php

namespace App\Http\Controllers\Api;

use App\Models\Person;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PersonController extends ResourceController
{
    /** @var class-string<Person> */
    protected string $model = Person::class;

    /** @var list<string> */
    protected array $with = ['occupation', 'residence'];

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'document_type' => ['required', 'in:DPI,PASSPORT'],
            'document_number' => ['required', 'string', 'max:25', Rule::unique('people', 'document_number')->ignore($record)],
            'email' => ['required', 'email', 'max:150'],
            'current_pets_count' => ['sometimes', 'integer', 'min:0'],
            'estado_civil' => ['required', 'string', 'max:50'],
            'cantidad_hijos' => ['required', 'integer', 'min:0', 'max:99'],
            'occupation_id' => ['required', 'integer', 'exists:occupations,id'],
            'residence_id' => ['required', 'integer', 'exists:residences,id'],
            'income' => ['required', 'integer', 'min:0'],
        ];
    }
}
