<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>
    @if (session('download_error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.dispatchEvent(new CustomEvent('download-error'));
            });
        </script>
    @endif
    <livewire:confirmation-modal />
    <x-toast />
</x-layouts::app.sidebar>
