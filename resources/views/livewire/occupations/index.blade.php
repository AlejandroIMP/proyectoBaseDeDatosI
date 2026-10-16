<?php

use App\Http\Controllers\Api\OccupationController;
use App\Livewire\CrudComponent;
use App\Models\Occupation;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('layouts.app')] #[Title('Ocupaciones')] class extends CrudComponent
{
    protected string $model = Occupation::class;

    protected string $controller = OccupationController::class;

    protected array $searchable = ['name', 'formalidad', 'horarios'];

    protected array $labels = ['name' => 'nombre'];
}; ?>

<x-crud.page title="Ocupaciones" singular="ocupación" :records="$records">
    <x-slot:head>
        <x-crud.th>ID</x-crud.th>
        <x-crud.th>Nombre</x-crud.th>
        <x-crud.th>Formalidad</x-crud.th>
        <x-crud.th>Horarios</x-crud.th>
    </x-slot:head>

    @foreach ($records as $record)
        <tr wire:key="{{ $record->id }}">
            <x-crud.td>{{ $record->id }}</x-crud.td>
            <x-crud.td>{{ $record->name }}</x-crud.td>
            <x-crud.td>{{ $record->formalidad }}</x-crud.td>
            <x-crud.td>{{ $record->horarios }}</x-crud.td>
            <x-crud.actions :id="$record->id" />
        </tr>
    @endforeach

    <x-slot:form>
        <x-crud.input name="name" label="Nombre" />
        <x-crud.input name="formalidad" label="Formalidad" />
        <x-crud.input name="horarios" label="Horarios" span />
    </x-slot:form>
</x-crud.page>
