<?php

namespace App\Filament\Support;

use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Carbon;

/**
 * The single definition of the "business day" date field.
 *
 * Every entry path - five batch pages, five single-entry resource forms and the
 * row edit modal - must use this so the rule cannot drift between them. It was
 * previously declared inline in eleven places, where the constraints had already
 * diverged: the batch pages validated `before:today` but still let you click
 * today, and the single-entry forms had no date validation at all, with one of
 * them even defaulting to today.
 *
 * THE RULE: performance may only be recorded for a day that has already
 * happened. Today and future dates are both rejected.
 *
 *  - maxDate() stops today and future being selectable or typed in the UI;
 *  - before:today is the server-side guard, so a hand-crafted request or a
 *    stale page cannot bypass the picker.
 *
 * The maxDate is what the user asked for ("disable"); the rule is what makes it
 * safe. Keep both.
 */
final class BusinessDay
{
    public const MESSAGE = 'Business day must be yesterday or earlier. Today and future dates cannot be entered.';

    /**
     * A business-day picker locked to the past.
     *
     * Chain further configuration onto the result, e.g. ->live() and
     * ->afterStateUpdated(...) on a batch page.
     */
    public static function picker(string $name = 'business_day', string $label = 'Business Day'): DatePicker
    {
        return DatePicker::make($name)
            ->label($label)
            ->native(false)
            ->default(Carbon::yesterday())
            // Today and every future date are unselectable.
            //
            // The bound is the END of yesterday, not its start. Filament turns
            // maxDate into a `before_or_equal` rule, and a stored value can carry
            // a time component ("2026-09-30 00:00:00"), which is strictly later
            // than a midnight bound and so got rejected by the very rule meant to
            // allow it. The calendar still only offers dates up to yesterday, and
            // the hard guard below is `before:today`.
            ->maxDate(Carbon::yesterday()->endOfDay()->toDateTimeString())
            ->minDate(Carbon::yesterday()->subYears(10)->startOfDay()->toDateTimeString())
            ->required()
            ->rules([
                'required',
                'date',
                'before:today',
            ])
            ->validationMessages([
                'required' => 'Please choose a business day.',
                'before' => self::MESSAGE,
            ])
            ->placeholder('Select date...');
    }
}
