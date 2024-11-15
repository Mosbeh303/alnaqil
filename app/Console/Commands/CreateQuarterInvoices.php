<?php

namespace App\Console\Commands;

use App\Models\Store;
use Illuminate\Console\Command;
use Log;
use Throwable;

class CreateQuarterInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'createInvoices:quarterly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $stores = Store::where('auto_billing', 1)->where('billing_period', 'quarter')->get();
        $to = date('Y-m-d');
        foreach($stores as $store){

            try{
                $from = $store->invoices()->exists() ? date("Y-m-d", strtotime($store->invoices()->latest()->first()->end_date  . "+1 day" )) : date("Y-m-d", strtotime( "2023-09-09"));

                createInvoice($store, $from, $to);
            } catch (Throwable $th) {
                Log::error($th);
            }

        }
    }
}
