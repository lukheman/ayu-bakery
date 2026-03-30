#!/usr/bin/env php
<?php

require __DIR__.'/helpers.php';

/**
 * Script Laravel Development Server
 * Otomatis membuka browser ke http://localhost:8000
 *
 * @author    Akmal
 *
 * @instagram @lukheeman
 *
 * @phone     082250223147
 *
 * @portfolio https://lukheman.github.io/portfolio/
 */

// ============================================================
//  MAIN
// ============================================================

chdir(__DIR__);
$url = 'http://localhost:8000';

out('');
out('+'.str_repeat('-', 63).'+', 'bold', 'green');
out('|'.str_pad('  SIMKA - SERVER', 63).'|', 'bold', 'green');
out('+'.str_repeat('-', 63).'+', 'bold', 'green');
out('');
info('Lokasi Project : '.__DIR__);
info("Server URL     : {$url}");
out('');
ok('Menjalankan server development...');
out('');

// Buka browser di background setelah server sempat start
if (IS_WIN) {
    pclose(popen("ping -n 3 127.0.0.1 > nul && start {$url}", 'r'));
} else {
    exec("(sleep 2 && xdg-open {$url} 2>/dev/null || open {$url} 2>/dev/null) &");
}

passthru('php artisan serve');
