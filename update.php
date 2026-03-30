#!/usr/bin/env php
<?php

require __DIR__.'/helpers.php';

/**
 * Script Update Project Laravel
 *
 * 1. Git pull perubahan terbaru
 * 2. Composer install jika ada perubahan dependencies
 * 3. Migrasi database
 * 4. Bersihkan cache
 * 5. Optimasi aplikasi
 *
 * @author    Akmal
 *
 * @instagram @lukheeman
 *
 * @phone     082250223147
 *
 * @portfolio https://lukheman.github.io/portfolio/
 */
$dir = __DIR__;
$start = microtime(true);
chdir($dir);

out('');
out('+'.str_repeat('-', 63).'+', 'bold', 'green');
out('|'.str_pad('  SIMKA - SCRIPT UPDATE APLIKASI', 63).'|', 'bold', 'green');
out('+'.str_repeat('-', 63).'+', 'bold', 'green');
out('');
info("Lokasi : {$dir}");
info('Waktu  : '.date('Y-m-d H:i:s'));

// ============================================================
//  Langkah 0 – Cek Kebutuhan
// ============================================================

step(0, 'Mengecek Kebutuhan');

if (! hasCmd('git')) {
    err('Git tidak ditemukan!');
    exit(1);
} ok('Git ditemukan');
if (! is_dir("$dir/.git")) {
    err('Bukan repository Git!');
    exit(1);
} ok('Repository Git terdeteksi');
if (! hasCmd('composer')) {
    err('Composer tidak ditemukan!');
    exit(1);
} ok('Composer ditemukan');

// ============================================================
//  Langkah 1 – Status Git
// ============================================================

step(1, 'Mengecek Status Git');

$branch = shell('git branch --show-current');
info("Branch saat ini: {$branch}");

$status = shell('git status --porcelain');
if (! empty($status)) {
    warn('Ada perubahan lokal yang belum di-commit:');
    out((USE_COLOR ? C['yellow'] : '').$status.(USE_COLOR ? C['reset'] : ''));
    out('');
    warn('Perubahan lokal akan tetap dipertahankan.');
    out('');
}

// ============================================================
//  Langkah 2 – Git Pull
// ============================================================

step(2, 'Mengambil Pembaruan dari Repository (Git Pull)');

$hashBefore = shell('git rev-parse HEAD');

if (! run('git pull')) {
    err('Git pull gagal!');
    out('');
    warn('Kemungkinan penyebab:');
    out('  1. Tidak ada koneksi internet', 'yellow');
    out('  2. Ada konflik dengan perubahan lokal', 'yellow');
    out('  3. Remote repository tidak tersedia', 'yellow');
    exit(1);
}

$hashAfter = shell('git rev-parse HEAD');

if ($hashBefore === $hashAfter) {
    ok('Project sudah versi terbaru, tidak ada pembaruan.');
} else {
    ok('Pembaruan berhasil diambil!');
    info('Commit baru:');
    out((USE_COLOR ? C['cyan'] : '').shell("git log --oneline {$hashBefore}..{$hashAfter}").(USE_COLOR ? C['reset'] : ''));
}

// ============================================================
//  Langkah 3 – Composer Install (jika ada perubahan)
// ============================================================

step(3, 'Update Dependencies Composer');

$composerUpdated = false;

if ($hashBefore !== $hashAfter) {
    $changed = shell("git diff --name-only {$hashBefore} {$hashAfter}");

    if (str_contains($changed, 'composer.json') || str_contains($changed, 'composer.lock')) {
        info('Terdeteksi perubahan composer.json/composer.lock, menjalankan install...');

        if (! run('composer install --no-interaction --optimize-autoloader')) {
            err('Composer install gagal!');
            exit(1);
        }

        $composerUpdated = true;
        ok('Dependencies Composer berhasil diupdate!');
    } else {
        ok('Tidak ada perubahan dependencies, melewati composer install.');
    }
} else {
    ok('Tidak ada update, melewati composer install.');
}

// ============================================================
//  Langkah 4 – Migrasi Database
// ============================================================

step(4, 'Menjalankan Migrasi Database');

if (! run('php artisan migrate:fresh --seed')) {
    err('Migrasi database gagal!');
    out('');
    warn('Kemungkinan penyebab:');
    out('  1. Database tidak terhubung', 'yellow');
    out('  2. Kredensial database pada .env salah', 'yellow');
    out('  3. Ada error pada file migrasi', 'yellow');
    exit(1);
}
ok('Migrasi database selesai!');

// ============================================================
//  Langkah 5 – Bersihkan Cache
// ============================================================

step(5, 'Membersihkan Cache Aplikasi');

foreach (['config', 'route', 'view', 'cache'] as $cache) {
    run("php artisan {$cache}:clear");
}
run('php artisan clear-compiled');

ok('Semua cache berhasil dibersihkan!');

// ============================================================
//  Langkah 6 – Optimasi
// ============================================================

step(6, 'Mengoptimasi Aplikasi');

run('composer dump-autoload --optimize');
ok('Aplikasi berhasil dioptimasi!');

// ============================================================
//  Selesai!
// ============================================================

$duration = round(microtime(true) - $start, 2);

out('');
out('+'.str_repeat('-', 63).'+', 'bold', 'green');
out('|'.str_pad('  ✅  UPDATE PROJECT BERHASIL!', 63).'|', 'bold', 'green');
out('+'.str_repeat('-', 63).'+', 'bold', 'green');
out('');
info("Waktu proses: {$duration} detik");
info('Project berhasil diupdate ke versi terbaru!');
out('');

// Ringkasan
$composerStatus = $composerUpdated ? 'Selesai' : 'Dilewati (tidak ada perubahan)';
out('  Ringkasan Update:', 'bold', 'cyan');
out('  ├─ Git Pull            : Selesai', 'cyan');
out("  ├─ Composer Install    : {$composerStatus}", 'cyan');
out('  ├─ Database Migration  : Selesai', 'cyan');
out('  ├─ Clear Cache         : Selesai', 'cyan');
out('  └─ Optimasi            : Selesai', 'cyan');
out('');
out('  Untuk menjalankan aplikasi, gunakan:', 'yellow');
out('  php serve.php  atau  php artisan serve');
out('');

showContact();
