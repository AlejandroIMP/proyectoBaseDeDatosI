<?php

namespace App\Http\Controllers\Api;

use App\Models\AdoptionFollowUp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AdoptionFollowUpController extends ResourceController
{
    /** @var class-string<AdoptionFollowUp> */
    protected string $model = AdoptionFollowUp::class;

    /** @var list<string> */
    protected array $with = ['adoption', 'user'];

    protected function defaults(Request $request): array
    {
        return ['user_id' => $request->user()?->id];
    }

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'adoption_id' => ['required', 'integer', 'exists:adoptions,id'],
            'followed_at' => ['required', 'date'],
            'notes' => ['required', 'string'],
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
        ];
    }
}
