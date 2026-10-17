<?php

use App\Models\Adoption;
use App\Models\AdoptionStatus;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] #[Title('Mis solicitudes')] class extends Component
{
    public function cancel(int $id): void
    {
        $adoption = $this->requests()->with('adoptionStatus')->findOrFail($id);

        if ($adoption->adoptionStatus?->name !== AdoptionStatus::PENDING) {
            session()->flash('error', 'Solo puedes cancelar solicitudes pendientes.');

            return;
        }

        $adoption->update([
            'adoption_status_id' => AdoptionStatus::query()->where('name', AdoptionStatus::CANCELLED)->value('id'),
        ]);

        session()->flash('status', 'La solicitud fue cancelada.');
    }

    public function with(): array
    {
        return [
            'adoptions' => $this->requests()
                ->with(['pet.species', 'pet.breed', 'adoptionStatus'])
                ->orderByDesc('requested_at')
                ->orderByDesc('id')
                ->get(),
        ];
    }

    /**
     * Adoption requests made by the people record of the signed in adopter.
     *
     * @return Builder<Adoption>
     */
    private function requests(): Builder
    {
        return Adoption::query()->whereHas('person', fn (Builder $query) => $query->where('user_id', auth()->id()));
    }
}; ?>

<div>
    <x-adopter.header title="Mis solicitudes" subtitle="Sigue el estado de cada solicitud de adopción que has enviado.">
        <x-slot:actions>
            <a href="{{ route('adopter.pets') }}" wire:navigate class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Ver mascotas disponibles</a>
        </x-slot:actions>
    </x-adopter.header>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            @if (session('error'))
                <div class="rounded-md bg-red-50 p-4 text-sm text-red-800">{{ session('error') }}</div>
            @endif

            @forelse ($adoptions as $adoption)
                <article wire:key="{{ $adoption->id }}" class="flex flex-col gap-4 rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $adoption->pet?->name }}</h3>
                            <x-adopter.status-badge :status="$adoption->adoptionStatus?->name" />
                        </div>
                        <p class="mt-1 text-sm text-gray-600">{{ $adoption->pet?->species?->name }} · {{ $adoption->pet?->breed?->name }}</p>
                        <p class="mt-2 text-sm text-gray-500">
                            Solicitada el {{ $adoption->requested_at->format('d/m/Y') }}
                            @if ($adoption->approved_at) · aprobada el {{ $adoption->approved_at->format('d/m/Y') }} @endif
                            @if ($adoption->delivered_at) · entregada el {{ $adoption->delivered_at->format('d/m/Y') }} @endif
                        </p>
                    </div>

                    @if ($adoption->adoptionStatus?->name === \App\Models\AdoptionStatus::PENDING)
                        <x-danger-button type="button" wire:click="cancel({{ $adoption->id }})" wire:confirm="¿Cancelar la solicitud para adoptar a {{ $adoption->pet?->name }}?" class="shrink-0">
                            Cancelar
                        </x-danger-button>
                    @endif
                </article>
            @empty
                <div class="rounded-lg bg-white p-10 text-center shadow-sm">
                    <p class="text-gray-600">Aún no has enviado solicitudes de adopción.</p>
                    <a href="{{ route('adopter.pets') }}" wire:navigate class="mt-3 inline-flex text-sm font-medium text-indigo-600 hover:text-indigo-500">Conoce a las mascotas disponibles &rarr;</a>
                </div>
            @endforelse
        </div>
    </div>
</div>
