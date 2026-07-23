@if ($show)
    <div class="fixed inset-0 z-[200] flex items-center justify-center bg-black/50 dark:bg-black/70">
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm mx-4 border border-gray-200 dark:border-gray-700"
            x-data="{
                seconds: {{ $countdown }},
                progress: 0,
                ready: false,
                interval: null,
                start() {
                    this.interval = setInterval(() => {
                        if (this.seconds <= 0) {
                            clearInterval(this.interval);
                            this.ready = true;
                            return;
                        }
                        this.seconds -= 0.05;
                        this.progress = (({{ $countdown }} - this.seconds) / {{ $countdown }}) * 100;
                    }, 50);
                }
            }"
            x-init="start()"
        >
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $title }}</h3>
            </div>

            <div class="px-5 py-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $message }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">This action cannot be undone.</p>
            </div>

            {{-- Progress bar --}}
            <div class="px-5 pb-2">
                <div class="h-1 w-full bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div
                        class="h-full bg-red-400 dark:bg-red-600 rounded-full transition-none"
                        :style="`width: ${progress}%`"
                    ></div>
                </div>
                <p class="text-xs text-gray-400 mt-1" x-show="!ready">
                    Please wait <span x-text="Math.ceil(seconds)"></span>s…
                </p>
            </div>

            <div class="flex justify-end gap-2 px-5 py-4 border-t border-gray-200 dark:border-gray-700">
                <button
                    wire:click="cancel"
                    class="px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 transition-colors"
                >
                    Cancel
                </button>
                <button
                    wire:click="confirm"
                    :disabled="!ready"
                    :class="ready
                        ? 'bg-red-600 hover:bg-red-700 text-white cursor-pointer'
                        : 'bg-red-200 dark:bg-red-900/30 text-red-300 dark:text-red-600 cursor-not-allowed'"
                    class="px-3 py-1.5 text-xs rounded-lg transition-colors"
                >
                    {{ $dangerLabel }}
                </button>
            </div>
        </div>
    </div>
@endif
