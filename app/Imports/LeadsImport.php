<?php

namespace App\Imports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Add this
use Illuminate\Support\Facades\Log;

class LeadsImport implements ToModel, WithHeadingRow // Add WithHeadingRow
{
    public function model(array $row)
    {
        Log::info('Importing Lead: ' . $row['company_name']);
        
        try {
            Log::info('Succeeded in importing Lead: ' . $row['company_name']);
            return new Lead([
                'company_name' => $row['company_name'],
                'name' => $row['name'],
                'designation' => $row['designation'],
                'phone' => $row['phone'],
                'email' => $row['email'],
                'address' => $row['address'],
                'state' => $row['state'],
                'type' => $row['type'],
                'industry' => $row['industry'],
                'team' => $row['team'],
                'points_discussed' => $row['points_discussed'],
                've_responsibility' => $row['ve_responsibility'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to import Lead: ' . $row['company_name'] . ' due to: ' . $e->getMessage());
        }
    }
}
