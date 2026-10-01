<?php
/**
 * Boots the admin panel the way a real request does. Filament resolves every
 * panel page at boot, so a fatal error in ANY page class breaks the whole panel
 * including /admin/login. This catches that class of mistake without a browser.
 */

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$failures = [];
$pages = [];

foreach (['admin'] as $panelId) {
    $panel = Filament\Facades\Filament::getPanel($panelId);

    foreach ($panel->getPages() as $class) {
        // getPages() returns class-string values, not instances.
        $pages[] = $class;

        try {
            // Instantiating forces the class body, including property
            // compatibility checks against the parent.
            new $class();
        } catch (\Throwable $e) {
            $failures[] = $class . ' => ' . $e->getMessage();
        }
    }
}

// Also load every widget, since those are resolved on the dashboard.
$widgetDir = __DIR__ . '/app/Filament/Widgets';
foreach (glob($widgetDir . '/*.php') as $file) {
    $class = 'App\\Filament\\Widgets\\' . basename($file, '.php');

    if (! class_exists($class)) {
        continue;
    }

    $pages[] = $class;

    try {
        new $class();
    } catch (\Throwable $e) {
        $failures[] = $class . ' => ' . $e->getMessage();
    }
}

echo 'classes loaded: ' . count($pages) . "\n";

if ($failures) {
    echo 'FAILURES (' . count($failures) . "):\n";
    foreach ($failures as $f) {
        echo "  {$f}\n";
    }
    exit(1);
}

echo "PASS - every panel page and widget class loads\n";
