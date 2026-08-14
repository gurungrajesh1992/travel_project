<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $newsletterSubject, public string $body) {}

    public function build(): self
    {
        return $this->subject($this->newsletterSubject)
            ->view('emails.newsletter');
    }
}
