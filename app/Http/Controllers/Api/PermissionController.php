<?php

namespace App\Http\Controllers\Api;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermissionController extends ResourceController
{
    /** @var class-string<Permission> */
    protected string $model = Permission::class;

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'name' => ['required', 'string', 'max:50', Rule::unique('permissions', 'name')->ignore($record)],
            'description' => ['nullable', 'string', 'max:100'],
        ];
    }
}
