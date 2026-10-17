<?php

use App\Http\Controllers\Api\PetHistoryController;
use App\Livewire\CrudComponent;
use App\Models\Pet;
use App\Models\PetHistory;
use App\Models\PetStatus;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('layouts.app')] #[Title('Historial de mascotas')] class extends CrudComponent
{
    protected string $model = PetHistory::class;

    protected string $controller = PetHistoryController::class;

    protected array $with = ['pet.species', 'pet.breed', 'pet.color', 'petStatus', 'user'];

    protected array $searchable = ['notes', 'pet.name'];

    protected array $labels = ['pet_id' => 'mascota', 'pet_status_id' => 'estado', 'notes' => 'observaciones', 'recorded_at' => 'fecha'];

    protected function options(): array
    {
        return [
            'pets' => Pet::query()->with(['species', 'breed', 'color'])->orderBy('name')->get(),
            'petStatuses' => PetStatus::query()->orderBy('name')->get(),
        ];
    }
}; ?>

<x-crud.page title="Historial de mascotas" singular="registro de historial" :records="$records">
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
            <x-crud.actions :id="$record->id" />
        </tr>
    @endforeach

    <x-slot:form>
        <x-crud.pet-select :pets="$pets" />
        <x-crud.select name="pet_status_id" label="Estado" :options="$petStatuses" />
        <x-crud.input name="recorded_at" label="Fecha" type="date" />
        <x-crud.textarea name="notes" label="Observaciones" />
    </x-slot:form>
</x-crud.page>
