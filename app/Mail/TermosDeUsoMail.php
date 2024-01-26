<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TermosDeUsoMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $html;

    public function __construct($html)
    {
        $this->html = $html;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Termos de serviços - VG SCHOOL')
                    ->html($this->html)
                    ->attach('storage/termos/termoDeCompra.pdf');
    }
}
