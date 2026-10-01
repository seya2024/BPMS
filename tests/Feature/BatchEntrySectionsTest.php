<?php

namespace Tests\Feature;

use App\Filament\Pages\BatchDailyAccountOpenings;
use App\Filament\Pages\BatchDailyDepositPerformances;
use App\Models\District;
use App\Models\User;
use Database\Seeders\TestDataSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * The two batch entry pages must group their rows by branches.bankingType_id,
 * with disjoint sections: Conventional lists bankingType_id = 1 branches only,
 * IFB lists bankingType_id = 2 branches only.
 *
 * Asserted on the component's entries state rather than on rendered HTML: the
 * rendered page is ~0.7MB on one line, which is slow to produce and unusable as a
 * failure haystack.
 */
class BatchEntrySectionsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{0: class-string}>
     */
    public static function pages(): array
    {
        return [
            'account openings' => [BatchDailyAccountOpenings::class],
            'deposit' => [BatchDailyDepositPerformances::class],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        Model::withoutEvents(function (): void {
            $this->seed(TestDataSeeder::class);
        });

        $this->actingAs(User::where('email', 'seidm2031@gmail.com')->firstOrFail());
    }

    /**
     * @param  class-string  $page
     * @return array{conventional: array<int, int>, ifb: array<int, int>}
     */
    protected function sectionBranchIds(string $page): array
    {
        $component = Livewire::test($page)
            ->set('data.district_id', District::firstOrFail()->id)
            ->set('data.business_day', now()->subDay()->toDateString());

        $entries = $component->instance()->data['entries'] ?? [];

        $ids = fn (string $key): array => array_map(
            fn ($row) => (int) ($row['branch_id'] ?? 0),
            $entries[$key] ?? []
        );

        return [
            'conventional' => $ids('conventional'),
            'ifb' => $ids('ifb'),
        ];
    }

    /**
     * @dataProvider pages
     */
    public function test_conventional_section_lists_exactly_the_conventional_branches(string $page): void
    {
        $expected = DB::table('branches')->where('bankingType_id', 1)->pluck('id')
            ->map(fn ($id) => (int) $id)->all();

        $actual = $this->sectionBranchIds($page)['conventional'];

        $this->assertEqualsCanonicalizing($expected, $actual);
    }

    /**
     * @dataProvider pages
     */
    public function test_ifb_section_lists_exactly_the_ifb_branches(string $page): void
    {
        $expected = DB::table('branches')->where('bankingType_id', 2)->pluck('id')
            ->map(fn ($id) => (int) $id)->all();

        $actual = $this->sectionBranchIds($page)['ifb'];

        $this->assertEqualsCanonicalizing($expected, $actual);
    }

    /**
     * @dataProvider pages
     */
    public function test_sections_are_disjoint(string $page): void
    {
        $sections = $this->sectionBranchIds($page);

        $overlap = array_intersect($sections['conventional'], $sections['ifb']);

        $this->assertSame(
            [],
            array_values($overlap),
            'A branch must appear in exactly one section; Conventional branches must not be listed under IFB.'
        );
    }

    /**
     * @dataProvider pages
     */
    public function test_ifb_section_is_the_smaller_set(string $page): void
    {
        $sections = $this->sectionBranchIds($page);

        $this->assertLessThan(
            count($sections['conventional']),
            count($sections['ifb']),
            'The IFB section should be the smaller set.'
        );
    }
}
