<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ItemHeading;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\LocationPerformance;

class LocationPerformanceController extends Controller
{
    protected LocationPerformance $locationPerformanceService;

    public function __construct(LocationPerformance $locationPerformanceService)
    {
        $this->locationPerformanceService = $locationPerformanceService;
    }

    public function performance(Request $request)
    {
        $states = Location::$indianStatesAndUTs;
        $regions = Location::$regions;
        $headings = ItemHeading::where('status', '1')->get();
        $result = true;

        if ($request->method() == 'GET') {
            Log::info("Location Performance Page fetched by " . Auth::user()->name);
            $result = false;
            return view('performance.location', compact('states', 'result', 'regions', 'headings'));
        }

        if ($request->method() == 'POST') {
            Log::info("Location Performance Report fetched by " . Auth::user()->name);

            $filters = [
                'state' => $request->state,
                'area' => $request->area,
                'team_type' => $request->team_type,
                'item_heading' => $request->item_heading,
                'from' => $request->from_date,
                'to' => $request->to_date
            ];

            Log::info("Applied Filters: ", $filters);

            // Get tenders using service
            $bidedTenders = $this->locationPerformanceService->getTendersByLocation($filters);

            // Calculate summary using service
            $summary = $this->locationPerformanceService->calculateSummary($bidedTenders);

            // Get additional metrics
            $metrics = $this->locationPerformanceService->getLocationMetrics($bidedTenders);

            return view('performance.location', compact(
                'states',
                'result',
                'regions',
                'headings',
                'bidedTenders',
                'summary',
                'metrics'
            ));
        }
    }

    public function performance_old(Request $request)
    {
        $states = Location::$indianStatesAndUTs;;
        $regions = Location::$regions;
        $headings = ItemHeading::where('status', '1')->get();
        $result = true;
        if ($request->method() == 'GET') {
            Log::info("Location Performance Page fetched by " . Auth::user()->name);
            $result = false;
            return view('performance.location', compact('states', 'result', 'regions', 'headings'));
        }

        if ($request->method() == 'POST') {
            Log::info("Location Performance Report fetched by " . Auth::user()->name);
            $result = true;
            $state = $request->state;
            $area = $request->area;
            $team_type = $request->team_type;
            $item_heading = $request->item_heading;
            $from = $request->from_date;
            $to = $request->to_date;

            // select tender_id from bid_submissions where tender_id is in
            // (select id from tender_infos where locations is in
            // (select id from locations where state = $state or area = $area) and item is in
            // (select id from item_headings where id = $item_heading))
            // and created_at between $from_date and $to_date

            $bidedTenders = DB::table('bid_submissions as bs')
                ->select('bs.id', 'bs.tender_id', 'ti.id', 'ti.team', 'ti.location', 'ti.item', 'l.id', 'l.state', 'l.address', 'l.region', 'ih.name', 'ih.team')
                ->join('tender_infos as ti', 'bs.tender_id', '=', 'ti.id')
                ->join('locations as l', 'ti.location', '=', 'l.id')
                ->join('item_headings as ih', 'ti.item', '=', 'ih.id')
                ->when($state || $area, function ($query) use ($state, $area) {
                    $query->where(function ($q) use ($state, $area) {
                        if ($state) {
                            $q->where('l.state', $state);
                        }
                        if ($area) {
                            $q->orWhere('l.region', $area);
                        }
                    });
                })
                ->when($item_heading, function ($query) use ($item_heading) {
                    $query->where('ih.id', $item_heading);
                })
                ->when($from && $to, function ($query) use ($from, $to) {
                    $query->whereBetween('bs.bid_submissions_date', [$from, $to]);
                })
                ->get();

            Log::info("Total Bided Tenders: ", [$bidedTenders->count()]);

            $summary = [
                'tenders_assigned' => ['tender' => [], 'count' => 0, 'value' => 0],
                'tenders_approved' => ['tender' => [], 'count' => 0, 'value' => 0],
                'tenders_missed' => ['tender' => [], 'count' => 0, 'value' => 0],
                'tenders_bid' => ['tender' => [], 'count' => 0, 'value' => 0],
                'tender_results awaited' => ['tender' => [], 'count' => 0, 'value' => 0],
                'tenders_disqualified' => ['tender' => [], 'count' => 0, 'value' => 0],
                'tenders_won' => ['tender' => [], 'count' => 0, 'value' => 0],
                'tenders_lost' => ['tender' => [], 'count' => 0, 'value' => 0],
            ];

            Log::info("Applied Filters: ", ['state' => $state, 'area' => $area, 'team_type' => $team_type, 'item_heading' => $item_heading, 'from' => $from, 'to' => $to]);
            return view('performance.location', compact('states', 'result', 'regions', 'headings', 'bidedTenders', 'summary'));
        }
    }
}
