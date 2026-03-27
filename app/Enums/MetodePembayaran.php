<?php

namespace App\Enums;

enum MetodePembayaran: string
{
    case TUNAI = 'tunai';
    case TRANSFER = 'transfer';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::TUNAI => 'Tunai',
            self::TRANSFER => 'Transfer',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::TUNAI => 'success',
            self::TRANSFER => 'primary',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::TUNAI => 'fas fa-money-bill-wave',
            self::TRANSFER => 'fas fa-exchange-alt',
        };
    }
}
