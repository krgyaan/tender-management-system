<?php

namespace App\Services;

use App\Models\EmdResponsiblity;
use App\Services\EmailSignatureService;

class EmdResponsibilityService
{
    public function getResponsibleUser($instrumentType)
    {
        // Try to find responsibility record
        $resp = EmdResponsiblity::where('responsible_for', $instrumentType)->first();
        if ($resp) {
            return $resp->responsible;
        }
        return null;
    }

    public function getSignatureForUser($user)
    {
        if ($user) {
            $signatureService = new EmailSignatureService();
            return $signatureService->generateByUserId($user->id);
        }
        return '';
    }
}
