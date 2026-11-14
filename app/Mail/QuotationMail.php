<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuotationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Quotation $quotation,
        public ?string $customMessage = null,
    ) {}

    public function envelope(): Envelope
    {
        $company = Company::current();

        return new Envelope(
            subject: __('quotations.email_subject', [
                'number' => $this->quotation->reference(),
                'company' => $company?->name ?? config('app.name'),
            ]),
            replyTo: array_filter([$company?->email]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.quotation',
            with: [
                'company' => Company::current(),
                'customMessage' => $this->customMessage,
                'link' => route('public.quotations.show', $this->quotation->public_token),
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $pdf = Pdf::loadView('quotations.pdf', [
            'quotation' => $this->quotation->load('lines', 'customer', 'contact'),
            'company' => Company::current(),
        ])->setPaper('a4');

        return [
            Attachment::fromData(fn () => $pdf->output(), $this->quotation->reference().'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
