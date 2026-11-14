<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Bankgiro = 'bankgiro';
    case Swish = 'swish';
    case BankTransfer = 'bank_transfer';
    case Card = 'card';
    case Cash = 'cash';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Bankgiro => __('invoices.method_bankgiro'),
            self::Swish => __('invoices.method_swish'),
            self::BankTransfer => __('invoices.method_bank_transfer'),
            self::Card => __('invoices.method_card'),
            self::Cash => __('invoices.method_cash'),
            self::Other => __('invoices.method_other'),
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
