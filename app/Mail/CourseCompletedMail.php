<?php

namespace App\Mail;

use App\Models\Product;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourseCompletedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;

    public Product $product;

    public function __construct(User $user, Product $product)
    {
        $this->user = $user;
        $this->product = $product;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Selamat! Kamu Telah Menyelesaikan '.$this->product->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.course-completed',
            with: [
                'user' => $this->user,
                'product' => $this->product,
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
