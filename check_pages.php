<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$resources = [
    App\Filament\Resources\Users\UserResource::class,
    App\Filament\Resources\BankingTypes\BankingTypeResource::class,
    App\Filament\Resources\UserGroups\UserGroupResource::class,
    App\Filament\Resources\DailyAccountOpenings\DailyAccountOpeningResource::class,
    App\Filament\Resources\DailyDepositPerformances\DailyDepositPerformanceResource::class,
    App\Filament\Resources\Branches\BranchResource::class,
    App\Filament\Resources\Districts\DistrictResource::class,
    App\Filament\Resources\AnnualPlans\AnnualPlanResource::class,
    App\Filament\Resources\Kpis\KpiResource::class,
    App\Filament\Resources\Permissions\PermissionResource::class,
];

printf("%-72s %-8s %-6s %-6s %-6s\n", 'Resource', 'create', 'edit', 'view', 'index');
echo str_repeat('-', 104) . PHP_EOL;

foreach ($resources as $r) {
    printf(
        "%-72s %-8s %-6s %-6s %-6s\n",
        class_basename($r),
        $r::hasPage('create') ? 'PAGE' : 'modal',
        $r::hasPage('edit') ? 'PAGE' : 'modal',
        $r::hasPage('view') ? 'yes' : 'no',
        $r::hasPage('index') ? 'yes' : 'no',
    );
}
