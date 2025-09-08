<?php

namespace App\Support;

use Illuminate\Mail\Mailable;

class MailRender
{
    public static function html(Mailable $mailable): string
    {
        // Ensures view data is resolved like normal mail sends
        return $mailable->render();
    }
}
