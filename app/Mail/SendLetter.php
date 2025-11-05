<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\IncomingLetter;

class SendLetter extends Mailable
{
    use Queueable, SerializesModels;

    public $letter;
    public $messageBody;
    public $subjectLine;

    /**
     * Create a new message instance.
     */
    public function __construct(IncomingLetter $letter, string $messageBody, string $subjectLine)
    {
        $this->letter = $letter;
        $this->messageBody = $messageBody;
        $this->subjectLine = $subjectLine;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject($this->subjectLine)
                    ->view('emails.incoming_letter')
                    ->with([
                        'letter' => $this->letter,
                        'messageBody' => $this->messageBody,
                    ]);
    }
}
