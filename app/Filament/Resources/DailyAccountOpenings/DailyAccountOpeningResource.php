<?php

namespace App\Filament\Resources\DailyAccountOpenings;

use App\Filament\Resources\DailyAccountOpenings\Pages\CreateDailyAccountOpening;
use App\Filament\Resources\DailyAccountOpenings\Pages\EditDailyAccountOpening;
use App\Filament\Resources\DailyAccountOpenings\Pages\ListDailyAccountOpenings;
use App\Filament\Resources\DailyAccountOpenings\Pages\ViewDailyAccountOpening;
use App\Filament\Resources\DailyAccountOpenings\Schemas\DailyAccountOpeningForm;
use App\Filament\Resources\DailyAccountOpenings\Schemas\DailyAccountOpeningInfolist;
use App\Filament\Resources\DailyAccountOpenings\Tables\DailyAccountOpeningsTable;
use App\Models\DailyAccountOpening;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class DailyAccountOpeningResource extends Resource
{
    protected static ?string $model = DailyAccountOpening::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUserPlus;
    protected static ?string $navigationLabel = 'Daily Account Openings';
    protected static string|\UnitEnum|null $navigationGroup = 'Branch Performance';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return DailyAccountOpeningForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DailyAccountOpeningInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DailyAccountOpeningsTable::configure($table)
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

                Group::make('branch.bankingType.name')
                    ->label('Banking Type')
                    ->collapsible(),
            ])
            ->defaultGroup(
                Group::make('branch.district.name')
                    ->label('District Office')
            );
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDailyAccountOpenings::route('/'),
            // 'create' => CreateDailyAccountOpening::route('/create'),
            'view' => ViewDailyAccountOpening::route('/{record}'),
            // 'edit' => EditDailyAccountOpening::route('/{record}/edit'),
        ];
    }
}
