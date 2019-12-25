<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MoqNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        /*if(count($this->data)>0){
            //Attachment
            if (!empty($this->data['uploaded']) && sizeof($this->data['uploaded']) > 0) 
            {
                foreach ($this->data['uploaded'] as $key=>$attachment) 
                {                               
                    $this->attach($attachment['path']);
                }
            }
        }*/
        // dump('in build');
        // dd($this->data);

        $viewName='admin.mail.moq-notify';
        $subject = 'Store - Moq MoqNotification';
        $this->subject($subject);
        $this->from(config('constants.ADMINEMAIL'),config('ADMINFROMNAME'));
        $this->replyTo(config('constants.ADMINEMAIL'),config('ADMINFROMNAME'));
        return $this->view($viewName, ['data' => $this->data]);
    }
}