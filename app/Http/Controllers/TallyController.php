<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TallyExportService;

class TallyController extends Controller
{
    protected $tally;

    public function __construct(TallyExportService $tally)
    {
        $this->tally = $tally;
    }

    public function journal(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ], [
            'from_date.required' => 'Date range is required. We cannot send data without a start date as it may cause duplicate entries in Tally.',
            'to_date.required' => 'Date range is required. We cannot send data without an end date as it may cause duplicate entries in Tally.',
            'to_date.after_or_equal' => 'The end date must be the same or later than the start date to ensure accurate voucher export.',
        ]);


        $from = $request->query('from_date'); // e.g., 2024-01-01
        $to = $request->query('to_date');     // e.g., 2024-12-31

        $journals = $this->tally->getJournalVouchers($from, $to);

        return response()->json([
            'SECURITY' => $this->tally->getSecurityBlock(),
            'VOUCHER' => $journals
        ]);
    }


    public function purchase(Request $request)
    {
        if ($request->header('X-SECURITY-KEY') !== config('tally.security_key')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'SECURITY' => $this->tally->getSecurityBlock(),
            'VOUCHER' => $this->tally->getPurchaseVouchers(),
        ]);
    }
}
