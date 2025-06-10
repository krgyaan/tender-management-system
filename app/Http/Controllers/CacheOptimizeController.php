<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CacheOptimizeController extends Controller
{
    public function optimize()
    {
        try {
            Artisan::call('config:cache');
            Log::info('✅ config:cache executed successfully.');

            Artisan::call('route:cache');
            Log::info('✅ route:cache executed successfully.');

            Artisan::call('view:cache');
            Log::info('✅ view:cache executed successfully.');

            return back()->with('success', 'Cache optimized successfully.');
        } catch (\Exception $e) {
            Log::error('❌ Cache optimization failed: ' . $e->getMessage());

            return back()->with('error', 'Cache optimization failed. Check logs.');
        }
    }
}
