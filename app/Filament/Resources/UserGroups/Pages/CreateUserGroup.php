<?php

namespace App\Filament\Resources\UserGroups\Pages;

use App\Filament\Resources\UserGroups\UserGroupResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserGroup extends CreateRecord
{
    protected static string $resource = UserGroupResource::class;

    protected function afterCreate(): void
    {
        $this->syncGroupPermissions();
    }

    protected function syncGroupPermissions(): void
    {
        $permissions = $this->data['permissions'] ?? [];
        $permissionNames = array_column($permissions, 'name');

        if (!empty($permissionNames)) {
            $this->record->syncPermissions($permissionNames);
        }
    }
}
