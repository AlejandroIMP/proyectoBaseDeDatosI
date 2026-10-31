<?php

namespace App\Http\Controllers\Api;

use App\Models\Color;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ColorController extends ResourceController
{
    /** @var class-string<Color> */
    protected string $model = Color::class;

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'hex_color' => ['nullable', 'string', 'max:10'],
            'name' => ['required', 'string', 'max:50', Rule::unique('colors', 'name')->ignore($record)],
        ];
    }
}
