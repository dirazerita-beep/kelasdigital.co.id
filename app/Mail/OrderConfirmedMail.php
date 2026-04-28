<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order->loadMissing(['user', 'product']);
    }

    public function envelope(): Envelope
    {
        $product = $this->order->product?->title ?? 'Produk';

        return new Envelope(
            subject: 'Pembelian Berhasil - '.$product,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.order-confirmed',
            with: [
                'order' => $this->order,
                'user' => $this->order->user,
                'product' => $this->order->product,
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
