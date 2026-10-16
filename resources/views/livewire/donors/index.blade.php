<?php

use App\Http\Controllers\Api\DonorController;
use App\Livewire\CrudComponent;
use App\Models\Donor;
use App\Models\Person;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('layouts.app')] #[Title('Donantes')] class extends CrudComponent
{
    protected string $model = Donor::class;

    protected string $controller = DonorController::class;

    protected array $with = ['person'];

    protected array $searchable = ['name', 'person.name'];

    protected array $labels = ['name' => 'nombre', 'person_id' => 'persona'];

    protected function options(): array
    {
        return ['people' => Person::query()->orderBy('name')->get()];
    }
}; ?>

<x-crud.page title="Donantes" singular="donante" :records="$records">
    <x-slot:head>
        <x-crud.th>ID</x-crud.th>
        <x-crud.th>Nombre</x-crud.th>
        <x-crud.th>Persona</x-crud.th>
    </x-slot:head>

    @foreach ($records as $record)
        <tr wire:key="{{ $record->id }}">
            <x-crud.td>{{ $record->id }}</x-crud.td>
            <x-crud.td>{{ $record->name }}</x-crud.td>
            <x-crud.td>{{ $record->person?->name }}</x-crud.td>
            <x-crud.actions :id="$record->id" />
        </tr>
    @endforeach

    <x-slot:form>
        <x-crud.input name="name" label="Nombre" />
        <x-crud.select name="person_id" label="Persona" :options="$people" />
    </x-slot:form>
</x-crud.page>
