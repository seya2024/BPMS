<?php

namespace App\Filament\Resources\BranchDepositPlans;

use App\Filament\Resources\BranchDepositPlans\Pages\CreateBranchDepositPlan;
use App\Filament\Resources\BranchDepositPlans\Pages\EditBranchDepositPlan;
use App\Filament\Resources\BranchDepositPlans\Pages\ListBranchDepositPlans;
use App\Filament\Resources\BranchDepositPlans\Pages\ViewBranchDepositPlan;
use App\Filament\Resources\BranchDepositPlans\Schemas\BranchDepositPlanForm;
use App\Filament\Resources\BranchDepositPlans\Schemas\BranchDepositPlanInfolist;
use App\Filament\Resources\BranchDepositPlans\Tables\BranchDepositPlansTable;
use App\Models\BranchDepositPlan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class BranchDepositPlanResource extends Resource
{
    protected static ?string $model = BranchDepositPlan::class;

   
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChevronRight;
    protected static ?string $navigationLabel = 'Branch Deposit Plan';
    protected static ?string $recordTitleAttribute = 'BranchDepositPlan';
    protected static string|\UnitEnum|null $navigationGroup = 'Cascading Targets';

    public static function form(Schema $schema): Schema
    {
        return BranchDepositPlanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BranchDepositPlanInfolist::configure($schema);
    }


    public static function table(Table $table): Table
{
    return BranchDepositPlansTable::configure($table)
        ->deferLoading()
        ->searchable()
        ->groups([
            Group::make('annualDepositPlan.annualPlan.financialYear.name')  
                ->label('Financial Year'),
            // Group::make('branch.name')
            //     ->label('Branch'),

            Group::make('branch.district.name')
                ->label('District Office'),
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
            'index' => ListBranchDepositPlans::route('/'),
           // 'create' => CreateBranchDepositPlan::route('/create'),
            'view' => ViewBranchDepositPlan::route('/{record}'),
           // 'edit' => EditBranchDepositPlan::route('/{record}/edit'),
        ];
    }
}
