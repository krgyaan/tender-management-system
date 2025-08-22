<?php

namespace App\Console\Commands;

use App\Http\Controllers\FollowUpsController;
use Illuminate\Console\Command;

class AutoFollowupMail5 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:followup-mail5';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Automatically Followup Mail to Client Twice a week';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mail = new FollowUpsController();
        $mail->TwiceAWeekFollowupMail();
        $this->info('Command executed successfully for Twice a week Followup mail.');
    }
}
