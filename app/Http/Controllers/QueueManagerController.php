<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Carbon\Carbon;

class QueueManagerController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'pending_jobs' => DB::table('jobs')->count(),
            'failed_jobs' => DB::table('failed_jobs')->count(),
            'processed_today' => DB::table('jobs')
                ->where('created_at', '>=', now()->startOfDay()->timestamp)
                ->count(),
        ];

        $recentFailed = DB::table('failed_jobs')
            ->orderBy('failed_at', 'desc')
            ->limit(10)
            ->get();

        $pendingJobs = DB::table('jobs')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($job) {
                $payload = json_decode($job->payload, true);
                return [
                    'id' => $job->id,
                    'queue' => $job->queue,
                    'job_name' => $payload['displayName'] ?? 'Unknown',
                    'attempts' => $job->attempts,
                    'created_at' => Carbon::createFromTimestamp($job->created_at)->format('Y-m-d H:i:s'),
                ];
            });

        return view('queue.dashboard', compact('stats', 'recentFailed', 'pendingJobs'));
    }

    public function failedJobs()
    {
        $failedJobs = DB::table('failed_jobs')
            ->orderBy('failed_at', 'desc')
            ->paginate(20);

        return view('queue.failed-jobs', compact('failedJobs'));
    }

    public function retryJob($uuid)
    {
        try {
            $job = DB::table('failed_jobs')->where('uuid', $uuid)->first();

            if (!$job) {
                return redirect()->back()->with('error', 'Job not found');
            }

            // Decode the payload
            $payload = json_decode($job->payload, true);

            // Re-queue the job
            DB::table('jobs')->insert([
                'queue' => $job->queue,
                'payload' => $job->payload,
                'attempts' => 0,
                'reserved_at' => null,
                'available_at' => time(),
                'created_at' => time(),
            ]);

            // Remove from failed jobs
            DB::table('failed_jobs')->where('uuid', $uuid)->delete();

            return redirect()->back()->with('success', 'Job retried successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to retry job: ' . $e->getMessage());
        }
    }

    public function retryAllFailed()
    {
        try {
            $failedJobs = DB::table('failed_jobs')->get();
            $count = 0;

            foreach ($failedJobs as $job) {
                DB::table('jobs')->insert([
                    'queue' => $job->queue,
                    'payload' => $job->payload,
                    'attempts' => 0,
                    'reserved_at' => null,
                    'available_at' => time(),
                    'created_at' => time(),
                ]);
                $count++;
            }

            // Clear all failed jobs
            DB::table('failed_jobs')->truncate();

            return redirect()->back()->with('success', "Retried {$count} failed jobs");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to retry jobs: ' . $e->getMessage());
        }
    }

    public function deleteFailedJob($uuid)
    {
        DB::table('failed_jobs')->where('uuid', $uuid)->delete();
        return redirect()->back()->with('success', 'Failed job deleted');
    }

    public function clearAllFailed()
    {
        DB::table('failed_jobs')->truncate();
        return redirect()->back()->with('success', 'All failed jobs cleared');
    }

    public function processQueue()
    {
        try {
            // Process queued jobs manually
            $this->processJobs();
            return redirect()->back()->with('success', 'Queue processed successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error processing queue: ' . $e->getMessage());
        }
    }

    private function processJobs($limit = 10)
    {
        $jobs = DB::table('jobs')
            ->where('available_at', '<=', time())
            ->where('reserved_at', null)
            ->orderBy('created_at')
            ->limit($limit)
            ->get();

        foreach ($jobs as $jobRecord) {
            try {
                // Mark job as reserved
                DB::table('jobs')
                    ->where('id', $jobRecord->id)
                    ->update(['reserved_at' => time()]);

                $payload = json_decode($jobRecord->payload, true);
                $job = unserialize($payload['data']['command']);

                // Execute the job
                $job->handle();

                // Remove job from queue on success
                DB::table('jobs')->where('id', $jobRecord->id)->delete();
            } catch (\Exception $e) {
                // Increment attempts
                $attempts = $jobRecord->attempts + 1;

                if ($attempts >= 3) {
                    // Move to failed jobs
                    DB::table('failed_jobs')->insert([
                        'uuid' => \Illuminate\Support\Str::uuid(),
                        'connection' => 'database',
                        'queue' => $jobRecord->queue,
                        'payload' => $jobRecord->payload,
                        'exception' => $e->getMessage() . "\n" . $e->getTraceAsString(),
                        'failed_at' => now(),
                    ]);

                    // Remove from jobs table
                    DB::table('jobs')->where('id', $jobRecord->id)->delete();
                } else {
                    // Increment attempts and make available again
                    DB::table('jobs')
                        ->where('id', $jobRecord->id)
                        ->update([
                            'attempts' => $attempts,
                            'reserved_at' => null,
                            'available_at' => time() + 60 // Retry after 1 minute
                        ]);
                }
            }
        }
    }
}
