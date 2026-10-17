<?php

use App\Models\Pet;
use App\Models\Species;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] #[Title('Mascotas disponibles')] class extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $species = '';

    #[Url(except: '')]
    public string $sex = '';

    public function updated(): void
    {
        $this->resetPage();
    }

    public function with(): array
    {
        return [
            'pets' => Pet::query()
                ->available()
                ->with(['species', 'breed', 'color', 'shelter'])
                ->when($this->search !== '', fn (Builder $query) => $query->where('name', 'like', '%'.$this->search.'%'))
                ->when($this->species !== '', fn (Builder $query) => $query->where('species_id', $this->species))
                ->when($this->sex !== '', fn (Builder $query) => $query->where('sex', $this->sex))
                ->orderByDesc('registered_at')
                ->paginate(12),
            'speciesOptions' => Species::query()->orderBy('name')->get(),
        ];
    }
}; ?>

<div>
    <x-adopter.header title="Mascotas disponibles" subtitle="Conoce a los animales que esperan un hogar y envía tu solicitud de adopción." />

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-3 rounded-lg bg-white p-4 shadow-sm sm:grid-cols-3">
                <x-text-input wire:model.live.debounce.300ms="search" type="search" placeholder="Buscar por nombre..." class="w-full" />
                <select wire:model.live="species" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todas las especies</option>
                    @foreach ($speciesOptions as $option)
                        <option value="{{ $option->id }}">{{ $option->name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="sex" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Macho o hembra</option>
                    <option value="M">Macho</option>
                    <option value="F">Hembra</option>
                </select>
            </div>

            @if ($pets->isEmpty())
                <div class="rounded-lg bg-white p-10 text-center text-gray-500 shadow-sm">
                    No hay mascotas disponibles con esos filtros.
                </div>
            @else
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($pets as $pet)
                        <a href="{{ route('adopter.pet', $pet) }}" wire:navigate wire:key="{{ $pet->id }}" class="group overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200 transition hover:shadow-md hover:ring-indigo-200">
                            <div class="flex h-32 items-center justify-center bg-gradient-to-br from-indigo-50 to-indigo-100 text-indigo-300">
                                <svg class="h-14 w-14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="5" cy="9" r="2.2"/><circle cx="9.5" cy="5" r="2.2"/><circle cx="14.5" cy="5" r="2.2"/><circle cx="19" cy="9" r="2.2"/><path d="M12 10.5c-3.2 0-6.5 4.6-6.5 7.4 0 1.7 1.3 2.6 3 2.6 1.3 0 2.2-.7 3.5-.7s2.2.7 3.5.7c1.7 0 3-.9 3-2.6 0-2.8-3.3-7.4-6.5-7.4z"/></svg>
                            </div>
                            <div class="p-5">
                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-700">{{ $pet->name }}</h3>
                                    <x-crud.badge :color="$pet->sex === 'M' ? 'blue' : 'red'">{{ $pet->sex === 'M' ? 'Macho' : 'Hembra' }}</x-crud.badge>
                                </div>
                                <p class="mt-1 text-sm text-gray-600">{{ $pet->species?->name }} · {{ $pet->breed?->name }}</p>
                                <dl class="mt-4 grid grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <dt class="text-gray-500">Edad</dt>
                                        <dd class="font-medium text-gray-900">{{ $pet->ageLabel() }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-gray-500">Refugio</dt>
                                        <dd class="truncate font-medium text-gray-900">{{ $pet->shelter?->name }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div>{{ $pets->links() }}</div>
            @endif
        </div>
    </div>
</div>
