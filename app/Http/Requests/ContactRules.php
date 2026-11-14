<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;

/**
 * Validation shared by contact create and update.
 */
class ContactRules
{
    /**
     * @return array<string, mixed>
     */
    public static function shared(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'is_primary' => ['boolean'],
            'receives_quotations' => ['boolean'],
            'receives_invoices' => ['boolean'],
            'receives_project_updates' => ['boolean'],
            'receives_renewals' => ['boolean'],
        ];
    }

    /**
     * @return array<string, bool>
     */
    public static function booleans(Request $request): array
    {
        return [
            'is_primary' => $request->boolean('is_primary'),
            'receives_quotations' => $request->boolean('receives_quotations'),
            'receives_invoices' => $request->boolean('receives_invoices'),
            'receives_project_updates' => $request->boolean('receives_project_updates'),
            'receives_renewals' => $request->boolean('receives_renewals'),
        ];
    }
}
