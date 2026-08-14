<?php

namespace App\Mail;

use App\Models\ContactUs;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewContactUsMessage extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactUs $contactMessage
    ) {}

    public function build()
    {
        return $this
            ->subject('New Contact Message from '  . $this->contactMessage->user->full_name)
            ->replyTo($this->contactMessage->user->email, $this->contactMessage->user->full_name)
            ->view('Emails.contact-us', [
                'studentName' => $this->contactMessage->user->full_name,
                'studentEmail' => $this->contactMessage->user->email,
                'text' => $this->contactMessage->text,
                'sentAt' => $this->contactMessage->created_at->format('Y-m-d H:i'),
            ]);
    }
}
