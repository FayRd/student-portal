<div class="flex flex-col gap-6">
    <x-auth-header
        :title="__('Set your password')"
        :description="__('Your account was created with a temporary password. Please set a new one to continue.')"
    />

    <div class="flex flex-col gap-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                New password
            </label>
            <input
                wire:model="password"
                type="password"
                autocomplete="new-password"
                class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            @error('password')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                Confirm password
            </label>
            <input
                wire:model="password_confirmation"
                type="password"
                autocomplete="new-password"
                class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
        </div>

        <p class="text-xs text-gray-400">
            Minimum 8 characters including letters, numbers, and at least one symbol.
        </p>

        <flux:button
            wire:click="changePassword"
            type="button"
            variant="primary"
            class="w-full"
        >
            {{ __('Set password and continue') }}
        </flux:button>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <flux:button variant="ghost" type="submit" class="w-full text-sm cursor-pointer">
                {{ __('Sign out') }}
            </flux:button>
        </form>
    </div>
</div>
