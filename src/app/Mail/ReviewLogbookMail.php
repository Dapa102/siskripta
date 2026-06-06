<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReviewLogbookMail extends Mailable
{
    use Queueable, SerializesModels;

    public $logbook;

    public function __construct($logbook)
    {
        $this->logbook = $logbook;
    }

    public function build()
    {
        return $this->subject('Notifikasi Review Bimbingan SKRIPSI')
                    ->view('emails.review-logbook'); // Mengarah ke template bladenya
    }
}
