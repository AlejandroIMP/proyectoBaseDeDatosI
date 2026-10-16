<?php

use App\Http\Controllers\Api\UserController;
use App\Livewire\CrudComponent;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('layouts.app')] #[Title('Usuarios')] class extends CrudComponent
{
    protected string $model = User::class;

    protected string $controller = UserController::class;

    protected array $with = ['roles'];

    protected array $searchable = ['name', 'email'];

    protected array $defaults = ['role_ids' => []];

    protected array $relationFields = ['role_ids'];

    protected array $labels = ['name' => 'nombre', 'email' => 'correo', 'password' => 'contraseña', 'role_ids' => 'roles'];

    public function delete(int $id): void
    {
        if ($id === auth()->id()) {
            session()->flash('error', 'No puedes eliminar tu propio usuario.');

            return;
        }

        parent::delete($id);
    }

    protected function rules(Request $request, ?Model $record): array
    {
        $rules = parent::rules($request, $record);

        // Al editar, la contraseña solo cambia si se escribe una nueva.
        if ($record) {
            $rules['password'] = ['nullable', 'string', 'min:8'];
        }

        return [
            ...$rules,
            'role_ids' => ['array'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
        ];
    }

    protected function persistableData(array $validated, ?Model $record): array
    {
        $data = parent::persistableData($validated, $record);

        return blank($data['password'] ?? null) ? Arr::except($data, ['password']) : $data;
    }

    protected function formData(Model $record): array
    {
        return [
            ...parent::formData($record),
            'password' => null,
            'role_ids' => $record->roles()->pluck('roles.id')->map(fn ($id) => (string) $id)->all(),
        ];
    }

    protected function afterSave(Model $record, array $validated): void
    {
        $record->roles()->sync($validated['role_ids'] ?? []);
    }

    protected function options(): array
    {
        return ['roles' => Role::query()->orderBy('name')->get()];
    }
}; ?>

<x-crud.page title="Usuarios" singular="usuario" :records="$records">
    <x-slot:head>
        <x-crud.th>ID</x-crud.th>
        <x-crud.th>Nombre</x-crud.th>
        <x-crud.th>Correo</x-crud.th>
        <x-crud.th>Roles</x-crud.th>
    </x-slot:head>

    @foreach ($records as $record)
        <tr wire:key="{{ $record->id }}">
            <x-crud.td>{{ $record->id }}</x-crud.td>
            <x-crud.td>{{ $record->name }}</x-crud.td>
            <x-crud.td>{{ $record->email }}</x-crud.td>
            <x-crud.td>
                <div class="flex flex-wrap gap-1">
                    @foreach ($record->roles as $role)
                        <x-crud.badge color="blue">{{ $role->name }}</x-crud.badge>
                    @endforeach
                </div>
            </x-crud.td>
            <x-crud.actions :id="$record->id" />
        </tr>
    @endforeach

    <x-slot:form>
        <x-crud.input name="name" label="Nombre" />
        <x-crud.input name="email" label="Correo" type="email" />
        <x-crud.input name="password" label="Contraseña" type="password" autocomplete="new-password" span />
        <p class="-mt-2 text-xs text-gray-500 sm:col-span-2" x-show="$wire.editingId">Deja la contraseña en blanco para conservar la actual.</p>
        <x-crud.checkbox-group name="role_ids" label="Roles" :options="$roles" />
    </x-slot:form>
</x-crud.page>
