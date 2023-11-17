<?php

namespace App\Models\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\ShopOrder;

class SendOrderPaymentStatus extends Mailable
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
            subject: 'Статус заказа ' . $this->ShopOrder->id . ' был изменен на оплачен.',
        );
    }

    public function build()
    {


        return $this->view('mails.update-order-status', ['ShopOrder' => $this->ShopOrder]);
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