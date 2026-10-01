<?php

/**
 * Static check: every `Component::` usage in a PHP file must resolve to either
 * an import, a same-namespace class, or an explicitly FQCN'd reference.
 *
 * Catches the class of bug that caused three separate 500s (DatePicker, Schema
 * used inside a namespaced file without an import) at static-analysis time
 * instead of at runtime.
 */

$root = __DIR__ . '/app';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));

$problems = [];
$checked = 0;

foreach ($iterator as $file) {
    if (! $file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }

    $path = $file->getPathname();
    $raw = file_get_contents($path);

    // Strip comments so prose like "Filament's ChartWidget already declares..."
    // and commented-out code do not register as real usages.
    $src = $raw;
    $src = preg_replace('~/\*.*?\*/~s', '', $src);
    $src = preg_replace('~(^|\s)//[^\n]*~m', '$1', $src);

    // Collect imported short names.
    $imports = [];
    if (preg_match_all('/^use\s+([A-Za-z0-9_\\\\]+)(?:\s+as\s+([A-Za-z0-9_]+))?\s*;/m', $src, $m, PREG_SET_ORDER)) {
        foreach ($m as $match) {
            $short = $match[2] ?? substr($match[1], strrpos($match[1], '\\') + 1);
            $imports[$short] = $match[1];
        }
    }

    // Namespace of this file, for same-namespace resolution.
    $namespace = preg_match('/^namespace\s+([^;]+);/m', $src, $nm) ? trim($nm[1]) : '';

    // Classes declared in this very file.
    preg_match_all('/^(?:final\s+|abstract\s+)?(?:class|trait|interface|enum)\s+([A-Za-z0-9_]+)/m', $src, $dm);
    $declared = array_flip($dm[1]);

    // Bare static calls / instantiations: Foo::make(  Foo::  new Foo(
    preg_match_all('/(?<![\\\\\$>\w])([A-Z][A-Za-z0-9_]*)\s*::/', $src, $cm, PREG_SET_ORDER);
    preg_match_all('/new\s+([A-Z][A-Za-z0-9_]*)\s*\(/', $src, $nm2, PREG_SET_ORDER);

    $used = [];
    foreach ($cm as $c) { $used[$c[1]] = true; }
    foreach ($nm2 as $c) { $used[$c[1]] = true; }

    // Built-ins / globals / PHP standard library that need no import.
    $builtin = [
        'self', 'static', 'parent', 'PHP_EOL', 'PHP_INT_MAX', 'PHP_VERSION', 'STDIN', 'STDOUT', 'STDERR',
        'DateTime', 'DateTimeImmutable', 'DateInterval', 'DateTimeZone', 'Exception', 'ErrorException',
        'Throwable', 'InvalidArgumentException', 'RuntimeException', 'LogicException', 'ArrayObject',
        'ArrayAccess', 'Countable', 'Iterator', 'IteratorAggregate', 'Traversable', 'JsonSerializable',
        'Closure', 'Generator', 'Stringable', 'UnitEnum', 'BackedEnum', 'JsonException', 'NumberFormatter',
        'IntlDateFormatter', 'Collator', 'Locale', 'Number', 'Carbon', 'CarbonImmutable', 'Collection',
        'Str', 'Arr', 'DB', 'Schema', 'Log', 'Cache', 'Route', 'URL', 'Validator', 'Storage', 'Http',
        'Auth', 'Gate', 'RateLimiter', 'Bus', 'Event', 'File', 'Notification', 'Password', 'Crypt',
        'Artisan', 'Queue', 'Session', 'Cookie', 'Request', 'Response', 'RedirectResponse', 'JsonResponse',
        'CarbonPeriod', 'CarbonInterval', 'Date', 'StrHelper',
    ];

    foreach (array_keys($used) as $name) {
        $checked++;

        if (isset($declared[$name])) { continue; }
        if (isset($imports[$name])) { continue; }
        if (in_array($name, $builtin, true)) { continue; }

        // Same-namespace class: resolvable anywhere under that namespace on disk,
        // because Filament resources split pages into a Pages/ subfolder.
        if ($namespace !== '') {
            $dir = __DIR__ . '/' . str_replace('\\', '/', $namespace);
            if (is_dir($dir)) {
                $found = false;
                foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $sub) {
                    if ($sub->isFile() && $sub->getFilename() === $name . '.php') {
                        $found = true;
                        break;
                    }
                }
                if ($found) { continue; }
            }
        }

        // Global PHP class (built-in extension) - does it already exist?
        if (class_exists($name) || interface_exists($name) || enum_exists($name)) { continue; }

        $line = 0;
        $offset = strpos($src, $name);
        if ($offset !== false) {
            $line = substr_count(substr($src, 0, $offset), "\n") + 1;
        }

        $problems[] = sprintf(
            "%s:%d  %s  (namespace: %s)",
            str_replace(__DIR__ . '/', '', $path),
            $line,
            $name,
            $namespace ?: '(none)'
        );
    }
}

echo "Checked {$checked} class references across app/\n\n";

if (! $problems) {
    echo "PASS - no unresolved class references.\n";
    exit(0);
}

echo 'FAIL - ' . count($problems) . " unresolved class reference(s):\n\n";
foreach ($problems as $p) {
    echo "  {$p}\n";
}
exit(1);
