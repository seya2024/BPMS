<?php

namespace App\Filament\Resources\AnnualPlans;

use App\Filament\Resources\AnnualPlans\Pages\CreateAnnualPlan;
use App\Filament\Resources\AnnualPlans\Pages\EditAnnualPlan;
use App\Filament\Resources\AnnualPlans\Pages\ListAnnualPlans;
use App\Filament\Resources\AnnualPlans\Pages\ViewAnnualPlan;
use App\Filament\Resources\AnnualPlans\Schemas\AnnualPlanForm;
use App\Filament\Resources\AnnualPlans\Schemas\AnnualPlanInfolist;
use App\Filament\Resources\AnnualPlans\Tables\AnnualPlansTable;
use App\Models\AnnualPlan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class AnnualPlanResource extends Resource
{
    protected static ?string $model = AnnualPlan::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChevronRight;
    protected static ?string $navigationLabel = 'Annual Plan';
    protected static ?string $recordTitleAttribute = 'AnnualPlan';
    protected static string|\UnitEnum|null $navigationGroup = 'Planning';

    public static function form(Schema $schema): Schema
    {
        return AnnualPlanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AnnualPlanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnnualPlansTable::configure($table)
            ->deferLoading()
            ->searchable()
            ->paginated([10, 25, 50])
            ->groups([
                Group::make('financialYear.name')
                    ->label('Financial Year')
                    ->collapsible(),

                Group::make('district.name')
                    ->label('District Office')
                    ->collapsible(),

                Group::make('creator.name')
                    ->label('Created By')
                    ->collapsible(),
            ])
            ->defaultGroup(
                Group::make('financialYear.name')
                    ->label('Financial Year')
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
            'index' => ListAnnualPlans::route('/'),
          //  'create' => CreateAnnualPlan::route('/create'),
            'view' => ViewAnnualPlan::route('/{record}'),
           // 'edit' => EditAnnualPlan::route('/{record}/edit'),
        ];
    }
}
