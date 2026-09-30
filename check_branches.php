<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Branch;

echo "=== All Branches with Banking Type ===\n\n";

Branch::with('bankingType:id,name')
    ->orderBy('bankingType_id')
    ->orderBy('name')
    ->get(['id', 'name', 'bankingType_id'])
    ->each(function($branch) {
        $type = $branch->bankingType?->name ?? 'NULL';
        print "  [{$branch->id}] {$branch->name} -> bankingType_id={$branch->bankingType_id} ({$type})\n";
    });

echo "\n=== Conventional (bankingType_id=1) ===\n";
Branch::where('bankingType_id', 1)->orderBy('name')->get()->each(function($b) {
    print "  {$b->name}\n";
});

echo "\n=== IFB (bankingType_id=2) ===\n";
Branch::where('bankingType_id', 2)->orderBy('name')->get()->each(function($b) {
    print "  {$b->name}\n";
});