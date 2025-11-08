<?php
 
// function along with three parameters
 function duplicateCheck(Request $request)
    {
        
            $duplicateRecords=DB::select("SELECT * FROM `retailer_wallet_txns` where barcode_id != NULL GROUP BY barcode_id HAVING COUNT(*)>1");
            
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
                        $data = [
                                   
                                    "command" => 'yes',
                                   
                                    "created_at" => date('Y-m-d H:i:s'),
                                    "updated_at" => date('Y-m-d H:i:s'),
                                ];

                                $resp = DB::table('cron_logs')->insertGetId($data); 
         
                    
    }
 

echo "done";
 
?>