<?php

namespace App\Http\Controllers\Front;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\User;
use Auth;
use App\Models\Activity;
use DB;
use Carbon\Carbon;
class TestController extends Controller
{
    public function test(Request $request)
{
    $batchSize = 10; // Number of rows to delete in each iteration

    do {
        // Delete rows in batches of $batchSize
        $deletedRows = DB::table('retailer_barcodes as rb')
            ->leftJoin('retailer_wallet_txns as rwt', 'rb.code', '=', 'rwt.barcode')
            ->whereNull('rwt.barcode')
            ->where('rb.name', 'HARYANA_16-8-2024_2000')
            ->limit($batchSize)
            ->delete();
    } while ($deletedRows ); // Continue until no more rows are deleted

    dd('done');
}
    
}