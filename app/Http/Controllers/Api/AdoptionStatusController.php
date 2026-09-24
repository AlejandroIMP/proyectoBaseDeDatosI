<?php

namespace App\Http\Controllers\Api;

use App\Models\AdoptionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AdoptionStatusController extends ResourceController
{
    /** @var class-string<AdoptionStatus> */
    protected string $model = AdoptionStatus::class;

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
        ];
    }
}
