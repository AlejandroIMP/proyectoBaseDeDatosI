<?php

namespace App\Http\Controllers\Api;

use App\Models\Adoption;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AdoptionController extends ResourceController
{
    /** @var class-string<Adoption> */
    protected string $model = Adoption::class;

    /** @var list<string> */
    protected array $with = ['pet', 'person', 'adoptionStatus', 'user'];

    protected function defaults(Request $request): array
    {
        return ['user_id' => $request->user()?->id];
    }

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'pet_id' => ['required', 'integer', 'exists:pets,id'],
            'person_id' => ['required', 'integer', 'exists:people,id'],
            'requested_at' => ['required', 'date'],
            'approved_at' => ['nullable', 'date'],
            'delivered_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'document' => ['nullable', 'string', 'max:255'],
            'adoption_status_id' => ['required', 'integer', 'exists:adoption_statuses,id'],
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
        ];
    }
}
