<?php

namespace App\Http\Controllers\Api;

use App\Models\Shelter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ShelterController extends ResourceController
{
    /** @var class-string<Shelter> */
    protected string $model = Shelter::class;

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
        ];
    }
}
