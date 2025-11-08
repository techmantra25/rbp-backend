<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Cart;
use App\Models\User;
use App\Models\Store;
use App\Models\Visit;
use Illuminate\Support\Facades\Validator;
use DB;
use Carbon\CarbonPeriod;
class ReportController extends Controller
{
    //for ASE store wise report
    public function storeReportASE(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ase_id' => ['required'],
            'date_from' => ['nullable'],
            'date_to' => ['nullable'],
            'collection' => ['nullable'],
            'category' => ['nullable'],
            'orderBy' => ['nullable'],
            'style_no' => ['nullable'],
        ]);
         DB::enableQueryLog();
        if (!$validator->fails()) {
            $userName = User::findOrFail($request->ase_id);
            $userName = $userName->name;

            $retailers = Store::select('id','name','address','area_id','state_id','pin')->where('user_id',$request->ase_id)->orderby('name')->get();
            $retailerResp = $resp = [];

            foreach($retailers as $retailer) {
                if ( !empty($request->date_from) || !empty($request->date_to) ) {
                    // date from
                    if (!empty($request->date_from)) {
                        $from = date('Y-m-d', strtotime($request->date_from));
                    } else {
                        $from = date('Y-m-01');
                    }

                    // date to
                    if (!empty($request->date_to)) {
                        $to = date('Y-m-d', strtotime($request->date_to));
                    } else {
                        $to = date('Y-m-d', strtotime('+1 day'));
                    }

                    // collection
                    if (!isset($request->collection) || $request->collection == '10000') {
                        $collectionQuery = "";
                    } else {
                        $collectionQuery = " AND p.collection_id = ".$request->collection;
                    }

                    // category
                    if ($request->category == '10000' || !isset($request->category)) {
                        $categoryQuery = "";
                    } else {
                        $categoryQuery = " AND p.cat_id = ".$request->category;
                    }

                    // style no
                    if (!isset($request->style_no)) {
                        $styleNoQuery = "";
                    } else {
                        $styleNoQuery = " AND p.style_no LIKE '%".$request->style_no."%'";
                    }

                    // order by
                    if ($request->orderBy == 'date_asc') {
                        $orderByQuery = "op.id ASC";
                    } elseif ($request->orderBy == 'qty_asc') {
                        $orderByQuery = "qty ASC";
                    } elseif ($request->orderBy == 'qty_desc') {
                        $orderByQuery = "qty DESC";
                    } else {
                        $orderByQuery = "op.id DESC";
                    }

                    $report = DB::select("SELECT IFNULL(SUM(op.qty), 0) AS qty FROM `orders` AS o
                    INNER JOIN order_products AS op ON op.order_id = o.id
                    INNER JOIN products p ON p.id = op.product_id
                    WHERE o.store_id = '".$retailer->id."'
                    ".$collectionQuery."
                    ".$categoryQuery."
                    ".$styleNoQuery."
                    AND (date(o.created_at) BETWEEN '".$from."' AND '".$to."')
                    ORDER BY ".$orderByQuery);
                } else {
                    $report = DB::select("SELECT IFNULL(SUM(op.qty), 0) AS qty FROM `orders` AS o INNER JOIN order_products AS op ON op.order_id = o.id WHERE o.store_id = '".$retailer->id."' AND (date(o.created_at) BETWEEN '".date('Y-m-01')."' AND '".date('Y-m-d', strtotime('+1 day'))."')");
                }

                $retailerResp[] = [
                    'store_id' => $retailer->id,
                    'store_name' => $retailer->name,
                    'address' => $retailer->address,
                    'area' => $retailer->areas->name,
                    'state' => $retailer->states->name,
                    'pin' => $retailer->pin,
                    'quantity' => $report[0]->qty
                ];
            }

            $resp[] = [
                'secondary_sales' => $retailerResp,
            ];

            return response()->json(['error' => false, 'resp' => 'ASE report - Store wise', 'data' => $resp]);
        } else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }
    }

     //for ASE product wise report
     public function productReportASE(Request $request)
    {
        \DB::connection()->enableQueryLog();
        $validator = Validator::make($request->all(), [
            'ase_id' => ['required'],
            'date_from' => ['nullable'],
            'date_to' => ['nullable'],
            'collection' => ['nullable'],
            'category' => ['nullable'],
            'orderBy' => ['nullable'],
            'style_no' => ['nullable'],
        ]);

        if (!$validator->fails()) {
            $userName = User::findOrFail($request->ase_id);
            $userName = $userName->name;

            $retailerResp = $resp = [];

            if ( !empty($request->date_from) || !empty($request->date_to) ) {
                // date from
                if (!empty($request->date_from)) {
                    $from = date('Y-m-d', strtotime($request->date_from));
                } else {
                    $from = date('Y-m-01');
                }

                // date to
                if (!empty($request->date_to)) {
                    $to = date('Y-m-d', strtotime($request->date_to));
                } else {
                    $to = date('Y-m-d', strtotime('+1 day'));
                }

                // collection
                if ($request->collection == '10000' || !isset($request->collection)) {
                    $collectionQuery = "";
                } else {
                    $collectionQuery = " AND p.collection_id = ".$request->collection;
                }

                // category
                if ($request->category == '10000' || !isset($request->category)) {
                    $categoryQuery = "";
                } else {
                    $categoryQuery = " AND p.cat_id = ".$request->category;
                }

                // style no
                if (!isset($request->style_no)) {
                    $styleNoQuery = "";
                } else {
                    $styleNoQuery = " AND p.style_no LIKE '%".$request->style_no."%'";
                }

                // order by
                if ($request->orderBy == 'date_asc') {
                    $orderByQuery = "op.id ASC";
                } elseif ($request->orderBy == 'qty_asc') {
                    $orderByQuery = "qty ASC";
                } elseif ($request->orderBy == 'qty_desc') {
                    $orderByQuery = "qty DESC";
                } else {
                    $orderByQuery = "op.id DESC";
                }

                $report = DB::select("SELECT  p.name,op.product_id, p.style_no,IFNULL(SUM(op.qty), 0) AS product_count FROM `order_products` op
                INNER JOIN products p ON p.id = op.product_id
                INNER JOIN orders o ON o.id = op.order_id
                INNER JOIN stores s ON s.id = o.store_id
                WHERE o.user_id = ".$request->ase_id."
                AND (DATE(op.created_at) BETWEEN '".$from."' AND '".$to."')
                ".$collectionQuery."
                ".$categoryQuery."
                ".$styleNoQuery."
                GROUP BY op.product_id
                ORDER BY ".$orderByQuery);
                
            } else {
                $report = DB::select("SELECT  p.name,op.product_id, p.style_no, IFNULL(SUM(op.qty), 0) AS product_count FROM `order_products` op
                INNER JOIN products p ON p.id = op.product_id
                INNER JOIN orders o ON o.id = op.order_id
                INNER JOIN stores s ON s.id = o.store_id
                WHERE o.user_id = ".$request->ase_id."
                AND (DATE(op.created_at) BETWEEN '".date('Y-m-01')."' AND '".date('Y-m-d', strtotime('+1 day'))."')
                GROUP BY op.product_id
                ORDER BY op.id DESC");
            }

            foreach($report as $item) {
                $retailerResp[] = [
                    'style_no' => $item->style_no,
                    'product' => $item->name,
                    'quantity' => $item->product_count
                ];
            }

			$resp[] = [
				'secondary_sales' => $retailerResp,
			];
         	return response()->json(['error' => false, 'resp' => 'ASE report - Product wise', 'data' => $resp]);
        } else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }
    }
    
    
    
    
    //ase productivity
    
    public function aseProductivity(Request $request)
    {
        \DB::connection()->enableQueryLog();
        $validator = Validator::make($request->all(), [
            'ase_id' => ['required'],
            'date_from' => ['nullable'],
            'date_to' => ['nullable'],
        ]);

        if (!$validator->fails()) {
            
            
           
           
            $resp =$retailerResp= [];
            $respData=$respTotalData=[];
             $all_sc_total_amount = 0;
                        $all_tc_total_amount=0;
                        $all_pc_total_amount=0;
                        $all_mub_total_amount=0;
             if (!empty($request->date_from)) {
                    $from = date('Y-m-d', strtotime($request->date_from));
                } else {
                    $from = date('Y-m-01');
                }

                // date to
                if (!empty($request->date_to)) {
                    $to = date('Y-m-d', strtotime($request->date_to));
                } else {
                    $to = date('Y-m-d', strtotime('+1 day'));
                }
            //if ( !empty($request->date_from) || !empty($request->date_to) ) {
                // date from
               /* if (!empty($request->date_from)) {
                    $from = date('Y-m-d', strtotime($request->date_from));
                } else {
                    $from = date('Y-m-01');
                }

                // date to
                if (!empty($request->date_to)) {
                    $to = date('Y-m-d', strtotime($request->date_to));
                } else {
                    $to = date('Y-m-d', strtotime('+1 day'));
                }*/
                $period = CarbonPeriod::create($from, $to);

                // Iterate over the period
                foreach ($period as $date) {
                   $p[] = $date->format('Y-m-d');
                    
                }
                foreach($p as $item){
                     $month = date('m', strtotime($item));
                //$res=DB::select("select * from stores where user_id='$request->ase_id'");
                
                $visit=DB::select("select * from visits where user_id='$request->ase_id' and DATE_FORMAT(created_at,'%Y-%m-%d') = '$item' ORDER BY id asc");
                
                if(!empty($visit)){
                $res=Store::select('stores.*')->join('teams', 'teams.store_id', 'stores.id')->where('stores.area_id',$visit[0]->area_id)->where('teams.distributor_id',$visit[0]->distributor_id)->get();
                }else{
                    $res=[];
                }
                $resp['sc'] = count($res) ;
               
                $res2=DB::select("select * from activities where user_id='$request->ase_id' and DATE_FORMAT(created_at,'%Y-%m-%d') = '$item' and  (type='Order Upload' or type='No Order Placed' or type='Order On Call') GROUP BY created_at");
           
                $resp['tc'] = count($res2);
            
                $res3=DB::select("select * from orders where user_id='$request->ase_id' and DATE_FORMAT(created_at,'%Y-%m-%d') = '$item' GROUP BY created_at ");
           
                $resp['pc'] = count($res3);
          
                $res4=DB::select("select * from orders where user_id='$request->ase_id' and MONTH(created_at) = $month GROUP BY  store_id");
           
                $resp['mub'] = count($res4);
                if($resp['pc']!=0 && $resp['tc']!=0){
                     //$productivityCount_pc_p= number_format((float)($resp['pc']/$resp['tc'])*100);
                      $qrdQty = DB::select("SELECT count(op.id) AS total, sum(op.qty) AS qty FROM `orders` AS o 
                                            inner join order_products AS op on o.id = op.order_id 
                                            where o.user_id = '$request->ase_id' AND DATE(o.created_at) = '$item' ");
                                            $productivityCount_pc_p= number_format($qrdQty[0]->qty);
               }else{
                    $productivityCount_pc_p=0;
               }
                $retailerResp[] = [
                    'date' => $item,
                    'sc' =>  $resp['sc'],
                    'tc' => $resp['tc'],
                    'pc' => $resp['pc'],
                    'pc_p' =>$productivityCount_pc_p,
                   
                ];
                
                $all_sc_total_amount += ($resp['sc']);
                $all_tc_total_amount += ($resp['tc']);
                $all_pc_total_amount += ($resp['pc']);
                $all_mub_total_amount = ($resp['mub']);
                }
                	$respData[] = [
			        	'productivity' => $retailerResp,
			         ];
			         
			         $respTotalData[]=[
                            'total_sc' =>   $all_sc_total_amount,
                            'total_tc' => $all_tc_total_amount,
                            'total_pc' => $all_pc_total_amount,
                            
			             ];
			         
                 return response()->json(['error' => false, 'resp' => 'ASE productivity report - Store wise', 'data' => $respData,'total_count'=>$respTotalData]);
                
            //}
                
        }else{
             return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }
    }
    
    
    
    
    
    //asm productivity
    
    public function asmProductivity(Request $request)
    {
        \DB::connection()->enableQueryLog();
        $validator = Validator::make($request->all(), [
            'asm_id' => ['required'],
            'date_from' => ['nullable'],
            'date_to' => ['nullable'],
        ]);

        if (!$validator->fails()) {
            
            
           
           
            $resp =$retailerResp= [];
            $respData=$respTotalData=[];
             $all_sc_total_amount = 0;
                        $all_tc_total_amount=0;
                        $all_pc_total_amount=0;
                        $all_mub_total_amount=0;
             if (!empty($request->date_from)) {
                    $from = date('Y-m-d', strtotime($request->date_from));
                } else {
                    $from = date('Y-m-01');
                }

                // date to
                if (!empty($request->date_to)) {
                    $to = date('Y-m-d', strtotime($request->date_to));
                } else {
                    $to = date('Y-m-d', strtotime('+1 day'));
                }
            //if ( !empty($request->date_from) || !empty($request->date_to) ) {
                // date from
               /* if (!empty($request->date_from)) {
                    $from = date('Y-m-d', strtotime($request->date_from));
                } else {
                    $from = date('Y-m-01');
                }

                // date to
                if (!empty($request->date_to)) {
                    $to = date('Y-m-d', strtotime($request->date_to));
                } else {
                    $to = date('Y-m-d', strtotime('+1 day'));
                }*/
                $period = CarbonPeriod::create($from, $to);

                // Iterate over the period
                foreach ($period as $date) {
                   $p[] = $date->format('Y-m-d');
                    
                }
                foreach($p as $item){
                     $month = date('m', strtotime($item));
                //$res=DB::select("select * from stores where user_id='$request->asm_id'");
                $visit=DB::select("select * from visits where user_id='$request->asm_id' and DATE_FORMAT(created_at,'%Y-%m-%d') = '$item' ORDER BY id asc");
                
                if(!empty($visit)){
                $res=Store::select('stores.*')->join('teams', 'teams.store_id', 'stores.id')->where('stores.area_id',$visit[0]->area_id)->where('teams.distributor_id',$visit[0]->distributor_id)->get();
                }else{
                    $res=[];
                }
                $resp['sc'] = count($res);
           
                $res2=DB::select("select * from activities where user_id='$request->asm_id' and DATE_FORMAT(created_at,'%Y-%m-%d') = '$item' and  (type='Order Upload' or type='No Order Placed' or type='Order On Call') GROUP BY created_at");
           
                $resp['tc'] = count($res2);
            
                $res3=DB::select("select * from orders where user_id='$request->asm_id' and DATE_FORMAT(created_at,'%Y-%m-%d') = '$item' GROUP BY created_at ");
           
                $resp['pc'] = count($res3);
          
                $res4=DB::select("select * from orders where user_id='$request->asm_id' and MONTH(created_at) = $month GROUP BY  store_id");
           
                $resp['mub'] = count($res4);
                if($resp['pc']!=0 && $resp['tc']!=0){
                     //$productivityCount_pc_p= number_format((float)($resp['pc']/$resp['tc'])*100);
                      $qrdQty = DB::select("SELECT count(op.id) AS total, sum(op.qty) AS qty FROM `orders` AS o 
                                            inner join order_products AS op on o.id = op.order_id 
                                            where o.user_id = '$request->ase_id' AND DATE(o.created_at) = '$item' ");
                                            $productivityCount_pc_p= number_format($qrdQty[0]->qty);
               }else{
                    $productivityCount_pc_p=0;
               }
                $retailerResp[] = [
                    'date' => $item,
                    'sc' =>  $resp['sc'],
                    'tc' => $resp['tc'],
                    'pc' => $resp['pc'],
                    'pc_p' =>$productivityCount_pc_p,
                   
                ];
                
                $all_sc_total_amount += ($resp['sc']);
                $all_tc_total_amount += ($resp['tc']);
                $all_pc_total_amount += ($resp['pc']);
                $all_mub_total_amount = ($resp['mub']);
                }
                	$respData[] = [
			        	'productivity' => $retailerResp,
			         ];
			         
			         $respTotalData[]=[
                            'total_sc' =>   $all_sc_total_amount,
                            'total_tc' => $all_tc_total_amount,
                            'total_pc' => $all_pc_total_amount,
                           
			             ];
			         
                 return response()->json(['error' => false, 'resp' => 'ASM productivity report - Store wise', 'data' => $respData,'total_count'=>$respTotalData]);
                
            //}
                
        }else{
             return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }
    }
}
