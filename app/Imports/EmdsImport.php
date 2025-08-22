<?php

namespace App\Imports;

use App\Models\Emds;
use App\Models\PayOnPortal;
use App\Models\BankTransfer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EmdsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        Log::info('Starting EMD import');
        foreach ($rows as $row) {
            Log::info('Processing row: ' . json_encode($row));
            // Create EMD record
            $emd = Emds::create([
                'type' => 'Other Than TMS',
                'tender_id' => 0,
                'tender_no' => $row['tender_number'],
                'due_date' => Carbon::createFromFormat('d-m-Y', $row['tender_due_date'])->format('Y-m-d'),
                'instrument_type' => '5',
                'project_name' => $row['tender_name'],
                'requested_by' => $row['requested_by'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create BankTransfer record
            BankTransfer::create([
                'emd_id' => $emd->id,
                'purpose' => $row['purpose'],
                'bt_acc_name' => $row['account_name'],
                'bt_acc' => $row['account_number'],
                'bt_ifsc' => $row['ifsc'],
                'bt_amount' => $row['amount'],
                'date_time' => Carbon::createFromFormat('d-m-Y', $row['payment_date'])->format('Y-m-d'),
                'action' => '1',
                'status' => 'Accepted',
                'utr' => $row['utr'],
                'utr_mgs' => $row['utr_message'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create PayOnPortal record
            // PayOnPortal::create([
            //     'emd_id' => $emd->id,
            //     'purpose' => $row['purpose'],
            //     'portal' => $row['portal'],
            //     'is_netbanking' => $row['is_netbanking'],
            //     'is_debit' => $row['is_debit_card'],
            //     'amount' => $row['amount'],
            //     'action' => '1',
            //     'status' => 'Accepted',
            //     'utr' => $row['utr'],
            //     'utr_mgs' => $row['utr_message'],
            //     'date_time' => Carbon::createFromFormat('d-m-Y', $row['payment_date'])->format('Y-m-d'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ]);
        }
        Log::info('EMD import completed');
    }
}
