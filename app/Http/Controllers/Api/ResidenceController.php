<?php

namespace App\Http\Controllers\Api;

use App\Models\Residence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ResidenceController extends ResourceController
{
    /** @var class-string<Residence> */
    protected string $model = Residence::class;

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'name' => ['nullable', 'string', 'max:50'],
            'animal_limit' => ['required', 'integer', 'min:0', 'max:32767'],
        ];
    }
}
