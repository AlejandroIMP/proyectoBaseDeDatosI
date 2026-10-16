@props(['name', 'label', 'options' => [], 'optionLabel' => 'name'])

<fieldset class="sm:col-span-2">
    <legend class="block text-sm font-medium text-gray-700">{{ $label }}</legend>
    <div class="mt-2 grid max-h-56 grid-cols-1 gap-2 overflow-y-auto rounded-md border border-gray-200 p-3 sm:grid-cols-2">
        @forelse ($options as $option)
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" value="{{ $option->getKey() }}" wire:model="form.{{ $name }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                {{ data_get($option, $optionLabel) }}
            </label>
        @empty
            <p class="text-sm text-gray-500">No hay opciones disponibles.</p>
        @endforelse
    </div>
    <x-input-error :messages="$errors->get('form.'.$name)" class="mt-2" />
</fieldset>
