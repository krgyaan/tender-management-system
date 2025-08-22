<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\Employeeimprest;
use App\Models\Employeeimprestamount;
use Illuminate\Support\Facades\Log;

class EmployeeImprestExport implements FromCollection, WithHeadings, WithMapping
{
    protected $user;
    protected $startDate;
    protected $endDate;

    protected $nameId;

    public function __construct($user, $startDate, $endDate, $nameId = null)
    {
        $this->user = $user;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->nameId = $nameId;
    }

    public function collection()
    {
        $query = EmployeeImprest::with(['user', 'category', 'team']);
        Log::info($this->nameId);

        // Apply role-based filtering
        if ($this->user->role != 'admin' && $this->user->role != 'coordinator') {
            $query->where('name_id', $this->user->id);
        } else {
            $query->where('name_id', $this->nameId);
        }

        // Apply date filters if provided
        if ($this->startDate) {
            $query->where('strtotime', '>=', strtotime($this->startDate));
        }
        if ($this->endDate) {
            $query->where('strtotime', '<=', strtotime($this->endDate));
        }

        Log::info('EmployeeImprestExport: ' . $query->toSql());

        return $query->get();
    }
    public function headings(): array
    {
        return [
            'ID',
            'Employee Name',
            'Party Name',
            'Category',
            'Project Name',
            'Team',
            'Amount',
            'Button Status',
            'Remarks',
            'Accountant Remarks',
            'Created At',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->name_id ? $row->user->name : 'NA',
            $row->party_name,
            $row->category_id ? $row->category->category : 'NA',
            $row->project_name,
            $row->team_id ? $row->team->name : 'NA',
            $row->amount,
            $row->buttonstatus ? 'Approved' : 'Pending',
            $row->remark,
            $row->acc_remark,
            $row->created_at->format('d-m-Y'),
        ];
    }
}
