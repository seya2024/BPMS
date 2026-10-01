@php
    $weekRows = $this->getWeekRows();
    $weekLabels = $this->getWeekLabels();
    $colorScale = $this->getColorScale();
    $kpiLabel = $this->getKpiLabel();
    $periodLabel = $this->getPeriodLabel();
@endphp

<x-filament-widgets::widget class="fi-wi-heatmap-calendar">
    <x-filament::section
        :heading="$this->getHeading()"
        :description="$periodLabel"
        class="fi-wi-heatmap-calendar-ctn"
    >
        <x-slot name="afterHeader">
            <x-filament::input.wrapper
                wire:target="selectedKpi"
                class="fi-wi-chart-filter"
            >
                <x-filament::input.select
                    wire:model.live="selectedKpi"
                    inline-prefix
                >
                    @foreach ($this->getKpiOptions() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>

            {{-- District filter --}}
            <x-filament::input.wrapper
                wire:target="filter"
                class="fi-wi-chart-filter"
            >
                <x-filament::input.select
                    inline-prefix
                    wire:model.live="filter"
                >
                    @foreach ($this->districtFilterOptions() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>

            {{-- Date range filter --}}
            @foreach ($this->getDateRangeFilterSchema() as $component)
                {{ $component->render() }}
            @endforeach
        </x-slot>

        <div class="fi-wi-heatmap-calendar-body">
            {{-- Legend --}}
            <div class="fi-wi-heatmap-legend mb-4 flex items-center gap-4 flex-wrap">
                <span class="text-sm font-medium">Intensity:</span>
                @php
                    $legendStops = [0, 20, 40, 60, 80, 100];
                @endphp
                @foreach ($legendStops as $stop)
                    <div class="fi-wi-heatmap-legend-item flex items-center gap-1">
                        <div
                            class="w-6 h-4 rounded"
                            style="background-color: {{ $colorScale[$stop] }};"
                        ></div>
                        <span class="text-xs text-gray-600 dark:text-gray-400">{{ $stop }}%</span>
                    </div>
                @endforeach
            </div>

            {{-- Calendar Grid --}}
            <div class="fi-wi-heatmap-grid overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="fi-wi-heatmap-header">
                            @foreach ($weekLabels as $label)
                                <th class="fi-wi-heatmap-day-label text-center py-2 px-1 text-xs font-medium text-gray-500 dark:text-gray-400">
                                    {{ $label }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($weekRows as $weekIndex => $week)
                            <tr class="fi-wi-heatmap-week">
                                @foreach ($week as $dayIndex => $cell)
                                    <td
                                        class="fi-wi-heatmap-cell relative"
                                        style="background-color: {{ $cell['inRange'] ? ($cell['value'] > 0 ? $this->getColorForIntensity($cell['intensity']) : '#f9fafb') : '#f3f4f6' }};"
                                        wire:tooltip="{{ $cell['inRange'] ? ($this->formatValue($cell['value']) . ' on ' . \Carbon\Carbon::parse($cell['date'])->format('l, M d, Y')) : 'Outside selected range' }}"
                                    >
                                        @if ($cell['inRange'])
                                            <div class="fi-wi-heatmap-cell-content p-1">
                                                <span class="text-xs font-medium {{ $cell['isToday'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300' }}">
                                                    {{ \Carbon\Carbon::parse($cell['date'])->day }}
                                                </span>
                                                @if ($cell['value'] > 0)
                                                    <span class="text-[10px] text-gray-500 dark:text-gray-400 block truncate">
                                                        {{ $this->formatValue($cell['value']) }}
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <div class="fi-wi-heatmap-cell-content p-1 text-gray-300 dark:text-gray-600">
                                                <span class="text-xs">{{ \Carbon\Carbon::parse($cell['date'])->day }}</span>
                                            </div>
                                        @endif

                                        @if ($cell['isToday'] && $cell['inRange'])
                                            <div class="absolute inset-0 border-2 border-blue-500 pointer-events-none rounded"></div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Summary Stats.

                 These are rendered as plain markup using Filament's own stat CSS
                 classes rather than <x-filament::stats-overview-widget::stat>.

                 That component cannot be used from a custom widget view: the
                 "filament" view namespace resolves only to
                 filament/support/resources/views, while stat.blade.php lives in
                 filament/widgets, so the tag threw
                 "Unable to locate a class or view for component". Its $getIcon()
                 / $getValue() helpers also only exist inside a schema, so the
                 namespace cannot simply be corrected either. Reusing the class
                 names keeps the cards visually identical to a native Stat. --}}
            <div class="fi-wi-heatmap-summary mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $data = $this->getHeatmapData();
                    $total = collect($data)->sum('value');
                    $avg = count($data) > 0 ? $total / count($data) : 0;
                    $max = collect($data)->max('value') ?? 0;
                    $daysWithData = collect($data)->filter(fn ($d) => $d['value'] > 0)->count();

                    $summaryStats = [
                        ['label' => 'Total ' . $this->getKpiLabel(), 'value' => $this->formatValue($total), 'icon' => 'heroicon-o-chart-bar', 'color' => 'primary'],
                        ['label' => 'Daily Average', 'value' => $this->formatValue($avg), 'icon' => 'heroicon-o-calculator', 'color' => 'info'],
                        ['label' => 'Peak Day', 'value' => $this->formatValue($max), 'icon' => 'heroicon-o-arrow-trending-up', 'color' => 'success'],
                        ['label' => 'Active Days', 'value' => $daysWithData . ' / ' . count($data), 'icon' => 'heroicon-o-calendar-days', 'color' => 'warning'],
                    ];
                @endphp

                @foreach ($summaryStats as $stat)
                    <div class="fi-wi-stats-overview-stat fi-color-{{ $stat['color'] }}">
                        <div class="fi-wi-stats-overview-stat-content">
                            <div class="fi-wi-stats-overview-stat-label-ctn">
                                {{ \Filament\Support\generate_icon_html($stat['icon']) }}

                                <span class="fi-wi-stats-overview-stat-label">
                                    {{ $stat['label'] }}
                                </span>
                            </div>

                            <div class="fi-wi-stats-overview-stat-value">
                                {{ $stat['value'] }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

@push('filament-widgets-styles')
<style>
    .fi-wi-heatmap-calendar {
        font-family: inherit;
    }

    .fi-wi-heatmap-calendar-ctn {
        padding: 1rem;
    }

    .fi-wi-heatmap-grid table {
        font-size: 0.75rem;
    }

    .fi-wi-heatmap-cell {
        width: 40px;
        height: 40px;
        border: 1px solid #e5e7eb;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .fi-wi-heatmap-cell:hover {
        transform: scale(1.15);
        z-index: 10;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border-color: #3b82f6;
    }

    .fi-wi-heatmap-cell-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
    }

    .fi-wi-heatmap-day-label {
        width: 40px;
    }

    .fi-wi-heatmap-legend-item {
        display: flex;
        align-items: center;
    }

    .fi-wi-heatmap-summary {
        margin-top: 1rem;
    }

    @media (max-width: 768px) {
        .fi-wi-heatmap-cell {
            width: 32px;
            height: 32px;
        }
        .fi-wi-heatmap-day-label {
            width: 32px;
        }
    }
</style>
@endpush