<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TallyExportService
{
    public function getSecurityBlock(): array
    {
        return [
            'COMPANYNAME'   => config('tally.company_name', 'Tally Accounting India'),
            'CompanyGSTIN'  => config('tally.gstin', '07AAADC2511K1Z0'),
            'SECURITYKEY'   => config('tally.security_key'),
            'APPSERIALNO'   => config('tally.serial_no', '737500725'),
            'APPVERSION'    => '1.1',
            'APPSYSDATE'    => now()->format('d-M-y'),
        ];
    }

    public function getJournalVouchers($from, $to): array
    {
        $query = DB::table('employeeimprests')
            ->leftJoin('categories', 'employeeimprests.category_id', '=', 'categories.id')
            ->select('employeeimprests.*', 'categories.category as category_name')
            ->where('tallystatus', '0')
            ->whereNotNull('approved_date');

        Log::info('Applying date range filter', compact('from', 'to'));
        if ($from) {
            $query->whereDate('employeeimprests.approved_date', '>=', $from);
        }

        if ($to) {
            $query->whereDate('employeeimprests.approved_date', '<=', $to);
        }


        DB::enableQueryLog();
        $records = $query->get();
        Log::info(DB::getQueryLog());

        Log::info('Filtered journal count', ['count' => $records->count()]);

        $vouchers = [];

        foreach ($records as $record) {
            $amount = (float) $record->amount;

            $vouchers[] = [
                'MASTERID' => $record->id,
                'ALTERID' => $record->id + 1000,
                'VOUCHERDATE' => Carbon::parse($record->approved_date)->format('j-M-y'),
                'VOUCHERNUMBER' => (string) $record->id,
                'VOUCHERTYPE' => 'Journal',
                'VOUCHERNATURE' => 'Journal',
                'PARTYNAME' => $record->party_name ?? 'Unknown Party',
                'PARTYGSTIN' => '', // Optional
                'PARTYSTATE' => '', // Optional
                'ALLLEDGERENTRIES' => [
                    [
                        'LEDGERNAME' => $record->category_name ?? 'Expenses',
                        'AMOUNT' => -$amount,
                    ],
                    [
                        'LEDGERNAME' => $record->party_name ?? 'Unknown Party',
                        'AMOUNT' => $amount,
                    ]
                ]
            ];
        }

        return $vouchers;
    }

    public function getPurchaseVouchers(): array
    {
        $purchases = DB::table('purchases')->get(); // Modify this based on your actual table

        $vouchers = [];

        foreach ($purchases as $purchase) {
            $vouchers[] = [
                'MASTERID' => $purchase->id,
                'ALTERID' => $purchase->id + 1000,
                'VOUCHERDATE' => Carbon::parse($purchase->purchase_date)->format('j-M-y'),
                'VOUCHERNUMBER' => $purchase->voucher_no,
                'VOUCHERTYPE' => 'Purchase',
                'VOUCHERNATURE' => 'Purchase',
                'PARTYNAME' => $purchase->vendor_name,
                'PARTYGSTIN' => $purchase->gstin,
                'PARTYADDRESS1' => $purchase->address_line1,
                'PARTYADDRESS2' => $purchase->address_line2,
                'PARTYADDRESS3' => $purchase->address_line3,
                'PARTYSTATE' => $purchase->state,
                'PartyBank' => $purchase->bank_details,
                'PartyAcNo' => $purchase->account_no,
                'PartyIFSC' => $purchase->ifsc,
                'TOTALAMOUNT' => $purchase->total_amount,
                'ALLINVENTORYENTRIES' => json_decode($purchase->inventory_json, true) ?? [],
                'ALLLEDGERENTRIES' => json_decode($purchase->ledger_json, true) ?? [],
            ];
        }

        return $vouchers;
    }
}
