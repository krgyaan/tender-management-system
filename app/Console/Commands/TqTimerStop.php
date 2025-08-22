<?php

namespace App\Console\Commands;

use App\Http\Controllers\TQController;
use App\Services\TimerService;
use Illuminate\Console\Command;

class TqTimerStop extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:tq-timer-stop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stop the timer for a TQ Replied of a tender';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $controller = new TQController(new TimerService());
        $controller->autoStopTimer();
        $this->info('Command executed successfully.');
    }
}
