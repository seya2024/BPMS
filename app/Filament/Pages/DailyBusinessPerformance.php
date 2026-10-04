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
                    ->options(function (): array {
                        $options = ['All Districts' => 'All Districts'];
                        foreach (District::query()->orderBy('name')->pluck('name', 'name') as $name => $value) {
                            $options[$value] = $value;
                        }
                        return $options;
                    })
                    ->default('All Districts')
                    ->live()
                    ->afterStateUpdated(
                        fn (Set $set) => $set('branch', 'All Branches')
                    ),

                Select::make('branch')
                    ->label('Branch')
                    ->options(function (Get $get): array {
                        $districtName = $get('district');

                        if (!$districtName || $districtName === 'All Districts') {
                            return ['All Branches' => 'All Branches'];
                        }

                        return District::query()
                            ->where('name', $districtName)
                            ->first()
                            ?->branches()
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->prepend('All Branches', 'All Branches')
                            ->all() ?? ['All Branches' => 'All Branches'];
                    })
                    ->default('All Branches')
                    ->searchable()
                    ->live(),
            ]);
    }

    /**
     * Fetch and format all branch and district data from the database.
     */
    public function getDashboardData(): array
    {
        $reportDate = $this->data['businessDate'] ?? now()->subDay()->toDateString();
        $selectedDistrict = $this->data['district'] ?? 'All Districts';
        $selectedBranch = $this->data['branch'] ?? 'All Branches';

        $tableData = [];

        // Fetch Districts based on filter
        $districtsQuery = District::query()->orderBy('name');
        if ($selectedDistrict !== 'All Districts') {
            $districtsQuery->where('name', $selectedDistrict);
        }
        $districts = $districtsQuery->get();

        foreach ($districts as $district) {
            // Fetch Branches for this District based on filter
            $branchesQuery = $district->branches()->orderBy('name');
            if ($selectedBranch !== 'All Branches') {
                $branchesQuery->where('name', $selectedBranch);
            }
            $branches = $branchesQuery->get();

            if ($branches->isEmpty()) {
                continue;
            }

            $districtTotals = $this->initTotals();
            $branchRows = [];

            foreach ($branches as $index => $branch) {
                // =========================================================
                // KPI DATA FETCH
                // =========================================================
                // Replace this section with your actual KPI model query.
                // Example: 
                // $kpis = \App\Models\KpiPerformance::where('branch_id', $branch->id)
                //     ->where('date', $reportDate)
                //     ->first();
                
                $kpis = null; // -> Replace with query above

                $rowData = $this->buildRowData($kpis);

                // Determine Banking Type from branch name (IFB vs Conventional)
                $type = stripos($branch->name, 'IFB') !== false ? 'IFB' : 'Conventional';
                
                $branchRows[] = [
                    'name' => ($index + 1) . '. ' . $branch->name,
                    'type' => $type,
                    'data' => $rowData
                ];

                $this->addToTotals($districtTotals, $rowData);
            }

            $formattedDistrictData = $this->formatTotals($districtTotals);

            // Determine district-level banking type
            $types = collect($branchRows)->pluck('type')->unique();
            $districtType = $types->count() === 1 ? $types->first() : 'Mixed';

            $tableData[] = [
                'district_name' => strtoupper($district->name) . ' DISTRICT',
                'district_type' => $districtType,
                'district_data' => $formattedDistrictData,
                'branches' => $branchRows,
            ];
        }

        return ['table_data' => $tableData];
    }

    private function initTotals(): array
    {
        return [
            'major' => [
                'deposit' => ['numeric_a' => 0, 'numeric_t' => 0],
                'accounts' => ['numeric_a' => 0, 'numeric_t' => 0],
                'fcy' => ['numeric_a' => 0, 'numeric_t' => 0],
                'loans' => ['numeric_a' => 0, 'numeric_t' => 0],
            ],
            'digital' => [
                'card_sub' => ['numeric_a' => 0, 'numeric_t' => 0],
                'pos_sub' => ['numeric_a' => 0, 'numeric_t' => 0],
                'super_sub' => ['numeric_a' => 0, 'numeric_t' => 0],
            ],
            'activation' => [
                'acc_act' => ['numeric_a' => 0, 'numeric_t' => 0],
                'card_act' => ['numeric_a' => 0, 'numeric_t' => 0],
                'super_act' => ['numeric_a' => 0, 'numeric_t' => 0],
            ],
            'other' => [
                'service_quality' => ['numeric_a' => 0, 'numeric_t' => 0],
                'atm_txn' => ['numeric_a' => 0, 'numeric_t' => 0],
                'pos_txn' => ['numeric_a' => 0, 'numeric_t' => 0],
                'nps' => ['numeric_a' => 0, 'numeric_t' => 0],
            ],
        ];
    }

    private function buildRowData($kpis): array
    {
        return [
            'major' => [
                'deposit' => $this->calc($kpis->deposit_actual ?? 0, $kpis->deposit_target ?? 0),
                'accounts' => $this->calc($kpis->accounts_actual ?? 0, $kpis->accounts_target ?? 0),
                'fcy' => $this->calc($kpis->fcy_actual ?? 0, $kpis->fcy_target ?? 0),
                'loans' => $this->calc($kpis->loans_actual ?? 0, $kpis->loans_target ?? 0),
            ],
            'digital' => [
                'card_sub' => $this->calc($kpis->card_sub_actual ?? 0, $kpis->card_sub_target ?? 0),
                'pos_sub' => $this->calc($kpis->pos_sub_actual ?? 0, $kpis->pos_sub_target ?? 0),
                'super_sub' => $this->calc($kpis->super_sub_actual ?? 0, $kpis->super_sub_target ?? 0),
            ],
            'activation' => [
                'acc_act' => $this->calc($kpis->acc_act_actual ?? 0, $kpis->acc_act_target ?? 0),
                'card_act' => $this->calc($kpis->card_act_actual ?? 0, $kpis->card_act_target ?? 0),
                'super_act' => $this->calc($kpis->super_act_actual ?? 0, $kpis->super_act_target ?? 0),
            ],
            'other' => [
                'service_quality' => $this->calc($kpis->service_quality_actual ?? 0, $kpis->service_quality_target ?? 0),
                'atm_txn' => $this->calc($kpis->atm_txn_actual ?? 0, $kpis->atm_txn_target ?? 0),
                'pos_txn' => $this->calc($kpis->pos_txn_actual ?? 0, $kpis->pos_txn_target ?? 0),
                'nps' => $this->calc($kpis->nps_actual ?? 0, $kpis->nps_target ?? 0),
            ],
            'overall' => $this->calcOverall($kpis->overall_score ?? 0),
        ];
    }

    private function calc($actual, $target): array
    {
        $variance = $actual - $target;
        $achievement = $target > 0 ? (int) round(($actual / $target) * 100) : 0;
        $varianceStr = ($variance > 0 ? '+' : '') . $this->formatNumber($variance);

        $trend = '→';
        if ($achievement >= 100) {
            $trend = '↑';
        } elseif ($achievement < 90) {
            $trend = '↓';
        }

        return [
            'a' => $this->formatNumber($actual),
            't' => $this->formatNumber($target),
            'v' => $varianceStr,
            'g' => $achievement,
            'tr' => $trend,
            'numeric_a' => $actual,
            'numeric_t' => $target,
        ];
    }

    private function calcOverall($score): array
    {
        $trend = 'stable';
        if ($score >= 95) {
            $trend = 'up';
        } elseif ($score < 90) {
            $trend = 'down';
        }

        return [
            'score' => $score,
            'trend' => $trend,
        ];
    }

    private function formatNumber($number): string
    {
        $number = (float) $number;
        if (abs($number) >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        } elseif (abs($number) >= 1000) {
            return round($number / 1000, 0) . 'K';
        }
        return (string) round($number);
    }

    private function addToTotals(array &$totals, array $rowData): void
    {
        foreach (['major', 'digital', 'activation', 'other'] as $category) {
            foreach ($rowData[$category] as $key => $values) {
                $totals[$category][$key]['numeric_a'] += $values['numeric_a'];
                $totals[$category][$key]['numeric_t'] += $values['numeric_t'];
            }
        }
    }

    private function formatTotals(array $totals): array
    {
        $formatted = [];
        
        foreach (['major', 'digital', 'activation', 'other'] as $category) {
            foreach ($totals[$category] as $key => $values) {
                $formatted[$category][$key] = $this->calc(
                    $values['numeric_a'], 
                    $values['numeric_t']
                );
            }
        }

        $formatted['overall'] = $this->calcOverall(0); 

        return $formatted;
    }
}