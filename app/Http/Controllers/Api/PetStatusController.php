<?php

namespace App\Http\Controllers\Api;

use App\Models\PetStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PetStatusController extends ResourceController
{
    /** @var class-string<PetStatus> */
    protected string $model = PetStatus::class;

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
        ];
    }
}
