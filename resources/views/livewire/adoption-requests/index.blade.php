<?php

use App\Models\Adoption;
use App\Models\AdoptionStatus;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] #[Title('Solicitudes de adopción')] class extends Component
{
    use WithPagination;

    #[Url(as: 'estado', except: AdoptionStatus::PENDING)]
    public string $status = AdoptionStatus::PENDING;

    #[Url(as: 'buscar', except: '')]
    public string $search = '';

    #[Url(as: 'solicitud')]
    public ?int $selectedId = null;

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function select(int $id): void
    {
        $this->selectedId = $id;
    }

    public function move(string $status): void
    {
        try {
            Adoption::query()->findOrFail($this->selectedId)->moveTo($status);
            session()->flash('status', "Solicitud #{$this->selectedId}: {$status}.");
        } catch (DomainException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function with(): array
    {
        $selected = Adoption::query()
            ->with(['pet.species', 'pet.breed', 'pet.petStatus', 'pet.shelter', 'person.occupation', 'person.residence', 'adoptionStatus'])
            ->find($this->selectedId);

        return [
            'requests' => Adoption::query()
                ->with(['pet', 'person', 'adoptionStatus'])
                ->when($this->status !== '', fn (Builder $query) => $query->whereRelation('adoptionStatus', 'name', $this->status))
                ->when($this->search !== '', fn (Builder $query) => $query->where(fn (Builder $query) => $query
                    ->whereRelation('pet', 'name', 'like', '%'.$this->search.'%')
                    ->orWhereRelation('person', 'name', 'like', '%'.$this->search.'%')))
                ->orderBy('requested_at')
                ->orderBy('id')
                ->paginate(10),
            'statuses' => AdoptionStatus::query()->withCount('adoptions')->orderBy('id')->get(),
            'selected' => $selected,
            'steps' => collect($selected?->nextStatuses())
                ->map(fn (string $label, string $status) => [
                    'label' => $label,
                    'blocker' => Adoption::blockerFor($status, $selected->pet_id, $selected->id),
                ]),
            'competing' => $selected === null ? collect() : Adoption::query()
                ->with(['person', 'adoptionStatus'])
                ->where('pet_id', $selected->pet_id)
                ->whereKeyNot($selected->id)
                ->whereHas('adoptionStatus', fn (Builder $query) => $query->whereIn('name', AdoptionStatus::OPEN))
                ->get(),
        ];
    }
}; ?>

<div>
    <x-adopter.header title="Solicitudes de adopción" subtitle="Evalúa cada solicitud y avanza su estado; la mascota se actualiza sola.">
        <x-slot:actions>
            <x-text-input wire:model.live.debounce.300ms="search" type="search" placeholder="Buscar mascota o adoptante..." class="w-full sm:w-72" />
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

            <nav class="flex flex-wrap gap-2" aria-label="Filtrar por estado">
                @foreach ($statuses as $option)
                    <button type="button" wire:click="$set('status', @js($option->name))" wire:key="status-{{ $option->id }}" @class([
                        'rounded-full px-3 py-1.5 text-sm font-medium transition',
                        'bg-indigo-600 text-white' => $status === $option->name,
                        'bg-white text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50' => $status !== $option->name,
                    ])>
                        {{ $option->name }} <span class="tabular-nums opacity-75">{{ $option->adoptions_count }}</span>
                    </button>
                @endforeach
                <button type="button" wire:click="$set('status', '')" @class([
                    'rounded-full px-3 py-1.5 text-sm font-medium transition',
                    'bg-indigo-600 text-white' => $status === '',
                    'bg-white text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50' => $status !== '',
                ])>Todas</button>
            </nav>

            <div class="grid gap-6 lg:grid-cols-5">
                <section class="space-y-3 lg:col-span-2">
                    <div class="divide-y divide-gray-100 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                        @forelse ($requests as $request)
                            <button type="button" wire:click="select({{ $request->id }})" wire:key="request-{{ $request->id }}" @class([
                                'block w-full p-4 text-left transition',
                                'bg-indigo-50' => $selectedId === $request->id,
                                'hover:bg-gray-50' => $selectedId !== $request->id,
                            ])>
                                <span class="flex items-center justify-between gap-2">
                                    <span class="truncate font-medium text-gray-900">{{ $request->pet?->name }}</span>
                                    <x-adopter.status-badge :status="$request->adoptionStatus?->name" />
                                </span>
                                <span class="mt-1 block truncate text-sm text-gray-600">{{ $request->person?->name }}</span>
                                <span class="mt-1 block text-xs text-gray-500">#{{ $request->id }} · {{ $request->requested_at->format('d/m/Y') }}</span>
                            </button>
                        @empty
                            <p class="p-6 text-center text-gray-500">No hay solicitudes.</p>
                        @endforelse
                    </div>

                    <div>{{ $requests->links() }}</div>
                </section>

                <section class="lg:col-span-3">
                    @if ($selected === null)
                        <div class="rounded-xl bg-white p-10 text-center text-gray-500 shadow-sm ring-1 ring-gray-200">
                            Selecciona una solicitud para evaluarla.
                        </div>
                    @else
                        <article class="divide-y divide-gray-100 rounded-xl bg-white shadow-sm ring-1 ring-gray-200" wire:key="detail-{{ $selected->id }}">
                            <header class="flex flex-wrap items-center justify-between gap-3 p-6">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Solicitud #{{ $selected->id }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Solicitada el {{ $selected->requested_at->format('d/m/Y') }}
                                        @if ($selected->approved_at) · aprobada el {{ $selected->approved_at->format('d/m/Y') }} @endif
                                        @if ($selected->delivered_at) · entregada el {{ $selected->delivered_at->format('d/m/Y') }} @endif
                                    </p>
                                </div>
                                <x-adopter.status-badge :status="$selected->adoptionStatus?->name" />
                            </header>

                            <div class="p-6">
                                <h4 class="text-sm font-semibold text-gray-900">Mascota</h4>
                                <dl class="mt-3 grid grid-cols-2 gap-x-6 gap-y-3 text-sm sm:grid-cols-3">
                                    <div>
                                        <dt class="text-gray-500">Nombre</dt>
                                        <dd class="font-medium text-gray-900">{{ $selected->pet?->name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-gray-500">Especie / raza</dt>
                                        <dd class="font-medium text-gray-900">{{ $selected->pet?->species?->name }} · {{ $selected->pet?->breed?->name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-gray-500">Estado</dt>
                                        <dd class="font-medium text-gray-900">{{ $selected->pet?->petStatus?->name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-gray-500">Refugio</dt>
                                        <dd class="font-medium text-gray-900">{{ $selected->pet?->shelter?->name }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div class="p-6">
                                <h4 class="text-sm font-semibold text-gray-900">Adoptante</h4>
                                <dl class="mt-3 grid grid-cols-2 gap-x-6 gap-y-3 text-sm sm:grid-cols-3">
                                    <div>
                                        <dt class="text-gray-500">Nombre</dt>
                                        <dd class="font-medium text-gray-900">{{ $selected->person?->name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-gray-500">Correo</dt>
                                        <dd class="truncate font-medium text-gray-900">{{ $selected->person?->email }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-gray-500">Documento</dt>
                                        <dd class="font-medium text-gray-900">{{ $selected->person?->document_type }} {{ $selected->person?->document_number }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-gray-500">Ocupación</dt>
                                        <dd class="font-medium text-gray-900">{{ $selected->person?->occupation?->name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-gray-500">Ingresos</dt>
                                        <dd class="font-medium text-gray-900">Q{{ number_format((int) $selected->person?->income) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-gray-500">Estado civil / hijos</dt>
                                        <dd class="font-medium text-gray-900">{{ $selected->person?->estado_civil }} · {{ $selected->person?->cantidad_hijos }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-gray-500">Vivienda</dt>
                                        <dd class="font-medium text-gray-900">{{ $selected->person?->residence?->name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-gray-500">Mascotas / límite</dt>
                                        <dd class="font-medium text-gray-900">{{ $selected->person?->current_pets_count }} de {{ $selected->person?->residence?->animal_limit }}</dd>
                                    </div>
                                </dl>

                                @if ($selected->person && $selected->person->residence && $selected->person->current_pets_count >= $selected->person->residence->animal_limit)
                                    <p class="mt-4 rounded-md bg-yellow-50 p-3 text-sm text-yellow-800">La vivienda del adoptante ya alcanzó su límite de animales.</p>
                                @endif

                                @if ($selected->notes)
                                    <p class="mt-4 text-sm whitespace-pre-line text-gray-600">{{ $selected->notes }}</p>
                                @endif
                            </div>

                            @if ($competing->isNotEmpty())
                                <div class="p-6">
                                    <h4 class="text-sm font-semibold text-gray-900">Otras solicitudes en curso para esta mascota</h4>
                                    <ul class="mt-3 space-y-2 text-sm">
                                        @foreach ($competing as $other)
                                            <li class="flex items-center justify-between gap-2" wire:key="other-{{ $other->id }}">
                                                <button type="button" wire:click="select({{ $other->id }})" class="truncate font-medium text-indigo-600 hover:text-indigo-500">#{{ $other->id }} · {{ $other->person?->name }}</button>
                                                <x-adopter.status-badge :status="$other->adoptionStatus?->name" />
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <footer class="p-6">
                                @if ($steps->isEmpty())
                                    <p class="text-sm text-gray-500">Esta solicitud ya está cerrada.</p>
                                @else
                                    @if ($blocker = $steps->pluck('blocker')->filter()->first())
                                        <p class="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-800">{{ $blocker }}</p>
                                    @endif

                                    <div class="flex flex-wrap justify-end gap-3">
                                        @foreach ($steps as $next => $step)
                                            @if ($next === \App\Models\AdoptionStatus::REJECTED)
                                                <x-danger-button type="button" wire:click="move('{{ $next }}')" wire:confirm="¿{{ $step['label'] }} la solicitud #{{ $selected->id }}?" wire:loading.attr="disabled">{{ $step['label'] }}</x-danger-button>
                                            @elseif ($next === \App\Models\AdoptionStatus::IN_REVIEW)
                                                <x-secondary-button type="button" wire:click="move('{{ $next }}')" wire:loading.attr="disabled">{{ $step['label'] }}</x-secondary-button>
                                            @else
                                                <x-primary-button type="button" wire:click="move('{{ $next }}')" wire:confirm="¿{{ $step['label'] }} la solicitud #{{ $selected->id }}?" wire:loading.attr="disabled" :disabled="$step['blocker'] !== null" class="disabled:cursor-not-allowed disabled:opacity-50">{{ $step['label'] }}</x-primary-button>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </footer>
                        </article>
                    @endif
                </section>
            </div>
        </div>
    </div>
</div>
