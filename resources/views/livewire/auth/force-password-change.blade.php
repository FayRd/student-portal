<div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900">
    <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">

        <div class="mb-6">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Set your password</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Your account was created with a temporary password. Please set a new password to continue.
            </p>
        </div>

        <div class="flex flex-col gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">New password</label>
                <input
                    wire:model="password"
                    type="password"
                    autocomplete="new-password"
                    class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Confirm password</label>
                <input
                    wire:model="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <p class="text-xs text-gray-400">
                Minimum 8 characters including letters, numbers, and symbols.
            </p>

            <button
                wire:click="changePassword"
                class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors"
            >
                Set password and continue
            </button>
        </div>
    </div>
</div>
