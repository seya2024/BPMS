<?php

namespace App\Filament\Resources\UserGroups;

use App\Filament\Resources\UserGroups\Pages\CreateUserGroup;
use App\Filament\Resources\UserGroups\Pages\EditUserGroup;
use App\Filament\Resources\UserGroups\Pages\ListUserGroups;
use App\Filament\Resources\UserGroups\Pages\ViewUserGroup;
use App\Filament\Resources\UserGroups\Schemas\UserGroupForm;
use App\Filament\Resources\UserGroups\Schemas\UserGroupInfolist;
use App\Filament\Resources\UserGroups\Tables\UserGroupsTable;
use App\Models\UserGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class UserGroupResource extends Resource
{
    protected static ?string $model = UserGroup::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;
    protected static ?string $navigationLabel = 'User Groups';
    protected static string|\UnitEnum|null $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return UserGroupForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserGroupInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserGroupsTable::configure($table)
            ->deferLoading()
            ->searchable()
            ->paginated([10, 25, 50])
            ->groups([
                Group::make('is_active')
                    ->label('Status')
                    ->collapsible(),
            ])
            ->defaultGroup(
                Group::make('is_active')
                    ->label('Status')
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
            'index' => ListUserGroups::route('/'),
            // 'create' => CreateUserGroup::route('/create'),
            'view' => ViewUserGroup::route('/{record}'),
            // 'edit' => EditUserGroup::route('/{record}/edit'),
        ];
    }
}
