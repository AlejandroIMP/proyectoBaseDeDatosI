<?php

namespace App\Http\Controllers\Api;

use App\Models\Species;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class SpeciesController extends ResourceController
{
    /** @var class-string<Species> */
    protected string $model = Species::class;

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'description' => ['required', 'string', 'max:50'],
        ];
    }
}
