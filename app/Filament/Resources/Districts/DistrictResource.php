<?php

namespace App\Filament\Resources\Districts;

//use App\Filament\Resources\Districts\Pages\CreateDistrict;
use App\Filament\Resources\Districts\Pages\EditDistrict;
use App\Filament\Resources\Districts\Pages\ListDistricts;
//use App\Filament\Resources\Districts\Pages\ViewDistrict;
use App\Filament\Resources\Districts\Schemas\DistrictForm;
use App\Filament\Resources\Districts\Schemas\DistrictInfolist;
use App\Filament\Resources\Districts\Tables\DistrictsTable;
use App\Models\District;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
class DistrictResource extends Resource
{
    protected static ?string $model = District::class;
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChevronRight;
    protected static ?string $navigationLabel = 'List of Districts';
    protected static ?string $recordTitleAttribute = 'District';
    protected static string|\UnitEnum|null $navigationGroup = 'Organizational Unit';

    public static function form(Schema $schema): Schema
    {
        return DistrictForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DistrictInfolist::configure($schema);
    }

  public static function table(Table $table): Table
{
    return DistrictsTable::configure($table)
        ->deferLoading()
        ->searchable()
        ->groups([
            'location_type',     
        ])
      
        ->defaultGroup('location_type');
}


     public static function getNavigationBadge(): ?string
    {
        return District::count();
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
            'index' => ListDistricts::route('/'),
            // 'create' => CreateDistrict::route('/create'),
           //   'view' => ViewDistrict::route('/{record}'),
           //  'edit' => EditDistrict::route('/{record}/edit'),
        ];
    }
}
