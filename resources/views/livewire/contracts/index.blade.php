<?php

use App\Http\Controllers\Api\ContractController;
use App\Livewire\CrudComponent;
use App\Models\Adoption;
use App\Models\Contract;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('layouts.app')] #[Title('Contratos')] class extends CrudComponent
{
    protected string $model = Contract::class;

    protected string $controller = ContractController::class;

    protected array $with = ['adoption.pet', 'adoption.person', 'user'];

    protected array $searchable = ['name', 'document_path'];

    protected array $labels = ['adoption_id' => 'adopción', 'name' => 'nombre', 'document_path' => 'ruta del documento'];

    protected function options(): array
    {
        return [
            'adoptions' => Adoption::query()->with(['pet', 'person'])->orderBy('id')->get()
                ->mapWithKeys(fn (Adoption $adoption) => [$adoption->id => "#{$adoption->id} · {$adoption->pet?->name} → {$adoption->person?->name}"]),
        ];
    }
}; ?>

<x-crud.page title="Contratos" singular="contrato" :records="$records">
    <x-slot:head>
        <x-crud.th>ID</x-crud.th>
        <x-crud.th>Nombre</x-crud.th>
        <x-crud.th>Adopción</x-crud.th>
        <x-crud.th>Documento</x-crud.th>
        <x-crud.th>Registrado por</x-crud.th>
    </x-slot:head>

    @foreach ($records as $record)
        <tr wire:key="{{ $record->id }}">
            <x-crud.td>{{ $record->id }}</x-crud.td>
            <x-crud.td>{{ $record->name }}</x-crud.td>
            <x-crud.td>#{{ $record->adoption_id }} · {{ $record->adoption?->pet?->name }} → {{ $record->adoption?->person?->name }}</x-crud.td>
            <x-crud.td class="font-mono text-xs">{{ $record->document_path }}</x-crud.td>
            <x-crud.td>{{ $record->user?->name }}</x-crud.td>
            <x-crud.actions :id="$record->id" />
        </tr>
    @endforeach

    <x-slot:form>
        <x-crud.input name="name" label="Nombre" />
        <x-crud.select name="adoption_id" label="Adopción" :options="$adoptions" />
        <x-crud.input name="document_path" label="Ruta del documento" span />
    </x-slot:form>
</x-crud.page>
