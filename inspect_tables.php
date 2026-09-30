<?php
// Temporary inspection script - lists TextColumn/make() fields per table config
$files = [];
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/app/Filament'));
foreach ($rii as $f) {
    if ($f->isDir()) continue;
    if (substr($f->getFilename(), -4) !== '.php') continue;
    $files[] = $f->getPathname();
}

foreach ($files as $file) {
    $src = file_get_contents($file);
    if (!preg_match('/->columns\(\[(.*?)\]\)/s', $src, $m)) continue;

    preg_match_all('/(TextColumn|IconColumn|TextEntry)::make\(\s*[\'"]([^\'"]+)[\'"]/', $m[1], $cols, PREG_SET_ORDER);
    if (!$cols) continue;

    $rel = __DIR__ . '/' . str_replace('\\', '/', substr($file, strlen(__DIR__ . '/')));
    echo "### " . str_replace(__DIR__ . '/', '', $file) . "\n";
    echo "  cols: " . implode(', ', array_map(fn($c) => $c[2], $cols)) . "\n";
}
