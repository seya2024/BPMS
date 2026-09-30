<?php
// Temporary inspection script - lists fillable fields and relationships per model
$dir = __DIR__ . '/app/Models';

foreach (glob($dir . '/*.php') as $file) {
    $src = file_get_contents($file);
    $name = basename($file, '.php');

    // fillable
    $fillable = [];
    if (preg_match('/\$fillable\s*=\s*\[(.*?)\]/s', $src, $m)) {
        preg_match_all("/['\"]([^'\"]+)['\"]/", $m[1], $f);
        $fillable = $f[1];
    }

    // relationships: public function xxx()... return $this->belongsTo(... etc
    $rels = [];
    if (preg_match_all('/public function (\w+)\(\)\s*(?::[^{]+)?\s*\{\s*\$(?:this->)?(\w+)\(/s', $src, $m, PREG_SET_ORDER)) {
        foreach ($m as $match) {
            if (in_array($match[2], ['belongsTo', 'hasMany', 'belongsToMany', 'hasOne', 'morphMany'])) {
                $rels[$match[1]] = $match[2];
            }
        }
    }

    // also catch `return $this->hasMany(` where method name captured differently
    if (preg_match_all('/public function (\w+)\(\)[^{]*\{[^}]*\$this->(belongsTo|hasMany|hasOne|belongsToMany)\(/s', $src, $m2, PREG_SET_ORDER)) {
        foreach ($m2 as $match) {
            $rels[$match[1]] = $match[2];
        }
    }

    echo "### {$name}\n";
    echo "  fillable: " . implode(', ', $fillable) . "\n";
    echo "  rels: " . (count($rels) ? implode(', ', array_map(fn($k, $v) => "$k($v)", array_keys($rels), $rels)) : '(none)') . "\n";
}
