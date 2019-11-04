<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderAddedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $data;

    public function __construct($data,$type)
    {
        $this->data = $data;
        $this->type = $type;
    }

    public function build()
    {
        if(count($this->data)>0){

            //Attachment
            if (!empty($this->data['uploaded']) && sizeof($this->data['uploaded']) > 0) 
            {
                foreach ($this->data['uploaded'] as $key=>$attachment) 
                {                               
                    $this->attach($attachment['path']);
                }
            }
        }
        // dd($this->type);

        if($this->type=="customer"){
            $viewName='admin.mail.customer-order-added';
            $subject = 'Thanks for creating order - '.$this->data['company']->name;
        }else{
            $viewName='admin.mail.order-enquiry';
            $subject = 'New Order Received - '.$this->data['company']->name;
            $this->data['company']->user_type = $this->type;   
        }
        $this->subject($subject);
        $this->replyTo(config('constants.ADMINEMAIL'),config('ADMINFROMNAME'));
        return $this->view($viewName, ['orderinfo' => $this->data['order'],'company' => $this->data['company']]);
    }
}