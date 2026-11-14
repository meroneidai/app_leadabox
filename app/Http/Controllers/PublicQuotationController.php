<?php

namespace App\Http\Controllers;

use App\Actions\NotifyTeam;
use App\Enums\QuotationStatus;
use App\Mail\QuotationAcceptedMail;
use App\Models\Company;
use App\Models\Quotation;
use App\Support\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

/**
 * Customer-facing quotation approval. No account required; the token is the key.
 * Internal notes and costs are never exposed here.
 */
class PublicQuotationController extends Controller
{
    public function __construct(private readonly NotifyTeam $notifier) {}

    public function show(string $token): View
    {
        $quotation = $this->resolve($token);

        // First open is the closest thing to a delivery receipt we can honestly report.
        if (! $quotation->viewed_at) {
            $quotation->forceFill(['viewed_at' => now()])->save();
        }

        return view('quotations.public', [
            'quotation' => $quotation,
            'company' => Company::current(),
            'comments' => $quotation->publicComments()->with('user', 'contact')->get(),
        ]);
    }

    public function accept(Request $request, string $token): RedirectResponse
    {
        $quotation = $this->resolve($token);

        abort_unless($quotation->canBeAnsweredByCustomer(), 403);

        $validated = $request->validate([
            'decision_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $quotation->forceFill([
            'status' => QuotationStatus::Accepted,
            'accepted_at' => now(),
            'decision_note' => $validated['decision_note'] ?? null,
        ])->save();

        $this->thankCustomer($quotation);

        $this->notifier->handle(
            headline: __('comments.alert_accepted_headline', ['number' => $quotation->reference()]),
            summary: __('comments.alert_accepted_summary', [
                'customer' => $quotation->customer->name,
                'number' => $quotation->reference(),
                'total' => Money::format((string) $quotation->total, $quotation->currency),
            ]),
            details: [
                ['label' => __('quotations.customer'), 'value' => $quotation->customer->name],
                ['label' => __('quotations.total'), 'value' => Money::format((string) $quotation->total, $quotation->currency)],
            ],
            actionUrl: route('quotations.show', $quotation),
            quote: $validated['decision_note'] ?? null,
        );

        return redirect()
            ->route('public.quotations.show', $token)
            ->with('status', __('quotations.public_accepted'));
    }

    public function reject(Request $request, string $token): RedirectResponse
    {
        $quotation = $this->resolve($token);

        abort_unless($quotation->canBeAnsweredByCustomer(), 403);

        $validated = $request->validate([
            'decision_note' => ['required', 'string', 'max:2000'],
        ]);

        $quotation->forceFill([
            'status' => QuotationStatus::Rejected,
            'rejected_at' => now(),
            'decision_note' => $validated['decision_note'],
        ])->save();

        $this->notifier->handle(
            headline: __('comments.alert_rejected_headline', ['number' => $quotation->reference()]),
            summary: __('comments.alert_rejected_summary', [
                'customer' => $quotation->customer->name,
                'number' => $quotation->reference(),
            ]),
            actionUrl: route('quotations.show', $quotation),
            quote: $validated['decision_note'],
        );

        return redirect()
            ->route('public.quotations.show', $token)
            ->with('status', __('quotations.public_rejected'));
    }

    public function comment(Request $request, string $token): RedirectResponse
    {
        $quotation = $this->resolve($token);

        $validated = $request->validate([
            'author_name' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $contact = $quotation->contact ?? $quotation->customer->primaryContact();

        $quotation->addCustomerComment(
            $validated['body'],
            $contact?->id,
            $validated['author_name'] ?? $contact?->name,
        );

        $this->notifier->handle(
            headline: __('comments.alert_quotation_headline', ['number' => $quotation->reference()]),
            summary: __('comments.alert_quotation_summary', [
                'name' => $validated['author_name'] ?? $contact?->name ?? __('comments.customer'),
                'number' => $quotation->reference(),
            ]),
            actionUrl: route('quotations.show', $quotation),
            quote: $validated['body'],
        );

        return back()->with('status', __('comments.thanks'));
    }

    /** A failed thank-you must never undo the acceptance itself. */
    private function thankCustomer(Quotation $quotation): void
    {
        $recipient = $quotation->contact?->email
            ?? $quotation->customer->primaryContact()?->email
            ?? $quotation->customer->email;

        if (! $recipient) {
            return;
        }

        try {
            Mail::to($recipient)->send(new QuotationAcceptedMail($quotation));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function resolve(string $token): Quotation
    {
        $quotation = Quotation::query()
            ->where('public_token', $token)
            ->with('lines', 'customer.contacts', 'contact')
            ->firstOrFail();

        // A draft was never sent, so it must not be reachable from a guessed link.
        abort_if($quotation->status === QuotationStatus::Draft, 404);

        return $quotation;
    }
}
