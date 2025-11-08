<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\User;
use App\Models\Team;
use App\Models\Area;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;
class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function distributorListStoreCountASE(Request $request)
    {
        \DB::connection()->enableQueryLog();
        $validator = Validator::make($request->all(), [
            'area_id' => ['required'],
            'user_id' => ['required'],
        ]);

        if (!$validator->fails()) {
        $userId = $_GET['user_id'];
        $areaId = $_GET['area_id'];
        $distributors =Team::select('distributor_id','area_id')->where('ase_id',$userId)->where('area_id',$areaId)->where('store_id',NULL)->with('distributors:id,name,mobile,email,address,city,state')->distinct('distributor_id')->get();
        $retailerResp = $resp = [];
        foreach($distributors as $item) {
            
           $report =DB::select("SELECT  COUNT(*) as store_count FROM `stores` s
                INNER JOIN teams t ON s.id = t.store_id
                WHERE t.distributor_id = ".$item->distributor_id." and s.status= 1
                GROUP BY t.distributor_id
                ORDER BY t.distributor_id DESC");
            
        $retailerResp[] = [
                    'distributor_id' => $item->distributor_id??'',
                    'distributor_name' => $item->distributors->name??'',
                    'count' => $report[0]->store_count ??''
                ];
            }

        $resp[] = [
                'store_count' => $retailerResp,
            ];
        return response()->json(['error' => false, 'resp' => 'Store report - Distributor wise', 'data' => $resp]);
        } else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }

    }
    
    
     public function index(Request $request)
    {
        $userId = $_GET['distributor_id'];
        $stores =Store::select('stores.*')->join('teams', 'stores.id', '=', 'teams.store_id')
        ->where('teams.distributor_id',$userId)->where('stores.status',1)->orderby('stores.id','desc')->with('states:id,name','areas:id,name')->get();
        if ($stores) {
		    return response()->json(['error'=>false, 'resp'=>'Store data fetched successfully','data'=>$stores]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }

    }
    
    
    public function distributorListStoreCountASM(Request $request)
    {
        \DB::connection()->enableQueryLog();
        $validator = Validator::make($request->all(), [
            'area_id' => ['required'],
            'user_id' => ['required'],
        ]);

        if (!$validator->fails()) {
        $userId = $_GET['user_id'];
        $areaId = $_GET['area_id'];
        $distributors =Team::select('distributor_id','area_id')->where('asm_id',$userId)->where('area_id',$areaId)->where('store_id',NULL)->with('distributors:id,name,mobile,email,address,city,state')->distinct('distributor_id')->get();
        $retailerResp = $resp = [];
        foreach($distributors as $item) {
            
           $report =DB::select("SELECT  COUNT(*) as store_count FROM `stores` s
                INNER JOIN teams t ON s.id = t.store_id
                WHERE t.distributor_id = ".$item->distributor_id." and s.status= 1
                GROUP BY t.distributor_id
                ORDER BY t.distributor_id DESC");
            
        $retailerResp[] = [
                    'distributor_id' => $item->distributor_id,
                    'distributor_name' => $item->distributors->name,
                    'count' => $report[0]->store_count
                ];
            }

        $resp[] = [
                'store_count' => $retailerResp,
            ];
        return response()->json(['error' => false, 'resp' => 'Store report - Distributor wise', 'data' => $resp]);
        } else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }

    }

   
   
   public function distributorListStoreCountRSM(Request $request)
    {
        \DB::connection()->enableQueryLog();
        $validator = Validator::make($request->all(), [
            
            'user_id' => ['required'],
        ]);

        if (!$validator->fails()) {
        $userId = $_GET['user_id'];
      
        $distributors =Team::select('distributor_id','area_id')->where('rsm_id',$userId)->where('store_id',NULL)->with('distributors:id,name,mobile,email,address,city,state')->distinct('distributor_id')->get();
        $retailerResp = $resp = [];
        foreach($distributors as $item) {
            
           $report =DB::select("SELECT  COUNT(*) as store_count FROM `stores` s
                INNER JOIN teams t ON s.id = t.store_id
                WHERE t.distributor_id = ".$item->distributor_id." and s.status= 1
                GROUP BY t.distributor_id
                ORDER BY t.distributor_id DESC");
            
        $retailerResp[] = [
                    'distributor_id' => $item->distributor_id,
                    'distributor_name' => $item->distributors->name,
                    'count' => $report[0]->store_count
                ];
            }

        $resp[] = [
                'store_count' => $retailerResp,
            ];
        return response()->json(['error' => false, 'resp' => 'Store report - Distributor wise', 'data' => $resp]);
        } else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }

    }
    
    
    public function distributorListStoreCountZSM(Request $request)
    {
        \DB::connection()->enableQueryLog();
        $validator = Validator::make($request->all(), [
            
            'user_id' => ['required'],
        ]);

        if (!$validator->fails()) {
        $userId = $_GET['user_id'];
      
        $distributors =Team::select('distributor_id','area_id')->where('zsm_id',$userId)->where('store_id',NULL)->with('distributors:id,name,mobile,email,address,city,state')->distinct('distributor_id')->get();
        $retailerResp = $resp = [];
        foreach($distributors as $item) {
            
           $report =DB::select("SELECT  COUNT(*) as store_count FROM `stores` s
                INNER JOIN teams t ON s.id = t.store_id
                WHERE t.distributor_id = ".$item->distributor_id." and s.status= 1
                GROUP BY t.distributor_id
                ORDER BY t.distributor_id DESC");
            
        $retailerResp[] = [
                    'distributor_id' => $item->distributor_id,
                    'distributor_name' => $item->distributors->name,
                    'count' => $report[0]->store_count
                ];
            }

        $resp[] = [
                'store_count' => $retailerResp,
            ];
        return response()->json(['error' => false, 'resp' => 'Store report - Distributor wise', 'data' => $resp]);
        } else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }

    }

}
