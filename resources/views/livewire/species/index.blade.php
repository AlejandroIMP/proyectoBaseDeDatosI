<?php

use App\Http\Controllers\Api\SpeciesController;
use App\Livewire\CrudComponent;
use App\Models\Species;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('layouts.app')] #[Title('Especies')] class extends CrudComponent
{
    protected string $model = Species::class;

    protected string $controller = SpeciesController::class;

    protected array $searchable = ['name', 'description'];

    protected array $labels = ['name' => 'nombre', 'description' => 'descripción'];
}; ?>

<x-crud.page title="Especies" singular="especie" :records="$records">
    <x-slot:head>
        <x-crud.th>ID</x-crud.th>
        <x-crud.th>Nombre</x-crud.th>
        <x-crud.th>Descripción</x-crud.th>
    </x-slot:head>

    @foreach ($records as $record)
        <tr wire:key="{{ $record->id }}">
            <x-crud.td>{{ $record->id }}</x-crud.td>
            <x-crud.td>{{ $record->name }}</x-crud.td>
            <x-crud.td>{{ $record->description }}</x-crud.td>
            <x-crud.actions :id="$record->id" />
        </tr>
    @endforeach

    <x-slot:form>
        <x-crud.input name="name" label="Nombre" />
        <x-crud.input name="description" label="Descripción" />
    </x-slot:form>
</x-crud.page>
