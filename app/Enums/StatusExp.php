<?php

namespace App\Enums;

enum StatusExp: string
{
    case AMAN = 'aman';
    case HAMPIR_EXP = 'hampir_exp';
    case EXPIRED = 'expired';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::AMAN => 'Aman',
            self::HAMPIR_EXP => 'Hampir Expired',
            self::EXPIRED => 'Expired',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::AMAN => 'success',
            self::HAMPIR_EXP => 'warning',
            self::EXPIRED => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::AMAN => 'fas fa-check-circle',
            self::HAMPIR_EXP => 'fas fa-exclamation-triangle',
            self::EXPIRED => 'fas fa-times-circle',
        };
    }
}
