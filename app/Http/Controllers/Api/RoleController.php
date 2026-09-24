<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends ResourceController
{
    /** @var class-string<Role> */
    protected string $model = Role::class;

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'name' => ['required', 'string', 'max:50', Rule::unique('roles', 'name')->ignore($record)],
            'description' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function syncPermissions(Request $request, Role $role): JsonResponse
    {
        $data = $request->validate([
            'permission_ids' => ['present', 'array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role->permissions()->sync($data['permission_ids']);

        return response()->json($role->load('permissions'));
    }
}
