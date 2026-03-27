<?php

namespace App\Enums;

enum StatusPembayaran: string
{
    case BELUM_BAYAR = 'belum_bayar';
    case SUDAH_BAYAR = 'sudah_bayar';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::BELUM_BAYAR => 'Belum Bayar',
            self::SUDAH_BAYAR => 'Sudah Bayar',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::BELUM_BAYAR => 'danger',
            self::SUDAH_BAYAR => 'success',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::BELUM_BAYAR => 'fas fa-times-circle',
            self::SUDAH_BAYAR => 'fas fa-check-circle',
        };
    }
}
