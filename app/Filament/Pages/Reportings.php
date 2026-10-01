<?php

namespace App\Filament\Pages;

use App\Exports\ReportExport;
use App\Filament\Support\BusinessDay;
use App\Models\District;
use App\Services\ReportService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\Toggle;

/**
 * Reportings: the five daily/period reports, exportable to Excel.
 *
 * Defaults to a single business day of yesterday. A date range is optional and its
 * upper bound is clamped to yesterday, so no report can be pulled for a day that
 * has not finished reporting.
 */
class Reportings extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-chart-bar';

    // Types must match Filament's Page exactly: an inherited property's type
    // cannot be widened or removed.
    protected static string | \UnitEnum | null $navigationGroup = 'Reportings';

    protected static ?string $navigationLabel = 'Report';

    protected static ?string $title = 'Report';

    protected static ?int $navigationSort = 1;

    /**
     * Must be PUBLIC: Livewire only syncs public properties, so a protected
     * $data is omitted from the snapshot and every field raises
     * "property ['data.x'] cannot be found on component".
     *
     * @var array<string, mixed>
     */
    public ?array $data = [];

    public function getView(): string
    {
        return 'filament.pages.reportings';
    }

    public function mount(): void
    {
        $this->data = [
            'report' => array_key_first(ReportService::REPORTS),
            'from' => Carbon::yesterday()->toDateString(),
            'to' => Carbon::yesterday()->toDateString(),
            'district_id' => null,
            'use_range' => false,
        ];
    }
public function form(Schema $schema): Schema
{
    return $schema
        ->statePath('data')
        ->components([
            Section::make('Report options')
                ->description(
                    'Reports cover yesterday by default. Tick "Use a date range" to report over a custom period; the end date cannot be today or later.'
                )
                ->columnSpanFull()
                ->compact()
                ->columns(4)
                ->schema([
                    Select::make('report')
                        ->label('Report')
                        ->options(ReportService::REPORTS)
                        ->live()
                        ->required()
                        ->columnSpan(1),

                    Select::make('district_id')
                        ->label('District Office')
                        ->options(fn (): array => District::query()
                            ->whereHas('branches')
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all())
                        ->searchable()
                        ->placeholder('All districts')
                        ->columnSpan(1),

                    Toggle::make('use_range')
                        ->label('Use a date range')
                        ->helperText('Off = a single day')
                        ->live()
                        ->default(false)
                        ->columnSpan(2),

                    BusinessDay::picker('from', 'From')
                        ->columnSpan(1),

                    BusinessDay::picker('to', 'To')
                        ->columnSpan(1),
                ]),
        ]);
}

    public function getReportRows(): array
    {
        [$report, $from, $to, $districtId] = $this->exportContext();

        return ReportService::run($report, $from, $to, $districtId);
    }

    public function getWindowLabel(): string
    {
        $window = $this->resolvedWindow();

        return $window['from'] === $window['to']
            ? Carbon::parse($window['from'])->format('j M Y')
            : Carbon::parse($window['from'])->format('j M Y') . ' to ' . Carbon::parse($window['to'])->format('j M Y');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function getHeaderActions(): array
    {
        return [
            // A plain Action rather than Filament's ExportAction: that one is bound
            // to a table (it needs visible table columns and a query), and these
            // reports are built by the service, not by a table query.
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
                    [$report, $from, $to, $districtId] = $this->exportContext();

                    return Excel::download(
                        new ReportExport($report, $from, $to, $districtId),
                        ReportExport::filename($report, $from, $to),
                    );
                }),
        ];
    }

    /**
     * The report, window and district the export should use.
     *
     * @return array{0: string, 1: string, 2: string, 3: int|null}
     */
    public function exportContext(): array
    {
        $window = $this->resolvedWindow();

        return [
            (string) ($this->data['report'] ?? 'all_kpi'),
            $window['from'],
            $window['to'],
            $this->data['district_id'] !== null && $this->data['district_id'] !== ''
                ? (int) $this->data['district_id']
                : null,
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
