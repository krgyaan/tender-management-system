<?php

namespace App\Console\Commands;

use App\Http\Controllers\FollowUpsController;
use Illuminate\Console\Command;

class AutoFollowupMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:followup-mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Automatically Followup Mail to Client Daily';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mail = new FollowUpsController();
        $mail->DailyFollowupMail();
        $this->info('Command executed successfully for Daily Auto Followup mail.');
    }
}
