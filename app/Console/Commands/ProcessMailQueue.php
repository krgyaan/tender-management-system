<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ProcessMailQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Options:
     *  --times: how many single-pass iterations to run (default 10)
     *  --queue: comma-separated queues to work (default "mail,default")
     */
    protected $signature = 'app:queue-work-mail {--times=10} {--queue=mail,default}';

    /**
     * The console command description.
     */
    protected $description = 'Process queued mail jobs in short single-pass iterations (cron friendly)';

    public function handle(): int
    {
        $times  = (int) $this->option('times');
        if ($times < 1) $times = 1;
        if ($times > 100) $times = 100; // safety cap

        $queues = (string) $this->option('queue');

        // Allow overriding worker behavior via env
        $tries   = (int) env('QUEUE_WORKER_TRIES', 3);
        $backoff = (int) env('QUEUE_WORKER_BACKOFF', 60);
        $timeout = (int) env('QUEUE_WORKER_TIMEOUT', 120);

        $this->info("Working queues: {$queues}; iterations: {$times}; tries: {$tries}; backoff: {$backoff}; timeout: {$timeout}s");

        for ($i = 0; $i < $times; $i++) {
            Artisan::call('queue:work', [
                '--queue'   => $queues,
                '--once'    => true,         // single job reservation per pass
                '--tries'   => $tries,
                '--backoff' => $backoff,
                '--timeout' => $timeout,
            ]);
        }

        $this->info('Mail queue processing iterations completed');
        return self::SUCCESS;
    }
}

