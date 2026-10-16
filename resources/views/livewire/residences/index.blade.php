<?php

use App\Http\Controllers\Api\ResidenceController;
use App\Livewire\CrudComponent;
use App\Models\Residence;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('layouts.app')] #[Title('Viviendas')] class extends CrudComponent
{
    protected string $model = Residence::class;

    protected string $controller = ResidenceController::class;

    protected array $labels = ['name' => 'nombre', 'animal_limit' => 'límite de animales'];
}; ?>

<x-crud.page title="Viviendas" singular="vivienda" :records="$records">
    <x-slot:head>
        <x-crud.th>ID</x-crud.th>
        <x-crud.th>Nombre</x-crud.th>
        <x-crud.th>Límite de animales</x-crud.th>
    </x-slot:head>

    @foreach ($records as $record)
        <tr wire:key="{{ $record->id }}">
            <x-crud.td>{{ $record->id }}</x-crud.td>
            <x-crud.td>{{ $record->name }}</x-crud.td>
            <x-crud.td>{{ $record->animal_limit }}</x-crud.td>
            <x-crud.actions :id="$record->id" />
        </tr>
    @endforeach

    <x-slot:form>
        <x-crud.input name="name" label="Nombre" />
        <x-crud.input name="animal_limit" label="Límite de animales" type="number" min="0" />
    </x-slot:form>
</x-crud.page>
