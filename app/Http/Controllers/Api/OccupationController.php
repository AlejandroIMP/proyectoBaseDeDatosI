<?php

namespace App\Http\Controllers\Api;

use App\Models\Occupation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class OccupationController extends ResourceController
{
    /** @var class-string<Occupation> */
    protected string $model = Occupation::class;

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'formalidad' => ['required', 'string', 'max:50'],
            'horarios' => ['required', 'string', 'max:100'],
        ];
    }
}
