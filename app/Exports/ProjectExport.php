<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProjectExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(protected $projects)
    {
        $this->projects = $projects;
    }

    public function collection()
    {
        return $this->projects;
    }

    public function headings(): array
    {
        return [
            'Project Name',
            'Project Code',
            'Location',
            'PO Number',
            'PO Date',
        ];
    }

    public function map($row): array
    {
        return [
            $row->project_name,
            $row->project_code,
            $row->location->address ?? '',
            $row->po_no,
            $row->po_date ? date('d-m-Y', strtotime($row->po_date)) : ''
        ];
    }
}
