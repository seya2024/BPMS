<?php

namespace App\Filament\Support;

use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Central place for user-facing notification wording.
 *
 * Notifications were previously written inline at every call site, which made
 * them drift: five different "Nothing to save" bodies, hand-rolled pluralisation
 * producing "3 branch foreign currency generation(s)", title-only successes with
 * no detail, and no error notification at all on the batch pages. Funnelling the
 * wording through here keeps it consistent and gives one place to review tone.
 */
final class Notify
{
    /**
     * A batch of rows was persisted.
     *
     * @param  string  $entity  e.g. "deposit performance" - used in the title
     * @param  int  $count  number of branch rows written
     * @param  int  $zeroCount  rows written as an explicit zero
     * @param  int  $updatedCount  rows that already existed and were overwritten
     */
    public static function batchSaved(
        string $entity,
        int $count,
        string $businessDay,
        int $zeroCount = 0,
        int $updatedCount = 0,
    ): void {
        if ($count === 0) {
            self::nothingToSave($entity);

            return;
        }

        $day = Carbon::parse($businessDay)->format('j M Y');

        $body = sprintf(
            '%s %s saved for %s.',
            number_format($count),
            self::plural($count, 'branch', 'branches'),
            $day
        );

        // Only mention the extras when they are meaningful, so the common case
        // stays a single clean sentence.
        if ($updatedCount > 0) {
            $body .= sprintf(' %s already existed and %s updated.', number_format($updatedCount), $updatedCount === 1 ? 'was' : 'were');
        }

        if ($zeroCount > 0) {
            $body .= sprintf(
                ' %s recorded as zero performance.',
                number_format($zeroCount)
            );
        }

        self::send('Saved ' . $entity, $body, 'success');
    }

    /**
     * The user submitted a batch with nothing in it.
     */
    public static function nothingToSave(string $entity): void
    {
        self::send(
            'Nothing to save',
            'Choose a district office that has branches, then enter the ' . $entity . '.',
            'warning'
        );
    }

    /**
     * A save failed. The transaction has already rolled back, so the user is told
     * plainly that nothing was written and given a readable reason, rather than
     * being shown a stack trace.
     *
     * The exception is logged with its class so it can be traced; the user only
     * ever sees a safe summary.
     */
    public static function saveFailed(string $action, Throwable $e): void
    {
        Log::error('Batch save failed', [
            'action' => $action,
            'exception' => $e::class,
            'message' => $e->getMessage(),
            'file' => $e->getFile() . ':' . $e->getLine(),
        ]);

        self::send(
            'Could not save ' . $action,
            'Nothing was saved and no data was changed. Please try again, or contact support if this keeps happening.',
            'danger'
        );
    }

    /**
     * A single-record action completed.
     *
     * @param  string  $title  Past-tense summary, e.g. "User activated"
     * @param  string  $body  What changed, and any consequence worth knowing
     */
    public static function done(string $title, string $body, string $severity = 'success'): void
    {
        self::send($title, $body, $severity);
    }

    /**
     * A request was declined by a deliberate business rule, not a failure.
     *
     * Uses warning(), not danger(): rejecting a request that was correctly
     * refused is a normal outcome, and painting it red implies the system broke.
     */
    public static function declined(string $title, string $body): void
    {
        self::send($title, $body, 'warning');
    }

    /**
     * Something the user must fix before they can continue.
     */
    public static function invalid(string $body): void
    {
        self::send('Check the form', $body, 'danger');
    }

    private static function send(string $title, string $body, string $severity): void
    {
        $notification = Notification::make()
            ->title($title)
            ->body($body);

        match ($severity) {
            'danger' => $notification->danger(),
            'warning' => $notification->warning(),
            'info' => $notification->info(),
            default => $notification->success(),
        };

        $notification->send();
    }

    /**
     * English pluralisation for the countable nouns in these messages.
     */
    public static function plural(int $count, string $singular, string $plural): string
    {
        return $count === 1 ? $singular : $plural;
    }
}

