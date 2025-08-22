<?php

namespace App\Http\Controllers;

use App\Models\TenderInfo;
use App\Models\TimerTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimerTrackerController extends Controller
{
    private $stageDurations = [
        'tender_created' => 72,
        'tender_info_sheet' => 72,
        'tender_approval' => 24,
        'rfq' => 24,
        'physical_docs' => 48,
        'emd_request' => 24
    ];
    public function startNextStage(TenderInfo $tender, $currentStage)
    {
        $currentTimer = TimerTracker::where('tender_id', $tender->id)
            ->where('stage', $currentStage)
            ->first();

        if ($currentTimer) {
            $currentTimer->stopTimer();
        }

        $nextStage = $this->getNextStage($currentStage);
        $duration = $this->stageDurations[$nextStage] ?? 24;

        TimerTracker::create([
            'tender_id' => $tender->id,
            'user_id' => Auth::id(),
            'stage' => $nextStage,
            'start_time' => now(),
            'duration_hours' => $duration,
            'status' => 'pending'
        ]);
    }

    public function checkAllTimers()
    {
        $pendingTimers = TimerTracker::where('status', 'pending')->get();

        foreach ($pendingTimers as $timer) {
            $timer->checkTimeout();
        }
    }

    private function getNextStage($currentStage)
    {
        $stages = array_keys($this->stageDurations);
        $currentIndex = array_search($currentStage, $stages);

        return $stages[$currentIndex + 1] ?? null;
    }
}
