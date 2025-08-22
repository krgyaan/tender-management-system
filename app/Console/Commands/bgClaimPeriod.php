<?php

namespace App\Console\Commands;

use App\Http\Controllers\EmdDashboardController;
use App\Models\EmdBg;
use App\Services\PdfGeneratorService;
use App\Services\TimerService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
class bgClaimPeriod extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:bg-claim-period';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bank Guarantee Claim Period Reminder';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Bank Guarantee Claim Period Reminder started');
        $bgs = EmdBg::where('bg_expiry', '<', now())->get();
        Log::info("BG Cross Expiry Reminder Mail Data: " . json_encode($bgs));
        foreach ($bgs as $bg) {
            $mail = new EmdDashboardController(new TimerService(), new PdfGeneratorService());
            $mail->bgClaimPeriodMail($bg->id);
        }
        $this->info('BG Claim Period Reminder sent successfully');
    }
}
