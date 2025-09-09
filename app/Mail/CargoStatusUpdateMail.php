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

class CargoStatusUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public CargoTracking $cargoTracking;
    public string $oldStatus;
    public string $newStatus;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, CargoTracking $cargoTracking, string $oldStatus, string $newStatus)
    {
        $this->order = $order;
        $this->cargoTracking = $cargoTracking;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $statusLabels = [
            'created' => 'Kargo Oluşturuldu',
            'picked_up' => 'Kargo Alındı',
            'in_transit' => 'Yolda',
            'out_for_delivery' => 'Dağıtımda',
            'delivered' => 'Teslim Edildi',
            'exception' => 'Sorun Var',
            'returned' => 'İade Edildi',
        ];

        $newStatusLabel = $statusLabels[$this->newStatus] ?? $this->newStatus;

        return new Envelope(
            subject: 'Kargo Durumu Güncellendi - ' . $newStatusLabel . ' - ' . $this->order->order_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.cargo-status-update-simple',
            with: [
                'order' => $this->order,
                'cargoTracking' => $this->cargoTracking,
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->newStatus,
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
