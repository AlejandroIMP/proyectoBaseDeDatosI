<?php

use App\Http\Controllers\Api\PetHistoryController;
use App\Livewire\CrudComponent;
use App\Models\PetHistory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('layouts.app')] #[Title('Historial de mascotas')] class extends CrudComponent
{
    protected string $model = PetHistory::class;

    protected string $controller = PetHistoryController::class;

    // La aplicacion registra cada cambio de estado de la mascota (ver Pet::booted()).
    protected bool $readOnly = true;

    protected array $with = ['pet.species', 'pet.breed', 'pet.color', 'petStatus', 'user'];

    protected array $searchable = ['notes', 'pet.name'];
}; ?>

<x-crud.page title="Historial de mascotas" singular="registro de historial" :records="$records" readonly>
    <x-slot:head>
        <x-crud.th>ID</x-crud.th>
        <x-crud.th>Fecha</x-crud.th>
        <x-crud.th>Mascota</x-crud.th>
        <x-crud.th>Estado</x-crud.th>
        <x-crud.th>Observaciones</x-crud.th>
        <x-crud.th>Registrado por</x-crud.th>
    </x-slot:head>

    @foreach ($records as $record)
        <tr wire:key="{{ $record->id }}">
            <x-crud.td>{{ $record->id }}</x-crud.td>
            <x-crud.td>{{ $record->recorded_at?->format('Y-m-d') }}</x-crud.td>
            <x-crud.td><x-crud.pet-name :pet="$record->pet" /></x-crud.td>
            <x-crud.td>{{ $record->petStatus?->name }}</x-crud.td>
            <x-crud.td class="max-w-xs truncate" :title="$record->notes">{{ $record->notes }}</x-crud.td>
            <x-crud.td>{{ $record->user?->name }}</x-crud.td>
        </tr>
    @endforeach
</x-crud.page>
