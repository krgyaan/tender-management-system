<?php

namespace App\Console\Commands;

use App\Http\Controllers\FinanceController;
use Illuminate\Console\Command;

class CallRentExpiryMethod extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:rent-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to send mail to user about rent aggreement expiry';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $controller = new FinanceController();
        $controller->RentExpiryMail();
        $this->info('Command executed successfully.');
    }
}
