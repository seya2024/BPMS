<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDistrictFilter;
use App\Models\DailyForeignCurrencyGeneration;
use App\Models\DailySuperAppSubscription;
use App\Models\KPI;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

/**
 * KPI scorecard.
 *
 * The schema stores KPIs as definitions only (name, unit, calculation_method) with
 * no measurement table, so each KPI is resolved against live performance data by
 * unit. Anything that cannot be resolved is reported as unconfigured rather than
 * being shown as a misleading zero.
 */
class KpiScorecardWidget extends BaseWidget
{
    use HasDistrictFilter;

    protected ?string $heading = 'KPI Scorecard';

    protected static ?int $sort = 9;

    protected int | string | array $columnSpan = 1;

    /** Compact 2-up layout so the widget fits a third of the dashboard row. */
    protected int | array | null $columns = 1;

    protected string $view = 'widgets.analytics-stats';

    /** StatsOverviewWidget does not declare $filter, so the trait's state lives here. */
    public ?string $filter = '';

    protected function getStats(): array
    {
        // Four KPIs keeps this card compact at quarter width; the full list is on the
        // KPIs resource page.
        $kpis = KPI::query()
            ->with('category')
            ->orderBy('category_id')
            ->orderBy('name')
            ->limit(4)
            ->get();

        if ($kpis->isEmpty()) {
            return [
                Stat::make('KPI Scorecard', 'No KPIs')
                    ->description('Define KPIs to populate this scorecard')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->color('gray'),
            ];
        }

        $branchIds = $this->branchIdsForFilter();
        $stats = [];

        foreach ($kpis as $kpi) {
            $resolved = $this->resolveKpi($kpi, $branchIds);

            $stat = Stat::make($kpi->name, $resolved['value'])
                ->description($resolved['description'])
                ->icon('heroicon-o-clipboard-document-list')
                ->color($resolved['color'] ?? 'gray');

            $stat->extraAttributes([
                'data-kpi-unit' => $this->unitFor($kpi),
                'data-kpi-has-data' => $resolved['has_data'] ? '1' : '0',
            ]);

            if ($kpi->category) {
                // merge: true, otherwise this call replaces the attributes above.
                $stat->extraAttributes(['data-kpi-category' => $kpi->category->name], merge: true);
            }

            $stats[] = $stat;
        }

        return $stats;
    }

    /**
     * Renders a figure with its measurement unit attached, so a number on the
     * scorecard is never read as a bare quantity.
     *
     * Stat has no prefix()/suffix() in Filament v4, so the unit is part of the
     * value string: currency reads "ETB 1.2M", counts read "182,340 accounts".
     */
    protected function withUnit(float|int $value, KPI $kpi, bool $abbreviate = false): string
    {
        $number = $abbreviate
            ? Number::abbreviate((float) $value, precision: 1)
            : Number::format($value, precision: 0);

        $unit = $this->unitFor($kpi);

        return $this->isCurrencyUnit($kpi)
            ? $unit . ' ' . $number
            : $number . ' ' . strtolower($unit);
    }

    /**
     * The measurement unit for a KPI, falling back to a neutral label so the
     * widget never renders a value with no unit at all.
     */
    protected function unitFor(KPI $kpi): string
    {
        $unit = trim((string) $kpi->unit);

        return $unit !== '' ? $unit : 'Units';
    }

    /**
     * True when the KPI is measured in currency, which decides whether the unit
     * is prefixed or suffixed and how the value is formatted.
     */
    protected function isCurrencyUnit(KPI $kpi): bool
    {
        $unit = strtolower($this->unitFor($kpi));

        return $unit === 'etb'
            || $unit === 'currency'
            || $unit === 'amount'
            || str_contains($unit, 'etb')
            || str_contains($unit, 'birr')
            || str_contains($unit, 'usd');
    }

    /**
     * Resolves a KPI definition against live performance data.
     *
     * Routing is driven by the KPI's name and calculation method rather than its
     * id, so the mapping survives reseeding.
     *
     * @param  array<int, int>|null  $branchIds
     * @return array{value: string, description: string, color: string|null, is_currency: bool, has_data: bool}
     */
    protected function resolveKpi(KPI $kpi, ?array $branchIds): array
    {
        $name = strtolower((string) $kpi->name);
        $method = strtolower((string) $kpi->calculation_method);
        $haystack = $name . ' ' . $method;
        $isCurrency = $this->isCurrencyUnit($kpi);

        $empty = fn (string $why): array => [
            'value' => 'No data',
            'description' => $why,
            'color' => 'gray',
            'is_currency' => $isCurrency,
            'has_data' => false,
        ];

        // Super App subscriptions and foreign currency generation, measured from
        // their own daily tables.
        if (str_contains($haystack, 'super app') || str_contains($haystack, 'subscription')) {
            $totals = AnalyticsService::kpiTotals(DailySuperAppSubscription::class, 'subscriptions', branchIds: $branchIds);

            if ($totals['days'] === 0) {
                return $empty('No super app subscriptions recorded');
            }

            return [
                'value' => $this->withUnit((int) $totals['total'], $kpi),
                'description' => 'Total over ' . $totals['days'] . ' business days, '
                    . Number::format((int) $totals['avg'], precision: 0) . ' ' . strtolower($this->unitFor($kpi)) . '/day',
                'color' => 'info',
                'is_currency' => false,
                'has_data' => true,
            ];
        }

        if (str_contains($haystack, 'foreign currency') || str_contains($haystack, 'forex')) {
            $totals = AnalyticsService::kpiTotals(DailyForeignCurrencyGeneration::class, 'amount', branchIds: $branchIds);

            if ($totals['days'] === 0) {
                return $empty('No foreign currency generation recorded');
            }

            return [
                'value' => $this->withUnit((float) $totals['total'], $kpi, abbreviate: true),
                'description' => 'Generated over ' . $totals['days'] . ' business days',
                'color' => 'info',
                'is_currency' => true,
                'has_data' => true,
            ];
        }

        // Deposit KPIs: window actuals against the achievable plan.
        if (str_contains($haystack, 'deposit')) {
            $totals = AnalyticsService::depositTotals(branchIds: $branchIds);

            if ($totals['days'] === 0) {
                return $empty('No deposit performance recorded');
            }

            return [
                'value' => $isCurrency
                    ? $this->withUnit((float) $totals['total'], $kpi, abbreviate: true)
                    : Number::format($totals['days'], precision: 0) . ' days',
                'description' => 'Total deposits over ' . $totals['days'] . ' business days',
                'color' => $totals['net'] >= 0 ? 'success' : 'danger',
                'is_currency' => $isCurrency,
                'has_data' => true,
            ];
        }

        // Account KPIs: the latest balances.
        if (str_contains($haystack, 'account')) {
            $snapshot = AnalyticsService::accountSnapshot($branchIds);

            if (! $snapshot['day']) {
                return $empty('No account performance recorded');
            }

            return [
                'value' => $this->withUnit((float) $snapshot['total'], $kpi),
                'description' => Number::format((float) $snapshot['active_rate'], precision: 1) . '% active',
                'color' => ((float) $snapshot['active_rate']) >= 70 ? 'success' : 'warning',
                'is_currency' => false,
                'has_data' => true,
            ];
        }

        return [
            'value' => 'Not configured',
            'description' => 'KPI has no measurement source yet',
            'color' => 'gray',
            'is_currency' => $isCurrency,
            'has_data' => false,
        ];
    }
}
