@props(['status'])

{{-- Estado de una solicitud de adopcion (adoption_statuses.name) --}}
<x-crud.badge :color="match ($status) {
    'Pendiente' => 'yellow',
    'En revisión' => 'blue',
    'Aprobada', 'Entregada' => 'green',
    'Rechazada' => 'red',
    default => 'gray',
}">{{ $status }}</x-crud.badge>
