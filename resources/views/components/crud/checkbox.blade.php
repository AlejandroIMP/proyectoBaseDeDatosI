@props(['name', 'label'])

<div class="flex items-center gap-2 sm:col-span-2">
    <input id="{{ $name }}" type="checkbox" wire:model="form.{{ $name }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
    <x-input-label :for="$name" :value="$label" />
    <x-input-error :messages="$errors->get('form.'.$name)" class="mt-2" />
</div>
