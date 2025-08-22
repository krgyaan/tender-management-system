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

            // Assigned & Approved summary
            $assignedApprovedSummary = $this->locationPerformanceService->getAssignedAndApprovedSummary($filters);

            // Get tenders using service
            $bidedTenders = $this->locationPerformanceService->getTendersByLocation($filters);

            // Calculate summary using service
            $summary = $this->locationPerformanceService->calculateSummary($bidedTenders);
            // Merge assigned/approved into summary
            $summary = array_merge($assignedApprovedSummary, $summary);

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
}
