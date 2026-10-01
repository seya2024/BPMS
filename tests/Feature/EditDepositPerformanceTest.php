<?php

namespace Tests\Feature;

use App\Filament\Resources\DailyDepositPerformances\Pages\ListDailyDepositPerformances;
use App\Filament\Resources\DailyDepositPerformances\Tables\Actions\EditDepositPerformanceAction;
use App\Models\BankingType;
use App\Models\Branch;
use App\Models\BusinessSegment;
use App\Models\DailyDepositPerformance;
use App\Models\DailyDepositPerformanceDetail;
use App\Models\District;
use App\Models\User;
use Database\Seeders\BranchPlanSeeder;
use Database\Seeders\PerformanceHistorySeeder;
use Database\Seeders\TestDataSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Covers the row-level edit modal for Daily Deposit Performance.
 *
 * The modal load is asserted through the real mounted action, because that
 * exercises the fillForm() wiring end to end. The persistence rules are asserted
 * against EditDepositPerformanceAction::persist() directly: driving a mounted
 * modal through a full Livewire round-trip in the test harness was unreliable
 * (callTableAction() applies its $data via fillForm(), which targets the page
 * form rather than the mounted action schema, so the values were dropped).
 */
class EditDepositPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Performance history and branch plans are required, otherwise the
        // assertions below would pass against an empty table.
        Model::withoutEvents(function (): void {
            $this->seed(TestDataSeeder::class);
            $this->seed(BranchPlanSeeder::class);
            $this->seed(PerformanceHistorySeeder::class);
        });

        $user = User::where('email', 'seidm2031@gmail.com')->firstOrFail();
        $this->actingAs($user);
    }

    /**
     * @return array{record: DailyDepositPerformance, branch: Branch, bankingType: BankingType, day: string, segments: Collection}
     */
    protected function makeRecord(string $bankingTypeName = 'Conventional Banking'): array
    {
        $district = District::firstOrFail();
        $bankingType = BankingType::where('name', $bankingTypeName)->firstOrFail();
        $branch = Branch::where('district_id', $district->id)
            ->where('bankingType_id', $bankingType->id)
            ->firstOrFail();

        $day = now()->subDay()->toDateString();

        // PerformanceHistorySeeder already populated this branch/day, so reuse the
        // existing row instead of inserting a duplicate against the unique index.
        // The lookup has to use whereDate(): the column is a DATE, but the seeder
        // stores it as "YYYY-MM-DD 00:00:00", so a bare "YYYY-MM-DD" equality test
        // would miss the row on SQLite and trip the unique index.
        $record = DailyDepositPerformance::query()
            ->where('branch_id', $branch->id)
            ->whereDate('business_day', $day)
            ->first();

        if ($record === null) {
            $record = new DailyDepositPerformance([
                'branch_id' => $branch->id,
                'business_day' => $day,
            ]);
        }

        $record->total_deposit_amount = 1111.11;
        $record->remarks = 'original remark';
        $record->save();

        $segments = BusinessSegment::whereIn('name', ['Corporate', 'Retail', 'MSME'])->get();
        $amounts = ['Corporate' => 500.00, 'Retail' => 400.00, 'MSME' => 211.11];

        DailyDepositPerformanceDetail::query()
            ->where('branch_id', $branch->id)
            ->whereDate('business_day', $day)
            ->delete();

        foreach ($segments as $segment) {
            DailyDepositPerformanceDetail::create([
                'branch_id' => $branch->id,
                'business_day' => $day,
                'banking_type_id' => $bankingType->id,
                'business_segment_id' => $segment->id,
                'amount' => $amounts[$segment->name],
                'remarks' => 'original remark',
            ]);
        }

        return [
            'record' => $record,
            'branch' => $branch,
            'bankingType' => $bankingType,
            'day' => $day,
            'segments' => $segments,
        ];
    }

    public function test_edit_modal_loads_existing_values(): void
    {
        ['record' => $record] = $this->makeRecord();

        $component = Livewire::test(ListDailyDepositPerformances::class)
            ->mountTableAction('edit', $record);

        $schema = $component->instance()->getMountedTableActionForm();

        $this->assertNotNull($schema, 'The edit action should mount a schema.');

        $state = $schema->getState();

        $this->assertSame(500.00, $state['corporate'], 'corporate should load from details');
        $this->assertSame(400.00, $state['retail']);
        $this->assertSame(211.11, round((float) $state['msme'], 2));
        $this->assertSame('original remark', $state['remarks']);
    }

    public function test_persist_saves_main_record_and_details(): void
    {
        ['record' => $record, 'segments' => $segments, 'day' => $day, 'branch' => $branch, 'bankingType' => $bankingType] = $this->makeRecord();

        EditDepositPerformanceAction::persist([
            'corporate' => 9000.00,
            'retail' => 800.00,
            'msme' => 300.00,
            'remarks' => 'edited by test',
        ], $record);

        $record->refresh();
        $this->assertEquals(10100.00, (float) $record->total_deposit_amount);
        $this->assertEquals('edited by test', $record->remarks);

        $amounts = DailyDepositPerformanceDetail::where('branch_id', $branch->id)
            ->whereDate('business_day', $day)
            ->pluck('amount', 'business_segment_id')
            ->all();

        $this->assertEquals(9000.00, (float) $amounts[$segments->firstWhere('name', 'Corporate')->id]);
        $this->assertEquals(800.00, (float) $amounts[$segments->firstWhere('name', 'Retail')->id]);
        $this->assertEquals(300.00, (float) $amounts[$segments->firstWhere('name', 'MSME')->id]);
        $this->assertCount(3, $amounts);

        foreach (array_keys($amounts) as $sid) {
            $this->assertEquals(
                $bankingType->id,
                DailyDepositPerformanceDetail::where('branch_id', $branch->id)
                    ->whereDate('business_day', $day)->where('business_segment_id', $sid)->value('banking_type_id'),
            );
        }
    }

    public function test_persist_replaces_only_its_own_banking_type_rows(): void
    {
        ['record' => $record, 'segments' => $segments, 'day' => $day, 'branch' => $branch, 'bankingType' => $bankingType] = $this->makeRecord();

        // A Conventional branch also holds IFB rows; they are legitimate business
        // and must survive an edit of the Conventional figures.
        $otherType = $bankingType->id === 1 ? 2 : 1;
        DailyDepositPerformanceDetail::create([
            'branch_id' => $branch->id,
            'business_day' => $day,
            'banking_type_id' => $otherType,
            'business_segment_id' => $segments->firstWhere('name', 'Corporate')->id,
            'amount' => 100.00,
            'remarks' => 'ifb leg',
        ]);

        EditDepositPerformanceAction::persist([
            'corporate' => 1.00,
            'retail' => 2.00,
            'msme' => 3.00,
            'remarks' => null,
        ], $record);

        $rows = DailyDepositPerformanceDetail::where('branch_id', $branch->id)
            ->whereDate('business_day', $day)
            ->get();

        $this->assertCount(4, $rows, 'The sibling banking type row must be preserved.');
        $this->assertSame(
            3,
            $rows->where('banking_type_id', $bankingType->id)->count(),
            'The edited banking type is replaced wholesale, so it keeps exactly one row per segment.',
        );
        $this->assertSame(
            1,
            $rows->where('banking_type_id', $otherType)->count(),
            'The sibling banking type must not be duplicated.',
        );

        // The branch-level total counts every banking type, not just the edited one.
        $this->assertEquals(106.00, (float) $rows->sum('amount'));

        $record->refresh();
        $this->assertEquals(106.00, (float) $record->total_deposit_amount);
    }

    public function test_persist_can_zero_out_all_segments(): void
    {
        ['record' => $record, 'day' => $day, 'branch' => $branch] = $this->makeRecord();

        EditDepositPerformanceAction::persist([
            'corporate' => 0,
            'retail' => 0,
            'msme' => 0,
            'remarks' => 'no movement',
        ], $record);

        $record->refresh();
        $this->assertEquals(0.0, (float) $record->total_deposit_amount);
        $this->assertEquals('no movement', $record->remarks);
        $this->assertEquals(0.0, (float) DailyDepositPerformanceDetail::where('branch_id', $branch->id)
            ->whereDate('business_day', $day)->sum('amount'));
    }

    public function test_persist_does_not_duplicate_details_on_repeat_saves(): void
    {
        ['record' => $record, 'day' => $day, 'branch' => $branch] = $this->makeRecord();

        EditDepositPerformanceAction::persist(['corporate' => 5, 'retail' => 6, 'msme' => 7], $record);
        EditDepositPerformanceAction::persist(['corporate' => 8, 'retail' => 9, 'msme' => 10], $record);

        $rows = DailyDepositPerformanceDetail::where('branch_id', $branch->id)
            ->whereDate('business_day', $day)
            ->get();

        $this->assertCount(3, $rows);
        $this->assertEquals(27.0, (float) $rows->sum('amount'));
    }

    public function test_edit_action_rejects_today_as_business_day(): void
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        validator(
            ['business_day' => now()->toDateString()],
            ['business_day' => ['required', 'date', 'before:today']]
        )->validate();
    }
}
