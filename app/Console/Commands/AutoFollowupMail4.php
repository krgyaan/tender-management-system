<?php

namespace App\Console\Commands;

use App\Http\Controllers\FollowUpsController;
use Illuminate\Console\Command;

class AutoFollowupMail4 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:followup-mail4';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Automatically Followup Mail to Client Twice a Day';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mail = new FollowUpsController();
        $mail->TwiceADayFollowupMail();
        $this->info('Command executed successfully for Twice a Day Followup mail.');
    }
}
