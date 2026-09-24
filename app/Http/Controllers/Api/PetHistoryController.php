<?php

namespace App\Http\Controllers\Api;

use App\Models\PetHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PetHistoryController extends ResourceController
{
    /** @var class-string<PetHistory> */
    protected string $model = PetHistory::class;

    /** @var list<string> */
    protected array $with = ['pet', 'petStatus', 'user'];

    protected function defaults(Request $request): array
    {
        return ['user_id' => $request->user()?->id];
    }

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'pet_id' => ['required', 'integer', 'exists:pets,id'],
            'pet_status_id' => ['required', 'integer', 'exists:pet_statuses,id'],
            'notes' => ['required', 'string'],
            'recorded_at' => ['required', 'date'],
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
        ];
    }
}
