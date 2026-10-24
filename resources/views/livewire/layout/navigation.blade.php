<?php

use App\Livewire\Actions\Logout;
use App\Support\Modules;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate>
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 text-white">
                            <x-application-logo class="h-5 w-5" />
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 lg:-my-px lg:ms-10 lg:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @can('adopt')
                        <x-nav-link :href="route('adopter.pets')" :active="request()->routeIs('adopter.pets', 'adopter.pet')" wire:navigate>Adoptar</x-nav-link>
                        <x-nav-link :href="route('adopter.requests')" :active="request()->routeIs('adopter.requests')" wire:navigate>Mis solicitudes</x-nav-link>
                        <x-nav-link :href="route('adopter.profile')" :active="request()->routeIs('adopter.profile')" wire:navigate>Mis datos</x-nav-link>
                    @endcan

                    @foreach (Modules::groupsFor(auth()->user()) as $group => $modules)
                        @php($groupActive = collect(array_keys($modules))->contains(fn ($slug) => request()->routeIs('crud.'.$slug)))
                        <div class="flex items-center">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button @class([
                                        'inline-flex h-16 items-center border-b-2 px-1 pt-1 text-sm leading-5 font-medium transition duration-150 ease-in-out focus:outline-none',
                                        'border-indigo-400 text-gray-900' => $groupActive,
                                        'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' => ! $groupActive,
                                    ])>
                                        {{ $group }}
                                        <svg class="ms-1 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    @foreach ($modules as $slug => $module)
                                        <x-dropdown-link :href="route('crud.'.$slug)" wire:navigate @class(['bg-gray-100 font-medium' => request()->routeIs('crud.'.$slug)])>
                                            {{ $module['label'] }}
                                        </x-dropdown-link>
                                    @endforeach
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden lg:flex lg:items-center lg:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center lg:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden lg:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @can('adopt')
                <x-responsive-nav-link :href="route('adopter.pets')" :active="request()->routeIs('adopter.pets', 'adopter.pet')" wire:navigate>Adoptar</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('adopter.requests')" :active="request()->routeIs('adopter.requests')" wire:navigate>Mis solicitudes</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('adopter.profile')" :active="request()->routeIs('adopter.profile')" wire:navigate>Mis datos</x-responsive-nav-link>
            @endcan

            @foreach (Modules::groupsFor(auth()->user()) as $group => $modules)
                <div class="px-4 pt-3 text-xs font-semibold tracking-wider text-gray-400 uppercase">{{ $group }}</div>
                @foreach ($modules as $slug => $module)
                    <x-responsive-nav-link :href="route('crud.'.$slug)" :active="request()->routeIs('crud.'.$slug)" wire:navigate>
                        {{ $module['label'] }}
                    </x-responsive-nav-link>
                @endforeach
            @endforeach
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
