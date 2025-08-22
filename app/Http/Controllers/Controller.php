<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function callAction($method, $parameters)
    {
        $user = Auth::user();
        Log::channel('controller_calls')->info(
            'Controller: ' . static::class . ', Method: ' . $method . ', Called by: ' . ($user->name ?? 'Guest') . ' (' . ($user->email ?? '') . '),
            Full URL: ' . request()->fullUrl()
        );

        return parent::callAction($method, $parameters);
    }
}
