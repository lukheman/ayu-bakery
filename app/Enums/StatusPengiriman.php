<?php

namespace App\Enums;

enum StatusPengiriman: string
{
    case MENUNGGU = 'menunggu';
    case DIKIRIM = 'dikirim';
    case DITERIMA = 'diterima';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::MENUNGGU => 'Menunggu',
            self::DIKIRIM => 'Dikirim',
            self::DITERIMA => 'Diterima',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::MENUNGGU => 'warning',
            self::DIKIRIM => 'primary',
            self::DITERIMA => 'success',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::MENUNGGU => 'fas fa-clock',
            self::DIKIRIM => 'fas fa-shipping-fast',
            self::DITERIMA => 'fas fa-box-open',
        };
    }
}
