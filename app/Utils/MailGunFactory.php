<?php


namespace App\Utils;

use Mailgun\Mailgun;

class MailGunFactory
{
    private $mailgun;
    private $dom = 'em.whiteclub.tech'; //WHITECLUB
    private $key = '8c97a4bcf1ba63a5e4018aac4e5cebb7-af778b4b-1113d204'; //WHITECLUB
    private $from = 'no-replay@whiteclub.tech';

    /*
     * Como instanciar =>
     *      $var = new MailGunFactory();
     *      $var->send('to', 'subject', 'text||html')
     * */

    public function __construct()
    {
        $this->mailgun = Mailgun::create($this->key);
    }

    public function setDom($dom)
    {
        $this->dom = $dom;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    public function setFrom($from)
    {
        $this->from = $from;
    }

    public function send($to, $subject, $html, $attach = null)
    {
        if ($attach){
            $result = $this->mailgun->messages()->send($this->dom, [
                'from' => $this->from,
                'to' => $to,
                'subject' => $subject,
                'html' => $html,
                'attachment' => [
                    [
                        'filePath' => $attach['filePath'],
                        'filename' => $attach['filename']
                    ]
                ]
            ]);
        }else{
            $result = $this->mailgun->messages()->send($this->dom, [
                'from' => $this->from,
                'to' => $to,
                'subject' => $subject,
                'html' => $html
            ]);
        }

        return $result;
    }
}
