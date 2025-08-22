<?php

namespace App\Services;

use App\Models\Emds;
use App\Models\User;
use App\Models\TimerTracker;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TimerService
{
    private $stageDurations = [
        'tender_created' => 72,
        'tender_info_sheet' => 24,
        'tender_approval' => 24,
        'document_checklist' => 0,
        'rfq' => 24,
        'physical_docs' => 48,
        'emd_request' => 24,
        'courier_created' => 0,
        'courier_despatched' => 0,
        'pop_acc_form' => 0,
        'bt_acc_form' => 0,
        'cheque_ac_form' => 0,
        'dd_acc_form' => 0,
        'bg_acc_form' => 0,
        'costing_sheet' => 0,
        'costing_sheet_approval' => 0,
    ];

    public function startTimer($tender, string $stage, ?int $customDuration = null)
    {
        // Check if there's already a running timer for this tender and stage
        $existingTimer = TimerTracker::where('tender_id', $tender->id)
            ->where('stage', $stage)
            ->where('status', 'running')
            ->first();

        // If timer already exists, return without creating new one
        if ($existingTimer) {
            Log::info(sprintf(
                'Timer already running for tender #%d stage %s - skipping creation',
                $tender->id,
                $stage
            ));
            return;
        }

        $duration = $customDuration ?? ($this->stageDurations[$stage] ?? 24);           

        $uid = match ($stage) {
            'pop_acc_form', 'bt_acc_form', 'cheque_ac_form', 'dd_acc_form', 'bg_acc_form' =>
            User::whereName(
                Emds::where('id', $tender->emd_id)
                    ->pluck('requested_by')->first()
            )
                ->pluck('id')->first(),
            'courier_created', 'courier_despatched' => $tender->emp_from,
            default => $tender->team_member,
        };

        $timer = TimerTracker::create([
            'tender_id' => $tender->id,
            'user_id' => $uid,
            'stage' => $stage,
            'start_time' => now(),
            'duration_hours' => $duration,
            'status' => 'running'
        ]);

        Log::info(sprintf(
            'Timer started for tender #%d by user %s for stage %s with duration %d hours',
            $uid,
            Auth::user()->name,
            $stage,
            $duration
        ));
    }

    public function getTimerDetails($tender, $stage = null)
    {
        $timer = TimerTracker::where('tender_id', $tender->id)
            ->where('status', 'running');

        if ($stage) {
            $timer = $timer->where('stage', $stage);
        }

        $timer = $timer->first();

        if (!$timer) {
            return null;
        }

        $startTime = strtotime($timer->start_time);
        $durationSeconds = $timer->duration_hours * 3600;
        $endTime = $startTime + $durationSeconds;
        $remainingSeconds = $endTime - time();

        return [
            'start_time' => $timer->start_time,
            'duration_hours' => $timer->duration_hours,
            'remaining_seconds' => $remainingSeconds,
            'is_expired' => $remainingSeconds < 0,
            'stage' => $timer->stage,
            'status' => $timer->status
        ];
    }

    public function stopTimer($tender, $stage)
    {
        $timer = TimerTracker::where('tender_id', $tender->id)
            ->where('stage', $stage)
            ->where('status', 'running')
            ->first();

        if ($timer) {
            $timerDetails = $this->getTimerDetails($tender, $stage);

            $timer->update([
                'end_time' => now(),
                'status' => 'completed',
                'remaining_time' => $timerDetails['remaining_seconds']
            ]);

            Log::info(sprintf(
                'Timer stopped for tender #%d by user %s for stage %s with remaining time %d seconds',
                $tender->id,
                Auth::user()->name,
                $stage,
                $timerDetails['remaining_seconds']
            ));

            return $timerDetails['remaining_seconds'];
        }

        return null;
    }
    
    public function getRemainingTime($tender, $stage, $customEndTime)
    {
        $timer = TimerTracker::where('tender_id', $tender->id)
            ->where('stage', $stage)
            ->where('status', 'running')
            ->first();

        if (!$timer) {
            return null;
        }

        $startTime = strtotime($timer->start_time);
        $durationSeconds = $timer->duration_hours * 3600;
        $expEndTime = $startTime + $durationSeconds;
        $actEndTime = strtotime($customEndTime);
        $remainingSeconds = $expEndTime - $actEndTime;

        Log::info(sprintf(
            'Remaining time for courier #%d by user %s for stage %s is %d seconds',
            $tender->id,
            $tender->user_id,
            $stage,
            $remainingSeconds
        ));
        return ['remaining_seconds' => $remainingSeconds,];
    }

    public function stopTimerOnDifferentTime($tender, $stage, $customEndTime)
    {
        $timer = TimerTracker::where('tender_id', $tender->id)
            ->where('stage', $stage)
            ->where('status', 'running')
            ->first();

        if ($timer) {
            $timerDetails = $this->getRemainingTime($tender, $stage, $customEndTime);

            $timer->update([
                'end_time' => $customEndTime,
                'status' => 'completed',
                'remaining_time' => $timerDetails['remaining_seconds']
            ]);

            Log::info(sprintf(
                'Timer stopped for tender #%d by user %s for stage %s with remaining time %d seconds',
                $tender->id,
                Auth::user()->name,
                $stage,
                $timerDetails['remaining_seconds']
            ));

            return $timerDetails['remaining_seconds'];
        }

        return null;
    }

    public function restartTimer($tender, $stage)
    {
        Log::info(sprintf(
            'Attempting to restart timer for tender #%d and stage %s',
            $tender->id,
            $stage
        ));

        $timer = TimerTracker::where('tender_id', $tender->id)
            ->where('stage', $stage)
            ->where('status', 'completed')
            ->first();

        if ($timer) {
            Log::info(sprintf(
                'Found completed timer for tender #%d and stage %s with remaining time %d seconds',
                $tender->id,
                $stage,
                $timer->remaining_time
            ));

            $timer->update([
                'status' => 'running',
                'end_time' => null
            ]);

            Log::info(sprintf(
                'Timer resumed for tender #%d by user %s for stage %s with remaining time %d seconds',
                $tender->id,
                Auth::user()->name,
                $stage,
                $timer->remaining_time
            ));

            return $timer->remaining_time;
        }

        Log::warning(sprintf(
            'No completed timer found to restart for tender #%d and stage %s',
            $tender->id,
            $stage
        ));

        return null;
    }
    
    public function deleteTimer($tender, $stage)
    {
        $timer = TimerTracker::where('tender_id', $tender->id)
            ->where('stage', $stage)
            ->first();

        if ($timer) {
            $timer->delete();

            Log::info(sprintf(
                'Timer deleted for tender #%d by user %s for stage %s',
                $tender->id,
                Auth::user()->name,
                $stage
            ));

            return true;
        }

        Log::warning(sprintf(
            'No timer found to delete for tender #%d and stage %s',
            $tender->id,
            $stage
        ));

        return false;
    }
    
    public function isTimerRunning($tender, $stage)
    {
        $timer = TimerTracker::where('tender_id', $tender->id)
            ->where('stage', $stage)
            ->where('status', 'running')
            ->first();

        return $timer !== null;
    }
    
        public function startChecklistTimer($fromTime = null)
    {
        $date = $fromTime ? \Carbon\Carbon::parse($fromTime) : now();
        $date->setTime(20, 0, 0); // Set to 8PM
        if ($date->isPast()) {
            $date->addDay();
        }
        while ($date->isSunday()) {
            $date->addDay();
        }
        return $date;
    }

    public function stopChecklistTimer($timerEnd, $forAccountability = false)
    {
        $end = \Carbon\Carbon::parse($timerEnd);
        $now = now();
        $remaining = $end->diffInSeconds($now, false); // negative if overdue
        if (!$forAccountability) {
            // For responsibility, return next 8PM (skipping Sunday) for accountability
            $nextAcc = $end->copy()->addDay()->setTime(20, 0, 0);
            while ($nextAcc->isSunday()) {
                $nextAcc->addDay();
            }
            return [
                'remaining_seconds' => $remaining,
                'next_acc_timer' => $nextAcc
            ];
        } else {
            return [
                'remaining_seconds' => $remaining
            ];
        }
    }
}
