<?php

namespace App\Filament\Pages;

use App\Exports\ReportExport;
use App\Filament\Support\BusinessDay;
use App\Models\BankingType;
use App\Models\Branch;
use App\Models\District;
use App\Services\ReportService;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class Reportings extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static string | \UnitEnum | null $navigationGroup = 'Reportings';

    protected static ?string $navigationLabel = 'Report';

    protected static ?string $title = 'Report';

    protected static ?int $navigationSort = 1;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function getView(): string
    {
        return 'filament.pages.reportings';
    }

    public function mount(): void
    {
        $reports = ReportService::reports();
        $defaultReport = array_key_exists('all_kpi', $reports)
            ? 'all_kpi'
            : (array_key_first($reports) ?? 'all_kpi');

        $this->data = [
            'report' => $defaultReport,
            'from' => Carbon::yesterday()->toDateString(),
            'to' => Carbon::yesterday()->toDateString(),
            'district_id' => null,
            'branch_id' => null,
            'banking_type_id' => null,
            'use_range' => false,
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Report options')
                    ->description('Reports cover yesterday by default. Filters cascade District → Branch → Banking Type.')
                    ->columnSpanFull()
                    ->compact()
                    ->columns(5)
                    ->schema([

                        Select::make('district_id')
                            ->label('District Office')
                            ->options(fn (): array => District::query()
                                ->whereHas('branches')
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->placeholder('All districts')
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('branch_id', null);
                                $set('banking_type_id', null);
                            })
                            ->columnSpan(1),

                        Select::make('banking_type_id')
                            ->label('Banking Type')
                            ->options(function (Get $get): array {
                                $districtId = $get('district_id');
                                $branchId   = $get('branch_id');

                                $usedTypeIds = \Illuminate\Support\Facades\DB::table('branches')
                                    ->when($branchId, fn ($q) => $q->where('id', $branchId))
                                    ->when(!$branchId && $districtId, fn ($q) => $q->where('district_id', $districtId))
                                    ->whereNotNull('bankingType_id')
                                    ->distinct()
                                    ->pluck('bankingType_id')
                                    ->all();

                                if (empty($usedTypeIds) && !$districtId && !$branchId) {
                                    return BankingType::query()->orderBy('name')->pluck('name', 'id')->all();
                                }
                                if (empty($usedTypeIds)) {
                                    return [];
                                }

                                return BankingType::query()
                                    ->whereIn('id', $usedTypeIds)
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all();
                            })
                            ->searchable()
                            ->placeholder('All banking types')
                            ->live()
                            ->columnSpan(1),

                        Select::make('report')
                            ->label('Select KPI')
                            ->options(fn (): array => ReportService::reports())
                            ->searchable()
                            ->live()
                            ->required()
                            ->columnSpan(1),

                        Toggle::make('use_range')
                            ->label('Use a date range')
                            ->helperText('Off = a single day')
                            ->live()
                            ->default(false)
                            ->columnSpan(1),

                        // Business Day (read-only) — shown when range is OFF
                        Placeholder::make('business_day_display')
                            ->label('Business Day')
                            ->content(fn () => Carbon::yesterday()->format('j M Y'))
                            ->visible(fn (Get $get) => ! $get('use_range'))
                            ->columnSpan(1),

                        // From — shown only when range is ON
                        BusinessDay::picker('from', 'From')
                            ->visible(fn (Get $get) => (bool) $get('use_range'))
                            ->columnSpan(1),

                        // To — shown only when range is ON
                        BusinessDay::picker('to', 'To')
                            ->visible(fn (Get $get) => (bool) $get('use_range'))
                            ->columnSpan(1),
                    ]),
            ]);
    }

    public function getReportRows(): array
    {
        [$report, $from, $to, $districtId, $branchId, $bankingTypeId] = $this->exportContext();

        return ReportService::run(
            $report,
            $from,
            $to,
            $districtId,
            $branchId,
            $bankingTypeId,
        );
    }

    public function getWindowLabel(): string
    {
        $window = $this->resolvedWindow();

        return $window['from'] === $window['to']
            ? Carbon::parse($window['from'])->format('j M Y')
            : Carbon::parse($window['from'])->format('j M Y') . ' to ' . Carbon::parse($window['to'])->format('j M Y');
    }

    public function getReportName(): string
    {
        $key = (string) ($this->data['report'] ?? 'all_kpi');
        $reports = ReportService::reports();

        return $reports[$key] ?? 'Report';
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportExcel')
                ->label('Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->outlined()
                ->size('sm')
                ->color('primary')
                ->modalHeading('Export report to Excel')
                ->modalDescription('The file contains one row per branch for the selected report and period.')
                ->modalSubmitActionLabel('Download')
                ->action(function (): mixed {
                    [$report, $from, $to, $districtId, $branchId, $bankingTypeId] = $this->exportContext();

                    return Excel::download(
                        new ReportExport($report, $from, $to, $districtId, $branchId, $bankingTypeId),
                        ReportExport::filename($report, $from, $to),
                    );
                }),
        ];
    }

    /**
     * @return array{0: string, 1: string, 2: string, 3: int|null, 4: int|null, 5: int|null}
     */
    public function exportContext(): array
    {
        $window = $this->resolvedWindow();

        return [
            (string) ($this->data['report'] ?? 'all_kpi'),
            $window['from'],
            $window['to'],
            !empty($this->data['district_id'])     ? (int) $this->data['district_id']     : null,
            !empty($this->data['branch_id'])       ? (int) $this->data['branch_id']       : null,
            !empty($this->data['banking_type_id']) ? (int) $this->data['banking_type_id'] : null,
        ];
    }

    /**
     * @return array{from: string, to: string, singleDay: bool}
     */
    protected function resolvedWindow(): array
    {
        $from = $this->data['from'] ?? null;
        $to = ($this->data['use_range'] ?? false) ? ($this->data['to'] ?? null) : $from;

        return ReportService::resolveWindow($from, $to);
    }
}