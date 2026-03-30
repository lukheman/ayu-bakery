#!/usr/bin/env php
<?php

require __DIR__.'/helpers.php';

/**
 * Script Setup Project Laravel
 *
 * 1. Composer install
 * 2. Copy .env.example ke .env
 * 3. Generate application key
 * 4. Konfigurasi database MySQL
 * 5. Migrasi database
 * 6. Seeder database
 * 7. Pengecekan akhir & artisan serve
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
//  MULAI SETUP
// ============================================================

$dir = __DIR__;
chdir($dir);

out('');
out('+'.str_repeat('-', 63).'+', 'bold', 'green');
out('|'.str_pad('  SIMKA - SETUP APLIKASI', 63).'|', 'bold', 'green');
out('+'.str_repeat('-', 63).'+', 'bold', 'green');
out('');
info("Lokasi  : {$dir}");
info('PHP     : '.phpversion());
info('OS      : '.PHP_OS_FAMILY.' ('.php_uname('s').' '.php_uname('r').')');

// ============================================================
//  Langkah 0 – Cek Kebutuhan Sistem
// ============================================================

step(0, 'Mengecek Kebutuhan Sistem');

$missing = [];
foreach (['php' => 'PHP CLI', 'composer' => 'Composer'] as $cmd => $label) {
    if (hasCmd($cmd)) {
        ok("{$label}: ".getVer($cmd));
    } else {
        err("{$label} tidak ditemukan!");
        $missing[] = $label;
    }
}

hasCmd('mysql') ? ok('MySQL Client: '.getVer('mysql')) : warn('MySQL Client tidak ditemukan');

if ($missing) {
    err('Kebutuhan kurang: '.implode(', ', $missing));
    exit(1);
}

// ============================================================
//  Langkah 1 – Composer Install
// ============================================================

step(1, 'Menginstall Dependensi Composer');

if (is_dir("{$dir}/vendor")) {
    warn('Folder vendor/ sudah ada, tetap memperbarui...');
}

if (! run('composer install --no-interaction --ignore-platform-reqs')) {
    err('Composer install gagal!');
    exit(1);
}
ok('Dependensi Composer berhasil diinstall!');

// ============================================================
//  Langkah 2 – Copy .env
// ============================================================

step(2, 'Menyiapkan File Environment');

$env = "{$dir}/.env";
$envEx = "{$dir}/.env.example";

if (file_exists($env)) {
    warn('File .env sudah ada, melewati copy.');
} else {
    if (! file_exists($envEx)) {
        err('.env.example tidak ditemukan!');
        exit(1);
    }
    copy($envEx, $env) ? ok('.env berhasil dibuat.') : (err('Gagal membuat .env') & exit(1));
}

// ============================================================
//  Langkah 3 – Generate App Key
// ============================================================

step(3, 'Membuat Application Key');

if (preg_match('/^APP_KEY=base64:.+/m', file_get_contents($env))) {
    warn('Application key sudah ada, dilewati.');
} else {
    run('php artisan key:generate --ansi') ? ok('Application key berhasil dibuat!') : (err('Gagal generate key') & exit(1));
}

// ============================================================
//  Langkah 4 – Konfigurasi Database
// ============================================================

step(4, 'Konfigurasi Database MySQL');

info('Masukkan konfigurasi database:');
out('');
$db = [
    'HOST' => input('Host MySQL', '127.0.0.1'),
    'PORT' => input('Port MySQL', '3306'),
    'DATABASE' => input('Nama Database', 'simka'),
    'USERNAME' => input('Username', 'root'),
    'PASSWORD' => input('Password', ''),
];

foreach ($db as $key => $val) {
    setEnv($env, "DB_{$key}", $val);
}
setEnv($env, 'DB_CONNECTION', 'mysql');

ok('Konfigurasi database tersimpan!');
out('');
foreach ($db as $key => $val) {
    $display = $key === 'PASSWORD' ? ($val ? str_repeat('*', strlen($val)) : '(kosong)') : $val;
    out('     '.str_pad($key, 10).": {$display}", 'cyan');
}
out('');
warn("Pastikan database '{$db['DATABASE']}' sudah dibuat di MySQL!");

// ============================================================
//  Langkah 5 – Migrasi
// ============================================================

step(5, 'Menjalankan Migrasi Database');

if (! run('php artisan migrate --force')) {
    err('Migrasi gagal! Cek koneksi database dan coba lagi.');
    exit(1);
}
ok('Migrasi berhasil!');

// ============================================================
//  Langkah 6 – Seeder
// ============================================================

step(6, 'Menjalankan Seeder Database');

run('php artisan db:seed --force') ? ok('Seeder berhasil!') : (err('Seeder gagal!') & exit(1));

// ============================================================
//  Langkah 7 – Pengecekan Akhir
// ============================================================

step(7, 'Pengecekan Akhir');

if (! file_exists("{$dir}/public/storage")) {
    run('php artisan storage:link') ? ok('Symlink storage dibuat.') : warn('Gagal buat storage link.');
} else {
    ok('Symlink storage sudah ada.');
}

foreach (['config', 'view', 'route'] as $cache) {
    run("php artisan {$cache}:clear");
}

// ============================================================
//  Selesai!
// ============================================================

out('');
out('+'.str_repeat('-', 63).'+', 'bold', 'green');
out('|'.str_pad(' SETUP APLIKASI SIMKA BERHASIL DISELESAIKAN!', 63).'|', 'bold', 'green');
out('+'.str_repeat('-', 63).'+', 'bold', 'green');
out('');
info('Jalankan server dengan: php artisan serve');

showContact();

out(str_repeat('─', 60), 'cyan');
out('  Jalankan server development sekarang? (y/n)', 'bold', 'cyan');
out(str_repeat('─', 60), 'cyan');

$ans = strtolower(trim(fgets(STDIN)));
if ($ans === 'y' || $ans === 'ya') {
    out('');
    info('Server berjalan di http://localhost:8000  |  Ctrl+C untuk berhenti');
    out('');
    passthru('php artisan serve');
} else {
    ok("Setup selesai! Jalankan 'php artisan serve' jika sudah siap.");
}
