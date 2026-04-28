<?php

namespace App\Mail;

use App\Models\Commission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CommissionEarnedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Commission $commission;

    public function __construct(Commission $commission)
    {
        $this->commission = $commission->loadMissing(['earner', 'order.product']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Komisi Masuk - Rp '.number_format((float) $this->commission->amount, 0, ',', '.'),
        );
    }

    public function content(): Content
    {
        $earner = $this->commission->earner;
        $product = optional($this->commission->order)->product;

        return new Content(
            markdown: 'emails.commission-earned',
            with: [
                'commission' => $this->commission,
                'earner' => $earner,
                'product' => $product,
                'balance' => (float) ($earner->balance ?? 0),
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
