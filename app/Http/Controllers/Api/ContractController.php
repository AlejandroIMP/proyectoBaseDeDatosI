<?php

namespace App\Http\Controllers\Api;

use App\Models\Contract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ContractController extends ResourceController
{
    /** @var class-string<Contract> */
    protected string $model = Contract::class;

    /** @var list<string> */
    protected array $with = ['user', 'adoption'];

    protected function defaults(Request $request): array
    {
        return ['user_id' => $request->user()?->id];
    }

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
            'adoption_id' => ['required', 'integer', 'exists:adoptions,id'],
            'name' => ['required', 'string', 'max:100'],
            'document_path' => ['nullable', 'string', 'max:255'],
        ];
    }
}
