<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invoice;
use Illuminate\View\View;

/**
 * Customer-facing invoice view. Internal notes and costs are never exposed here.
 */
class PublicInvoiceController extends Controller
{
    public function show(string $token): View
    {
        $invoice = Invoice::query()
            ->where('public_token', $token)
            ->with('lines', 'customer', 'contact')
            ->firstOrFail();

        abort_unless($invoice->status->isIssued(), 404);

        if (! $invoice->viewed_at) {
            $invoice->forceFill(['viewed_at' => now()])->save();
        }

        return view('invoices.public', [
            'invoice' => $invoice,
            'company' => Company::current(),
        ]);
    }
}
