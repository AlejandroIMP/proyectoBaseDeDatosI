<?php

use App\Http\Controllers\Api\DonationController;
use App\Livewire\CrudComponent;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\Pet;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('layouts.app')] #[Title('Donaciones')] class extends CrudComponent
{
    protected string $model = Donation::class;

    protected string $controller = DonationController::class;

    protected array $with = ['donor', 'pet.species', 'pet.breed', 'pet.color'];

    protected array $searchable = ['contribution_type', 'donor.name', 'pet.name'];

    protected array $defaults = ['contribution_type' => 'cash'];

    protected array $labels = ['donor_id' => 'donante', 'amount' => 'monto', 'contribution_type' => 'tipo de aporte', 'pet_id' => 'mascota'];

    public const TYPES = ['cash' => 'Efectivo', 'transfer' => 'Transferencia', 'in_kind' => 'En especie'];

    protected function options(): array
    {
        return [
            'donors' => Donor::query()->orderBy('name')->get(),
            'pets' => Pet::query()->with(['species', 'breed', 'color'])->orderBy('name')->get(),
        ];
    }
}; ?>

<x-crud.page title="Donaciones" singular="donación" :records="$records">
    <x-slot:head>
        <x-crud.th>ID</x-crud.th>
        <x-crud.th>Donante</x-crud.th>
        <x-crud.th class="text-right">Monto</x-crud.th>
        <x-crud.th>Tipo de aporte</x-crud.th>
        <x-crud.th>Mascota</x-crud.th>
        <x-crud.th>Fecha</x-crud.th>
    </x-slot:head>

    @foreach ($records as $record)
        <tr wire:key="{{ $record->id }}">
            <x-crud.td>{{ $record->id }}</x-crud.td>
            <x-crud.td>{{ $record->donor?->name }}</x-crud.td>
            <x-crud.td class="text-right tabular-nums">Q {{ number_format((float) $record->amount, 2) }}</x-crud.td>
            <x-crud.td>{{ $this::TYPES[$record->contribution_type] ?? $record->contribution_type }}</x-crud.td>
            <x-crud.td><x-crud.pet-name :pet="$record->pet" empty="General" /></x-crud.td>
            <x-crud.td>{{ $record->created_at?->format('Y-m-d') }}</x-crud.td>
            <x-crud.actions :id="$record->id" />
        </tr>
    @endforeach

    <x-slot:form>
        <x-crud.select name="donor_id" label="Donante" :options="$donors" />
        <x-crud.input name="amount" label="Monto (Q)" type="number" step="0.01" min="0" />
        <x-crud.select name="contribution_type" label="Tipo de aporte" :options="$this::TYPES" />
        <x-crud.pet-select :pets="$pets" label="Mascota (opcional)" placeholder="Donación general" />
    </x-slot:form>
</x-crud.page>
