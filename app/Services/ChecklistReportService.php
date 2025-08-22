<?php

namespace App\Services;

use App\Models\AccountChecklistReport;
use Carbon\Carbon;

class ChecklistReportService
{
    public function getTodayTasksGrouped()
    {
        $date = Carbon::today()->toDateString();

        $reports = AccountChecklistReport::with(['checklist.responsibleUser', 'checklist.accountableUser'])
            ->whereDate('due_date', $date)
            ->get();

        // Group by accountable_user, then by responsible_user
        return $reports->groupBy(function ($report) {
            return $report->checklist->accountability;
        })->map(function ($group) {
            return $group->groupBy(function ($report) {
                return $report->checklist->responsibility;
            });
        });
    }
}
