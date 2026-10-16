<?php

use App\Http\Controllers\Api\AdoptionFollowUpController;
use App\Livewire\CrudComponent;
use App\Models\Adoption;
use App\Models\AdoptionFollowUp;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('layouts.app')] #[Title('Seguimiento de adopciones')] class extends CrudComponent
{
    protected string $model = AdoptionFollowUp::class;

    protected string $controller = AdoptionFollowUpController::class;

    protected array $with = ['adoption.pet', 'adoption.person', 'user'];

    protected array $searchable = ['notes', 'adoption.pet.name'];

    protected array $labels = ['adoption_id' => 'adopción', 'followed_at' => 'fecha de seguimiento', 'notes' => 'observaciones'];

    protected function options(): array
    {
        return [
            'adoptions' => Adoption::query()->with(['pet', 'person'])->orderBy('id')->get()
                ->mapWithKeys(fn (Adoption $adoption) => [$adoption->id => "#{$adoption->id} · {$adoption->pet?->name} → {$adoption->person?->name}"]),
        ];
    }
}; ?>

<x-crud.page title="Seguimiento de adopciones" singular="seguimiento" :records="$records">
    <x-slot:head>
        <x-crud.th>ID</x-crud.th>
        <x-crud.th>Fecha</x-crud.th>
        <x-crud.th>Adopción</x-crud.th>
        <x-crud.th>Observaciones</x-crud.th>
        <x-crud.th>Registrado por</x-crud.th>
    </x-slot:head>

    @foreach ($records as $record)
        <tr wire:key="{{ $record->id }}">
            <x-crud.td>{{ $record->id }}</x-crud.td>
            <x-crud.td>{{ $record->followed_at?->format('Y-m-d') }}</x-crud.td>
            <x-crud.td>#{{ $record->adoption_id }} · {{ $record->adoption?->pet?->name }} → {{ $record->adoption?->person?->name }}</x-crud.td>
            <x-crud.td class="max-w-xs truncate" :title="$record->notes">{{ $record->notes }}</x-crud.td>
            <x-crud.td>{{ $record->user?->name }}</x-crud.td>
            <x-crud.actions :id="$record->id" />
        </tr>
    @endforeach

    <x-slot:form>
        <x-crud.select name="adoption_id" label="Adopción" :options="$adoptions" />
        <x-crud.input name="followed_at" label="Fecha de seguimiento" type="date" />
        <x-crud.textarea name="notes" label="Observaciones" />
    </x-slot:form>
</x-crud.page>
