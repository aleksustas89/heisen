<?php

namespace App\Models\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Client;

class RestorePassword extends Mailable
{
    use Queueable, SerializesModels;

    public $Client;

    public $newPass = '';

    /**
     * Create a new message instance.
     */
    public function __construct(Client $Client, $newPass)
    {
        $this->Client = $Client;
        $this->newPass = $newPass;
    }


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject:  'Восстановление пароля на сайте ' . env('APP_NAME', false),
        );
    }

    public function build()
    {


        return $this->view('mails.restore-password', ['Client' => $this->Client, 'newPass' => $this->newPass]);
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