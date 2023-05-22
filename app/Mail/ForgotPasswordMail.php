<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
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
        return $this->subject('パスワードリセットのお知らせ')
            ->view('emails.reset-password', ['token' => $this->token, 'redirect_url' => $this->redirect_url]);
    }
}
