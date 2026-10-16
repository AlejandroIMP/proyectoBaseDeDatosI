<?php

use App\Http\Controllers\Api\RoleController;
use App\Livewire\CrudComponent;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('layouts.app')] #[Title('Roles')] class extends CrudComponent
{
    protected string $model = Role::class;

    protected string $controller = RoleController::class;

    protected array $with = ['permissions'];

    protected array $searchable = ['name', 'description'];

    protected array $defaults = ['permission_ids' => []];

    protected array $relationFields = ['permission_ids'];

    protected array $labels = ['name' => 'nombre', 'description' => 'descripción', 'permission_ids' => 'permisos'];

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            ...parent::rules($request, $record),
            'permission_ids' => ['array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ];
    }

    protected function formData(Model $record): array
    {
        return [
            ...parent::formData($record),
            'permission_ids' => $record->permissions()->pluck('permissions.id')->map(fn ($id) => (string) $id)->all(),
        ];
    }

    protected function afterSave(Model $record, array $validated): void
    {
        $record->permissions()->sync($validated['permission_ids'] ?? []);
    }

    protected function options(): array
    {
        return ['permissions' => Permission::query()->orderBy('name')->get()];
    }
}; ?>

<x-crud.page title="Roles" singular="rol" :records="$records">
    <x-slot:head>
        <x-crud.th>ID</x-crud.th>
        <x-crud.th>Nombre</x-crud.th>
        <x-crud.th>Descripción</x-crud.th>
        <x-crud.th>Permisos</x-crud.th>
    </x-slot:head>

    @foreach ($records as $record)
        <tr wire:key="{{ $record->id }}">
            <x-crud.td>{{ $record->id }}</x-crud.td>
            <x-crud.td>{{ $record->name }}</x-crud.td>
            <x-crud.td>{{ $record->description }}</x-crud.td>
            <x-crud.td class="whitespace-normal">
                <div class="flex flex-wrap gap-1">
                    @foreach ($record->permissions as $permission)
                        <x-crud.badge color="blue">{{ $permission->name }}</x-crud.badge>
                    @endforeach
                </div>
            </x-crud.td>
            <x-crud.actions :id="$record->id" />
        </tr>
    @endforeach

    <x-slot:form>
        <x-crud.input name="name" label="Nombre" />
        <x-crud.input name="description" label="Descripción" />
        <x-crud.checkbox-group name="permission_ids" label="Permisos" :options="$permissions" />
    </x-slot:form>
</x-crud.page>
