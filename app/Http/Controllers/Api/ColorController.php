<?php

namespace App\Http\Controllers\Api;

use App\Models\Color;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ColorController extends ResourceController
{
    /** @var class-string<Color> */
    protected string $model = Color::class;

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'hex_color' => ['nullable', 'string', 'max:10'],
            'name' => ['required', 'string', 'max:50'],
        ];
    }
}
