<?php

namespace App\Filament\Widgets\Concerns;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

trait HasDateRangeFilter
{
    public ?string $dateRange = 'last_45_days';
    public ?string $fromDate = null;
    public ?string $toDate = null;
    public ?string $compareRange = 'none';

    protected function getDateRangeOptions(): array
    {
        return [
            'last_7_days' => 'Last 7 days',
            'last_30_days' => 'Last 30 days',
            'last_45_days' => 'Last 45 days',
            'last_90_days' => 'Last 90 days',
            'current_month' => 'Current month',
            'previous_month' => 'Previous month',
            'current_quarter' => 'Current quarter',
            'custom' => 'Custom range',
        ];
    }

    protected function getCompareRangeOptions(): array
    {
        return [
            'none' => 'No comparison',
            'previous_period' => 'Previous period',
            'same_period_last_year' => 'Same period last year',
        ];
    }

    protected function getDateRangeFilterSchema(): array
    {
        return [
            Select::make('dateRange')
                ->label('Period')
                ->options($this->getDateRangeOptions())
                ->default('last_45_days')
                ->live()
                ->afterStateUpdated(function (callable $set, $state): void {
                    if ($state !== 'custom') {
                        $dates = $this->resolveDateRange($state);
                        $set('fromDate', $dates['from']);
                        $set('toDate', $dates['to']);
                    }
                })
                ->native(false),

            DatePicker::make('fromDate')
                ->label('From')
                ->default(fn () => $this->resolveDateRange('last_45_days')['from'])
                ->maxDate(now()->subDay())
                ->displayFormat('M d, Y')
                ->visible(fn (callable $get) => $get('dateRange') === 'custom')
                ->live()
                ->afterStateUpdated(fn ($state, callable $set) => $this->validateDateRange($set)),

            DatePicker::make('toDate')
                ->label('To')
                ->default(now()->subDay()->toDateString())
                ->maxDate(now()->subDay())
                ->displayFormat('M d, Y')
                ->visible(fn (callable $get) => $get('dateRange') === 'custom')
                ->live()
                ->afterStateUpdated(fn ($state, callable $set) => $this->validateDateRange($set)),

            Select::make('compareRange')
                ->label('Compare with')
                ->options($this->getCompareRangeOptions())
                ->default('none')
                ->native(false),
        ];
    }

    protected function resolveDateRange(string $range): array
    {
        $to = Carbon::yesterday()->endOfDay();
        $from = match ($range) {
            'last_7_days' => $to->copy()->subDays(6)->startOfDay(),
            'last_30_days' => $to->copy()->subDays(29)->startOfDay(),
            'last_45_days' => $to->copy()->subDays(44)->startOfDay(),
            'last_90_days' => $to->copy()->subDays(89)->startOfDay(),
            'current_month' => $to->copy()->startOfMonth()->startOfDay(),
            'previous_month' => $to->copy()->subMonth()->startOfMonth()->startOfDay(),
            'current_quarter' => $to->copy()->startOfQuarter()->startOfDay(),
            default => $to->copy()->subDays(44)->startOfDay(),
        };

        return [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ];
    }

    protected function validateDateRange(callable $set): void
    {
        if ($this->fromDate && $this->toDate) {
            $from = Carbon::parse($this->fromDate);
            $to = Carbon::parse($this->toDate);

            if ($from->gt($to)) {
                $set('fromDate', $this->toDate);
                $set('toDate', $this->fromDate);
            }
        }
    }

    protected function getEffectiveDateRange(): array
    {
        if ($this->dateRange === 'custom') {
            return [
                'from' => $this->fromDate ?? Carbon::yesterday()->subDays(44)->toDateString(),
                'to' => $this->toDate ?? Carbon::yesterday()->toDateString(),
            ];
        }

        return $this->resolveDateRange($this->dateRange);
    }

    protected function getComparisonDateRange(): ?array
    {
        $range = $this->getEffectiveDateRange();
        $from = Carbon::parse($range['from']);
        $to = Carbon::parse($range['to']);
        $days = $from->diffInDays($to) + 1;

        return match ($this->compareRange) {
            'previous_period' => [
                'from' => $from->copy()->subDays($days)->toDateString(),
                'to' => $from->copy()->subDay()->toDateString(),
            ],
            'same_period_last_year' => [
                'from' => $from->copy()->subYear()->toDateString(),
                'to' => $to->copy()->subYear()->toDateString(),
            ],
            default => null,
        };
    }

    protected function getPeriodLabel(): string
    {
        $range = $this->getEffectiveDateRange();
        $from = Carbon::parse($range['from']);
        $to = Carbon::parse($range['to']);

        if ($from->isSameDay($to)) {
            return $from->format('M d, Y');
        }

        if ($from->isSameMonth($to)) {
            return $from->format('M Y');
        }

        return $from->format('M d') . ' - ' . $to->format('M d, Y');
    }
}