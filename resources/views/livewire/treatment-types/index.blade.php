<?php

use App\Http\Controllers\Api\TreatmentTypeController;
use App\Livewire\CrudComponent;
use App\Models\TreatmentType;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('layouts.app')] #[Title('Tipos de tratamiento')] class extends CrudComponent
{
    protected string $model = TreatmentType::class;

    protected string $controller = TreatmentTypeController::class;

    protected array $labels = ['name' => 'nombre'];
}; ?>

<x-crud.page title="Tipos de tratamiento" singular="tipo de tratamiento" :records="$records">
    <x-slot:head>
        <x-crud.th>ID</x-crud.th>
        <x-crud.th>Nombre</x-crud.th>
    </x-slot:head>

    @foreach ($records as $record)
        <tr wire:key="{{ $record->id }}">
            <x-crud.td>{{ $record->id }}</x-crud.td>
            <x-crud.td>{{ $record->name }}</x-crud.td>
            <x-crud.actions :id="$record->id" />
        </tr>
    @endforeach

    <x-slot:form>
        <x-crud.input name="name" label="Nombre" span />
    </x-slot:form>
</x-crud.page>
