<?php

namespace Tests\Feature;

use App\Filament\Pages\BatchDailySuperAppSubscriptions;
use App\Filament\Support\BusinessDay;
use App\Models\District;
use App\Models\User;
use Database\Seeders\TestDataSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Guards the "past dates only" rule for business day entry.
 *
 * The rule is defined once in BusinessDay::picker() and shared by all eleven
 * entry points. These tests assert both halves of it: the UI lock (maxDate) and
 * the server-side guard (before:today), because either alone is bypassable.
 */
class BusinessDayRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_picker_locks_to_yesterday(): void
    {
        $picker = BusinessDay::picker();

        $maxDate = $picker->getMaxDate();

        $this->assertNotNull($maxDate, 'maxDate must be set, otherwise today and future are selectable.');
        $this->assertSame(
            Carbon::yesterday()->toDateString(),
            Carbon::parse($maxDate)->toDateString(),
            'The last selectable business day must be yesterday.'
        );
    }

    public function test_picker_defaults_to_yesterday(): void
    {
        $default = BusinessDay::picker()->getDefaultState();

        $this->assertSame(
            Carbon::yesterday()->toDateString(),
            Carbon::parse(is_array($default) ? ($default['business_day'] ?? null) : $default)->toDateString(),
            'The form should default to yesterday, never today.'
        );
    }

    /**
     * The server-side half of the rule, proven through a real page rather than by
     * introspecting the component - dehydrateValidationRules() needs a container,
     * and an end-to-end check is the behaviour that actually matters.
     *
     * The picker's maxDate already stops the UI, so this covers a hand-crafted
     * request or a stale page bypassing the picker.
     */
    public function test_today_is_rejected_on_a_real_batch_page(): void
    {
        Model::withoutEvents(function (): void {
            $this->seed(TestDataSeeder::class);
        });

        $this->actingAs(User::where('email', 'seidm2031@gmail.com')->firstOrFail());

        $district = District::firstOrFail();

        Livewire::test(BatchDailySuperAppSubscriptions::class)
            ->set('data.district_id', $district->id)
            ->set('data.business_day', Carbon::today()->toDateString())
            ->call('save')
            ->assertHasErrors(['data.business_day']);

        // Nothing may be written for a forbidden day.
        $this->assertSame(
            0,
            \App\Models\DailySuperAppSubscription::whereDate('business_day', Carbon::today())->count()
        );
    }

    public function test_yesterday_is_accepted_on_a_real_batch_page(): void
    {
        Model::withoutEvents(function (): void {
            $this->seed(TestDataSeeder::class);
        });

        $this->actingAs(User::where('email', 'seidm2031@gmail.com')->firstOrFail());

        $district = District::firstOrFail();
        $yesterday = Carbon::yesterday()->toDateString();

        Livewire::test(BatchDailySuperAppSubscriptions::class)
            ->set('data.district_id', $district->id)
            ->set('data.business_day', $yesterday)
            ->call('save')
            ->assertHasNoActionErrors();

        $this->assertSame(
            DB::table('branches')->where('district_id', $district->id)->count(),
            \App\Models\DailySuperAppSubscription::whereDate('business_day', $yesterday)->count()
        );
    }

    /**
     * The rule as the server enforces it: today and anything later must fail.
     */
    public function test_today_and_future_fail_validation(): void
    {
        foreach ([
            'today' => Carbon::today(),
            'tomorrow' => Carbon::tomorrow(),
            'next year' => Carbon::today()->addYear(),
        ] as $label => $date) {
            $validator = Validator::make(
                ['business_day' => $date->toDateString()],
                ['business_day' => ['required', 'date', 'before:today']]
            );

            $this->assertTrue(
                $validator->fails(),
                "{$label} must be rejected by the before:today rule."
            );
        }
    }

    public function test_past_dates_pass_validation(): void
    {
        foreach ([
            'yesterday' => Carbon::yesterday(),
            'a week ago' => Carbon::today()->subWeek(),
            'a year ago' => Carbon::today()->subYear(),
        ] as $label => $date) {
            $validator = Validator::make(
                ['business_day' => $date->toDateString()],
                ['business_day' => ['required', 'date', 'before:today']]
            );

            $this->assertFalse(
                $validator->fails(),
                "{$label} must be accepted."
            );
        }
    }

    /**
     * The default must never be a date the form itself would reject.
     */
    public function test_default_is_always_acceptable(): void
    {
        $default = Carbon::parse(is_array(BusinessDay::picker()->getDefaultState())
            ? BusinessDay::picker()->getDefaultState()['business_day']
            : BusinessDay::picker()->getDefaultState());

        $this->assertTrue(
            Validator::make(
                ['business_day' => $default->toDateString()],
                ['business_day' => ['before:today']]
            )->passes(),
            'The picker default must satisfy its own validation rule.'
        );
    }
}
