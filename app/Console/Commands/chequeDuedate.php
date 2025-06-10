<?php

namespace App\Console\Commands;

use App\Http\Controllers\EmdDashboardController;
use App\Services\PdfGeneratorService;
use App\Services\TimerService;
use Illuminate\Console\Command;

class chequeDuedate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cheque-duedate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send cheque due date reminders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mail = new EmdDashboardController(new TimerService(), new PdfGeneratorService());
        $mail->chqDueDateReminder();
        $this->info('Cheque due date reminders processed successfully');
    }
}
