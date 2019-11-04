<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderConfirmMail extends Mailable
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
    }

    public function build()
    {
        $viewName='admin.mail.order-confirm';

         if($this->data['user_type']=='user'){
            $subject = 'Your Order is confirmed - '.$this->data['company']->name;
         }elseif($this->data['user_type']=='dispatcher') {
            $subject = 'Order confirmed - '.$this->data['company']->name;
         }
                   
        $this->subject($subject);
        $this->replyTo(config('constants.ADMINEMAIL'),config('ADMINFROMNAME'));
        return $this->view($viewName, ['orderinfo' => $this->data]);
    }
}