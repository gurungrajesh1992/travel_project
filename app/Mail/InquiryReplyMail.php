<?php

namespace App\Mail;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InquiryReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Inquiry $inquiry, public string $replyMessage) {}

    public function build(): self
    {
        return $this->subject('Re: '.($this->inquiry->subject ?: 'Your inquiry'))
            ->view('emails.inquiry-reply');
    }
}
