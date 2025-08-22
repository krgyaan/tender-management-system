<?php

namespace App\Http\Controllers;

use App\Models\FollowUps;
use Carbon\Carbon;
use App\Models\User;
use App\Models\TenderInfo;
use App\Models\Emds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\TenderingPerformance;
use Illuminate\Database\Eloquent\Collection;

class EmployeePerformanceController extends Controller
{
    protected TenderingPerformance $tenderingPerformanceService;

    public function __construct(TenderingPerformance $tenderingPerformanceService)
    {
        $this->tenderingPerformanceService = $tenderingPerformanceService;
    }

    public function performance(Request $request)
    {
        $users = User::whereIn('role', ['tender-executive', 'team-leader'])
            ->where('status', '1')
            ->orderByDesc('team')
            ->get();

        // dd($request->toArray());

        if ($request->method() == 'POST') {
            // get tender executive id
            $team_member = $request->team_member;
            $from = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : null;
            $to = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : null;

            Log::info("Step 1: Getting Tender Executive ID: $team_member");

            $user = User::find($team_member);
            $usersRole = $user->role;

            $TotalEmpPerformanceTask = $this->tenderingPerformanceService->getPerformanceTasksByRole($usersRole);
            $doableStages = count($TotalEmpPerformanceTask);

            if (in_array($usersRole, ['tender-executive', 'team-leader'])) {
                $baseConditions['team_member'] = $team_member;
            } elseif (in_array($usersRole, ['team-dc', 'team-ac'])) {
                $baseConditions['team'] = strtoupper(explode('-', $usersRole)[1]);
            }

            // Helper function to apply date filter
            $dateFilter = function ($query) use ($from, $to) {
                if ($from && $to) {
                    $query->whereHas('bs', function ($q) use ($from, $to) {
                        $q->whereBetween('bid_submissions_date', [$from, $to]);
                    });
                }
            };

            // Get bidded tenders
            $biddedTenders = DB::table('bid_submissions as bs')
                ->leftJoin('tender_infos as ti', 'bs.tender_id', '=', 'ti.id')
                ->when($from && $to, function ($query) use ($from, $to) {
                    $query->where(function ($q) use ($from, $to) {
                        $q->where(function ($sub) use ($from, $to) {
                            $sub->where('bs.status', 'Bid Submitted')
                                ->whereBetween('bs.bid_submissions_date', [$from, $to]);
                        })
                            ->orWhere(function ($sub) use ($from, $to) {
                                $sub->where('bs.status', 'Tender Missed')
                                    ->whereBetween('ti.due_date', [$from, $to]);
                            });
                    });
                })
                ->pluck('ti.id')
                ->all();

            Log::info("Total Bidded Tenders: " . count($biddedTenders));

            // Get total tenders with eager loading
            $totalTenders = TenderInfo::whereIn('id', $biddedTenders)
                ->where($baseConditions)
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

            $tenderStatusCounts = $this->classifyBiddedTenders($totalTenders);
            $emdsByTenderNo = Emds::with([
                'emdDemandDrafts',
                'emdFdrs',
                'emdCheques',
                'emdBgs',
                'emdBankTransfers',
                'emdPayOnPortals',
            ])->get()->keyBy('tender_no');

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
            Log::info("Step 3: Getting Doable Stages by ", [$usersRole => $TotalEmpPerformanceTask]);

            $stepsPerTender = $this->tenderingPerformanceService->getStepsPerTender($tenderIds, $TotalEmpPerformanceTask);
            Log::info("Step 5: Getting Executed Stages for Tender: ", $stepsPerTender->toArray());

            $stepsPerTenderOnTime = $this->tenderingPerformanceService->getStepsPerTenderOnTime($tenderIds, $TotalEmpPerformanceTask);
            Log::info("Step 6: Getting Executed Stages with Time for Tender: ", $stepsPerTenderOnTime->toArray());

            $summaryData = $this->tenderingPerformanceService->getStepsSummary(
                $tenderIds,
                $TotalEmpPerformanceTask,
                $usersRole,
            );

            // Access individual parts:
            $skippedStages = $summaryData['skippedStages'] ?? [];
            $stepsPerTender = $summaryData['stepsPerTender'] ?? [];
            $stepsPerTenderOnTime = $summaryData['stepsPerTenderOnTime'] ?? [];
            $mergedDoneStages = $summaryData['mergedDoneStages'] ?? [];
            $overallSummary = $summaryData['overallSummary'] ?? [];
            $performanceData = $this->preparePerformanceData(
                $tenderStatusCounts,
                $tenderWithEmd,
                $mergedDoneStages,
                $overallSummary,
                $usersRole,
                $totalTenders,
                $stepsPerTenderOnTime
            );

            $followupQuery = FollowUps::where('assigned_to', $team_member);
            if ($from && $to) {
                $followupQuery->whereBetween('start_from', [$from, $to]);
            }
            $assigned = $followupQuery->count();
            $target_amt = $followupQuery->sum('amount');
            $released_amt = (clone $followupQuery)->where('stop_reason', '2')->sum('amount');
            $pending_amt = $target_amt - $released_amt;

            $followup = [
                'summary' => [
                    'assigned' => $assigned,
                    'target_amt' => $target_amt,
                    'released_amt' => $released_amt,
                    'pending_amt' => $pending_amt,
                ]
            ];

            $isResult = true;
            return view('performance.employee', compact(
                'users',
                'isResult',
                'team_member',
                'totalTenderCount',
                'tenderStatusCounts',
                'tenderWithEmd',
                'mergedDoneStages',
                'overallSummary',
                'stepsPerTender',
                'doableStages',
                'performanceData',
                'followup'
            ));
        }

        if ($request->method() == 'GET') {
            $isResult = false;
            $totalTenderCount = 0;
            $stepsPerTender = 0;
            $overallSummary = 0;
            $team_member = '';
            $tenderStatusCounts = [];
            $tenderWithEmd = [];
            $followup = [];
            $mergedDoneStages = [];
            $doableStages = 0;

            return view('performance.employee', compact(
                'users',
                'isResult',
                'totalTenderCount',
                'team_member',
                'tenderStatusCounts',
                'tenderWithEmd',
                'mergedDoneStages',
                'overallSummary',
                'stepsPerTender',
                'doableStages',
                'followup'
            ));
        }
    }

