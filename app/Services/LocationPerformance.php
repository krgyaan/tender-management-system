<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LocationPerformance
{
    public function getTendersByLocation($filters)
    {
        return DB::table('bid_submissions as bs')
            ->select(
                'bs.id',
                'bs.tender_id',
                'bs.status as bid_status',
                'ti.id',
                'ti.team',
                'ti.location',
                'ti.item',
                'ti.tender_name',
                'ti.gst_values',
                'ti.status as tender_status',
                'l.id as location_id',
                'l.state',
                'l.address',
                'l.region',
                'ih.name as item_name',
                'ih.team as item_team'
            )
            ->join('tender_infos as ti', 'bs.tender_id', '=', 'ti.id')
            ->join('locations as l', 'ti.location', '=', 'l.id')
            ->join('item_headings as ih', 'ti.item', '=', 'ih.id')
            ->when($filters['state'] || $filters['area'], function ($query) use ($filters) {
                $query->where(function ($q) use ($filters) {
                    if ($filters['state']) {
                        $q->where('l.state', $filters['state']);
                    }
                    if ($filters['area']) {
                        $q->orWhere('l.region', $filters['area']);
                    }
                });
            })
            ->when($filters['item_heading'], function ($query) use ($filters) {
                $query->where('ih.id', $filters['item_heading']);
            })
            ->when($filters['from'] && $filters['to'], function ($query) use ($filters) {
                $query->whereBetween('bs.bid_submissions_date', [
                    Carbon::parse($filters['from'])->startOfDay(),
                    Carbon::parse($filters['to'])->endOfDay()
                ]);
            })
            ->get();
    }

    public function getAssignedAndApprovedSummary($filters)
    {
        $assigned = DB::table('tender_infos as ti')
            ->select(
                'ti.id',
                'ti.team',
                'ti.location',
                'ti.item',
                'ti.tender_name',
                'ti.gst_values',
                'ti.tlStatus',
                'ti.status as tender_status',
                'l.id as location_id',
                'l.state',
                'l.address',
                'l.region',
                'ih.name as item_name',
                'ih.team as item_team'
            )
            ->where('ti.deleteStatus', '0')
            ->join('locations as l', 'ti.location', '=', 'l.id')
            ->join('item_headings as ih', 'ti.item', '=', 'ih.id')
            ->when($filters['state'] || $filters['area'], function ($query) use ($filters) {
                $query->where(function ($q) use ($filters) {
                    if ($filters['state']) {
                        $q->where('l.state', $filters['state']);
                    }
                    if ($filters['area']) {
                        $q->orWhere('l.region', $filters['area']);
                    }
                });
            })
            ->when($filters['item_heading'], function ($query) use ($filters) {
                $query->where('ih.id', $filters['item_heading']);
            })
            ->when($filters['from'] && $filters['to'], function ($query) use ($filters) {
                $query->whereBetween('ti.due_date', [
                    Carbon::parse($filters['from'])->startOfDay(),
                    Carbon::parse($filters['to'])->endOfDay()
                ]);
            })
            ->get();

        $approved = $assigned->filter(function ($tender) {
            return isset($tender->tlStatus) && $tender->tlStatus == '1';
        });

        $assignedCount = $assigned->count();
        $assignedSum = $assigned->sum('gst_values');
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

    public function calculateSummary(Collection $tenders): array
    {
        $summary = [
            'tenders_missed' => ['tender' => [], 'count' => 0, 'value' => 0],
            'tenders_bid' => ['tender' => [], 'count' => 0, 'value' => 0],
            'tender_results_awaited' => ['tender' => [], 'count' => 0, 'value' => 0],
            'tenders_disqualified' => ['tender' => [], 'count' => 0, 'value' => 0],
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

    public function getLocationMetrics(Collection $tenders): array
    {
        $metrics = [
            'by_region' => [],
            'by_state' => [],
            'by_item' => [],
        ];

        // Group by region
        $metrics['by_region'] = $tenders->groupBy('region')
            ->map(fn($group) => [
                'count' => $group->count(),
                'value' => $group->sum('gst_values')
            ])->toArray();

        // Group by state
        $metrics['by_state'] = $tenders->groupBy('state')
            ->map(fn($group) => [
                'count' => $group->count(),
                'value' => $group->sum('gst_values')
            ])->toArray();

        // Group by item
        $metrics['by_item'] = $tenders->groupBy('item_name')
            ->map(fn($group) => [
                'count' => $group->count(),
                'value' => $group->sum('gst_values')
            ])->toArray();

        return $metrics;
    }
}
