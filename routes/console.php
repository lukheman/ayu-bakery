<?php

use App\Enums\StatusExp;
use App\Models\Persediaan;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $persediaans = Persediaan::whereNotNull('tgl_exp')->get();
    $now = Carbon::now()->startOfDay();

    foreach ($persediaans as $persediaan) {
        $tglExp = Carbon::parse($persediaan->tgl_exp)->startOfDay();
        $sisaHari = $now->diffInDays($tglExp, false);
        $sisaHariRounded = (int) ceil($sisaHari);

        if ($sisaHariRounded > 14) {
            $status = StatusExp::AMAN->value;
        } elseif ($sisaHariRounded >= 0) {
            $status = StatusExp::HAMPIR_EXP->value;
        } else {
            $status = StatusExp::EXPIRED->value;
        }

        if ($status === StatusExp::EXPIRED->value || $sisaHariRounded < 0) {
            $persediaan->delete();
        } else {
            $persediaan->update([
                'sisa_hari' => $sisaHariRounded,
                'status_exp' => $status,
            ]);
        }
    }
})->daily();