    private function classifyBiddedTenders(Collection $tenders): array
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


    private function preparePerformanceData($tenderStatusCounts, $tenderWithEmd, $mergedDoneStages, $overallSummary, $role, $totalTenders, $stepsPerTenderOnTime): array
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
        foreach ($mergedDoneStages as $tenderId => $stageData) {
            // Get the stages array for the role
            $roleStages = config("tenderingtasks.roles.$role", []);
            $tenderDetails = $totalTenders->keyBy('id')
                ->map(fn($tender) => [
                    'tender_no' => $tender->tender_no,
                    'tender_name' => $tender->tender_name,
                ]);
            // Convert stages arrays to ensure we're working with arrays
            $completedStages = is_array($stageData['stages']) ? $stageData['stages'] : [];
            $skippedStages = is_array($stageData['skipped']) ? $stageData['skipped'] : [];

            // Calculate per-tender done on time
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
        $tenderHave = $overallSummary['tenderhave'] ?? 0;
        $tenderDone = $overallSummary['done'] ?? 0;
        $tenderOntime = $overallSummary['ontime'] ?? 0;
        
        $performanceData['overall_metrics'] = [
            'total_tenders' => count($mergedDoneStages),
            'total_stages_possible' => $tenderHave,
            'total_stages_completed' => $tenderDone,
            'total_stages_on_time' => $tenderOntime,
            'overall_completion_rate' => $tenderHave > 0
                ? round(($tenderDone / $tenderHave) * 100, 2)
                : 0,
            'overall_on_time_rate' => $tenderDone > 0
                ? round(($tenderOntime / $tenderDone) * 100, 2)
                : 0,
            'total_emd_submitted' => array_sum(array_column($tenderWithEmd, 'emds')),
            'total_emd_returned' => array_sum(array_column($tenderWithEmd, 'emdback')),
            'total_emd_pending' => array_sum(array_column($tenderWithEmd, 'emds')) - array_sum(array_column($tenderWithEmd, 'emdback')),
        ];
        
        return $performanceData;
    }
}
