<?php

namespace App\Filament\Resources\UserGroups\Pages;

use App\Filament\Resources\UserGroups\UserGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserGroup extends EditRecord
{
    protected static string $resource = UserGroupResource::class;

    protected function afterSave(): void
    {
        $this->syncGroupPermissions();
    }

    protected function syncGroupPermissions(): void
    {
        $permissions = $this->data['permissions'] ?? [];
        $permissionNames = array_column($permissions, 'name');

        $this->record->syncPermissions($permissionNames);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
