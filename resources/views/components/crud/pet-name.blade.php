@props(['pet', 'empty' => ''])

{{-- Nombre de la mascota con especie/raza y color debajo, para distinguir nombres repetidos --}}
@if ($pet)
    {{ $pet->name }}
    <span class="block text-xs text-gray-500">{{ collect([$pet->species?->name, $pet->breed?->name])->filter()->join(' / ') }}{{ $pet->color ? ' · '.$pet->color->name : '' }}</span>
@else
    {{ $empty }}
@endif
