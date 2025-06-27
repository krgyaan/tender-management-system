<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\TenderInfo;
use App\Models\VendorOrg;
use Illuminate\Http\Request;
use App\Services\OemPerformance;

class OemPerformanceController extends Controller
{
    protected OemPerformance $oemPerformanceService;

    public function __construct(OemPerformance $oemPerformanceService)
    {
        $this->oemPerformanceService = $oemPerformanceService;
    }
    public function performance(Request $request)
    {
        $oems = VendorOrg::all();
        $result = false;
        $tenders = [];
        $summary = [];
        $notAllowedTenders = [];
        $rfqsSentToOem = [];
        $selectedOem = $request->oem ?? null;
        $from = $request->from_date ?? null;
        $to = $request->to_date ?? null;

        if ($request->isMethod('POST')) {
            $result = true;
            // Prepare filters for service
            $filters = [
                'oem' => $selectedOem,
                'from' => $from,
                'to' => $to
            ];

            $tenders = TenderInfo::with('users', 'rfqs', 'rfqs.rfqResponse')
                ->when($from && $to, function ($q) use ($from, $to) {
                    $q->whereBetween('due_date', [
                        Carbon::parse($from)->startOfDay(),
                        Carbon::parse($to)->endOfDay()
                    ]);
                })
                ->get();

            // Assigned & Approved summary
            $assignedApprovedSummary = $this->oemPerformanceService->getAssignedAndApprovedSummary($tenders, $selectedOem);

            // Use service for these
            $notAllowedTenders = $this->oemPerformanceService->getNotAllowedTenders($tenders, $selectedOem);
            $rfqsSentToOem = $this->oemPerformanceService->getRfqsSentToOem($tenders, $selectedOem);

            // Get tenders using service
            $bidedTenders = $this->oemPerformanceService->getTendersByOem($filters);
            // Calculate summary using service
            $summary = $this->oemPerformanceService->calculateSummary($bidedTenders);
            // Merge assigned/approved into summary
            $summary = array_merge($assignedApprovedSummary, $summary);
        }

        return view('performance.oem', compact('oems', 'result', 'notAllowedTenders', 'rfqsSentToOem', 'summary'));
    }
}
