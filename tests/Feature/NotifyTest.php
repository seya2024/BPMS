<?php

namespace Tests\Feature;

use App\Filament\Pages\BatchDailySuperAppSubscriptions;
use App\Filament\Support\Notify;
use App\Models\DailySuperAppSubscription;
use App\Models\District;
use App\Models\User;
use Database\Seeders\KpiTrackingHistorySeeder;
use Database\Seeders\TestDataSeeder;
use Filament\Notifications\Livewire\Notifications as NotificationsComponent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Livewire;
use RuntimeException;
use Tests\TestCase;

/**
 * Covers the notification work: wording helpers, and - most importantly - that a
 * failed batch save produces a danger notification, leaves the database
 * untouched, and is logged, rather than surfacing as a raw 500.
 */
class NotifyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Model::withoutEvents(function (): void {
            $this->seed(TestDataSeeder::class);
            $this->seed(KpiTrackingHistorySeeder::class);
        });

        $this->actingAs(User::where('email', 'seidm2031@gmail.com')->firstOrFail());
    }

    /**
     * Reads the notifications that were actually sent during the request.
     *
     * Filament's assertNotified() only matches on title, so title/body/status
     * assertions go through the same component it inspects.
     *
     * @return array<int, array{title: string, body: string, status: string}>
     */
    protected function sentNotifications(): array
    {
        $component = new NotificationsComponent();
        $component->mount();

        $out = $component->notifications
            ->map(function ($n) {
                // title/body/status are protected on Notification, so read them
                // through the public array form.
                $a = $n->toArray();

                return [
                    'title' => (string) ($a['title'] ?? ''),
                    'body' => (string) ($a['body'] ?? ''),
                    'status' => (string) ($a['status'] ?? ''),
                ];
            })
            ->values()
            ->all();

        if (getenv('NOTIFY_DEBUG')) {
            fwrite(STDERR, "\n  [sent] " . json_encode($out) . "\n");
        }

        return $out;
    }

    protected function findNotification(string $needle): ?array
    {
        foreach ($this->sentNotifications() as $n) {
            if (str_contains($n['title'], $needle)) {
                return $n;
            }
        }

        return null;
    }

    public function test_plural_helper(): void
    {
        $this->assertSame('branch', Notify::plural(1, 'branch', 'branches'));
        $this->assertSame('branches', Notify::plural(0, 'branch', 'branches'));
        $this->assertSame('branches', Notify::plural(2, 'branch', 'branches'));
    }

    public function test_nothing_to_save_is_a_warning_with_guidance(): void
    {
        // The "no entries" branch sits behind form validation (district_id is
        // required), so the helper is exercised directly rather than through the
        // page. The page's use of it is covered by the save tests below.
        Notify::nothingToSave('deposit performance');

        $n = $this->findNotification('Nothing to save');

        $this->assertNotNull($n, 'Expected a "Nothing to save" notification.');
        $this->assertSame('warning', $n['status']);
        $this->assertStringContainsString('district office', $n['body']);
        $this->assertStringContainsString('deposit performance', $n['body']);
    }

    public function test_successful_save_reports_count_date_and_severity(): void
    {
        $district = District::firstOrFail();
        $day = now()->subDays(3)->toDateString();

        $expected = DB::table('branches')->where('district_id', $district->id)->count();

        Livewire::test(BatchDailySuperAppSubscriptions::class)
            ->set('data.district_id', $district->id)
            ->set('data.business_day', $day)
            ->call('save');

        $n = $this->findNotification('Saved super app subscriptions');

        $this->assertNotNull($n, 'Expected a success notification.');
        $this->assertSame('success', $n['status']);
        $this->assertStringContainsString("{$expected} branches saved for", $n['body']);
        $this->assertStringContainsString(now()->subDays(3)->format('j M Y'), $n['body']);
    }

    /**
     * The reason this change exists: a failure part-way through the transaction
     * must notify, roll back, and log - not return a 500.
     *
     * The failure is injected with a model event so it is driver independent.
     */
    public function test_failed_save_notifies_rolls_back_and_logs(): void
    {
        $district = District::firstOrFail();
        $day = now()->subDays(4)->toDateString();

        $branchIds = DB::table('branches')
            ->where('district_id', $district->id)
            ->pluck('id');

        $countFor = fn (): int => DailySuperAppSubscription::whereIn('branch_id', $branchIds)
            ->whereDate('business_day', $day)
            ->count();

        $before = $countFor();

        $seen = 0;

        // `saving` rather than `creating`: the seeded day already has rows, so the
        // upsert takes the UPDATE path and a `creating` listener would never fire.
        DailySuperAppSubscription::saving(function () use (&$seen): void {
            // Let the first couple of rows through, then fail, so the rollback
            // has genuinely partial work to undo.
            if (++$seen > 2) {
                throw new RuntimeException('injected failure mid-batch');
            }
        });

        Log::shouldReceive('error')
            ->once()
            ->withArgs(fn ($message, $context) => $message === 'Batch save failed'
                && ($context['action'] ?? null) === 'super app subscriptions'
                && str_contains((string) ($context['exception'] ?? ''), 'RuntimeException'));

        Livewire::test(BatchDailySuperAppSubscriptions::class)
            ->set('data.district_id', $district->id)
            ->set('data.business_day', $day)
            ->call('save');

        $n = $this->findNotification('Could not save');

        $this->assertNotNull($n, 'Expected a failure notification instead of a 500.');
        $this->assertSame('danger', $n['status']);
        $this->assertStringContainsString('Nothing was saved', $n['body']);
        $this->assertStringNotContainsString('injected failure', $n['body'], 'Internal exception text must not leak to the user.');

        $this->assertSame(
            $before,
            $countFor(),
            'A failed batch save must roll back completely - no partial rows.'
        );
    }

    public function test_re_saving_an_existing_day_is_reported_as_an_update(): void
    {
        $district = District::firstOrFail();
        $day = now()->subDays(5)->toDateString();

        $component = Livewire::test(BatchDailySuperAppSubscriptions::class)
            ->set('data.district_id', $district->id)
            ->set('data.business_day', $day)
            ->call('save');

        // Second save of the same day: every row already exists.
        $component->set('data.district_id', $district->id)
            ->set('data.business_day', $day)
            ->call('save');

        $n = $this->findNotification('Saved super app subscriptions');

        $this->assertNotNull($n);
        $this->assertStringContainsString('already existed', $n['body']);
    }
}
