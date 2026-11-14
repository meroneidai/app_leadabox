<?php

namespace App\Enums;

enum CustomerType: string
{
    case Company = 'company';
    case Private = 'private';

    public function label(): string
    {
        return match ($this) {
            self::Company => __('customers.type_company'),
            self::Private => __('customers.type_private'),
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
