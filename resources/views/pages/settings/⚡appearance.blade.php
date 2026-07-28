<?php

use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Appearance settings')] class extends Component {
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Appearance settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Appearance')" :subheading="__('Update the appearance settings for your account')">
        <div class="space-y-6">
            <flux:radio.group x-data variant="segmented" x-model="$flux.appearance" class="w-full sm:w-auto">
                <flux:radio value="light" icon="sun">{{ __('Light') }}</flux:radio>
                <flux:radio value="dark" icon="moon">{{ __('Dark') }}</flux:radio>
                <flux:radio value="system" icon="computer-desktop">{{ __('System') }}</flux:radio>
            </flux:radio.group>

            {{-- Theme Visual Selection Cards --}}
            <div x-data class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                {{-- Light Mode Card --}}
                <div 
                    @click="$flux.appearance = 'light'"
                    :class="$flux.appearance === 'light' ? 'ring-2 ring-blue-500 border-blue-500' : 'border-zinc-200 dark:border-zinc-700'"
                    class="cursor-pointer rounded-xl border p-4 transition-all hover:border-blue-400 bg-white text-zinc-900 shadow-xs flex flex-col gap-3"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <flux:icon.sun class="w-4 h-4 text-amber-500" />
                            <span class="text-xs font-semibold">Light</span>
                        </div>
                        <template x-if="$flux.appearance === 'light'">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        </template>
                    </div>
                    <div class="rounded-lg bg-zinc-100 p-2 text-xs text-zinc-500 border border-zinc-200">
                        <div class="w-full h-2 bg-zinc-300 rounded mb-1"></div>
                        <div class="w-2/3 h-2 bg-zinc-300 rounded"></div>
                    </div>
                </div>

                {{-- Dark Mode Card --}}
                <div 
                    @click="$flux.appearance = 'dark'"
                    :class="$flux.appearance === 'dark' ? 'ring-2 ring-blue-500 border-blue-500' : 'border-zinc-200 dark:border-zinc-700'"
                    class="cursor-pointer rounded-xl border p-4 transition-all hover:border-blue-400 bg-zinc-900 text-white shadow-xs flex flex-col gap-3"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <flux:icon.moon class="w-4 h-4 text-indigo-400" />
                            <span class="text-xs font-semibold">Dark</span>
                        </div>
                        <template x-if="$flux.appearance === 'dark'">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        </template>
                    </div>
                    <div class="rounded-lg bg-zinc-800 p-2 text-xs text-zinc-400 border border-zinc-700">
                        <div class="w-full h-2 bg-zinc-600 rounded mb-1"></div>
                        <div class="w-2/3 h-2 bg-zinc-600 rounded"></div>
                    </div>
                </div>

                {{-- System Card --}}
                <div 
                    @click="$flux.appearance = 'system'"
                    :class="$flux.appearance === 'system' ? 'ring-2 ring-blue-500 border-blue-500' : 'border-zinc-200 dark:border-zinc-700'"
                    class="cursor-pointer rounded-xl border p-4 transition-all hover:border-blue-400 bg-gradient-to-r from-white to-zinc-900 text-zinc-900 dark:text-white shadow-xs flex flex-col gap-3"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <flux:icon.computer-desktop class="w-4 h-4 text-blue-500" />
                            <span class="text-xs font-semibold">System</span>
                        </div>
                        <template x-if="$flux.appearance === 'system'">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        </template>
                    </div>
                    <div class="rounded-lg bg-zinc-100 dark:bg-zinc-800 p-2 text-xs text-zinc-500 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                        <div class="w-full h-2 bg-zinc-300 dark:bg-zinc-600 rounded mb-1"></div>
                        <div class="w-2/3 h-2 bg-zinc-300 dark:bg-zinc-600 rounded"></div>
                    </div>
                </div>
            </div>
        </div>
    </x-pages::settings.layout>
</section>
