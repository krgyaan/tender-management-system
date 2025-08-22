<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Checklist;
use App\Models\AccountChecklistReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\TimerService;

class GenerateChecklistTasks extends Command
{
    protected $signature = 'app:generate-checklist-tasks';

    protected $description = 'Auto-generate Daily/Weekly/Monthly checklist tasks for responsible users.';

    public function handle()
    {
        Log::info('🟢 generate-checklist-tasks command started');
        
        $today = Carbon::today();
        $now = Carbon::now();
        $dueDateResp = $today->copy()->setTime(20, 0, 0); // 8:00 PM today
        $dueDateAcct = $today->copy()->addDay()->setTime(20, 0, 0); // 8:00 PM next day
        $dayOfWeek = $today->dayOfWeek; // 0=Sunday, 6=Saturday
        $dayOfMonth = $today->day;

        Log::info("📆 Today: {$today->toDateString()}, Day of week: {$dayOfWeek}, Day of month: {$dayOfMonth}");

        // Only run on Mon-Sat (skip Sunday)
        if ($dayOfWeek == 0) {
            Log::info('⛔ Today is Sunday. Skipping task creation.');
            $this->info('Today is Sunday. Skipping CRON.');
            return;
        }

        $timerService = app(TimerService::class);
        $respTimer = $timerService->startChecklistTimer($now); // Next 8PM from CRON time
        $accTimer = $timerService->startChecklistTimer($respTimer->copy()->addDay()); // Next 8PM after respTimer (skipping Sunday)

        $checklists = Checklist::all();
        Log::info('📋 Total checklists found: ' . $checklists->count());
        $created = 0;
        $createdAcct = 0;
        
        foreach ($checklists as $checklist) {
            $shouldInsert = false;
            $reason = '';
            if ($checklist->frequency === 'Daily') {
                $shouldInsert = true;
                $reason = 'Daily';
            } elseif ($checklist->frequency === 'Weekly') {
                // Use frequency_condition as weekday integer (0=Sunday,...,6=Saturday)
                if ($checklist->frequency_condition !== null && $dayOfWeek == (int) $checklist->frequency_condition) {
                    $shouldInsert = true;
                    $reason = 'Weekly (matches today)';
                } else {
                    $reason = 'Weekly (does not match today)';
                }
            } elseif ($checklist->frequency === 'Monthly') {
                // Use frequency_condition as day of month (1-30)
                if ($checklist->frequency_condition !== null && $dayOfMonth == (int) $checklist->frequency_condition) {
                    $shouldInsert = true;
                    $reason = 'Monthly (matches today)';
                } else {
                    $reason = 'Monthly (does not match today)';
                }
            } else {
                $reason = 'frequency does not match today';
            }
            Log::info("Checklist #{$checklist->id} [{$checklist->task_name}] frequency: {$checklist->frequency} | Reason: $reason");
            $this->info("Checklist #{$checklist->id} [{$checklist->task_name}] frequency: {$checklist->frequency} | Reason: $reason");
            if ($shouldInsert) {
                // Only create if not already exists for this checklist and due_date
                $exists = AccountChecklistReport::where('checklist_id', $checklist->id)
                    ->where('responsible_user_id', $checklist->responsibility)
                    ->where('accountable_user_id', $checklist->accountability)
                    ->whereDate('due_date', $dueDateResp->toDateString())
                    ->exists();
                if (!$exists) {
                    AccountChecklistReport::create([
                        'checklist_id' => $checklist->id,
                        'responsible_user_id' => $checklist->responsibility,
                        'accountable_user_id' => $checklist->accountability,
                        'due_date' => $dueDateResp,
                        'resp_remark' => '',
                        'resp_result_file' => '',
                        'acc_remark' => '',
                        'acc_result_file' => '',
                        'resp_completed_at' => null,
                        'acc_completed_at' => null,
                        'resp_timer' => $respTimer,
                        'acc_timer' => null,
                    ]);
                    $created++;
                    $this->info("  -> Created checklist task for users {$checklist->responsibility} (resp) & {$checklist->accountability} (acct) due {$dueDateResp}");
                    Log::info("  -> Created checklist task for users {$checklist->responsibility} (resp) & {$checklist->accountability} (acct) due {$dueDateResp}");
                } else {
                    $this->info("  -> Checklist task already exists for users {$checklist->responsibility} & {$checklist->accountability} due {$dueDateResp}");
                    Log::info("  -> Checklist task already exists for users {$checklist->responsibility} & {$checklist->accountability} due {$dueDateResp}");
                }
            } else {
                $this->info("  -> Skipped: $reason");
                Log::info($reason);
            }
        }
        $this->info("Checklist tasks generated: $created");
    }
}
