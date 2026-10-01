@php
    $year = now()->year;
@endphp

<div class="fi-copyright px-1 py-2 text-center">
    <p class="text-xs text-gray-500 dark:text-gray-400">
        &copy; {{ $year }} {{ config('app.name', 'BPMS') }}.
        All rights reserved.
    </p>
</div>
