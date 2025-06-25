<?php

namespace App\Http\Controllers;

use App\Models\ItemHeading;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\BusinessPerformance;

class BusinessPerformanceController extends Controller
{
    protected BusinessPerformance $businessPerformanceService;

    public function __construct(BusinessPerformance $businessPerformanceService)
    {
        $this->businessPerformanceService = $businessPerformanceService;
    }

    public function performance(Request $request)
    {
        $headings = ItemHeading::where('status', '1')->get();
        $result = true;

        if ($request->method() == 'GET') {
            $result = false;
            return view('performance.business', compact('headings', 'result'));
        }

        if ($request->method() == 'POST') {
            $filters = [
                'heading' => $request->heading,
                'from' => $request->from_date,
                'to' => $request->to_date,
            ];

            $tenders = $this->businessPerformanceService->getTenders($filters);
            $summary = $this->businessPerformanceService->calculateSummary($tenders);
            $metrics = $this->businessPerformanceService->getMetrics($tenders);
            $items = $this->businessPerformanceService->getItemsUnderHeading($filters['heading']);

            return view('performance.business', compact(
                'headings',
                'result',
                'summary',
                'metrics',
                'items'
            ));
        }
    }
}
