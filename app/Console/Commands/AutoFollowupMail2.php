<?php

namespace App\Console\Commands;

use App\Http\Controllers\FollowUpsController;
use Illuminate\Console\Command;

class AutoFollowupMail2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:followup-mail2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Automatically Followup Mail to Client Alternate Days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mail = new FollowUpsController();
        $mail->AlternateFollowupMail();
        $this->info('Command executed successfully for Alternate Followup mail.');
    }
}
