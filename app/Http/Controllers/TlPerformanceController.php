<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\TenderInfo;
use App\Models\Emds;
use Illuminate\Support\Facades\Log;
use App\Services\TenderingPerformance;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class TlPerformanceController extends Controller
{
    protected TenderingPerformance $tlPerformanceService;

    public function __construct(TenderingPerformance $tlPerformanceService)
    {
        $this->tlPerformanceService = $tlPerformanceService;
    }

    public function performance(Request $request)
    {
        $users = User::where('role', 'team-leader')->orWhere('role', 'operation-leader')->orWhere('role', 'account-leader')->get();
        $result = true;

        // Get the current authenticated user
        $currentUser = auth()->user();

        if ($request->method() == 'GET') {
            $result = false;
            // Set default team member to current user if they are a team leader
            $defaultTeamMember = null;
            if ($currentUser && in_array($currentUser->role, ['team-leader', 'operation-leader', 'account-leader'])) {
                $defaultTeamMember = $currentUser->id;
            }
            return view('performance.tl', compact('users', 'result', 'defaultTeamMember'));
        }

        if ($request->method() == 'POST') {
            $team_member = $request->team_member;
            $from = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : null;
            $to = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : null;
            $performance_mode = $request->input('performance_mode', 'team-leader'); // Default to team-leader

            $user = User::find($team_member);
            $user_role = $user->role;
            $user_team = $user->team;

            Log::info("Step 1: Getting Team Leader ID: $team_member, Mode: $performance_mode");

            if ($performance_mode == 'team') {
                $TotalEmpPerformanceTE = $this->tlPerformanceService->getPerformanceTasksByRole('tender-executive');
                $TotalEmpPerformanceTL = $this->tlPerformanceService->getPerformanceTasksByRole('team-leader');
                $TotalEmpPerformanceTask = array_merge($TotalEmpPerformanceTE, $TotalEmpPerformanceTL);
            } else {
                $TotalEmpPerformanceTask = $this->tlPerformanceService->getPerformanceTasksByRole($user_role);
            }
            // Log::info("Doable Task for $performance_mode: " . json_encode($TotalEmpPerformanceTask));
            $doableStages = count($TotalEmpPerformanceTask);

            // Helper function to apply date filter
            $dateFilter = function ($query) use ($from, $to) {
                if ($from && $to) {
                    $query->whereHas('bs', function ($q) use ($from, $to) {
                        $q->whereBetween('bid_submissions_date', [$from, $to]);
                    });
                }
            };

            // Get bided tenders based on performance mode
            $bidedTenders = DB::table('bid_submissions as bs')
                ->leftJoin('tender_infos as ti', 'bs.tender_id', '=', 'ti.id')
                ->when($from && $to, function ($query) use ($from, $to, $user_team, $performance_mode, $team_member) {
                    $query->where(function ($q) use ($from, $to, $user_team, $performance_mode, $team_member) {
                        $q->where(function ($sub) use ($from, $to, $user_team, $performance_mode, $team_member) {
                            $sub->where('bs.status', 'Bid Submitted')
                                ->whereBetween('bs.bid_submissions_date', [$from, $to])
                                ->where('ti.team', $user_team);

                            // If team mode, include all team members; if team-leader mode, only include the specific team leader
                            // Get all team members for this team
                            $teamMembers = User::where('team', $user_team)->pluck('id')->toArray();
                            if ($performance_mode === 'team') {
                                $sub->whereIn('ti.team_member', $teamMembers);
                            } else {
                                $sub->whereIn('ti.team_member', $teamMembers);
                            }
                        })
                            ->orWhere(function ($sub) use ($from, $to, $user_team, $performance_mode, $team_member) {
                                $sub->where('bs.status', 'Tender Missed')
                                    ->whereBetween('ti.due_date', [$from, $to])
                                    ->where('ti.team', $user_team);

                                // If team mode, include all team members; if team-leader mode, only include the specific team leader
                                if ($performance_mode === 'team') {
                                    // Get all team members for this team
                                    $teamMembers = User::where('team', $user_team)->pluck('id')->toArray();
                                    $sub->whereIn('ti.team_member', $teamMembers);
                                } else {
                                    $sub->where('ti.team_member', $team_member);
                                }
                            });
                    });
                })
                ->pluck('ti.id')
                ->all();

            Log::info("Total Bided Tenders: " . count($bidedTenders));

            // Get total tenders with eager loading
            $totalTenders = TenderInfo::whereIn('id', $bidedTenders)
                ->where('team', $user_team)
                ->tap($dateFilter)
                ->get();

            $totalTenderCount = $totalTenders->count();
            Log::info("Step 2: Getting Total Tenders: $totalTenderCount");

            $emds = [
                '1' => 'Demand Draft',
                '2' => 'FDR',
                '3' => 'Cheque',
                '4' => 'BG',
                '5' => 'Bank Transfer',
                '6' => 'Pay on Portal',
            ];

            $tenderWithEmd = [];

            $tenderStatusCounts = $this->classifyBidedTenders($totalTenders);
            $emdsByTenderNo = Emds::with([
                'emdDemandDrafts',
                'emdFdrs',
                'emdCheques',
                'emdBgs',
                'emdBankTransfers',
                'emdPayOnPortals',
            ])->get()->keyBy('tender_no');
        }

        foreach ($totalTenders as $tender) {
            Log::info("Processing Tender ID {$tender->id} - Status {$tender->status}");

            if ($tender->emd > 0) {
                $emdParsed = $this->parseEmdDetailsFromCollection($tender, $emdsByTenderNo);
                Log::info("Get Emds for {$tender->id}-{$tender->tender_no}: " . json_encode($emdParsed));
                $tenderWithEmd[$tender->id]['emds'] = $emdParsed['emds'];
                $tenderWithEmd[$tender->id]['emdback'] = $emdParsed['emdback'];
            }
        }

        // Get tender IDs for doable stages
        $tenderIds = $totalTenders->pluck('id')->toArray();
        Log::info("Step 3: Getting Doable Stages by ", [$user_role => $TotalEmpPerformanceTask]);

        $stepsPerTender = $this->tlPerformanceService->getStepsPerTender($tenderIds, $TotalEmpPerformanceTask);
        Log::info("Step 5: Getting Executed Stages for Tender: ", $stepsPerTender->toArray());

        $stepsPerTenderOnTime = $this->tlPerformanceService->getStepsPerTenderOnTime($tenderIds, $TotalEmpPerformanceTask);
        Log::info("Step 6: Getting Executed Stages with Time for Tender: ", $stepsPerTenderOnTime->toArray());

        $summaryData = $this->tlPerformanceService->getStepsSummary(
            $tenderIds,
            $TotalEmpPerformanceTask,
            $user_role,
        );

        // Access individual parts:
        $skippedStages = $summaryData['skippedStages'] ?? 0;
        $stepsPerTender = $summaryData['stepsPerTender'] ?? 0;
        $stepsPerTenderOnTime = $summaryData['stepsPerTenderOnTime'] ?? 0;
        $mergedDoneStages = $summaryData['mergedDoneStages'] ?? 0;
        $overallSummary = $summaryData['overallSummary'] ?? 0;
        $performanceData = $this->preparePerformanceData(
            $tenderStatusCounts,
            $tenderWithEmd,
            $mergedDoneStages,
            $overallSummary,
            $user_role,
            $totalTenders,
            $TotalEmpPerformanceTask,
            $performance_mode,
            $stepsPerTenderOnTime
        );

        return view('performance.tl', compact(
            'team_member',
            'result',
            'users',
            'totalTenderCount',
            'tenderStatusCounts',
            'tenderWithEmd',
            'mergedDoneStages',
            'overallSummary',
            'stepsPerTender',
            'doableStages',
            'performanceData',
            'performance_mode'
        ));
    }

    private function classifyBidedTenders(Collection $tenders): array
    {
        $statusGroups = [
            'missed_tender' => [8, 16],
            'disqualified_tender' => [21, 22],
            'result_awaited' => [17],
            'lost_tender' => [24],
            'won_tender' => [25, 26, 27, 28],
        ];

        // Start with all tenders as bidded
        $results = [
            'bid_tender' => ['tender' => [], 'count' => 0, 'value' => 0],
            'missed_tender' => ['tender' => [], 'count' => 0, 'value' => 0],
            'disqualified_tender' => ['tender' => [], 'count' => 0, 'value' => 0],
            'result_awaited' => ['tender' => [], 'count' => 0, 'value' => 0],
            'lost_tender' => ['tender' => [], 'count' => 0, 'value' => 0],
            'won_tender' => ['tender' => [], 'count' => 0, 'value' => 0],
        ];

        foreach ($tenders as $tender) {
            // Always counted in bid_tender
            $results['bid_tender']['count']++;
            $results['bid_tender']['value'] += $tender->sheet?->final_price;
            $results['bid_tender']['tender'][] = $tender->tender_name;

            // Classify into specific buckets
            foreach ($statusGroups as $label => $statuses) {
                if (in_array($tender->status, $statuses)) {
                    $results[$label]['count']++;
                    $results[$label]['value'] += $tender->sheet?->final_price;
                    $results[$label]['tender'][] = $tender->tender_name;
                    break; // one tender falls in one group only
                }
            }
        }

        return $results;
    }

    private function parseEmdDetailsFromCollection($tender, $emdsByTenderNo): array
    {
        $result = ['emds' => 0, 'emdback' => 0];

        $emds = $emdsByTenderNo[$tender->tender_no] ?? null;
        if (!$emds) {
            Log::warning("EMDs not found for tender", ['tender_no' => $tender->tender_no]);
            return $result;
        }

        $type = $emds->instrument_type ?? 0;
        Log::info("Tender Emd: ", ['tender_no' => $tender->tender_no, 'instrument_type' => $type]);

        switch ($type) {
            case 1: // Demand Draft
                $dd = $emds->emdDemandDrafts->whereNotNull('dd_no')->whereNotNull('action')->first();
                if ($dd) {
                    Log::info("Demand Draft found for ", ['tender' => $tender->id]);
                    $result['emds'] += $dd->dd_amt;
                    if (in_array($dd->action, [3, 4, 5])) {
                        Log::info("Demand Draft Back");
                        $result['emdback'] += $dd->dd_amt;
                    }
                }
                break;

            case 2: // FDR
                $fdr = $emds->emdFdrs->where('status', 'Accepted')->whereNotNull('action')->first();
                if ($fdr)
                    $result['emds'] += $fdr->fdr_amt;
                break;

            case 3: // Cheque
                $cheque = $emds->emdCheques->where('status', 'Accepted')->whereNotNull('action')->first();
                if ($cheque) {
                    Log::info("Cheque found for ", ['tender' => $tender->id]);
                    $result['emds'] += $cheque->cheque_amt;
                }
                break;

            case 4: // BG
                $bg = $emds->emdBgs->where('bg_req', 'Accepted')->whereNotNull('action')->first();
                if ($bg) {
                    Log::info("BG found for ", ['tender' => $tender->id]);
                    $result['emds'] += $bg->bg_amt;
                    if ($bg->action == 6) {
                        Log::info("BG Back");
                        $result['emdback'] += $bg->bg_amt;
                    }
                }
                break;

            case 5: // Bank Transfer
                $bt = $emds->emdBankTransfers->where('status', 'Accepted')->whereNotNull('action')->first();
                if ($bt) {
                    Log::info("Bank Transfer found for ", ['tender' => $tender->id]);
                    $result['emds'] += $bt->bt_amount;
                    if (in_array($bt->action, [3, 4])) {
                        Log::info("Bank Transfer Back");
                        $result['emdback'] += $bt->bt_amount;
                    }
                }
                break;

            case 6: // Pay on Portal
                $pop = $emds->emdPayOnPortals->where('status', 'Accepted')->whereNotNull('action')->first();
                if ($pop) {
                    Log::info("Pay on Portal found for ", ['tender' => $tender->id]);
                    $result['emds'] += $pop->amount;
                    if (in_array($pop->action, [3, 4])) {
                        Log::info("Pay on Portal Back");
                        $result['emdback'] += $pop->amount;
                    }
                }
                break;
        }

        return $result;
    }

    private function preparePerformanceData($tenderStatusCounts, $tenderWithEmd, $mergedDoneStages, $overallSummary, $role, $totalTenders, $roleStages = [], $performance_mode = 'team-leader', $stepsPerTenderOnTime): array
    {
        $performanceData = [
            'tender_statistics' => [
                'summary' => [
                    'total_count' => $tenderStatusCounts['bid_tender']['count'] ?? 0,
                    'total_value' => $tenderStatusCounts['bid_tender']['value'] ?? 0,
                    'missed_count' => $tenderStatusCounts['missed_tender']['count'] ?? 0,
                    'disqualified_count' => $tenderStatusCounts['disqualified_tender']['count'] ?? 0,
                    'awaiting_result_count' => $tenderStatusCounts['result_awaited']['count'] ?? 0,
                    'lost_count' => $tenderStatusCounts['lost_tender']['count'] ?? 0,
                    'won_count' => $tenderStatusCounts['won_tender']['count'] ?? 0
                ],
                'tender_details' => array_merge(
                    array_map(fn($tender) => ['status' => 'bidded'], $tenderStatusCounts['bid_tender']['tender'] ?? []),
                    array_map(fn($tender) => ['status' => 'missed'], $tenderStatusCounts['missed_tender']['tender'] ?? []),
                    array_map(fn($tender) => ['status' => 'disqualified'], $tenderStatusCounts['disqualified_tender']['tender'] ?? []),
                    array_map(fn($tender) => ['status' => 'awaiting'], $tenderStatusCounts['result_awaited']['tender'] ?? []),
                    array_map(fn($tender) => ['status' => 'lost'], $tenderStatusCounts['lost_tender']['tender'] ?? []),
                    array_map(fn($tender) => ['status' => 'won'], $tenderStatusCounts['won_tender']['tender'] ?? [])
                )
            ],
            'tender_wise_details' => []
        ];

        // Prepare tender-wise detailed information
        if (empty($mergedDoneStages)) {
            $performanceData['overall_metrics'] = [
                'total_tenders' => 0,
                'total_stages_possible' => 0,
                'total_stages_completed' => 0,
                'total_stages_on_time' => 0,
                'overall_completion_rate' => 0,
                'overall_on_time_rate' => 0,
                'total_emd_submitted' => 0,
                'total_emd_returned' => 0,
                'total_emd_pending' => 0,
            ];
            return $performanceData;
        }
        
        foreach ($mergedDoneStages as $tenderId => $stageData) {
            // Use merged stages for team mode, or config for single role
            if ($performance_mode === 'team') {
                $stagesList = array_values(array_unique($roleStages));
            } else {
                $stagesList = config("tenderingtasks.roles.$role", []);
            }
            $tenderDetails = $totalTenders->keyBy('id')
                ->map(fn($tender) => [
                    'tender_no' => $tender->tender_no,
                    'tender_name' => $tender->tender_name,
                ]);
            // Convert stages arrays to ensure we're working with arrays
            $completedStages = is_array($stageData['stages']) ? $stageData['stages'] : [];
            $skippedStages = is_array($stageData['skipped']) ? $stageData['skipped'] : [];

            $onTimeStages = 0;
            if (isset($stepsPerTenderOnTime[$tenderId]) && is_array($stepsPerTenderOnTime[$tenderId])) {
                foreach ($stepsPerTenderOnTime[$tenderId] as $stage => $remainingTime) {
                    if (is_numeric($remainingTime) && $remainingTime > 0) {
                        $onTimeStages++;
                    }
                }
            }

            $performanceData['tender_wise_details'][$tenderId] = [
                'tender_info' => $tenderDetails[$tenderId] ?? [
                    'tender_no' => 'N/A',
                    'tender_name' => 'N/A',
                ],
                'stages_info' => [
                    'total_stages' => $stageData['tenderhave'],
                    'completed_stages' => $stageData['done'],
                    'completed_on_time' => $onTimeStages, 
                    'completion_percentage' => round(($stageData['done'] / $stageData['tenderhave']) * 100, 2),
                    'stages_completed' => $completedStages,
                    'stages_skipped' => $skippedStages,
                    'pending_stages' => array_values(array_diff(
                        $roleStages,
                        array_merge($completedStages, $skippedStages)
                    ))
                ],
                'emd_info' => [
                    'submitted_amount' => $tenderWithEmd[$tenderId]['emds'] ?? 0,
                    'returned_amount' => $tenderWithEmd[$tenderId]['emdback'] ?? 0,
                ],
                'stage_timelines' => array_map(
                    fn($stage) => [
                        'stage' => $stage,
                        'completed' => in_array($stage, $completedStages),
                        'skipped' => in_array($stage, $skippedStages),
                        'on_time' => isset($stepsPerTenderOnTime[$tenderId][$stage]) && is_numeric($stepsPerTenderOnTime[$tenderId][$stage]) && $stepsPerTenderOnTime[$tenderId][$stage] > 0,
                    ],
                    $roleStages
                )
            ];
        }

        // Add overall metrics
        $performanceData['overall_metrics'] = [
            'total_tenders' => count($mergedDoneStages),
            'total_stages_possible' => $overallSummary['tenderhave'],
            'total_stages_completed' => $overallSummary['done'],
            'total_stages_on_time' => $overallSummary['ontime'],
            'overall_completion_rate' => $overallSummary['tenderhave'] > 0
                ? round(($overallSummary['done'] / $overallSummary['tenderhave']) * 100, 2)
                : 0,
            'overall_on_time_rate' => $overallSummary['done'] > 0
                ? round(($overallSummary['ontime'] / $overallSummary['done']) * 100, 2)
                : 0,
            'total_emd_submitted' => array_sum(array_column($tenderWithEmd, 'emds')),
            'total_emd_returned' => array_sum(array_column($tenderWithEmd, 'emdback')),
            'total_emd_pending' => array_sum(array_column($tenderWithEmd, 'emds')) - array_sum(array_column($tenderWithEmd, 'emdback'))
        ];

        return $performanceData;
    }
}
