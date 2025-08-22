<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BusinessPerformance
{
    public function getTenders($filters)
    {
        $query = DB::table('bid_submissions as bs')
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
                'l.state',
                'l.region',
                'ih.name as item_heading_name',
                'ih.team as heading_team',
                'it.name as item_name',
                'it.team as item_team'
            )
            ->join('tender_infos as ti', 'bs.tender_id', '=', 'ti.id')
            ->join('locations as l', 'ti.location', '=', 'l.id')
            ->join('items as it', 'ti.item', '=', 'it.id')
            ->leftJoin('item_headings as ih', function ($join) {
                $join->on('it.heading', '=', 'ih.name')
                    ->whereColumn('it.team', 'ih.team');
            })
            ->when(!empty($filters['heading']), function ($q) use ($filters) {
                $q->where('ih.id', $filters['heading']);
            })
            ->when(!empty($filters['from']) && !empty($filters['to']), function ($q) use ($filters) {
                $q->whereBetween('bs.bid_submissions_date', [$filters['from'], $filters['to']]);
            });

        return $query->get();
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
                'l.state',
                'l.region',
                'ih.name as item_heading_name',
                'ih.team as heading_team',
                'it.name as item_name',
                'it.team as item_team'
            )
            ->where('ti.deleteStatus', '0')
            ->join('locations as l', 'ti.location', '=', 'l.id')
            ->join('items as it', 'ti.item', '=', 'it.id')
            ->leftJoin('item_headings as ih', function ($join) {
                $join->on('it.heading', '=', 'ih.name')
                    ->whereColumn('it.team', 'ih.team');
            })
            ->when(!empty($filters['heading']), function ($q) use ($filters) {
                $q->where('ih.id', $filters['heading']);
            })
            ->when(!empty($filters['from']) && !empty($filters['to']), function ($q) use ($filters) {
                $q->whereBetween('ti.due_date', [$filters['from'], $filters['to']]);
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
            'tenders_bid' => ['tender' => [], 'count' => 0, 'value' => 0],
            'tenders_missed' => ['tender' => [], 'count' => 0, 'value' => 0],
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

    public function getMetrics($tenders)
    {
        $by_region = [];
        $by_state = [];
        $by_item = [];
        $total_count = 0;
        $total_value = 0;

        foreach ($tenders as $tender) {
            // Region
            $region = $tender->region ?? 'Unknown';
            $by_region[$region]['count'] = ($by_region[$region]['count'] ?? 0) + 1;
            $by_region[$region]['value'] = ($by_region[$region]['value'] ?? 0) + ($tender->bid_value ?? 0);

            // State
            $state = $tender->state ?? 'Unknown';
            $by_state[$state]['count'] = ($by_state[$state]['count'] ?? 0) + 1;
            $by_state[$state]['value'] = ($by_state[$state]['value'] ?? 0) + ($tender->bid_value ?? 0);

            // Item
            $item = $tender->item_name ?? 'Unknown';
            $by_item[$item]['count'] = ($by_item[$item]['count'] ?? 0) + 1;
            $by_item[$item]['value'] = ($by_item[$item]['value'] ?? 0) + ($tender->bid_value ?? 0);

            $total_count++;
            $total_value += $tender->bid_value ?? 0;
        }

        return [
            'by_region' => $by_region,
            'by_state' => $by_state,
            'by_item' => $by_item,
            'total_count' => $total_count,
            'total_value' => $total_value,
        ];
    }

    public function getItemsUnderHeading($headingId)
    {
        return DB::table('items as it')
            ->join('item_headings as ih', function ($join) {
                $join->on('it.heading', '=', 'ih.name')
                    ->whereColumn('it.team', 'ih.team');
            })
            ->where('ih.id', $headingId)
            ->select('it.id', 'it.name')
            ->get();
    }
}
