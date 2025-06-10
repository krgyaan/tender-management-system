<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\EmdDashboardController;
    use App\Services\TimerService;
use App\Services\PdfGeneratorService;
class bgExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:bg-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending BG Expiry Reminder';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mail = new EmdDashboardController(new TimerService(), new PdfGeneratorService());
        $mail->bgExpiryReminder();
        $this->info('BG Expiry Reminder sent successfully');
    }
}
