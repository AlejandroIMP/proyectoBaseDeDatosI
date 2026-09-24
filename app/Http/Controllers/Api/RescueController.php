<?php

namespace App\Http\Controllers\Api;

use App\Models\Rescue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RescueController extends ResourceController
{
    /** @var class-string<Rescue> */
    protected string $model = Rescue::class;

    /** @var list<string> */
    protected array $with = ['user', 'pet', 'petStatus'];

    protected function defaults(Request $request): array
    {
        return ['user_id' => $request->user()?->id];
    }

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
            'pet_id' => ['required', 'integer', 'exists:pets,id'],
            'notes' => ['nullable', 'string'],
            'pet_status_id' => ['required', 'integer', 'exists:pet_statuses,id'],
            'rescued_at' => ['required', 'date'],
        ];
    }
}
