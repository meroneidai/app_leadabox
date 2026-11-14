<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent to the customer the moment they accept a quotation.
 */
class QuotationAcceptedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Quotation $quotation) {}

    public function envelope(): Envelope
    {
        $company = Company::current();

        return new Envelope(
            subject: __('quotations.accepted_email_subject', [
                'company' => $company?->name ?? config('app.name'),
            ]),
            replyTo: array_filter([$company?->email]),
        );
    }

    public function content(): Content
    {
        $project = $this->quotation->customer
            ->projects()
            ->where('customer_can_view', true)
            ->latest('id')
            ->first();

        return new Content(
            view: 'mail.quotation-accepted',
            with: [
                'company' => Company::current(),
                'project' => $project,
                'projectLink' => $project
                    ? route('public.projects.show', $project->public_token)
                    : null,
                'quotationLink' => route('public.quotations.show', $this->quotation->public_token),
            ],
        );
    }
}
