<?php

namespace App\Mail;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InquiryReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Inquiry $inquiry
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New '.($this->inquiry->type === Inquiry::TYPE_BOOKING ? 'booking inquiry' : 'contact message').' — '.config('lakegarda.site_name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.inquiry-received',
        );
    }
}
