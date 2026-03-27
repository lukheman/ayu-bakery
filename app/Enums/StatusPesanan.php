<?php

namespace App\Enums;

enum StatusPesanan: string
{
    case PENDING = 'pending';
    case DIPROSES = 'diproses';
    case SELESAI = 'selesai';
    case DIBATALKAN = 'dibatalkan';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::DIPROSES => 'Diproses',
            self::SELESAI => 'Selesai',
            self::DIBATALKAN => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::DIPROSES => 'primary',
            self::SELESAI => 'success',
            self::DIBATALKAN => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PENDING => 'fas fa-clock',
            self::DIPROSES => 'fas fa-spinner',
            self::SELESAI => 'fas fa-check-circle',
            self::DIBATALKAN => 'fas fa-ban',
        };
    }
}
