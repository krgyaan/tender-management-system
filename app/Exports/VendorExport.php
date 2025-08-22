<?php

namespace App\Exports;

use App\Models\VendorOrg;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VendorExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        // Eager load related data for efficiency
        $orgs = VendorOrg::with(['vendors', 'gsts', 'accounts'])->get();
        $rows = collect();
        foreach ($orgs as $org) {
            $gstStates = $org->gsts->pluck('gst_state')->implode(", ");
            $gstNums = $org->gsts->pluck('gst_num')->implode(", ");
            $accNames = $org->accounts->pluck('account_name')->implode(", ");
            $accNums = $org->accounts->pluck('account_num')->implode(", ");
            $accIfscs = $org->accounts->pluck('account_ifsc')->implode(", ");
            if ($org->vendors->count() > 0) {
                foreach ($org->vendors as $vendor) {
                    $rows->push((object) [
                        'org_name' => $org->name,
                        'vendor_name' => $vendor->name,
                        'email' => $vendor->email,
                        'mobile' => $vendor->mobile,
                        'address' => $vendor->address,
                        'gst_states' => $gstStates,
                        'gst_nums' => $gstNums,
                        'acc_names' => $accNames,
                        'acc_nums' => $accNums,
                        'acc_ifsces' => $accIfscs,
                    ]);
                }
            } else {
                $rows->push((object) [
                    'org_name' => $org->name,
                    'vendor_name' => '',
                    'email' => '',
                    'mobile' => '',
                    'address' => '',
                    'gst_states' => $gstStates,
                    'gst_nums' => $gstNums,
                    'acc_names' => $accNames,
                    'acc_nums' => $accNums,
                    'acc_ifsces' => $accIfscs,
                ]);
            }
        }
        return $rows;
    }

    public function headings(): array
    {
        return [
            'Organisation Name',
            'Vendor Name',
            'Email',
            'Mobile',
            'Address',
            'GST State(s)',
            'GST Number(s)',
            'Account Name(s)',
            'Account Number(s)',
            'Account IFSC(s)'
        ];
    }

    public function map($row): array
    {
        return [
            $row->org_name,
            $row->vendor_name,
            $row->email,
            $row->mobile,
            $row->address,
            $row->gst_states,
            $row->gst_nums,
            $row->acc_names,
            $row->acc_nums,
            $row->acc_ifsces,
        ];
    }
}
