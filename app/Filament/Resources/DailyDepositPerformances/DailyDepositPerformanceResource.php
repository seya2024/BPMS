<?php

namespace App\Filament\Resources\DailyDepositPerformances;

use App\Filament\Resources\DailyDepositPerformances\Pages\CreateDailyDepositPerformance;
use App\Filament\Resources\DailyDepositPerformances\Pages\EditDailyDepositPerformance;
use App\Filament\Resources\DailyDepositPerformances\Pages\ListDailyDepositPerformances;
use App\Filament\Resources\DailyDepositPerformances\Pages\ViewDailyDepositPerformance;
use App\Filament\Resources\DailyDepositPerformances\Schemas\DailyDepositPerformanceForm;
use App\Filament\Resources\DailyDepositPerformances\Schemas\DailyDepositPerformanceInfolist;
use App\Filament\Resources\DailyDepositPerformances\Tables\DailyDepositPerformancesTable;
use App\Models\DailyDepositPerformance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class DailyDepositPerformanceResource extends Resource
{
    protected static ?string $model = DailyDepositPerformance::class;

  
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChevronRight;
    protected static ?string $navigationLabel = 'Daily Deposit Status';
    protected static ?string $recordTitleAttribute = 'DailyDepositPerformance';
    protected static string|\UnitEnum|null $navigationGroup = 'Branch Performance';


    public static function form(Schema $schema): Schema
    {
        return DailyDepositPerformanceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DailyDepositPerformanceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DailyDepositPerformancesTable::configure($table)
            ->deferLoading()
            ->searchable()
            ->paginated([10, 25, 50])
            ->groups([
                Group::make('branch.district.name')
                    ->label('District Office')
                    ->collapsible(),

                Group::make('branch.name')
                    ->label('Branch')
                    ->collapsible(),
            ])
            ->defaultGroup(
                Group::make('branch.district.name')
                    ->label('District Office')
            );
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDailyDepositPerformances::route('/'),
            // 'create' => CreateDailyDepositPerformance::route('/create'),
            'view' => ViewDailyDepositPerformance::route('/{record}'),
            // 'edit' => EditDailyDepositPerformance::route('/{record}/edit'),
        ];
    }
}
