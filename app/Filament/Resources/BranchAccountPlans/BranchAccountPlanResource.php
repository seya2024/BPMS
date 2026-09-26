<?php

namespace App\Filament\Resources\BranchAccountPlans;

use App\Filament\Resources\BranchAccountPlans\Pages\CreateBranchAccountPlan;
use App\Filament\Resources\BranchAccountPlans\Pages\EditBranchAccountPlan;
use App\Filament\Resources\BranchAccountPlans\Pages\ListBranchAccountPlans;
use App\Filament\Resources\BranchAccountPlans\Pages\ViewBranchAccountPlan;
use App\Filament\Resources\BranchAccountPlans\Schemas\BranchAccountPlanForm;
use App\Filament\Resources\BranchAccountPlans\Schemas\BranchAccountPlanInfolist;
use App\Filament\Resources\BranchAccountPlans\Tables\BranchAccountPlansTable;
use App\Models\BranchAccountPlan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class BranchAccountPlanResource extends Resource
{
    protected static ?string $model = BranchAccountPlan::class;

 
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChevronRight;
    protected static ?string $navigationLabel = 'Branch Account Plan';
    protected static ?string $recordTitleAttribute = 'BranchAccountPlan';
    protected static string|\UnitEnum|null $navigationGroup = 'Cascading Targets';

    public static function form(Schema $schema): Schema
    {
        return BranchAccountPlanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BranchAccountPlanInfolist::configure($schema);
    }

    // public static function table(Table $table): Table
    // {
    //     return BranchAccountPlansTable::configure($table);
    // }


public static function table(Table $table): Table
{
    return BranchAccountPlansTable::configure($table)
        ->deferLoading()
        ->searchable()
        ->groups([
            Group::make('annualAccountPlan.annualPlan.financialYear.name')  
                ->label('Financial Year'),
        //  Group::make('branch.name')
        //         ->label('Branch'),

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
            'index' => ListBranchAccountPlans::route('/'),
            //'create' => CreateBranchAccountPlan::route('/create'),
            'view' => ViewBranchAccountPlan::route('/{record}'),
           // 'edit' => EditBranchAccountPlan::route('/{record}/edit'),
        ];
    }
}
