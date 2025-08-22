<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ItemHeading;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\CustomerPerformance;
use Illuminate\Http\Request;

class CustomerPerformanceController extends Controller
{
    protected CustomerPerformance $customerPerformanceService;

    public function __construct(CustomerPerformance $customerPerformanceService)
    {
        $this->customerPerformanceService = $customerPerformanceService;
    }
    public function performance(Request $request)
    {
        $orgs = Organization::where('status', '1')->get();
        $headings = ItemHeading::where('status', '1')->get();
        $result = true;
        if ($request->method() == 'GET') {
            $result = false;
            return view('performance.customer', compact('orgs', 'result', 'headings'));
        }

        if ($request->method() == 'POST') {
            $result = true;
            Log::info("Customer Performance Report fetched by " . Auth::user()->name);

            $filters = [
                'org' => $request->organization,
                'team_type' => $request->team_type,
                'item_heading' => $request->item_heading,
                'from' => $request->from_date,
                'to' => $request->to_date
            ];
            Log::info("Applied Filters: ", $filters);

            // Get tenders using service
            $bidedTenders = $this->customerPerformanceService->getTendersByLocation($filters);

            // Calculate summary using service
            $summary = $this->customerPerformanceService->calculateSummary($bidedTenders);

            // Get additional metrics
            $metrics = $this->customerPerformanceService->getLocationMetrics($bidedTenders);

            return view('performance.customer', compact(
                'orgs',
                'result',
                'headings',
                'bidedTenders',
                'summary',
                'metrics'
            ));
        }
    }
}
