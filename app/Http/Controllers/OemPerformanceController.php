<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\TenderInfo;
use App\Models\VendorOrg;
use Illuminate\Http\Request;

class OemPerformanceController extends Controller
{
    public function performance(Request $request)
    {
        $oems = VendorOrg::all();
        $result = false;
        $tenders = [];
        $notAllowedTenders = [];
        $rfqsSentToOem = [];
        $selectedOem = $request->oem ?? null;
        $from = $request->from_date ?? null;
        $to = $request->to_date ?? null;

        if ($request->isMethod('POST')) {
            $result = true;

            $tenders = TenderInfo::with('users', 'rfqs', 'rfqs.rfqResponse')
                ->when($from && $to, function ($q) use ($from, $to) {
                    $q->whereBetween('due_date', [
                        Carbon::parse($from)->startOfDay(),
                        Carbon::parse($to)->endOfDay()
                    ]);
                })
                ->get();

            $notAllowedTenders = $tenders->filter(function ($tender) use ($selectedOem) {
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

            $rfqsSentToOem = $tenders->filter(function ($tender) use ($selectedOem) {
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
                    'rfq_sent_on' => $tender->rfqs->created_at->format('d-m-Y h:i A') ?? 'Not Yet',
                    'rfq_response' => $tender->rfqs->rfqResponse?->receipt_datetime->format('d-m-Y h:i A') ?? 'Not Yet',
                ];
            })->toArray();
        }

        return view('performance.oem', compact('oems', 'result', 'notAllowedTenders', 'rfqsSentToOem'));
    }
}
