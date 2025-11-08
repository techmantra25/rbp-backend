<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Models\Team;
use App\Models\User;
use App\Models\Store;
use App\Models\Activity;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class ZSMController extends Controller
{
      //inactive ASE report for ZSM in dashboard
      public function inactiveAseListZSM(Request $request)
      {
           $userId = $_GET['user_id'];
           $aseDetails = Team::select('users.id')->join('users', 'teams.ase_id', '=', 'users.id')->where('teams.zsm_id', '=', $userId)->groupby('teams.ase_id')->orderby('teams.ase_id')->get()->pluck('id')->toArray();
                   
           $activeASEreport=Activity::where('type','Visit Started')->whereDate('created_at', '=', Carbon::now())->whereIn('user_id',$aseDetails)->pluck('user_id')->toArray();
                   
           $inactiveASE=Team::select(DB::raw("users.id as id"),DB::raw("users.name as name"),DB::raw("users.mobile as mobile"),DB::raw("users.state as state"),DB::raw("users.city as city"))->join('users', 'teams.ase_id', '=', 'users.id')->where('teams.zsm_id', '=', $userId)->whereNotIn('users.id',$activeASEreport)->groupby('teams.ase_id')->orderby('teams.ase_id')->get();
               
           return response()->json(['error' => false, 'resp' => 'Inactive ASE report - Team wise', 'data' => $inactiveASE]);
           
      }
      
      
      public function aseListZSM(Request $request)
	  {
		  $userId = $_GET['user_id'];
		  $data=Team::select('ase_id')->where('zsm_id', '=', $userId)->groupby('teams.ase_id')->orderby('teams.ase_id')->with('ase:id,name')->get();
		  return response()->json(['error' => false, 'resp' => 'ASE report - ZSM wise', 'data' => $data]);
	  }
	  
	  
	  	public function asmListZSM(Request $request)
	  {
		  $userId = $_GET['user_id'];
		  $data=Team::select('asm_id')->where('zsm_id', '=', $userId)->groupby('teams.asm_id')->orderby('teams.asm_id')->with('asm:id,name')->get();
		  return response()->json(['error' => false, 'resp' => 'ASM report - ZSM wise', 'data' => $data]);
	  }
	  
	  public function areaList(Request $request)
	  {
		  $userId = $_GET['user_id'];
		  $data=Team::select('area_id')->where('zsm_id', '=', $userId)->groupby('teams.area_id')->orderby('teams.area_id')->with('areas:id,name')->get();
		  return response()->json(['error' => false, 'resp' => 'Area report - ZSM wise', 'data' => $data]);
	  }
	  
	  
	   public function distributorList(Request $request)
	  {
		  $userId = $_GET['user_id'];
		  $areaId = $_GET['area_id'];
		  $data=Team::select('distributor_id')->where('zsm_id', '=', $userId)->where('area_id', '=', $areaId)->where('store_id',NULL)->groupby('teams.distributor_id')->orderby('teams.distributor_id')->with('distributors:id,name')->get();
		  return response()->json(['error' => false, 'resp' => 'Distributor report - ZSM wise', 'data' => $data]);
	  }
	  	public function rsmListZSM(Request $request)
	  {
		  $userId = $_GET['user_id'];
		  $data=Team::select('rsm_id')->where('zsm_id', '=', $userId)->groupby('teams.rsm_id')->orderby('teams.rsm_id')->with('rsm:id,name')->get();
		  return response()->json(['error' => false, 'resp' => 'RSM report - ZSM wise', 'data' => $data]);
	  }
      //ase wise store order count
      private function aseWiseStoreData($ase_id,$asm_id,$date_from,$date_to,$collection,$category,$style_no){
		//$retailers = DB::table('stores')->select('id','name')->where('user_id',$ase_id)->orderby('name')->get();

		$total_quantity = 0;
		if($ase_id!=0 ){
		//	foreach($retailers as $retailer) {
				if ( !empty($date_from) || !empty($date_to) ) {
					// date from
					if (!empty($date_from)) {
						$from = date('Y-m-d', strtotime($date_from));
					} else {
						$from = date('Y-m-01');
					}

					// date to
					if (!empty($date_to)) {
						$to = date('Y-m-d', strtotime($date_to));
					} else {
						$to = date('Y-m-d', strtotime('+1 day'));
					}

					// collection
					if (!isset($collection) || $collection == '10000') {
						$collectionQuery = "";
					} else {
						$collectionQuery = " AND p.collection_id = ".$collection;
					}

					// category
					if ($category == '10000' || !isset($category)) {
						$categoryQuery = "";
					} else {
						$categoryQuery = " AND p.cat_id = ".$category;
					}

					 // style no
                    if (empty($style_no)) {
						//dd($style_no);
                        $styleNoQuery = "";
                    } else {
                        $styleNoQuery = " AND p.style_no LIKE '%".$style_no."%'";
                    }
					// order by
					$orderByQuery = "op.id ASC";

					$report = DB::select("SELECT IFNULL(SUM(op.qty), 0) AS qty FROM `orders` AS o
							INNER JOIN order_products AS op ON op.order_id = o.id
							INNER JOIN products p ON p.id = op.product_id
						    WHERE o.user_id='".$asm_id."' OR o.user_id = '".$ase_id."' 
							".$collectionQuery."
							".$categoryQuery."
							".$styleNoQuery."
							AND (date(o.created_at) BETWEEN '".$from."' AND '".$to."')
							ORDER BY ".$orderByQuery);
				} else {
					$report = DB::select("SELECT IFNULL(SUM(op.qty), 0) AS qty FROM `orders` AS o INNER JOIN order_products AS op ON op.order_id = o.id  WHERE o.user_id='".$asm_id."' OR o.user_id = '".$ase_id."'  AND (date(o.created_at) BETWEEN '".date('Y-m-01')."' AND '".date('Y-m-d', strtotime('+1 day'))."')");
				}


				$quantity = $report[0]->qty;

				//echo $quantity."<br>";
				$total_quantity+=$quantity;

			//}
		}
		
		return $total_quantity;
	}

    //team wise report
    public function storeReportZSM(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'user_id' => ['required'],
			'date_from' => ['nullable'],
			'date_to' => ['nullable'],
			'area_id' => ['nullable'],
			'collection' => ['nullable'],
			'category' => ['nullable'],
			'orderBy' => ['nullable'],
			'style_no' => ['nullable'],
		]);

		$aseResp = $resp = [];

		if (!$validator->fails()) {
			$user = User::findOrFail($request->user_id);
			$userName = $user->name;
			$area_id = $request->area_id;

			$rsm_arr_result = DB::select("SELECT DISTINCT rsm_id as rsm_n from teams where zsm_id='$request->user_id' and rsm_id is not null");
			
			foreach($rsm_arr_result as $rsm){
				$rsmResult = $rsm->rsm_n;
				$rsm_data=User::findOrFail($rsm->rsm_n);
                $rsm_name=$rsm_data->name;
                $rsm_id =$rsm_data->id;
				if($area_id!=''){
					$ase_arr_result = DB::select("SELECT DISTINCT ase_id as ase_n,asm_id as asm_n from teams where rsm_id='$rsmResult' and area_id='$area_id' and ase_id is not null");
				}else{
					$ase_arr_result = DB::select("SELECT DISTINCT ase_id as ase_n,asm_id as asm_n from teams where rsm_id='$rsmResult' and ase_id is not null");
				}
				

				$rsm_total_quantity = 0;
				
				foreach($ase_arr_result as $ase){
					$ase_name = $ase->ase_n;
					$asm_id=$ase->asm_n;
					$user_result = DB::select("SELECT IFNULL(id, 0) as id from users where id='$ase_name'");

					if(count($user_result)>0){
						$ase_id = $user_result[0]->id;
					}else{
						$ase_id = 0;
					}

					$total_quantity = 0;

					if($ase_id!=0){
						$total_quantity = $this->aseWiseStoreData($ase_id,$asm_id,$request->date_from,$request->date_to,$request->collection,$request->category,$request->style_no);
					}

					$rsm_total_quantity+=$total_quantity;

				}
				
				$asmResp[] = [
				               'rsm_id' => $rsm_id,
								'rsm' => $rsm_name,
								'quantity' => $rsm_total_quantity
							];
			}

			$resp[] = [
				'secondary_sales' => $asmResp,
			];

			return response()->json(['error' => false, 'message' => 'ZSM report - Team wise', 'data' => $resp]);
		} else {
			return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
		}
	}

        //product wise team report
    public function productReportZSM(Request $request)
    {
        \DB::connection()->enableQueryLog();
        $validator = Validator::make($request->all(), [
            'user_id' => ['required'],
            'date_from' => ['nullable'],
            'date_to' => ['nullable'],
            'collection' => ['nullable'],
            'category' => ['nullable'],
            'orderBy' => ['nullable'],
            'style_no' => ['nullable'],
        ]);

        if (!$validator->fails()) {
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
                INNER JOIN teams t ON s.id = t.store_id
                WHERE t.zsm_id = ".$request->user_id."
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
                INNER JOIN teams t ON s.id = t.store_id
                WHERE t.zsm_id = ".$request->user_id."
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
         	return response()->json(['error' => false, 'resp' => 'ZSM report - Product wise', 'data' => $resp]);
        } else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }
    }

    //notification list
    public function notificationList(Request $request){
		$validator = Validator::make($request->all(), [
			'user_id' => ['required'],
			'pageNo' => ['nullable'],
		]);
		
		if (!$validator->fails()) {
			$user_id = $request->user_id;
          	$pageNo =$request->pageNo;
			if(!$pageNo){
               $page=1;
             }else{
              $page=$pageNo;
			  }
              $limit=20;
              $offset=($page-1)*$limit;
			  $notifications = DB::select("select * from notifications where receiver_id='$user_id' ORDER BY id desc LIMIT ".$limit." OFFSET ".$offset."");
			  $notificationCount=DB::table('notifications')->where('receiver_id','=',$user_id)->count();
			  $count= (int) ceil($notificationCount / $limit);
				return response()->json(['error' => false, 'message' => 'User wise notification list', 'data' => $notifications,'count'=>$count]);
			
			
		}else{
			return response()->json(['error' => true, 'message' => 'Please send a valid user']);
		}
	}
	//notification update
	public function readNotification(Request $request){
		$id = $request->id;
		$read_time = date("Y-m-d G:i:s");
		
		DB::select("update notifications set read_flag=1, read_at='$read_time' where id='$id'");
		
		return response()->json(['error' => false, 'message' => 'Notification date updated successfully']);
	}
}
