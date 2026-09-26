<?php

namespace App\Filament\Resources\KPICategories;

use App\Filament\Resources\KPICategories\Pages\CreateKPICategory;
use App\Filament\Resources\KPICategories\Pages\EditKPICategory;
use App\Filament\Resources\KPICategories\Pages\ListKPICategories;
use App\Filament\Resources\KPICategories\Pages\ViewKPICategory;
use App\Filament\Resources\KPICategories\Schemas\KPICategoryForm;
use App\Filament\Resources\KPICategories\Schemas\KPICategoryInfolist;
use App\Filament\Resources\KPICategories\Tables\KPICategoriesTable;
use App\Models\KPICategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KPICategoryResource extends Resource
{
    protected static ?string $model = KPICategory::class;


    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChevronRight;
    protected static ?string $navigationLabel = 'KPI Category';
    protected static ?string $recordTitleAttribute = 'KPICategory';
    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    public static function form(Schema $schema): Schema
    {
        return KPICategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KPICategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KPICategoriesTable::configure($table);
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
            'index' => ListKPICategories::route('/'),
            //'create' => CreateKPICategory::route('/create'),
            'view' => ViewKPICategory::route('/{record}'),
            //'edit' => EditKPICategory::route('/{record}/edit'),
        ];
    }
}
