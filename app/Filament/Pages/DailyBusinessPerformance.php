<?php

namespace App\Filament\Pages;

use App\Filament\Support\BusinessDay;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use App\Models\Branch;
use App\Models\District;

/**
 * Filament v4 migration note
 * -------------------------
 * This page used the Filament v3 form API (HasForms + InteractsWithForms +
 * getForms() + makeForm(), and a later attempt as Form()). All of those were
 * removed in v4, so every request to this route failed with
 * "Method DailyBusinessPerformance::makeForm does not exist".
 *
 * The v4 equivalent is a form(Schema $schema): Schema method, with state filled
 * by assigning to $this->data instead of $this->form->fill().
 *
 * Property types must also match Filament\Pages\Page EXACTLY - see $view and
 * $navigationIcon. A mismatch is thrown as a fatal error at panel boot, so it
 * takes down every page including /admin/login, not just this one.
 */
class DailyBusinessPerformance extends Page
{

    // Type must match Filament's Page exactly: the inherited property is
    // declared `string | BackedEnum | null`, and a narrower `?string` is a fatal
    // error that breaks every page in the panel, not just this one.
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Follow Up';
    protected static ?string $title = 'Daily Business Performance';
        protected static string | \UnitEnum | null $navigationGroup = 'Reportings';
    // Filament\Pages\Page declares this as `protected string $view` - NOT static.
    // Redeclaring it as static is a fatal error, and because Filament resolves
    // every panel page at boot it takes down the whole panel, /admin/login
    // included, not just this page.
    protected string $view = 'filament.pages.daily-business-performance';

    /**
     * Must be PUBLIC. Livewire's wire:model can only write to a public
     * property, so a protected $data makes the console throw
     * "property ['data.district'] does not exist on component" on every
     * keystroke/selection, and the selects silently do nothing.
     *
     * @var array<string, mixed>
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->data = [
            'businessDate' => now()->subDay()->toDateString(),
            'district' => 'All Districts',
            'branch' => 'All Branches',
        ];
    }

public function form(Schema $schema): Schema
{
    return $schema
        ->statePath('data')
        ->columns([
            'default' => 1,
            'md' => 3,
        ])
        ->components([
            BusinessDay::picker('businessDate', 'Business Date'),

            Select::make('district')
                ->label('District')
                ->options([
                    'All Districts' => 'All Districts',
                    'Jimma' => 'Jimma',
                ])
                ->default('All Districts')
                ->live()
                ->afterStateUpdated(
                    fn (Set $set) => $set('branch', 'All Branches')
                ),

            Select::make('branch')
                ->label('Branch')
                ->options(function (Get $get): array {
                    $district = $get('district');

                    if ($district === 'All Districts') {
                        return [
                            'All Branches' => 'All Branches',
                        ];
                    }

                    return District::query()
                        ->where('name', $district)
                        ->first()
                        ?->branches()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->prepend('All Branches', 'All Branches')
                        ->all() ?? [
                            'All Branches' => 'All Branches',
                        ];
                })
                ->default('All Branches')
                ->searchable()
                ->live(),
        ]);
}

    public function getDashboardData(): array
    {
        return [
            'table_data' => [
                [
                    'district_name' => 'JIMMA DISTRICT',
                    'district_data' => $this->getBranchData(299, 310, 96, 52, 52, 100, 39, 50, 78, 2900, 3100, 94, 180, 200, 90, 94.2, 'down'),
                    'branches' => [
                        ['name' => '1. Jimma Main', 'data' => $this->getBranchData(125, 120, 104, 24, 20, 120, 18, 25, 72, 1245, 1300, 96, 95, 100, 95, 97.8, 'up')],
                        ['name' => '2. Agaro', 'data' => $this->getBranchData(98, 110, 89, 17, 20, 85, 12, 15, 80, 934, 1050, 89, 52, 60, 87, 87.2, 'down')],
                        ['name' => '3. Bedele', 'data' => $this->getBranchData(76, 80, 95, 11, 12, 92, 9, 10, 90, 721, 750, 96, 33, 40, 83, 91.4, 'stable')],
                    ]
                ],
                [
                    'district_name' => 'BAHIR DAR DISTRICT',
                    'district_data' => $this->getBranchData(410, 420, 98, 76, 80, 95, 62, 70, 89, 3800, 4000, 95, 250, 270, 93, 93.6, 'down'),
                    'branches' => [
                        ['name' => '5. Bahir Dar 1', 'data' => $this->getBranchData(160, 170, 94, 30, 34, 88, 25, 30, 83, 1450, 1600, 91, 100, 110, 91, 90.2, 'down')],
                        ['name' => '6. Bahir Dar 2', 'data' => $this->getBranchData(140, 135, 104, 26, 28, 93, 22, 26, 85, 1250, 1300, 96, 90, 95, 95, 94.8, 'up')],
                        ['name' => '7. Bahir Dar 3', 'data' => $this->getBranchData(110, 115, 96, 20, 18, 111, 15, 14, 107, 1100, 1100, 100, 60, 65, 92, 96.7, 'up')],
                    ]
                ],
                [
                    'district_name' => 'SNNP DISTRICT',
                    'district_data' => $this->getBranchData(320, 340, 94, 58, 60, 97, 48, 55, 87, 2450, 2700, 91, 160, 180, 89, 91.8, 'down'),
                    'branches' => [
                        ['name' => '9. Hawassa', 'data' => $this->getBranchData(130, 140, 93, 24, 26, 92, 20, 25, 80, 1100, 1200, 92, 85, 95, 89, 89.5, 'down')],
                        ['name' => '10. Wolayta Sodo', 'data' => $this->getBranchData(110, 120, 92, 18, 20, 90, 16, 20, 80, 950, 1100, 86, 50, 60, 83, 85.6, 'down')],
                        ['name' => '11. Dilla', 'data' => $this->getBranchData(80, 80, 100, 16, 14, 114, 12, 10, 120, 400, 400, 100, 25, 25, 100, 98.4, 'up')],
                    ]
                ]
            ]
        ];
    }

    private function getBranchData($depA, $depT, $depG, $accA, $accT, $accG, $mobA, $mobT, $mobG, $atmA, $atmT, $atmG, $loanA, $loanT, $loanG, $overall, $trend): array
    {
        return [
            'deposit' => ['a' => $depA, 't' => $depT, 'v' => $depA - $depT, 'g' => $depG, 'trend' => $trend],
            'accounts' => ['a' => $accA, 't' => $accT, 'v' => $accA - $accT, 'g' => $accG, 'trend' => $trend],
            'mobile' => ['a' => $mobA, 't' => $mobT, 'v' => $mobA - $mobT, 'g' => $mobG, 'trend' => $trend],
            'atm' => ['a' => $atmA, 't' => $atmT, 'v' => $atmA - $atmT, 'g' => $atmG, 'trend' => $trend === 'down' ? 'up' : 'down'],
            'loans' => ['a' => $loanA, 't' => $loanT, 'v' => $loanA - $loanT, 'g' => $loanG, 'trend' => 'down'],
            'overall' => ['score' => $overall, 'trend' => $trend],
        ];
    }
}