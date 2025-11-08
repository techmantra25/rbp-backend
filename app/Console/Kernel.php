<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\RetailerWalletTxn;
use App\Models\Store;
class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
   protected function schedule(Schedule $schedule)
    {

    $schedule->call(function () {

        // your schedule code
        $duplicateRecords=DB::select("SELECT * FROM `retailer_wallet_txns` GROUP BY barcode_id HAVING COUNT(*)>1 ");
           // dd($duplicateRecords);
		    //$duplicateRecords = DB::selectRaw('count(`*`) as `occurences`')
             // ->from('retailer_wallet_txns')
              //->groupBy('barcode_id')
              //->having('occurences', '>', 1)
              //->orderBy('id','desc')
             // ->get();
                        //duplicate exist check
                        if(!empty($duplicateRecords)){
                       foreach($duplicateRecords as $record) {
                            $store=Store::where('id',$record->user_id)->first();
                            $store->wallet-=$record->amount;
                            $store->save();
                            RetailerWalletTxn::where('id', $record->id)->delete();
                            
                        }
                        }
                        
         dd('done');
        
    })->everyMinute();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
