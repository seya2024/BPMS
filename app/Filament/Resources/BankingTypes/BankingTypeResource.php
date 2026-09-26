<?php

namespace App\Filament\Resources\BankingTypes;

use App\Filament\Resources\BankingTypes\Pages\CreateBankingType;
use App\Filament\Resources\BankingTypes\Pages\EditBankingType;
use App\Filament\Resources\BankingTypes\Pages\ListBankingTypes;
use App\Filament\Resources\BankingTypes\Pages\ViewBankingType;
use App\Filament\Resources\BankingTypes\Schemas\BankingTypeForm;
use App\Filament\Resources\BankingTypes\Schemas\BankingTypeInfolist;
use App\Filament\Resources\BankingTypes\Tables\BankingTypesTable;
use App\Models\BankingType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BankingTypeResource extends Resource
{
    protected static ?string $model = BankingType::class;


    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChevronRight;
    protected static ?string $navigationLabel = 'Banking type';
    protected static ?string $recordTitleAttribute = 'BankingType';
    protected static string|\UnitEnum|null $navigationGroup = 'Settings';


    public static function form(Schema $schema): Schema
    {
        return BankingTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BankingTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BankingTypesTable::configure($table);
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
            'index' => ListBankingTypes::route('/'),
            'create' => CreateBankingType::route('/create'),
            'view' => ViewBankingType::route('/{record}'),
            'edit' => EditBankingType::route('/{record}/edit'),
        ];
    }
}
