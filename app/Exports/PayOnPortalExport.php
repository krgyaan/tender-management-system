<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\User;

class PayOnPortalExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(protected $pops)
    {
        $this->pops = $pops;
    }

    public function collection()
    {
        return $this->pops;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Team',
            'Member',
            'Tender Name',
            'Tender Status',
            'Portal',
            'Amount',
            'Pay on Portal Status',
            'Payment Date',
            'UTR No',
            'UTR Message',
            'Rejection Reason',
        ];
    }
    public function map($pop): array
    {
        $actions = [
            1 => $pop->status,
            2 => 'Initiate Followup',
            3 => 'Returned via Bank Transfer',
            4 => 'Settled with Project Account',
        ];
        $team =
            User::where(
                'name',
                $pop->emd->requested_by,
            )->first()->team ?? '';

        return [
            $pop->created_at->format('d-m-Y'),
            $team,
            $pop->emd->requested_by,
            $pop->emd->project_name,
            $pop->emd->tender->statuses->name ?? $pop->emd->type,
            $pop->portal,
            format_inr($pop->amount),
            $pop->action ? $actions[$pop->action] : '',
            $pop->date_time ? date('d-m-Y', strtotime($pop->date_time)) : '',
            $pop->utr,
            $pop->utr_mgs,
            $pop->rejection_reason,
        ];
    }
}
