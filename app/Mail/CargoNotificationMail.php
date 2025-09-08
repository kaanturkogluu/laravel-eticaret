<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\CargoTracking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CargoNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public CargoTracking $cargoTracking;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, CargoTracking $cargoTracking)
    {
        $this->order = $order;
        $this->cargoTracking = $cargoTracking;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Siparişiniz Kargoya Verildi - ' . $this->order->order_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.cargo-notification',
            with: [
                'order' => $this->order,
                'cargoTracking' => $this->cargoTracking,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
