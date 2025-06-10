<?php

namespace App\Mail;

use App\Models\RaMgmt;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RaScheduleNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $ra;

    public function __construct(RaMgmt $ra)
    {
        $this->ra = $ra;
    }

    public function build()
    {
        return $this->view('emails.ra-schedule')
            ->with(['ra' => $this->ra]);
    }
}
