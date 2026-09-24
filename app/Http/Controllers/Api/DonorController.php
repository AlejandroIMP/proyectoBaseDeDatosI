<?php

namespace App\Http\Controllers\Api;

use App\Models\Donor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class DonorController extends ResourceController
{
    /** @var class-string<Donor> */
    protected string $model = Donor::class;

    /** @var list<string> */
    protected array $with = ['person'];

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'person_id' => ['required', 'integer', 'exists:people,id'],
        ];
    }
}
