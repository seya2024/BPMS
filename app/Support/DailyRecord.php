<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Driver-safe insert-or-update for the branch/day performance tables.
 *
 * Every performance table is keyed on (branch_id, business_day) and every entry
 * path - five batch pages, the row edit action, and the seeders - needs to write
 * one of those rows idempotently.
 *
 * The obvious `updateOrCreate(['branch_id' => .., 'business_day' => ..], ..)` is
 * not safe: `business_day` is a DATE column, but rows written elsewhere hold a
 * "YYYY-MM-DD 00:00:00" value. MySQL compares a DATE against the bare string and
 * matches; SQLite compares the stored text literally, so '2026-09-26' does not
 * equal '2026-09-26 00:00:00', updateOrCreate concludes the row is absent, tries to
 * INSERT, and the unique index throws. The failure is driver dependent and only
 * shows up under test, which makes it easy to ship broken.
 *
 * Matching through whereDate() normalises both sides and behaves identically on
 * every driver.
 */
final class DailyRecord
{
    /**
     * Inserts or updates the row for a branch/day.
     *
     * The returned model carries wasRecentlyCreated, so callers can report
     * "created vs updated" counts for free. Do not pre-check with exists() to
     * work that out: that is a second query per row, which turns a 30-branch
     * batch into 30 extra round trips.
     *
     * @template TModel of Model
     *
     * @param  class-string<TModel>  $modelClass
     * @param  array<string, mixed>  $attributes
     * @return TModel
     */
    public static function upsert(string $modelClass, int $branchId, string $businessDay, array $attributes): Model
    {
        $day = Carbon::parse($businessDay)->toDateString();

        $model = $modelClass::query()
            ->where('branch_id', $branchId)
            ->whereDate('business_day', $day)
            ->first();

        if ($model === null) {
            $model = $modelClass::query()->getModel()->newInstance();
            $model->branch_id = $branchId;
            $model->business_day = $day;
        }

        $model->forceFill($attributes)->save();

        return $model;
    }

    /**
     * Bulk pre-load which branch/day pairs already have a row.
     *
     * Lets a batch page report "N already existed" in a single query instead of
     * one existence check per branch.
     *
     * @param  class-string<Model>  $modelClass
     * @param  array<int, int>  $branchIds
     * @return array<int, true>  branch ids that already had a row
     */
    public static function existingBranchIds(string $modelClass, array $branchIds, string $businessDay): array
    {
        if ($branchIds === []) {
            return [];
        }

        $day = Carbon::parse($businessDay)->toDateString();

        return $modelClass::query()
            ->whereIn('branch_id', $branchIds)
            ->whereDate('business_day', $day)
            ->pluck('branch_id')
            ->mapWithKeys(fn ($id) => [(int) $id => true])
            ->all();
    }

    /**
     * Whether a row already exists for this branch and day.
     *
     * @param  class-string<Model>  $modelClass
     */
    public static function exists(string $modelClass, int $branchId, string $businessDay): bool
    {
        return $modelClass::query()
            ->where('branch_id', $branchId)
            ->whereDate('business_day', Carbon::parse($businessDay)->toDateString())
            ->exists();
    }

    /**
     * Deletes this branch's detail rows for one banking type only, leaving the
     * sibling banking type's rows intact.
     *
     * A Conventional branch reports both banking types, so a replacement must be
     * scoped to the type being edited or it would discard the other leg.
     *
     * @param  class-string<Model>  $modelClass
     */
    public static function replaceDetails(
        string $modelClass,
        int $branchId,
        string $businessDay,
        int $bankingTypeId,
        array $rows,
    ): void {
        $day = Carbon::parse($businessDay)->toDateString();

        $modelClass::query()
            ->where('branch_id', $branchId)
            ->whereDate('business_day', $day)
            ->where('banking_type_id', $bankingTypeId)
            ->delete();

        foreach ($rows as $row) {
            $modelClass::query()->create(array_merge([
                'branch_id' => $branchId,
                'business_day' => $day,
                'banking_type_id' => $bankingTypeId,
            ], $row));
        }
    }

    /**
     * Sums a branch's detail rows for one day across every banking type.
     *
     * @param  class-string<Model>  $modelClass
     */
    public static function detailTotal(string $modelClass, int $branchId, string $businessDay): float
    {
        return (float) $modelClass::query()
            ->where('branch_id', $branchId)
            ->whereDate('business_day', Carbon::parse($businessDay)->toDateString())
            ->sum('amount');
    }

    /**
     * Scope for a branch's rows on one day.
     *
     * @param  Builder<Model>  $query
     * @return Builder<Model>
     */
    public static function scopeBranchDay(Builder $query, int $branchId, string $businessDay): Builder
    {
        return $query
            ->where('branch_id', $branchId)
            ->whereDate('business_day', Carbon::parse($businessDay)->toDateString());
    }
}
