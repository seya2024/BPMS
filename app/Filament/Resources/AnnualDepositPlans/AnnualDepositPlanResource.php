<?php

namespace App\Filament\Resources\AnnualDepositPlans;

use App\Filament\Resources\AnnualDepositPlans\Pages\CreateAnnualDepositPlan;
use App\Filament\Resources\AnnualDepositPlans\Pages\EditAnnualDepositPlan;
use App\Filament\Resources\AnnualDepositPlans\Pages\ListAnnualDepositPlans;
use App\Filament\Resources\AnnualDepositPlans\Pages\ViewAnnualDepositPlan;
use App\Filament\Resources\AnnualDepositPlans\Schemas\AnnualDepositPlanForm;
use App\Filament\Resources\AnnualDepositPlans\Schemas\AnnualDepositPlanInfolist;
use App\Filament\Resources\AnnualDepositPlans\Tables\AnnualDepositPlansTable;
use App\Models\AnnualDepositPlan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class AnnualDepositPlanResource extends Resource
{
    protected static ?string $model = AnnualDepositPlan::class;


    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChevronRight;
    protected static ?string $navigationLabel = 'Deposit Plan';
    protected static ?string $recordTitleAttribute = 'AnnualDepositPlan';
    protected static string|\UnitEnum|null $navigationGroup = 'Planning';
    

    public static function form(Schema $schema): Schema
    {
        return AnnualDepositPlanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AnnualDepositPlanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnnualDepositPlansTable::configure($table)
            ->deferLoading()
            ->searchable()
            ->paginated([10, 25, 50])
            ->groups([
                Group::make('annualPlan.financialYear.name')
                    ->label('Financial Year')
                    ->collapsible(),

                Group::make('annualPlan.district.name')
                    ->label('District Office')
                    ->collapsible(),

                Group::make('annualPlan.creator.name')
                    ->label('Created By')
                    ->collapsible(),
            ])
            ->defaultGroup(
                Group::make('annualPlan.financialYear.name')
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
            'index' => ListAnnualDepositPlans::route('/'),
           // 'create' => CreateAnnualDepositPlan::route('/create'),
            'view' => ViewAnnualDepositPlan::route('/{record}'),
           // 'edit' => EditAnnualDepositPlan::route('/{record}/edit'),
        ];
    }
}
