<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\TenderInfo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerPerformance
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
                DB::raw('COALESCE(ti.gst_values, 0) as gst_values'),
                'ti.status as tender_status',
                'o.id as org_id',
                'o.name as org_name',
                'ih.name as item_name',
                'ih.team as item_team'
            )
            ->join('tender_infos as ti', 'bs.tender_id', '=', 'ti.id')
            ->join('organizations as o', 'ti.organisation', '=', 'o.id')
            ->join('item_headings as ih', 'ti.item', '=', 'ih.id')
            ->when($filters['org'], function ($query) use ($filters) {
                $query->where(function ($q) use ($filters) {
                    if ($filters['org']) {
                        $q->where('o.id', $filters['org']);
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

    public function calculateSummary(Collection $tenders): array
    {
        $summary = [
            'tenders_assigned' => ['tender' => [], 'count' => 0, 'value' => 0],
            'tenders_approved' => ['tender' => [], 'count' => 0, 'value' => 0],
            'tenders_missed' => ['tender' => [], 'count' => 0, 'value' => 0],
            'tenders_bid' => ['tender' => [], 'count' => 0, 'value' => 0],
            'tender_results_awaited' => ['tender' => [], 'count' => 0, 'value' => 0],
            'tenders_disqualified' => ['tender' => [], 'count' => 0, 'value' => 0],
            'tenders_won' => ['tender' => [], 'count' => 0, 'value' => 0],
            'tenders_lost' => ['tender' => [], 'count' => 0, 'value' => 0],
        ];

        foreach ($tenders as $tender) {
            // Count all tenders as assigned
            $summary['tenders_assigned']['count']++;
            $summary['tenders_assigned']['value'] += $tender->gst_values;
            $summary['tenders_assigned']['tender'][] = $tender->tender_name;

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

            // Count approved tenders (you might need to adjust this condition based on your business logic)
            if (in_array($tender->tender_status, [17, 24, 25, 26, 27, 28])) {
                $this->addToSummary($summary['tenders_approved'], $tender);
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
            'total_value' => $tenders->sum('gst_values'),
            'total_count' => $tenders->count(),
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
