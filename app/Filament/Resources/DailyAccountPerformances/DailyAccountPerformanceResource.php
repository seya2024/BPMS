<?php

namespace App\Filament\Resources\DailyAccountPerformances;

use App\Filament\Resources\DailyAccountPerformances\Pages\CreateDailyAccountPerformance;
use App\Filament\Resources\DailyAccountPerformances\Pages\EditDailyAccountPerformance;
use App\Filament\Resources\DailyAccountPerformances\Pages\ListDailyAccountPerformances;
use App\Filament\Resources\DailyAccountPerformances\Pages\ViewDailyAccountPerformance;
use App\Filament\Resources\DailyAccountPerformances\Schemas\DailyAccountPerformanceForm;
use App\Filament\Resources\DailyAccountPerformances\Schemas\DailyAccountPerformanceInfolist;
use App\Filament\Resources\DailyAccountPerformances\Tables\DailyAccountPerformancesTable;
use App\Models\DailyAccountPerformance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DailyAccountPerformanceResource extends Resource
{
    protected static ?string $model = DailyAccountPerformance::class;

 

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChevronRight;
    protected static ?string $navigationLabel = 'Daily Account Status';
    protected static ?string $recordTitleAttribute = 'DailyAccountPerformance';
    protected static string|\UnitEnum|null $navigationGroup = 'Branch Performance';
    


    public static function form(Schema $schema): Schema
    {
        return DailyAccountPerformanceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DailyAccountPerformanceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DailyAccountPerformancesTable::configure($table);
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
            'index' => ListDailyAccountPerformances::route('/'),
           // 'create' => CreateDailyAccountPerformance::route('/create'),
            'view' => ViewDailyAccountPerformance::route('/{record}'),
            //'edit' => EditDailyAccountPerformance::route('/{record}/edit'),
        ];
    }
}
