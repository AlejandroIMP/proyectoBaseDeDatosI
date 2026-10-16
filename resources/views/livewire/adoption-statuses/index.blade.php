<?php

use App\Http\Controllers\Api\AdoptionStatusController;
use App\Livewire\CrudComponent;
use App\Models\AdoptionStatus;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('layouts.app')] #[Title('Estados de adopción')] class extends CrudComponent
{
    protected string $model = AdoptionStatus::class;

    protected string $controller = AdoptionStatusController::class;

    protected array $labels = ['name' => 'nombre'];
}; ?>

<x-crud.page title="Estados de adopción" singular="estado de adopción" :records="$records">
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
