<?php

namespace App\Models\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\ShopOrder;

class SendOrder extends Mailable
{
    use Queueable, SerializesModels;

    public $ShopOrder;

    /**
     * Create a new message instance.
     */
    public function __construct(ShopOrder $oShopOrder)
    {
        $this->ShopOrder = $oShopOrder;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Новый заказ на сайте ' . env('APP_NAME', false),
        );
    }

    public function build()
    {


        return $this->view('mails.new-order-admin', ['ShopOrder' => $this->ShopOrder]);
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
