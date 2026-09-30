<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Filament\Pages\BatchDailyAccountOpenings;
use App\Filament\Resources\DailyAccountOpenings\Pages\ListDailyAccountOpenings;
use Livewire\Livewire;

$user = App\Models\User::where('email', 'seidm2031@gmail.com')->firstOrFail();
auth()->login($user);

echo "=== BatchDailyAccountOpenings ===\n";
$component = Livewire::test(BatchDailyAccountOpenings::class);

$checks = [
    'District Office select' => 'District Office',
    'Business Day picker'    => 'Business Day',
    'Branches section'       => 'Branches',
    'Save All'               => 'Save All',
    'Back button'            => 'Back',
    'Table border style'     => 'border: 1px solid #e5e7eb',
    'Monospace font'         => 'SF Mono',
];

foreach ($checks as $label => $needle) {
    $html = $component->html();
    $ok = str_contains($html, $needle);
    echo '  [' . ($ok ? 'OK  ' : 'FAIL') . "] {$label}\n";
}

// Test with district
$component->set('data.district_id', 1);
$component->set('data.business_day', now()->subDay()->toDateString());

$html = $component->html();
$rowCount = substr_count($html, 'wire:model="data.entries');
echo "\n  Repeater rows rendered: " . ($rowCount > 0 ? 'YES' : 'NO') . " ($rowCount bindings)\n";

echo "\n=== ListDailyAccountOpenings ===\n";
$listComponent = Livewire::test(ListDailyAccountOpenings::class);
$listComponent->call('loadTable');
$listHtml = $listComponent->html();

$listChecks = [
    'Batch Entry button' => 'Batch Entry',
    'Table loaded'       => 'fi-ta-table',
];

foreach ($listChecks as $label => $needle) {
    $ok = str_contains($listHtml, $needle);
    echo '  [' . ($ok ? 'OK  ' : 'FAIL') . "] {$label}\n";
}

echo "\nAll checks complete!\n";