<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\User;

class BankTransferExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(protected $bts)
    {
        $this->bts = $bts;
    }

    public function collection()
    {
        return $this->bts;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Team',
            'Member',
            'Tender Name',
            'Tender Status',
            'Amount',
            'Bank Transfer Status',
            'Payment Date',
            'UTR No',
            'UTR Message',
            'Account Name',
            'Account Number',
            'IFSC',
            'Rejection Reason',
        ];
    }
    public function map($bt): array
    {
        $actions = [
            1 => $bt->status,
            2 => 'Initiate Followup',
            3 => 'Returned via Bank Transfer',
            4 => 'Settled with Project Account',
        ];
        $team =
            User::where(
                'name',
                $bt->emd->requested_by,
            )->first()->team ?? '';

        return [
            $bt->created_at->format('d-m-Y'),
            $team,
            $bt->emd->requested_by,
            $bt->emd->project_name,
            $bt->emd->tender->statuses->name ?? $bt->emd->type,
            format_inr($bt->bt_amount),
            $bt->action ? $actions[$bt->action] : '',
            $bt->date_time ? date('d-m-Y', strtotime($bt->date_time)) : '',
            $bt->utr,
            $bt->utr_mgs,
            $bt->bt_acc_name,
            $bt->bt_acc,
            $bt->bt_ifsc,
            $bt->rejection_reason,
        ];
    }
}
