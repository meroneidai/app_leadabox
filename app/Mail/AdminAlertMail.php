<?php

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Internal alert so nothing a customer does goes unnoticed.
 */
class AdminAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int, array{label: string, value: string}>  $details
     */
    public function __construct(
        public string $headline,
        public string $summary,
        public array $details = [],
        public ?string $actionUrl = null,
        public ?string $actionLabel = null,
        public ?string $quote = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '['.(Company::current()?->name ?? config('app.name')).'] '.$this->headline,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.admin-alert',
            with: ['company' => Company::current()],
        );
    }
}
