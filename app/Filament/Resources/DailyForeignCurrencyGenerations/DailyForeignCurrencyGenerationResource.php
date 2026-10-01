<?php

namespace App\Filament\Resources\DailyForeignCurrencyGenerations;

use App\Filament\Resources\DailyForeignCurrencyGenerations\Pages\CreateDailyForeignCurrencyGeneration;
use App\Filament\Resources\DailyForeignCurrencyGenerations\Pages\EditDailyForeignCurrencyGeneration;
use App\Filament\Resources\DailyForeignCurrencyGenerations\Pages\ListDailyForeignCurrencyGenerations;
use App\Filament\Resources\DailyForeignCurrencyGenerations\Pages\ViewDailyForeignCurrencyGeneration;
use App\Filament\Resources\DailyForeignCurrencyGenerations\Schemas\DailyForeignCurrencyGenerationForm;
use App\Filament\Resources\DailyForeignCurrencyGenerations\Schemas\DailyForeignCurrencyGenerationInfolist;
use App\Filament\Resources\DailyForeignCurrencyGenerations\Tables\DailyForeignCurrencyGenerationsTable;
use App\Models\DailyForeignCurrencyGeneration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class DailyForeignCurrencyGenerationResource extends Resource
{
    protected static ?string $model = DailyForeignCurrencyGeneration::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;
    protected static ?string $navigationLabel = 'Foreign Currency Generation';
    protected static ?string $recordTitleAttribute = 'DailyForeignCurrencyGeneration';
    protected static string|\UnitEnum|null $navigationGroup = 'Branch Performance';

    public static function form(Schema $schema): Schema
    {
        return DailyForeignCurrencyGenerationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DailyForeignCurrencyGenerationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DailyForeignCurrencyGenerationsTable::configure($table)
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
            'index' => ListDailyForeignCurrencyGenerations::route('/'),
            // 'create' => CreateDailyForeignCurrencyGeneration::route('/create'),
            'view' => ViewDailyForeignCurrencyGeneration::route('/{record}'),
            // 'edit' => EditDailyForeignCurrencyGeneration::route('/{record}/edit'),
        ];
    }
}