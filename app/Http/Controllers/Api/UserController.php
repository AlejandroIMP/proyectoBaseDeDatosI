<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends ResourceController
{
    /** @var class-string<User> */
    protected string $model = User::class;

    /** @var list<string> */
    protected array $with = ['roles'];

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($record)],
            'password' => [$record ? 'nullable' : 'required', 'string', 'min:8'],
        ];
    }

    public function update(Request $request, string $id): JsonResponse
    {
        /** @var User $user */
        $user = $this->find($id);

        $rules = array_map(
            fn (array $fieldRules) => ['sometimes', ...$fieldRules],
            $this->rules($request, $user)
        );

        $data = $request->validate($rules);
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json($user->refresh()->load($this->with));
    }

    public function syncRoles(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'role_ids' => ['present', 'array'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
        ]);

        $user->roles()->sync($data['role_ids']);

        return response()->json($user->load('roles'));
    }
}

