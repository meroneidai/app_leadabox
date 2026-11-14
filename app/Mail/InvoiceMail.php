<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Invoice $invoice,
        public ?string $customMessage = null,
        public bool $isReminder = false,
    ) {}

    public function envelope(): Envelope
    {
        $company = Company::current();

        return new Envelope(
            subject: __($this->isReminder ? 'invoices.reminder_subject' : 'invoices.email_subject', [
                'number' => $this->invoice->reference(),
                'company' => $company?->name ?? config('app.name'),
            ]),
            replyTo: array_filter([$company?->email]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.invoice',
            with: [
                'company' => Company::current(),
                'customMessage' => $this->customMessage,
                'link' => route('public.invoices.show', $this->invoice->public_token),
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $this->invoice->load('lines', 'customer', 'contact', 'company'),
            'company' => Company::current(),
        ])->setPaper('a4');

        return [
            Attachment::fromData(fn () => $pdf->output(), $this->invoice->reference().'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
