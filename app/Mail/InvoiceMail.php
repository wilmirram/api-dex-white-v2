<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $html;
    protected $invoice;

    public function __construct($html, $invoice)
    {
        $this->html = $html;
        $this->invoice = $invoice;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('INVOICE - nº'.$this->invoice)
            ->html($this->html)
            ->attach('storage/termos/'.$this->invoice.'.pdf');
    }
}
