<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BgExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(protected $bgs)
    {
        $this->bgs = $bgs;
    }
    public function collection()
    {
        return $this->bgs;
    }
    public function headings(): array
    {
        return [
            'BG Date',
            'BG No.',
            'Beneficiary name',
            'Tender Name',
            'Amount',
            'BG Expiry Date',
            'BG Claim Period',
            'BG Charges paid',
            'BG Charges	Calculated',
            'FDR No',
            'FDR Amount',
            'BG Status',
        ];
    }


    public function map($bg): array
    {
        $actions = [
            1 => $bg->bg_req == 'Accepted' ? 'Format Accepted' : 'Rejected',
            2 => 'Created',
            3 => 'SFMS Submitted',
            4 => 'Followup Initiated',
            5 => 'Extension Request',
            6 => 'Returned via courier',
            7 => 'Cancellation Request',
            8 => 'BG Cancelled',
            9 => 'FDR released',
        ];
        $bgc = $bg->bg_charge_deducted ?? 0;
        $sfms = $bg->sfms_charge_deducted ?? 0;
        $stamp = $bg->stamp_charge_deducted ?? 0;
        $other = $bg->other_charge_deducted ?? 0;
        $total = $bgc + $sfms + $stamp + $other;

        $bgValue = $bg->bg_amt ?? 0;
        $stampPaper = $bg->stamp_charge_deducted ?? 0;
        $bgStampPaperValue = 300;
        $sfmsCharges = $bg->sfms_charge_deducted ?? 0;
        $bgCreationDate = Carbon::parse($bg->created_at);
        $bgClaimDate = Carbon::parse($bg->bg_claim);

        $dailyInterestRate = 0.01 / 365;
        $daysDifference = $bgClaimDate->diffInDays($bgCreationDate);
        $interestComponent = $bgValue * $dailyInterestRate * $daysDifference;
        $interestWithGST = $interestComponent * 1.18;
        $totalValue = $interestWithGST + $stampPaper + $bgStampPaperValue + $sfmsCharges;

        return [
            $bg->bg_date ? date('d-m-Y', strtotime($bg->bg_date)) : '',
            $bg->bg_no ?? '',
            $bg->bg_favour ?? '',
            $bg->emds->project_name,
            format_inr($bg->bg_amt) ?? 0,
            $bg->bg_expiry ? date('d-m-Y', strtotime($bg->bg_expiry)) : '',
            $bg->bg_claim ? date('d-m-Y', strtotime($bg->bg_claim)) : '',
            format_inr($total),
            format_inr($totalValue),
            $bg->fdr_no ?? '',
            format_inr($bg->fdr_amt) ?? '',
            $bg->action != null ? $actions[$bg->action] : ''
        ];
    }
}
