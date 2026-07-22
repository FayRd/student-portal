<div
    x-data="{
        toasts: [],
        add(message, type = 'info', duration = 4000) {
            const id = Date.now();
            this.toasts.push({ id, message, type, duration, visible: true });
            setTimeout(() => this.remove(id), duration);
        },
        remove(id) {
            const t = this.toasts.find(t => t.id === id);
            if (t) t.visible = false;
            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }, 300);
        }
    }"
    @toast.window="add($event.detail.message, $event.detail.type ?? 'info', $event.detail.duration ?? 4000)"
    @download-error.window="add('Oops, something went wrong! Try again later, maybe?', 'error', 4000)"
    class="fixed bottom-4 right-4 z-[100] flex flex-col gap-2 w-80"
    aria-live="polite"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast.visible"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2"
            class="flex items-start gap-3 px-4 py-3 rounded-lg shadow-lg text-sm border"
            :class="{
                'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-100': toast.type === 'info',
                'bg-green-50 dark:bg-green-900/30 border-green-200 dark:border-green-800 text-green-800 dark:text-green-200': toast.type === 'success',
                'bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-800 text-red-800 dark:text-red-200': toast.type === 'error',
            }"
        >
            <span class="flex-1 text-xs leading-relaxed" x-text="toast.message"></span>
            <button
                @click="remove(toast.id)"
                class="shrink-0 opacity-50 hover:opacity-100 transition-opacity text-xs leading-none mt-0.5"
                :aria-label="'Dismiss notification'"
            >✕</button>
        </div>
    </template>
</div>
