<?php

namespace App\Console\Commands;

use App\Http\Controllers\FollowUpsController;
use Illuminate\Console\Command;

class AutoFollowupMail3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:followup-mail3';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Automatically Followup Mail to Client Weekly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mail = new FollowUpsController();
        $mail->WeeklyFollowupMail();
        $this->info('Command executed successfully for Weekly Followup mail.');
    }
}
