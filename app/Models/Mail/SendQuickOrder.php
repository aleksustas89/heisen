<?php

namespace App\Models\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\ShopQuickOrder;

class SendQuickOrder extends Mailable
{
    use Queueable, SerializesModels;

    public $ShopQuickOrder;

    /**
     * Create a new message instance.
     */
    public function __construct(ShopQuickOrder $oShopQuickOrder)
    {
        $this->ShopQuickOrder = $oShopQuickOrder;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Новый быстрый заказ на сайте ' . env('APP_NAME', false),
        );
    }

    public function build()
    {


        return $this->view('mails.new-quick-order-admin', ['ShopQuickOrder' => $this->ShopQuickOrder]);
    }

    /**
     * Get the message content definition.
     */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'mails.adv'
    //     );
    // }

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
