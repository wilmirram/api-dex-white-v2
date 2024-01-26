<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUserEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $html;

    public function __construct($html)
    {
        $this->html = $html;
    }

    public function build()
    {
        return $this->subject('Troca de email!')
            ->html($this->html);
    }
}
