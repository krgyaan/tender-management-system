<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Checklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Helpers\MailHelper;
use Illuminate\Support\Facades\Config;
use App\Models\AccountChecklistReport;
use App\Mail\AccountChecklistReportMail;
use App\Services\ChecklistReportService;

class AccountsChecklistController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $userRole = Auth::user()->role;

        $query = Checklist::with(['responsibleUser', 'accountableUser']);

        if (!in_array($userRole, ['admin', 'coordinator'])) {
            $query->where(function ($query) use ($userId) {
                $query->where('responsibility', $userId)
                    ->orWhere('accountability', $userId);
            });
        }

        $checklists = $query->get();

        $groupedChecklists = null;
        if (in_array($userRole, ['admin', 'coordinator'])) {
            $groupedChecklists = $checklists->groupBy('responsibility');
        } else {
            $groupedChecklists = $checklists->groupBy('frequency');
        }

        $userTasksResponsibility = collect();
        $userTasksAccountability = collect();
        if (!in_array($userRole, ['admin', 'coordinator'])) {
            // All incomplete responsibility tasks for the user
            $userTasksResponsibility = AccountChecklistReport::with('checklist')
                ->where('responsible_user_id', $userId)
                ->whereNull('resp_completed_at')
                ->orderBy('due_date')
                ->get();

            // All incomplete accountability tasks for the user
            $userTasksAccountability = AccountChecklistReport::with(['checklist', 'checklist.responsibleUser'])
                ->where('accountable_user_id', $userId)
                ->whereNull('acc_completed_at')
                ->orderBy('due_date')
                ->get();
        }

        return view('accounts.checklist.index', compact('checklists', 'groupedChecklists', 'userId', 'userRole', 'userTasksResponsibility', 'userTasksAccountability'));
    }

    public function create()
    {
        $users = User::where('role', 'like', 'account%')->where('status', '1')->get();
        return view('accounts.checklist.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'task_name' => 'required|string|max:255',
            'frequency' => 'required|in:Daily,Weekly,Monthly',
            'responsibility' => 'required|exists:users,id',
            'accountability' => 'required|exists:users,id',
            'description' => 'nullable|string',
        ]);

        $frequencyCondition = null;
        if ($request->frequency === 'Weekly') {
            $request->validate([
                'frequency_condition' => 'required|integer|between:0,6',
            ]);
            $frequencyCondition = (int) $request->input('frequency_condition');
        } elseif ($request->frequency === 'Monthly') {
            $request->validate([
                'frequency_condition' => 'required|integer|min:1|max:30',
            ]);
            $frequencyCondition = (int) $request->input('frequency_condition');
        }
        // For Daily: no extra condition

        $data = $request->all();
        $data['frequency_condition'] = $frequencyCondition;

        Checklist::create($data);

        return redirect()->route('checklists.index')->with('success', 'Checklist created successfully.');
    }


    public function edit(Checklist $checklist)
    {
        $users = User::where('role', 'like', 'account%')->where('status', '1')->get();
        return view('accounts.checklist.edit', compact('checklist', 'users'));
    }

    public function update(Request $request, Checklist $checklist)
    {
        $request->validate([
            'task_name' => 'required|string|max:255',
            'frequency' => 'required|in:Daily,Weekly,Monthly,Quarterly,Annual',
            'responsibility' => 'required|exists:users,id',
            'accountability' => 'required|exists:users,id',
            'description' => 'nullable|string',
        ]);

        $frequencyCondition = null;
        if ($request->frequency === 'Weekly') {
            $request->validate([
                'frequency_condition' => 'required|integer|between:0,6',
            ]);
            $frequencyCondition = (int) $request->input('frequency_condition');
        } elseif ($request->frequency === 'Monthly') {
            $request->validate([
                'frequency_condition' => 'required|integer|min:1|max:30',
            ]);
            $frequencyCondition = (int) $request->input('frequency_condition');
        }
        // For Daily: no extra condition

        $data = $request->all();
        $data['frequency_condition'] = $frequencyCondition;

        $checklist->update($data);

        return redirect()->route('checklists.index')->with('success', 'Checklist updated successfully.');
    }

    public function destroy(Checklist $checklist)
    {
        $checklist->delete();

        return redirect()->route('checklists.index')->with('success', 'Checklist deleted successfully.');
    }

    public function storeResponsibilityRemark(Request $request, $id)
    {
        $request->validate([
            'resp_remark' => 'required|string|max:1000',
            'resp_result_file' => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf,xls,xlsx,csv,txt|max:10240',
        ]);

        $remark = $request->input('resp_remark');
        $resultFile = $request->file('resp_result_file');
        $fileName = null;

        if ($resultFile) {
            $fileName = Auth::user()->name . '_task_' . $id . '_' . rand(100, 999) . '.' . $resultFile->getClientOriginalExtension();
            $resultFile->move(public_path('checklist'), $fileName);
        }

        try {
            $userId = Auth::id();
            // Find the oldest incomplete responsibility report for this user and checklist
            $report = AccountChecklistReport::where('id', $id)
                ->where('responsible_user_id', $userId)
                ->whereNull('resp_completed_at')
                ->orderBy('due_date', 'asc')
                ->first();

            if ($report) {
                $now = now();
                
                Log::info('Updating responsibility task', [
                    'task_id' => $id,
                    'user_id' => $userId,
                    'due_date' => $report->due_date,
                    'completion_time' => $now->toDateTimeString()
                ]);
                
                $report->update([
                    'resp_remark' => $remark,
                    'resp_result_file' => $fileName,
                    'resp_completed_at' => $now,
                    'resp_timer' => $now->diffInSeconds($report->due_date),
                ]);
            }

            return redirect()->route('checklists.index')
                ->with('success', 'Responsibility remark saved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error occurred while saving the responsibility remark. ' . $e->getMessage());
        }
    }

    public function storeAccountabilityRemark(Request $request, $id)
    {
        $request->validate([
            'acc_remark' => 'required|string|max:1000',
            'acc_result_file' => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,pdf,xls,xlsx,csv,txt|max:10240',
        ]);

        $remark = $request->input('acc_remark');
        $resultFile = $request->file('acc_result_file');
        $fileName = null;

        if ($resultFile) {
            $fileName = Auth::user()->name . '_task_' . $id . '_' . rand(100, 999) . '.' . $resultFile->getClientOriginalExtension();
            $resultFile->move(public_path('checklist'), $fileName);
        }

        try {
            $userId = Auth::id();
            // Find the oldest incomplete accountability report for this user and checklist
            $report = AccountChecklistReport::where('id', $id)
                ->where('accountable_user_id', $userId)
                ->whereNull('acc_completed_at')
                ->orderBy('due_date', 'asc')
                ->first();

            if ($report) {
                $now = now();
                $report->update([
                    'acc_remark' => $remark,
                    'acc_result_file' => $fileName,
                    'acc_completed_at' => $now,
                    'acc_timer' => $now->diffInSeconds($report->due_date),
                ]);
            }

            return redirect()->route('checklists.index')
                ->with('success', 'Accountability remark saved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error occurred while saving the accountability remark. ' . $e->getMessage());
        }
    }

    public function report(Request $request, $id = null)
    {
        $users = User::where('role', 'LIKE', 'account%')->get();
        $selectedUser = $request->input('user') ?? $id;
        $selectedMonth = $request->input('month') ?? date('Y-m');

        return view('accounts.checklist.report', compact('users', 'selectedUser', 'selectedMonth'));
    }

    public function getTasks(Request $request)
    {
        try {
            $request->validate([
                'user' => 'required|exists:users,id',
                'month' => 'required|date_format:Y-m',
            ]);

            $userId = $request->input('user');
            $month = $request->input('month');
            $startDate = Carbon::parse($month)->startOfMonth()->toDateString();
            $endDate = Carbon::parse($month)->endOfMonth()->toDateString();

            $checklists = $this->getRelevantChecklists($userId, $endDate);
            if ($checklists->isEmpty()) {
                // Return an object with all days of the month as keys, each with empty arrays
                $daysInMonth = Carbon::parse($month)->daysInMonth;
                $result = [];
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = Carbon::parse($month)->day($day)->toDateString();
                    $result[$date] = [
                        'tasks' => [],
                        'accountability_tasks' => [],
                        'total' => 0,
                        'completed' => 0,
                        'percentage' => 0
                    ];
                }
                return response()->json($result);
            }

            $reports = $this->getReportsGroupedByDate($checklists, $startDate, $endDate);
            $result = $this->buildDayWiseResult($checklists, $reports, $userId, $month);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching tasks',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get checklists relevant to the user up to the end date.
     */
    private function getRelevantChecklists($userId, $endDate)
    {
        return Checklist::with(['responsibleUser', 'accountableUser'])
            ->where(function ($q) use ($userId) {
                $q->where('responsibility', $userId)
                    ->orWhere('accountability', $userId);
            })
            ->whereDate('created_at', '<=', $endDate)
            ->get();
    }

    /**
     * Get reports for the checklists grouped by date.
     */
    private function getReportsGroupedByDate($checklists, $startDate, $endDate)
    {
        $checklistIds = $checklists->pluck('id')->toArray();
        return AccountChecklistReport::whereIn('checklist_id', $checklistIds)
            ->whereBetween('due_date', [$startDate, $endDate])
            ->get()
            ->groupBy(fn($report) => Carbon::parse($report->due_date)->toDateString());
    }

    /**
     * Build the day-wise result array for the month.
     */
    private function buildDayWiseResult($checklists, $reports, $userId, $month)
    {
        $result = [];
        $daysInMonth = Carbon::parse($month)->daysInMonth;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::parse($month)->day($day)->toDateString();
            $carbonDate = Carbon::parse($date);
            $dailyReports = $reports->get($date, collect());

            $dailyTasks = [];
            $accountabilityTasks = [];

            foreach ($checklists as $checklist) {
                // Skip tasks that didn't exist on this day
                if (Carbon::parse($checklist->created_at)->toDateString() > $carbonDate->toDateString()) {
                    continue;
                }

                $frequency = strtolower($checklist->frequency);
                $addTask = false;
                if ($frequency === 'daily') {
                    $addTask = true;
                } elseif ($frequency === 'weekly') {
                    $weeklyDay = $checklist->getWeeklyDay();
                    if ($weeklyDay !== null && $carbonDate->dayOfWeek == $weeklyDay) {
                        $addTask = true;
                    }
                } elseif ($frequency === 'monthly') {
                    $monthlyDay = $checklist->getMonthlyDay();
                    if ($monthlyDay && $carbonDate->day == $monthlyDay) {
                        $addTask = true;
                    }
                } elseif ($frequency === 'quarterly' || $frequency === 'annual') {
                    // Extend here if needed
                }
                // Edge case: skip if no valid condition for weekly/monthly
                if (
                    ($frequency === 'weekly' && $checklist->getWeeklyDay() === null) ||
                    ($frequency === 'monthly' && !$checklist->getMonthlyDay())
                ) {
                    continue;
                }

                if (!$addTask) {
                    continue;
                }

                $report = $dailyReports->firstWhere('checklist_id', $checklist->id);

                $isResponsible = $checklist->responsibility == $userId;
                $isAccountable = $checklist->accountability == $userId;

                $completedAt = null;
                $remark = null;
                $file = null;

                if ($report) {
                    if ($isResponsible) {
                        $completedAt = $report?->resp_completed_at ? substr($report->resp_completed_at, 10) : null;
                        $remark = $report->resp_remark;
                        $file = $report->resp_result_file;
                    } elseif ($isAccountable) {
                        $completedAt = $report?->acc_completed_at ? substr($report->acc_completed_at, 10) : null;
                        $remark = $report->acc_remark;
                        $file = $report->acc_result_file;
                    }
                }

                $dailyTasks[] = [
                    'id' => $checklist->id,
                    'task_name' => $checklist->task_name,
                    'frequency' => $checklist->frequency,
                    'responsible_user' => optional($checklist->responsibleUser)->name,
                    'responsible_user_id' => $checklist->responsibility,
                    'accountable_user' => optional($checklist->accountableUser)->name,
                    'accountable_user_id' => $checklist->accountability,
                    'completed_at' => $completedAt,
                    'remark' => $remark,
                    'result_file' => $file,
                ];
            }

            // For accountability tasks, use previous day's dailyTasks
            $prevDate = Carbon::parse($date)->subDay()->toDateString();
            $prevTasks = $result[$prevDate]['tasks'] ?? [];
            foreach ($prevTasks as $task) {
                // Only include if the user is the accountable user
                if (isset($task['accountable_user_id']) && $task['accountable_user_id'] == $userId) {
                    $accountabilityTasks[] = $task;
                }
            }

            $total = count($dailyTasks);
            $completed = collect($dailyTasks)->whereNotNull('completed_at')->count();
            $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;

            $result[$date] = [
                'tasks' => $dailyTasks,
                'accountability_tasks' => $accountabilityTasks,
                'total' => $total,
                'completed' => $completed,
                'percentage' => $percentage
            ];
        }

        return $result;
    }
    
    public function sendEODChecklistReports(ChecklistReportService $service)
    {
        $date = Carbon::today()->toDateString();
        $grouped = $service->getTodayTasksGrouped();
        
        Log::info("Sending EOD checklist reports for date: $date");
        
        $coo = User::where('role', 'coordinator')->first();
        $adminEmails = ['goyal@volksenergie.in', 'arathi@volksenergie.in', 'imran@volksenergie.in'];

        foreach ($grouped as $accountableId => $responsibleGroups) {
            $accountableUser = User::find($accountableId);
            if (!$accountableUser || !$accountableUser->email) {
                Log::warning("Accountable user not found or email missing for ID: $accountableId");
                continue;
            }
            Log::info("Starting for $accountableUser->name (accountable).");

            foreach ($responsibleGroups as $responsibleId => $reports) {
                $responsibleUser = User::find($responsibleId);
  
                Log::info("$responsibleId -> $responsibleUser->name users tasks.");
            
                $tasks = [];
                foreach ($reports as $report) {
                    $tasks[] = [
                        'task_name' => $report->checklist->task_name,
                        'responsible_user' => $responsibleUser ? $responsibleUser->name : '',
                        'accountable_user' => $accountableUser->name,
                        'completed_at' => $report->resp_completed_at ?? $report->acc_completed_at,
                        'remark' => $report->resp_remark ?? $report->acc_remark,
                    ];
                }
                
                // One table for this responsible only
                $singleTablesPayload = [[
                    'responsible_user' => $responsibleUser?->name ?? '',
                    'tasks'            => $tasks,
                ]];
                
                // CC rule based on presence of responsible user 44 in this accountable's bundle
                $cc = $responsibleId === 44
                    ? ['md@comfortinnkarnal.com', 'kainaat@volksenergie.in']
                    : $adminEmails;
        
                Log::info("The CC/To mails for user {$responsibleUser->name}: ", [
                    'cc' => json_encode($cc),
                    'to' => $accountableUser->email
                ]);
        
                Log::info("Sending {$responsibleUser->name}'s report to accountable user: {$accountableUser->email}");
                
                MailHelper::configureMailer('gyan@volksenergie.in', 'tnwmivdctnencbav', $responsibleUser->name);
                $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';
                Mail::mailer($mailer)->to($accountableUser->email)
                    ->cc($cc)
                    ->send(new AccountChecklistReportMail([
                        'date' => $date,
                        'tables' => $singleTablesPayload,
                    ]));
            }

        }

        if (!empty($adminEmails)) {
            $allTasks = [];
            foreach ($grouped as $responsibleGroups) {
                foreach ($responsibleGroups as $reports) {
                    foreach ($reports as $report) {
                        $allTasks[] = [
                            'task_name' => $report->checklist->task_name,
                            'responsible_user' => $report->checklist->responsibleUser->name ?? '',
                            'accountable_user' => $report->checklist->accountableUser->name ?? '',
                            'completed_at' => $report->resp_completed_at ?? $report->acc_completed_at,
                            'remark' => $report->resp_remark ?? $report->acc_remark,
                        ];
                    }
                }
            }
            Log::info("Sending consolidated report email to admin users");
            MailHelper::configureMailer('gyan@volksenergie.in', 'tnwmivdctnencbav', "Gyan");
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';
            Mail::mailer($mailer)->to($adminEmails)
                ->send(new AccountChecklistReportMail([
                    'date' => $date,
                    'tables' => [
                        ['responsible_user' => 'All', 'tasks' => $allTasks]
                    ],
                ]));
        } else {
            Log::warning("No admin emails found to send the consolidated report");
        }
    }
}
