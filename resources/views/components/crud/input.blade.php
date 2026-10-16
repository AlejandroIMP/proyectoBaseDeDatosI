@props(['name', 'label', 'type' => 'text', 'span' => false])

<div @class(['sm:col-span-2' => $span])>
    <x-input-label :for="$name" :value="$label" />
    <x-text-input :id="$name" :type="$type" wire:model="form.{{ $name }}" {{ $attributes->merge(['class' => 'mt-1 block w-full']) }} />
    <x-input-error :messages="$errors->get('form.'.$name)" class="mt-2" />
</div>
