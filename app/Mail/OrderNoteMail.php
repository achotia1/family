<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderNoteMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
        //$this->type = $type; ,$type
    }

    public function build()
    {
        
        $viewName='admin.mail.order-note';
        if($this->data['company']->mail_type=="user"){
            $subject = 'Note Added - '.$this->data['company']->name;
        }else{
            $subject = 'Note Added - '.$this->data['company']->name;
        }
        $this->subject($subject);
        $this->replyTo(config('constants.ADMINEMAIL'),config('ADMINFROMNAME'));

        return $this->view($viewName, ['company' => $this->data['company']]);
    }
}