<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyTallyKey
{
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('VerifyTallyKey middleware executed', ['ip' => request()->ip()]);
        $keyFromRequest = $request->header('X-SECURITY-KEY');
        $validKey = config('tally.security_key');

        $logContext = [
            'ip' => $request->ip(),
            'route' => $request->fullUrl(),
            'timestamp' => now()->toDateTimeString(),
            'provided_key' => $keyFromRequest,
        ];

        if ($keyFromRequest !== $validKey) {
            Log::channel('tally')->warning('Access denied', $logContext);
            return response()->json(['error' => 'Access denied', 'message' => 'Invalid security key provided or missing'], 401);
        }
        Log::channel('tally')->info('Access granted', $logContext);

        return $next($request);
    }
}
