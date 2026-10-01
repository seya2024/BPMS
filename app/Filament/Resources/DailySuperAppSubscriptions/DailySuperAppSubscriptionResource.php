<?php

namespace App\Filament\Resources\DailySuperAppSubscriptions;

use App\Filament\Resources\DailySuperAppSubscriptions\Pages\CreateDailySuperAppSubscription;
use App\Filament\Resources\DailySuperAppSubscriptions\Pages\EditDailySuperAppSubscription;
use App\Filament\Resources\DailySuperAppSubscriptions\Pages\ListDailySuperAppSubscriptions;
use App\Filament\Resources\DailySuperAppSubscriptions\Pages\ViewDailySuperAppSubscription;
use App\Filament\Resources\DailySuperAppSubscriptions\Schemas\DailySuperAppSubscriptionForm;
use App\Filament\Resources\DailySuperAppSubscriptions\Schemas\DailySuperAppSubscriptionInfolist;
use App\Filament\Resources\DailySuperAppSubscriptions\Tables\DailySuperAppSubscriptionsTable;
use App\Models\DailySuperAppSubscription;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class DailySuperAppSubscriptionResource extends Resource
{
    protected static ?string $model = DailySuperAppSubscription::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;
    protected static ?string $navigationLabel = 'Super App Subscriptions';
    protected static ?string $recordTitleAttribute = 'DailySuperAppSubscription';
    protected static string|\UnitEnum|null $navigationGroup = 'Branch Performance';

    public static function form(Schema $schema): Schema
    {
        return DailySuperAppSubscriptionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DailySuperAppSubscriptionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DailySuperAppSubscriptionsTable::configure($table)
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
            'index' => ListDailySuperAppSubscriptions::route('/'),
            // 'create' => CreateDailySuperAppSubscription::route('/create'),
            'view' => ViewDailySuperAppSubscription::route('/{record}'),
            // 'edit' => EditDailySuperAppSubscription::route('/{record}/edit'),
        ];
    }
}