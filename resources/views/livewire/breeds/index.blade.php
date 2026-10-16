<?php

use App\Http\Controllers\Api\BreedController;
use App\Livewire\CrudComponent;
use App\Models\Breed;
use App\Models\Species;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('layouts.app')] #[Title('Razas')] class extends CrudComponent
{
    protected string $model = Breed::class;

    protected string $controller = BreedController::class;

    protected array $with = ['species'];

    protected array $searchable = ['name', 'description', 'species.name'];

    protected array $labels = ['species_id' => 'especie', 'name' => 'nombre', 'description' => 'descripción'];

    protected function options(): array
    {
        return ['speciesOptions' => Species::query()->orderBy('name')->get()];
    }
}; ?>

<x-crud.page title="Razas" singular="raza" :records="$records">
    <x-slot:head>
        <x-crud.th>ID</x-crud.th>
        <x-crud.th>Nombre</x-crud.th>
        <x-crud.th>Especie</x-crud.th>
        <x-crud.th>Descripción</x-crud.th>
    </x-slot:head>

    @foreach ($records as $record)
        <tr wire:key="{{ $record->id }}">
            <x-crud.td>{{ $record->id }}</x-crud.td>
            <x-crud.td>{{ $record->name }}</x-crud.td>
            <x-crud.td>{{ $record->species?->name }}</x-crud.td>
            <x-crud.td>{{ $record->description }}</x-crud.td>
            <x-crud.actions :id="$record->id" />
        </tr>
    @endforeach

    <x-slot:form>
        <x-crud.input name="name" label="Nombre" />
        <x-crud.select name="species_id" label="Especie" :options="$speciesOptions" />
        <x-crud.input name="description" label="Descripción" span />
    </x-slot:form>
</x-crud.page>
