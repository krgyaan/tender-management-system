<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OemPerformance
{
    public function getNotAllowedTenders($tenders, $selectedOem)
    {
        return $tenders->filter(function ($tender) use ($selectedOem) {
            if (!$tender->oem_who_denied) return false;
            $denied = is_array($tender->oem_who_denied)
                ? $tender->oem_who_denied
                : explode(',', $tender->oem_who_denied);
            return in_array($selectedOem, array_map('trim', $denied));
        })->map(function ($tender) {
            return [
                'id' => $tender->id,
                'team' => $tender->team,
                'tender_no' => $tender->tender_no,
                'tender_name' => $tender->tender_name,
                'due_date' => date('d-m-Y h:i A', strtotime("$tender->due_date $tender->due_time")),
                'gst_values' => $tender->gst_values,
                'member' => $tender->users->name ?? '',
            ];
        })->toArray();
    }

    public function getRfqsSentToOem($tenders, $selectedOem)
    {
        return $tenders->filter(function ($tender) use ($selectedOem) {
            if (!$tender->rfq_to) return false;
            $sent = is_array($tender->rfq_to)
                ? $tender->rfq_to
                : explode(',', $tender->rfq_to);
            return in_array($selectedOem, array_map('trim', $sent));
        })->map(function ($tender) {
            return [
                'id' => $tender->id,
                'team' => $tender->team,
                'tender_no' => $tender->tender_no,
                'tender_name' => $tender->tender_name,
                'due_date' => date('d-m-Y h:i A', strtotime("$tender->due_date $tender->due_time")),
                'gst_values' => $tender->gst_values,
                'member' => $tender->users->name ?? '',
                'rfq_sent_on' => $tender->rfqs?->created_at->format('d-m-Y h:i A') ?? 'Not Yet',
                'rfq_response' => $tender->rfqs?->rfqResponse?->receipt_datetime->format('d-m-Y h:i A') ?? 'Not Yet',
            ];
        })->toArray();
    }

    public function getAssignedAndApprovedSummary($tenders, $selectedOem)
    {
        $assigned = $tenders->filter(function ($tender) use ($selectedOem) {
            if (!$tender->rfq_to) return false;
            $sent = is_array($tender->rfq_to) ? $tender->rfq_to : explode(',', $tender->rfq_to);
            return in_array($selectedOem, array_map('trim', $sent));
        });

        $assignedCount = $assigned->count();
        $assignedSum = $assigned->sum('gst_values');

        $approved = $assigned->filter(function ($tender) {
            return isset($tender->tlStatus) && $tender->tlStatus == 1;
        });

        $approvedCount = $approved->count();
        $approvedSum = $approved->sum('gst_values');

        return [
            'tenders_assigned' => [
                'count' => $assignedCount,
                'value' => $assignedSum,
                'tender' => $assigned->pluck('tender_name')->toArray(),
            ],
            'tenders_approved' => [
                'count' => $approvedCount,
                'value' => $approvedSum,
                'tender' => $approved->pluck('tender_name')->toArray(),
            ],
        ];
    }

    public function getTendersByOem($filters)
    {
        return DB::table('bid_submissions as bs')
            ->select(
                'bs.id',
                'bs.tender_id',
                'bs.status as bid_status',
                'ti.id',
                'ti.tender_name',
                'ti.gst_values',
                'ti.status as tender_status',
                'ti.rfq_to',
            )
            ->join('tender_infos as ti', 'bs.tender_id', '=', 'ti.id')
            ->when($filters['oem'], function ($query) use ($filters) {
                $query->where(function ($q) use ($filters) {
                    if ($filters['oem']) {
                        $q->where(function ($qq) use ($filters) {
                            foreach (explode(',', $filters['oem']) as $oem) {
                                $qq->orWhereRaw("FIND_IN_SET('{$oem}', ti.rfq_to)");
                            }
                        });
                    }
                });
            })
            ->when($filters['from'] && $filters['to'], function ($query) use ($filters) {
                $query->whereBetween('bs.bid_submissions_date', [
                    Carbon::parse($filters['from'])->startOfDay(),
                    Carbon::parse($filters['to'])->endOfDay()
                ]);
            })
            ->get();
    }

    public function calculateSummary(Collection $tenders): array
    {
        $summary = [
            'tenders_bid' => ['tender' => [], 'count' => 0, 'value' => 0],
            'tenders_missed' => ['tender' => [], 'count' => 0, 'value' => 0],
            'tenders_disqualified' => ['tender' => [], 'count' => 0, 'value' => 0],
            'tender_results_awaited' => ['tender' => [], 'count' => 0, 'value' => 0],
            'tenders_won' => ['tender' => [], 'count' => 0, 'value' => 0],
            'tenders_lost' => ['tender' => [], 'count' => 0, 'value' => 0],
        ];

        foreach ($tenders as $tender) {

            // Classify based on status
            switch ($tender->tender_status) {
                case 8:
                case 16:
                    $this->addToSummary($summary['tenders_missed'], $tender);
                    break;
                case 21:
                case 22:
                    $this->addToSummary($summary['tenders_disqualified'], $tender);
                    break;
                case 17:
                    $this->addToSummary($summary['tender_results_awaited'], $tender);
                    break;
                case 24:
                    $this->addToSummary($summary['tenders_lost'], $tender);
                    break;
                case 25:
                case 26:
                case 27:
                case 28:
                    $this->addToSummary($summary['tenders_won'], $tender);
                    break;
            }

            // Count bid submitted tenders
            if ($tender->bid_status === 'Bid Submitted') {
                $this->addToSummary($summary['tenders_bid'], $tender);
            }
        }

        return $summary;
    }

    private function addToSummary(array &$summaryItem, $tender): void
    {
        $summaryItem['count']++;
        $summaryItem['value'] += $tender->gst_values;
        $summaryItem['tender'][] = $tender->tender_name;
    }
}
