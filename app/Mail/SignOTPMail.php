<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SignOTPMail extends Mailable
{
    use Queueable, SerializesModels;
    public $email;
    public $otp;
    public $name;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $otp, $name)
    {
        //
        $this->email = $email;
        $this->otp = $otp;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.signup_otp');
    }
}
