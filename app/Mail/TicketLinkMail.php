<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public string $publicUrl,
        public ?string $messageText = null,
        public ?string $qrUrl = null,
        public ?string $pdfBinary = null,
        public ?string $pdfFilename = null,
    ) {}

    public function build()
    {
        $code = strtoupper(substr((string)$this->ticket->token, 0, 8));

        $mail = $this->subject("Tu boleto ($code)")
            ->view('emails.ticket-link', [
                'code'        => $code,
                'publicUrl'   => $this->publicUrl,
                'messageText' => $this->messageText,
                'qrUrl'       => $this->qrUrl,
            ]);

        if ($this->pdfBinary) {
            $name = $this->pdfFilename ?: "boleto-{$code}.pdf";
            $mail->attachData($this->pdfBinary, $name, ['mime' => 'application/pdf']);
        }

        return $mail;
    }
}
