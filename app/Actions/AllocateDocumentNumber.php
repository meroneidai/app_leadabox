<?php

namespace App\Actions;

use App\Models\Company;
use Illuminate\Support\Facades\DB;

/**
 * Allocates sequential document numbers. The company row is locked so two
 * users saving at the same moment can never receive the same number.
 */
class AllocateDocumentNumber
{
    public const QUOTATION = 'quotation';

    public const INVOICE = 'invoice';

    public function handle(int $companyId, string $type): string
    {
        return DB::transaction(function () use ($companyId, $type) {
            $company = Company::query()->lockForUpdate()->findOrFail($companyId);

            $prefixColumn = "{$type}_prefix";
            $counterColumn = "{$type}_next_number";

            $number = (int) $company->{$counterColumn};
            $company->forceFill([$counterColumn => $number + 1])->save();

            Company::forgetCurrent();

            return $company->{$prefixColumn}.str_pad((string) $number, 4, '0', STR_PAD_LEFT);
        });
    }
}
