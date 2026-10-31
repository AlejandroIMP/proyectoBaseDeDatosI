<?php

use App\Http\Controllers\Api\PetStatusController;
use App\Livewire\CrudComponent;
use App\Models\PetStatus;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('layouts.app')] #[Title('Estados de mascota')] class extends CrudComponent
{
    protected string $model = PetStatus::class;

    protected string $controller = PetStatusController::class;

    protected array $defaults = ['is_adoptable' => false];

    protected array $labels = ['name' => 'nombre', 'is_adoptable' => 'elegible para adopción'];
}; ?>

<x-crud.page title="Estados de mascota" singular="estado de mascota" :records="$records">
    <x-slot:head>
        <x-crud.th>ID</x-crud.th>
        <x-crud.th>Nombre</x-crud.th>
        <x-crud.th>Elegible para adopción</x-crud.th>
    </x-slot:head>

    @foreach ($records as $record)
        <tr wire:key="{{ $record->id }}">
            <x-crud.td>{{ $record->id }}</x-crud.td>
            <x-crud.td>{{ $record->name }}</x-crud.td>
            <x-crud.td>
                <x-crud.badge :color="$record->is_adoptable ? 'green' : 'gray'">{{ $record->is_adoptable ? 'Sí' : 'No' }}</x-crud.badge>
            </x-crud.td>
            <x-crud.actions :id="$record->id" />
        </tr>
    @endforeach

    <x-slot:form>
        <x-crud.input name="name" label="Nombre" span />
        <x-crud.checkbox name="is_adoptable" label="Elegible para adopción" />
    </x-slot:form>
</x-crud.page>
