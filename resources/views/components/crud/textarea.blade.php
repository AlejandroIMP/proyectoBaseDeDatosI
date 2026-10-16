@props(['name', 'label'])

<div class="sm:col-span-2">
    <x-input-label :for="$name" :value="$label" />
    <textarea id="{{ $name }}" wire:model="form.{{ $name }}" rows="3" {{ $attributes->merge(['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500']) }}></textarea>
    <x-input-error :messages="$errors->get('form.'.$name)" class="mt-2" />
</div>
