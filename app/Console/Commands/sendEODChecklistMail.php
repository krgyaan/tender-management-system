<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\ChecklistReportService;
use App\Http\Controllers\AccountsChecklistController;

class sendEODChecklistMail extends Command
{
    protected $signature = 'app:send-eod-checklist-mail';

    protected $description = 'Send End of Day Checklist Mail to Accountable users';

    public function handle()
    {
        Log::info('📧 send-eod-checklist-mail command started');
        $today = Carbon::today();
        $dayOfWeek = $today->dayOfWeek;
        if ($dayOfWeek == 0) {
            Log::info('â›” Today is Sunday. Skipping EOD Checklist Mail.');
            $this->info('Today is Sunday. Skipping EOD Checklist Mail.');
            return;
        }
        $mail = new AccountsChecklistController();
        $mail->sendEODChecklistReports(app(ChecklistReportService::class));

        $this->info('EOD Checklist Mail sent successfully.');
    }
}
