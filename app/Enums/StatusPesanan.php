<?php

namespace App\Enums;

enum StatusPesanan: string
{
    case PENDING = 'pending';
    case DITERIMA = 'diterima';
    case DIPACKING = 'dipacking';
    case DIANTAR = 'diantar';
    case SELESAI = 'selesai';
    case DIBATALKAN = 'dibatalkan';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Konfirmasi',
            self::DITERIMA => 'Diterima',
            self::DIPACKING => 'Dipacking',
            self::DIANTAR => 'Diantar',
            self::SELESAI => 'Selesai',
            self::DIBATALKAN => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::DITERIMA => 'info',
            self::DIPACKING => 'primary',
            self::DIANTAR => 'indigo', 
            self::SELESAI => 'success',
            self::DIBATALKAN => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PENDING => 'fas fa-clock',
            self::DITERIMA => 'fas fa-clipboard-check',
            self::DIPACKING => 'fas fa-box',
            self::DIANTAR => 'fas fa-truck-loading',
            self::SELESAI => 'fas fa-check-circle',
            self::DIBATALKAN => 'fas fa-ban',
        };
    }
}
