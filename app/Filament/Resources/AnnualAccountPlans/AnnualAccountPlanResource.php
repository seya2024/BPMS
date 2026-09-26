<?php

namespace App\Filament\Resources\AnnualAccountPlans;

use App\Filament\Resources\AnnualAccountPlans\Pages\CreateAnnualAccountPlan;
use App\Filament\Resources\AnnualAccountPlans\Pages\EditAnnualAccountPlan;
use App\Filament\Resources\AnnualAccountPlans\Pages\ListAnnualAccountPlans;
use App\Filament\Resources\AnnualAccountPlans\Pages\ViewAnnualAccountPlan;
use App\Filament\Resources\AnnualAccountPlans\Schemas\AnnualAccountPlanForm;
use App\Filament\Resources\AnnualAccountPlans\Schemas\AnnualAccountPlanInfolist;
use App\Filament\Resources\AnnualAccountPlans\Tables\AnnualAccountPlansTable;
use App\Models\AnnualAccountPlan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AnnualAccountPlanResource extends Resource
{
    protected static ?string $model = AnnualAccountPlan::class;

   
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChevronRight;
    protected static ?string $navigationLabel = 'Account Plan';
    protected static ?string $recordTitleAttribute = 'AnnualAccountPlan';
    protected static string|\UnitEnum|null $navigationGroup = 'Planning';
    

    public static function form(Schema $schema): Schema
    {
        return AnnualAccountPlanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AnnualAccountPlanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnnualAccountPlansTable::configure($table);
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
            'index' => ListAnnualAccountPlans::route('/'),
           // 'create' => CreateAnnualAccountPlan::route('/create'),
            'view' => ViewAnnualAccountPlan::route('/{record}'),
            //'edit' => EditAnnualAccountPlan::route('/{record}/edit'),
        ];
    }
}
