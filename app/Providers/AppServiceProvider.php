<?php

namespace App\Providers;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Super Admin has root access - bypass all permission checks
        Gate::before(function ($user, $ability) {
            if (method_exists($user, 'hasRole') && $user->hasRole('super_admin')) {
                return true;
            }
        });

        // Global action styling: all buttons are small and outlined
        Action::configureUsing(function (Action $action) {
            $action->outlined()->size('sm');
        });

        CreateAction::configureUsing(function (CreateAction $action) {
            $action->outlined()->size('sm')->icon('heroicon-o-plus');
        });

        EditAction::configureUsing(function (EditAction $action) {
            $action->outlined()->size('sm')->icon('heroicon-o-pencil-square');
        });

        ViewAction::configureUsing(function (ViewAction $action) {
            $action->outlined()->size('sm')->icon('heroicon-o-eye');
        });

        DeleteAction::configureUsing(function (DeleteAction $action) {
            $action->outlined()->size('sm')->icon('heroicon-o-trash');
        });

        BulkAction::configureUsing(function (BulkAction $action) {
            $action->outlined()->size('sm');
        });

        DeleteBulkAction::configureUsing(function (DeleteBulkAction $action) {
            $action->outlined()->size('sm')->icon('heroicon-o-trash');
        });
    }
}
