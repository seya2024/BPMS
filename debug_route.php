<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Filament\Facades\Filament;

$panel = Filament::getPanel('admin');

echo "Panel ID: " . $panel->getId() . "\n";
echo "Panel path: " . $panel->getPath() . "\n\n";

echo "Registered pages:\n";
foreach ($panel->getPages() as $page) {
    echo "  - " . $page . "\n";
}

echo "\nRoute names from panel pages:\n";
foreach ($panel->getPages() as $page) {
    $name = $page::getRouteName($panel);
    echo "  - $name\n";
}

echo "\nFull route collection check:\n";
$routes = app('router')->getRoutes();
$found = false;
foreach ($routes as $route) {
    $name = $route->getName();
    if (str_contains($name, 'daily-account-openings') && str_contains($name, 'batch')) {
        echo "  FOUND: $name\n";
        echo "    URI: " . $route->uri() . "\n";
        echo "    Methods: " . implode(', ', $route->methods()) . "\n";
        $found = true;
    }
}
if (! $found) {
    echo "  NOT FOUND\n";
}