<?php

namespace App\Console\Commands;

use App\Http\Controllers\FollowUpsController;
use Illuminate\Console\Command;

class AutoMailNow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-mail-now';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending auto mail now';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mail = new FollowUpsController();
        $mail->autoMailNow();
        $this->info('Auto Mail Now sent successfully');
    }
}
