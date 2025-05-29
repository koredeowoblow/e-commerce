<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email_verify_token;

    public function __construct($user)
    {
        // Make sure $user is not null
        if (!$user) {
            throw new \Exception("User is null in SendVerificationMail");
        }

        $this->name = $user->first_name ?? 'User';
        $this->email_verify_token = $user->email_verify_token ?? '';
    }

    public function build()
    {
        $verificationUrl = url('/verify-email?token=' . $this->email_verify_token);

        return $this->subject('Verify Your Email Address')
            ->view('emails.verify-email')
            ->with([
                'name' => $this->name,
                'verificationUrl' => $verificationUrl
            ]);
    }
}
