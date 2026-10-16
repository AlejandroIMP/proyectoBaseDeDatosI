<?php

use App\Http\Controllers\Api\RescueController;
use App\Livewire\CrudComponent;
use App\Models\Pet;
use App\Models\PetStatus;
use App\Models\Rescue;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('layouts.app')] #[Title('Rescates')] class extends CrudComponent
{
    protected string $model = Rescue::class;

    protected string $controller = RescueController::class;

    protected array $with = ['user', 'pet', 'petStatus'];

    protected array $searchable = ['notes', 'pet.name'];

    protected array $labels = ['pet_id' => 'mascota', 'notes' => 'observaciones', 'pet_status_id' => 'estado de la mascota', 'rescued_at' => 'fecha de rescate'];

    protected function options(): array
    {
        return [
            'pets' => Pet::query()->orderBy('name')->get(),
            'petStatuses' => PetStatus::query()->orderBy('name')->get(),
        ];
    }
}; ?>

<x-crud.page title="Rescates" singular="rescate" :records="$records">
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
            <x-crud.td>{{ $record->rescued_at?->format('Y-m-d') }}</x-crud.td>
            <x-crud.td>{{ $record->pet?->name }}</x-crud.td>
            <x-crud.td>{{ $record->petStatus?->name }}</x-crud.td>
            <x-crud.td class="max-w-xs truncate" :title="$record->notes">{{ $record->notes }}</x-crud.td>
            <x-crud.td>{{ $record->user?->name }}</x-crud.td>
            <x-crud.actions :id="$record->id" />
        </tr>
    @endforeach

    <x-slot:form>
        <x-crud.select name="pet_id" label="Mascota" :options="$pets" />
        <x-crud.select name="pet_status_id" label="Estado de la mascota" :options="$petStatuses" />
        <x-crud.input name="rescued_at" label="Fecha de rescate" type="date" />
        <x-crud.textarea name="notes" label="Observaciones" />
    </x-slot:form>
</x-crud.page>
