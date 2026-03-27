<?php

namespace App\Enums;

enum JenisMutasi: string
{
    case MASUK = 'masuk';
    case KELUAR = 'keluar';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::MASUK => 'Masuk',
            self::KELUAR => 'Keluar',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::MASUK => 'success',
            self::KELUAR => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::MASUK => 'fas fa-arrow-down',
            self::KELUAR => 'fas fa-arrow-up',
        };
    }
}
