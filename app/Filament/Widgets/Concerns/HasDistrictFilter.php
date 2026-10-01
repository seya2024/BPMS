<?php

namespace App\Filament\Widgets\Concerns;

use App\Models\Branch;
use App\Models\District;
use Illuminate\Support\Facades\Cache;

/**
 * Adds a district selector to the top-right of every analytics widget.
 *
 * The select is rendered by the shared Blade views in
 * resources/views/filament/widgets/analytics-*.blade.php and is bound to the
 * built-in public `filter` property, which Filament chart widgets already support.
 *
 * Selection is per-widget state, so filtering one widget leaves the others alone.
 * Null / '' means "All districts".
 */
trait HasDistrictFilter
{
    /**
     * View shared by chart widgets using this trait.
     *
     * @var string
     */
    protected string $analyticsView = 'filament-widgets.analytics-chart';

    /**
     * Filament's ChartWidget already declares `public ?string $filter = null`,
     * so this trait must NOT redeclare it: PHP treats the differing definitions as
     * an incompatible property conflict. StatsOverviewWidget has no such property,
     * so those widgets declare `public ?string $filter = '';` themselves.
     */

    /**
     * Public accessor for the chart payload.
     *
     * Filament's ChartWidget::getCachedData() is protected, which makes the
     * rendered series impossible to assert in tests. This exposes it read-only.
     *
     * @return array<string, mixed>
     */
    public function analyticsChartData(): array
    {
        return $this->getCachedData();
    }

    /**
     * Public accessor for the stats payload (StatsOverviewWidget only).
     *
     * @return array<int, \Filament\Widgets\StatsOverviewWidget\Stat>
     */
    public function analyticsStats(): array
    {
        return $this->getCachedStats();
    }

    /**
     * Rendered as the inline select options.
     *
     * @return array<string, string>
     */
    public function districtFilterOptions(): array
    {
        return ['' => 'All districts'] + static::districtOptions();
    }

    /**
     * @return array<int|string, string>
     */
    protected static function districtOptions(): array
    {
        return Cache::remember('analytics_district_options', 3600, fn () => District::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all());
    }

    /**
     * Currently selected district id, or null for "All districts".
     */
    protected function selectedDistrictId(): ?int
    {
        return filled($this->filter) ? (int) $this->filter : null;
    }

    /**
     * Branch ids in the selected district, or null when "All districts" is active.
     *
     * @return array<int, int>|null
     */
    protected function branchIdsForFilter(): ?array
    {
        $districtId = $this->selectedDistrictId();

        if ($districtId === null) {
            return null;
        }

        return Branch::query()
            ->where('district_id', $districtId)
            ->pluck('id')
            ->all();
    }

    /**
     * Apply the district scope to an Eloquent query.
     *
     * @template TQuery of \Illuminate\Database\Eloquent\Builder
     *
     * @param  TQuery  $query
     * @return TQuery
     */
    protected function scopeToDistrict($query, string $column = 'branch_id')
    {
        $branchIds = $this->branchIdsForFilter();

        return $branchIds === null
            ? $query
            : $query->whereIn($column, $branchIds);
    }

    /**
     * Apply the district scope to a Query Builder built from a raw table join.
     *
     * @template TQuery of \Illuminate\Database\Query\Builder
     *
     * @param  TQuery  $query
     * @return TQuery
     */
    protected function scopeRawToDistrict($query, string $column = 'd.branch_id')
    {
        $branchIds = $this->branchIdsForFilter();

        return $branchIds === null
            ? $query
            : $query->whereIn($column, $branchIds);
    }

    /**
     * Drop cached data whenever the district selection changes.
     */
    public function updatedFilter(): void
    {
        if (property_exists($this, 'cachedData')) {
            $this->cachedData = null;
        }

        if (property_exists($this, 'cachedStats')) {
            $this->cachedStats = null;
        }
    }
}
