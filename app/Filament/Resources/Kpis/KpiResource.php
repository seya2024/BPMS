<?php

namespace App\Filament\Resources\Kpis;

use App\Filament\Resources\Kpis\Pages\CreateKpi;
use App\Filament\Resources\Kpis\Pages\EditKpi;
use App\Filament\Resources\Kpis\Pages\ListKpis;
use App\Filament\Resources\Kpis\Pages\ViewKpi;
use App\Filament\Resources\Kpis\Schemas\KpiForm;
use App\Filament\Resources\Kpis\Schemas\KpiInfolist;
use App\Filament\Resources\Kpis\Tables\KpisTable;
use App\Models\Kpi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class KpiResource extends Resource
{
    protected static ?string $model = Kpi::class;


   protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChevronRight;
    protected static ?string $navigationLabel = 'List of KPIs ';
    protected static ?string $recordTitleAttribute = 'KPI';
    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    public static function form(Schema $schema): Schema
    {
        return KpiForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KpiInfolist::configure($schema);
    }

public static function table(Table $table): Table
{
    return KpisTable::configure($table)
        ->deferLoading()
        ->searchable()
        ->groups([
            Group::make('category.name')
                ->label('KPI Category'),
        ])
        ->defaultGroup('category.name');
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
            'index' => ListKpis::route('/'),
           // 'create' => CreateKpi::route('/create'),
            'view' => ViewKpi::route('/{record}'),
           // 'edit' => EditKpi::route('/{record}/edit'),
        ];
    }
}
