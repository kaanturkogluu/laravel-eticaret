<?php

namespace App\Mail;

use App\Models\Product;
use App\Models\ProductPriceHistory;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PriceDropNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;
    public Product $product;
    public ProductPriceHistory $priceHistory;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Product $product, ProductPriceHistory $priceHistory)
    {
        $this->user = $user;
        $this->product = $product;
        $this->priceHistory = $priceHistory;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Favori Ürününüzde İndirim! - ' . $this->product->ad,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.price-drop-notification',
            with: [
                'user' => $this->user,
                'product' => $this->product,
                'priceHistory' => $this->priceHistory,
                'oldPrice' => $this->priceHistory->old_best_price,
                'newPrice' => $this->priceHistory->new_best_price,
                'discountAmount' => abs($this->priceHistory->price_difference),
                'discountPercentage' => $this->priceHistory->discount_percentage,
                'currencySymbol' => Product::getCurrencySymbolFor($this->product->doviz),
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
