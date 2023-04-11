<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVarify extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $token, $redirect_url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token, $redirect_url)
    {
        $this->token = $token;
        $this->redirect_url = $redirect_url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Email Varification')
            ->view('emails.email-varify', ['token' => $this->token, 'redirect_url' => $this->redirect_url]);
    }
}
