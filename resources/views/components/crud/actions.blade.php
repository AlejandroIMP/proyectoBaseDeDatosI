@props(['id'])

<td class="px-4 py-3 text-right whitespace-nowrap">
    <button type="button" wire:click="edit({{ $id }})" class="font-medium text-indigo-600 hover:text-indigo-900">Editar</button>
    <button type="button" wire:click="delete({{ $id }})" wire:confirm="¿Seguro que deseas eliminar este registro?" class="ms-3 font-medium text-red-600 hover:text-red-900">Eliminar</button>
</td>
