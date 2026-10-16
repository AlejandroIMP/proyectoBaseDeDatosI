@props(['name', 'label', 'options' => [], 'optionLabel' => 'name', 'placeholder' => 'Seleccione...', 'live' => false])

{{-- $options: coleccion de modelos (usa id + $optionLabel) o arreglo valor => etiqueta --}}
<div>
    <x-input-label :for="$name" :value="$label" />
    <select id="{{ $name }}" @if ($live) wire:model.live="form.{{ $name }}" @else wire:model="form.{{ $name }}" @endif {{ $attributes->merge(['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500']) }}>
        <option value="">{{ $placeholder }}</option>
        @foreach ($options as $value => $option)
            @if ($option instanceof \Illuminate\Database\Eloquent\Model)
                <option value="{{ $option->getKey() }}">{{ data_get($option, $optionLabel) }}</option>
            @else
                <option value="{{ $value }}">{{ $option }}</option>
            @endif
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('form.'.$name)" class="mt-2" />
</div>
