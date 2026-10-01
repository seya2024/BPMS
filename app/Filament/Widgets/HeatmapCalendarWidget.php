<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDateRangeFilter;
use App\Filament\Widgets\Concerns\HasDistrictFilter;
use App\Models\DailyAccountOpening;
use App\Models\DailyAccountPerformance;
use App\Models\DailyDepositPerformance;
use App\Models\DailyForeignCurrencyGeneration;
use App\Models\DailySuperAppSubscription;
use Filament\Widgets\Widget;
use Illuminate\Support\Number;

class HeatmapCalendarWidget extends Widget
{
    use HasDistrictFilter, HasDateRangeFilter;

    protected ?string $heading = 'Performance Heatmap';

    protected static ?int $sort = 13;

    protected int | string | array $columnSpan = 'full';

    protected ?string $maxHeight = '400px';

    protected string $view = 'widgets.heatmap-calendar';

    /**
     * Declared here because this widget extends the plain Widget, not ChartWidget.
     *
     * HasDistrictFilter reads and writes $filter. ChartWidget declares the
     * property itself, but a bare Widget does not, so without this the filter
     * threw "Property [$filter] not found on component" on every render.
     */
    public ?string $filter = '';

    public ?string $selectedKpi = 'deposits';

    protected function getKpiOptions(): array
    {
        return [
            'deposits' => 'Deposits',
            'account_openings' => 'Account Openings',
            'account_performance' => 'Account Performance (New)',
            'fx_generation' => 'FX Generation',
            'super_app' => 'Super App Subscriptions',
        ];
    }

    protected function getHeatmapData(): array
    {
        $branchIds = $this->branchIdsForFilter();
        $range = $this->getEffectiveDateRange();
        $from = \Carbon\Carbon::parse($range['from']);
        $to = \Carbon\Carbon::parse($range['to']);

        return match ($this->selectedKpi) {
            'deposits' => AnalyticsService::heatmapData(
                DailyDepositPerformance::class,
                'total_deposit_amount',
                $from,
                $to,
                $branchIds
            ),
            'account_openings' => AnalyticsService::heatmapData(
                DailyAccountOpening::class,
                'conventional_accounts + ifb_accounts',
                $from,
                $to,
                $branchIds
            ),
            'account_performance' => AnalyticsService::heatmapData(
                DailyAccountPerformance::class,
                'new_accounts',
                $from,
                $to,
                $branchIds
            ),
            'fx_generation' => AnalyticsService::heatmapData(
                DailyForeignCurrencyGeneration::class,
                'amount',
                $from,
                $to,
                $branchIds
            ),
            'super_app' => AnalyticsService::heatmapData(
                DailySuperAppSubscription::class,
                'subscriptions',
                $from,
                $to,
                $branchIds
            ),
            default => [],
        };
    }

    public function getKpiLabel(): string
    {
        return $this->getKpiOptions()[$this->selectedKpi] ?? 'Deposits';
    }

    public function getHeatmapCells(): array
    {
        $data = $this->getHeatmapData();
        $range = $this->getEffectiveDateRange();
        $from = \Carbon\Carbon::parse($range['from']);
        $to = \Carbon\Carbon::parse($range['to']);

        // Build a complete calendar grid
        $cells = [];
        $current = $from->copy()->startOfWeek();

        // Add empty cells for days before the start date
        while ($current->lt($from)) {
            $cells[] = [
                'date' => $current->toDateString(),
                'value' => null,
                'intensity' => 0,
                'inRange' => false,
                'label' => $current->format('M d'),
                'dayOfWeek' => $current->dayOfWeek,
            ];
            $current->addDay();
        }

        // Add actual data cells
        $dataByDate = collect($data)->keyBy('date');
        while ($current->lte($to)) {
            $dateKey = $current->toDateString();
            $datum = $dataByDate->get($dateKey);

            $cells[] = [
                'date' => $dateKey,
                'value' => $datum['value'] ?? 0,
                'intensity' => $datum['intensity'] ?? 0,
                'inRange' => true,
                'label' => $current->format('M d'),
                'dayOfWeek' => $current->dayOfWeek,
                'isToday' => $current->isToday(),
                'isWeekend' => $current->isWeekend(),
            ];
            $current->addDay();
        }

        // Complete the last week
        while ($current->dayOfWeek !== 0) { // 0 = Sunday
            $cells[] = [
                'date' => $current->toDateString(),
                'value' => null,
                'intensity' => 0,
                'inRange' => false,
                'label' => $current->format('M d'),
                'dayOfWeek' => $current->dayOfWeek,
            ];
            $current->addDay();
        }

        return $cells;
    }

    public function getWeekRows(): array
    {
        $cells = $this->getHeatmapCells();
        $rows = [];
        $currentWeek = [];

        foreach ($cells as $cell) {
            $currentWeek[] = $cell;
            if ($cell['dayOfWeek'] === 6) { // Saturday = end of week
                $rows[] = $currentWeek;
                $currentWeek = [];
            }
        }

        if (!empty($currentWeek)) {
            $rows[] = $currentWeek;
        }

        return $rows;
    }

    public function getColorScale(): array
    {
        return [
            0 => '#f3f4f6',    // gray-100
            20 => '#bfdbfe',   // blue-200
            40 => '#60a5fa',   // blue-400
            60 => '#3b82f6',   // blue-500
            80 => '#2563eb',   // blue-600
            100 => '#1e40af',  // blue-800
        ];
    }

    /**
     * Heading accessor for the custom view.
     *
     * Only ChartWidget and StatsOverviewWidget provide getHeading(); the plain
     * Widget base class does not, so the view's $this->getHeading() call failed
     * with a BadMethodCallException.
     */
    public function getHeading(): ?string
    {
        return $this->heading;
    }

    /**
     * Formats a heatmap figure for display.
     *
     * The selected KPI decides the unit: subscription counts are whole numbers,
     * currency and deposit totals are money.
     */
    public function formatValue(float|int|null $value): string
    {
        $value = (float) ($value ?? 0);

        if ($this->selectedKpi === 'super_app') {
            return Number::format((int) round($value), precision: 0);
        }

        if ($this->selectedKpi === 'account_openings' || $this->selectedKpi === 'account_performance') {
            return Number::format((int) round($value), precision: 0);
        }

        return Number::format($value, precision: 2);
    }

    /**
     * Picks the heatmap background for an intensity of 0-100, snapping to the
     * nearest stop in the colour scale.
     */
    public function getColorForIntensity(float|int|null $intensity): string
    {
        $intensity = max(0.0, min(100.0, (float) ($intensity ?? 0)));
        $scale = $this->getColorScale();

        $best = $scale[array_key_first($scale)];

        foreach ($scale as $threshold => $colour) {
            if ($intensity >= (float) $threshold) {
                $best = $colour;
            }
        }

        return $best;
    }

    public function getWeekLabels(): array
    {
        return ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    }
}