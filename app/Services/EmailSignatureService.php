<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class EmailSignatureService
{
    public function generateByUserId(int $userId): string
    {
        $user = User::findOrFail($userId);

        $data = [
            'name' => $user->name,
            'designation' => $user->role ?? 'Team Member',
            'email' => $user->email,
            'phone' => $user->mobile ?? '',
            'company' => config('app.company_name', 'Volks Energie Pvt. Ltd.'),
            'address' => $user->address ?? '',
            'companyLogo' => 'https://tms.volksenergie.in/assets/images/ve_logo_2.png',
            'website' => 'https://www.volksenergie.in'
        ];
        return View::make('partials.email-signature', $data)->render();
    }
}
