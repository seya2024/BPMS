@php
    $s = $this->getSummary();

    $tile = function (string $icon, string $color, string $title, string $line1, string $line2) {
        $tones = [
            'primary' => 'bg-primary-100 text-primary-600 dark:bg-primary-400/20 dark:text-primary-400',
            'info' => 'bg-info-100 text-info-600 dark:bg-info-400/20 dark:text-info-400',
            'success' => 'bg-success-100 text-success-600 dark:bg-success-400/20 dark:text-success-400',
            'warning' => 'bg-warning-100 text-warning-600 dark:bg-warning-400/20 dark:text-warning-400',
        ];

        return <<<HTML
        <div class="flex items-start gap-3">
            <div class="flex size-8 shrink-0 items-center justify-center rounded-lg {$tones[$color]}">
                <x-filament::icon icon="{$icon}" class="size-5" />
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-950 dark:text-white">{$title}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400">{$line1}</p>
                <p class="text-xs text-gray-500">{$line2}</p>
            </div>
        </div>
        HTML;
    };
@endphp

<x-filament-widgets::widget class="fi-wi-dashboard-summary">
    <x-filament::section :collapsible="false" class="fi-dashboard-summary">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

            {!! $tile(
                'heroicon-o-calendar-days',
                'primary',
                'Reporting Window',
                $s['windowStart']->format('M d').' &ndash; '.$s['windowEnd']->format('M d, Y'),
                $s['reportedDays'].' of '.$s['windowDays'].' days reported',
            ) !!}

            {!! $tile(
                'heroicon-o-building-library',
                'info',
                $s['fy'] ?? 'No active FY',
                $s['quarter'] ?? 'No active quarter',
                'Latest business day '.($s['totals']['days'] > 0 ? $s['windowEnd']->format('M d, Y') : 'n/a'),
            ) !!}

            {!! $tile(
                'heroicon-o-banknotes',
                'success',
                'ETB '.number_format($s['totals']['total'], 2),
                'Deposits in window',
                'Net '.($s['totals']['net'] >= 0 ? '+' : '-').' ETB '.number_format(abs($s['totals']['net']), 2),
            ) !!}

            {!! $tile(
                'heroicon-o-users',
                'warning',
                number_format((float) $s['accounts']['total'], 0).' accounts',
                number_format((float) $s['accounts']['active_rate'], 1).'% active',
                number_format((float) $s['accounts']['dormant'], 0).' dormant',
            ) !!}

        </div>

        {{-- Copyright notice --}}
        <div class="mt-4 border-t border-gray-200 dark:border-white/10 pt-3 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                &copy; {{ now()->year }} {{ config('app.name', 'BPMS') }}. All rights reserved.
            </p>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
