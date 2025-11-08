<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\WarehouseStock;
use App\Models\RetailerBarcode;
use App\Models\OrderDistributor;
use App\Models\User;
use App\Models\State;
use App\Models\Area;
use App\Models\CodeStockIn;
use App\Models\DistributorStock;
use App\Models\Order;
use App\Models\OrderQrcode;
use App\Models\RetailerUserTxnHistory;
use App\Models\RetailerWalletTxn;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    public function ase(Request $request)
    {
        
        $ase =User::whereIN('type',[5,6])->where('status','=',1)->orderby('name')->get();
        if ($ase) {
		    return response()->json(['error'=>false, 'resp'=>'ASE data fetched successfully','data'=>$ase]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }

    }
    
    
    
    
    public function distributor(Request $request)
    {
        
        $distributor =User::where('type',7)->where('status','=',1)->orderby('name')->get();
        if ($distributor) {
		    return response()->json(['error'=>false, 'resp'=>'Distributor data fetched successfully','data'=>$distributor]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }

    }
    
    
    public function codesearch(Request $request)
    {
        
        $data =RetailerBarcode::where('code','like' ,'%'.$request->keyword.'%')->orWhere('name','like' ,'%'.$request->keyword.'%')->orderby('name')->get();
        if ($data) {
		    return response()->json(['error'=>false, 'resp'=>'Qrcode data fetched successfully','data'=>$data,'count'=>$data->count()]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }

    }
    
    public function codestockIn(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'code' => ['required'],
            'user_id' =>['required'],
        ]);

        if (!$validator->fails()) {
          
          $code = $request->code;
          $userId = $request->user_id;
          
            
            $codeArray = RetailerBarcode::where('name','like','%'. $code.'%')->get();
            $arr=[];
            foreach ($codeArray as $barcode) {
                $obj=RetailerBarcode::where('id',$barcode->id)->first();
                if (!$barcode) {
                    return response()->json(['error' => true, 'resp' => 'Sorry! QR code ' . $barcode . ' is invalid']);
                }
                
                
                $obj->member_id = $userId;
                
                $obj->is_warehouse_stock_in = 1;
                $obj->stock_in_date = now();
                $obj->updated_at = now();
                $obj->save();
            
            }
           
          return response()->json(['error'=>false, 'resp'=>'Stock-in for SAP code completed successfully.' ,'data'=>$obj]);
        } else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }

    }
    
    
    public function Stockincode(Request $request,$id)
    {
        
        $data = RetailerBarcode::where('member_id', $id)
            ->whereDoesntHave('stock', function ($query) {
                $query->where('is_stock', 0); // Adjust field name if needed
            })
            ->get();
        if ($data) {
		    return response()->json(['error'=>false, 'resp'=>'Data fetched successfully','data'=>$data,'count'=>$data->count()]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }

    }
    
     public function posearch(Request $request)
    {
        
        $data = OrderDistributor::where('order_no','like' ,'%'.$request->keyword.'%')->orWhere('distributor_name','like' ,'%'.$request->keyword.'%')->orderby('order_no')->with('orderProducts','orderProducts.product')->get();
        if ($data) {
		    return response()->json(['error'=>false, 'resp'=>'Primary Order data fetched successfully','data'=>$data,'count'=>$data->count()]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }

    }
    
    //stock out
   public function codestockOut(Request $request)
{
    $validator = Validator::make($request->all(), [
        'code' => ['required'],
        'user_id' => ['required'],
        'order_id' => ['required'],
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
    }

    $codeArray = is_array($request->code) ? $request->code : explode(',', $request->code);
    $userId = $request->user_id;
    $responses = [];

    foreach ($codeArray as $code) {
        $code = trim($code);

        $barcode = RetailerBarcode::where('code', $code)->first();
        if (!$barcode) {
            $responses[] = ['code' => $code, 'status' => false, 'message' => 'QR code is invalid'];
            continue;
        }

        if ($barcode->start_date > \Carbon\Carbon::now()) {
            $responses[] = ['code' => $code, 'status' => false, 'message' => 'QR code is not valid now'];
            continue;
        }

        if ($barcode->end_date < \Carbon\Carbon::now() || $barcode->status == 0) {
            $responses[] = ['code' => $code, 'status' => false, 'message' => 'QR code is expired'];
            continue;
        }

        

        $usage = WarehouseStock::where('qrcode_id', $barcode->id)->where('user_id', $userId)->count();
        if ($usage) {
            $responses[] = ['code' => $code, 'status' => false, 'message' => 'The stock has already been dispatched'];
            continue;
        }

        $userExist = User::where('id', $userId)->first();
        if (!$userExist) {
            $responses[] = ['code' => $code, 'status' => false, 'message' => 'User is invalid'];
            continue;
        }

        $order = OrderDistributor::findOrFail($request->order_id);
        $walletTxn = new WarehouseStock();
        $walletTxn->user_id = $userId;
        $walletTxn->qrcode_id = $barcode->id;
        $walletTxn->order_id = $request->order_id;
        $walletTxn->distributor_id = $order->user_id;
        $walletTxn->is_stock = 0;
        $walletTxn->created_at = now();
        $walletTxn->updated_at = now();
        $walletTxn->save();

        

        $responses[] = ['code' => $code, 'status' => true, 'message' => 'The stock has been dispatched'];
    }

    return response()->json(['error' => false, 'results' => $responses]);
}



    public function distributorposearch(Request $request)
    {
        $orderArray='';
        $data = OrderDistributor::where('order_no','like' ,'%'.$request->keyword.'%')->orWhere('distributor_name','like' ,'%'.$request->keyword.'%')->orderby('order_no')->with('orderProducts','orderProducts.product')->get();
        foreach($data as $item){
            $orderArray=WarehouseStock::where('order_id',$item->id)->with('qrcode')->get();
            
        }
        
        if ($data) {
		    return response()->json(['error'=>false, 'resp'=>'Primary Order data fetched successfully','data'=>$data,'qrcode'=>$orderArray,'count'=>$data->count()]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }

    }
    
//distributor stock in
public function scanQrDistributor(Request $request)
{
    $validator = Validator::make($request->all(), [
        'code' => ['required'],
        'user_id' => ['required'],
        
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
    }

    $codeArray = is_array($request->code) ? $request->code : explode(',', $request->code);
    $userId = $request->user_id;
    $responses = [];

    foreach ($codeArray as $code) {
        $code = trim($code);

        $barcode = RetailerBarcode::where('code', $code)->first();
        if (!$barcode) {
            $responses[] = ['code' => $code, 'status' => false, 'message' => 'QR code is invalid'];
            continue;
        }

        if ($barcode->start_date > \Carbon\Carbon::now()) {
            $responses[] = ['code' => $code, 'status' => false, 'message' => 'QR code is not valid now'];
            continue;
        }

        if ($barcode->end_date < \Carbon\Carbon::now() || $barcode->status == 0) {
            $responses[] = ['code' => $code, 'status' => false, 'message' => 'QR code is expired'];
            continue;
        }

        

        $usage = DistributorStock::where('qrcode_id', $barcode->id)->where('distributor_id', $userId)->count();
        if ($usage) {
            $responses[] = ['code' => $code, 'status' => false, 'message' => 'The stock has already been dispatched'];
            continue;
        }

        $userExist = User::where('id', $userId)->first();
        if (!$userExist) {
            $responses[] = ['code' => $code, 'status' => false, 'message' => 'User is invalid'];
            continue;
        }

        
        $walletTxn = new DistributorStock();
        $walletTxn->distributor_id = $userId;
        $walletTxn->qrcode_id = $barcode->id;
        $walletTxn->stock_in = 1;
        $walletTxn->stock_in_date = now();
        $walletTxn->created_at = now();
        $walletTxn->updated_at = now();
        $walletTxn->save();

        

        $responses[] = ['code' => $code, 'status' => true, 'message' => 'Item stocked in successfully.'];
    }

    return response()->json(['error' => false, 'results' => $responses]);
}


public function ordersearch(Request $request)
    {
        
        $data = Order::where('order_no','like' ,'%'.$request->keyword.'%')->orderby('order_no')->with('orderProducts','orderProducts.product')->get();
        if ($data) {
		    return response()->json(['error'=>false, 'resp'=>'Secondary Order data fetched successfully','data'=>$data,'count'=>$data->count()]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }

    }
    
    public function Stockincodedistributor(Request $request,$id)
    {
        
        $data = DistributorStock::where('distributor_id', $id)->where('stock_in',1)->where('stock_out',NULL)->with('qrcode')
           
            ->get();
        if ($data) {
		    return response()->json(['error'=>false, 'resp'=>'Data fetched successfully','data'=>$data,'count'=>$data->count()]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }

    }
    
    public function codestockOutDistributor(Request $request)
{
    $validator = Validator::make($request->all(), [
        'code' => ['required'],
        'user_id' => ['required'],
        'order_id' => ['required'],
       
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
    }
    $scannedCount = 0;
    $codeArray = is_array($request->code) ? $request->code : explode(',', $request->code);
    $userId = $request->user_id;
    $responses = [];
    $totalAmount = 0;
    $orderTitle=Order::where('id',$request->order_id)->with('orderProducts')->first();
   
    //dd($orderTitle->orderProducts[0]->qty);
    foreach ($codeArray as $code) {
        $code = trim($code);

        $barcode = RetailerBarcode::where('code', $code)->first();
        if (!$barcode) {
            $responses[] = ['code' => $code, 'status' => false, 'message' => 'QR code is invalid'];
            continue;
        }

        if ($barcode->start_date > \Carbon\Carbon::now()) {
            $responses[] = ['code' => $code, 'status' => false, 'message' => 'QR code is not valid now'];
            continue;
        }

        if ($barcode->end_date < \Carbon\Carbon::now() || $barcode->status == 0) {
            $responses[] = ['code' => $code, 'status' => false, 'message' => 'QR code is expired'];
            continue;
        }

        $walletTxn = DistributorStock::where('qrcode_id', $barcode->id)
                        ->where('distributor_id', $userId)
                        ->where('stock_in', 1)
                        ->first();
        if (!$walletTxn) {
            $responses[] = ['code' => $code, 'status' => false, 'message' => 'This item is out of stock.'];
            continue;
        }

        $userExist = User::where('id', $userId)->first();
        if (!$userExist) {
            $responses[] = ['code' => $code, 'status' => false, 'message' => 'User is invalid'];
            continue;
        }

        // Stock out update
        $walletTxn->stock_out = 1;
        $walletTxn->stock_out_date = now();
        $walletTxn->retailer_order_id = $request->order_id;
        $walletTxn->created_at = now();
        $walletTxn->updated_at = now();
        $walletTxn->save();
        $scannedCount++;
        // Add barcode amount to total
        $totalAmount += $barcode->amount;

        $responses[] = ['code' => $code, 'status' => true, 'message' => 'Item stocked out successfully.'];
        if ($scannedCount >= $orderTitle->orderProducts->sum('qty')) {
            break;
        }
    }
    
    $item=OrderQrcode::where('order_id',$request->order_id)->first();
    // If at least one item was stocked out, create a single new QR code
    if ($totalAmount > 0) {
        if(empty($item)){
        // Helper function to generate unique alphanumeric string
            function generateUniqueAlphaNumeric($length = 10) {
                $random_string = '';
                for ($i = 0; $i < $length; $i++) {
                    $number = random_int(0, 36);
                    $character = base_convert($number, 10, 36);
                    $random_string .= $character;
                }
                return $random_string;
            }
    
            $slug = \Str::slug($request['name'], '-');
            $slugExistCount = OrderQrcode::where('slug', $slug)->count();
            if ($slugExistCount > 0) $slug = $slug . '-' . ($slugExistCount + 1);
    
            $storeData = new OrderQrcode();
            $storeData->name = $orderTitle->order_no;
            $storeData->slug = $slug;
            
            $storeData->code = strtoupper(generateUniqueAlphaNumeric(10));
            $storeData->amount = $totalAmount;
            $storeData->order_id = $request['order_id'];
            $storeData->max_time_of_use = 1;
            $storeData->max_time_one_can_use = 1;
            $storeData->start_date = now();
            $storeData->end_date = now()->addYears(5);
            
            $storeData->save();
            $responses[] = ['new_qrcode' => $storeData->code, 'status' => true, 'message' => 'New QR code generated with total amount'];
        }else{
            $responses[] = [ 'status' => true, 'message' => 'QR code already generated'];
        }

        
    }

    return response()->json(['error' => false, 'results' => $responses]);
}




 public function oldindex(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'code' => ['required'],
            'user_id' =>['required'],
        ]);

        if (!$validator->fails()) {
            $code = $request->code;
            $userId =$request->user_id;
            $barcode=OrderQrcode::where('code',$code)->first();
            //barcode exist check
            if(!$barcode){
                return response()->json(['error'=>false, 'resp'=>'QR code is invalid']);
            }else{
				if ($barcode->start_date > \Carbon\Carbon::now()) {
                    return response()->json(['error'=>true, 'resp'=>'QR code is not valid now']);
                }else{
                // coupon code validity check
					if ($barcode->end_date < \Carbon\Carbon::now() || $barcode->status == 0) {
						return response()->json(['error'=>true, 'resp'=>'QR code is expired']);
					}else{
						//no of usage check
						if ($barcode->no_of_usage == $barcode->max_time_of_use || $barcode->no_of_usage >= $barcode->max_time_of_use){
							return response()->json(['error'=>true, 'resp'=>'Already scanned']);
						}else{
							 $usage = RetailerWalletTxn::where('barcode_id',$barcode->id)->where('user_id',$userId)->count();
                             if ($usage == $barcode->max_time_one_can_use || $usage >= $barcode->max_time_one_can_use) {
                                 return response()->json(['error'=>true, 'resp'=>'Already scanned']);
                            }else{
							    $userExist=Store::where('id',$userId)->first();
								if(!$userExist){
									return response()->json(['error'=>false, 'resp'=>'User is invalid']);
								}else{
									$user=Store::findOrFail($userId);
									$user->wallet += $barcode->amount;
									$user->save();
									
									$userAmount=RetailerWalletTxn::where('user_id',$userId)->orderby('id','desc')->first();
									$walletTxn=new RetailerWalletTxn();
									$walletTxn->user_id = $userId;
									$walletTxn->barcode_id = $barcode->id;
									$walletTxn->barcode = $barcode->code;
									$walletTxn->amount = $barcode->amount;
									$walletTxn->type = 1 ?? '';
									if(!$userAmount)
										$walletTxn->final_amount += $barcode->amount ?? '';
									else
									$walletTxn->final_amount = $userAmount->final_amount+ $barcode->amount ?? '';
									$walletTxn->created_at = date('Y-m-d H:i:s');
									$walletTxn->updated_at = date('Y-m-d H:i:s');
									$walletTxn->save();
									$userwalletTxn=new RetailerUserTxnHistory();
									$userwalletTxn->user_id = $userId;
									$userwalletTxn->barcode_id = $barcode->id;
									$userwalletTxn->barcode = $barcode->code;
									$userwalletTxn->amount = $barcode->amount;
									$userwalletTxn->type = 'Qrcode scan' ?? '';
									$userwalletTxn->title = $barcode->amount.' points earn';
									$userwalletTxn->description = 'Using '.$barcode->code.' code';
									$userwalletTxn->status = 'increment';
									$userwalletTxn->created_at = date('Y-m-d H:i:s');
									$userwalletTxn->updated_at = date('Y-m-d H:i:s');
									$userwalletTxn->save();
									$barcodeDetails=OrderQrcode::findOrFail($barcode->id);
									$barcodeDetails->no_of_usage = $barcode->no_of_usage+1;
									$barcodeDetails->save();
								}
						    }
					    }
					}
				}
                return response()->json(['error'=>false, 'resp'=>'QR code data fetched successfully,you have earned' .$barcode->amount.' Sales Drive currency','data'=>$barcode]);
            }
        
		} else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
   
    }
    
    
    
    public function index(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            
            'outlet_id' =>['required'],
            'amount' => ['required'],
            'remarks' => ['required'],
            'type' => ['required'],
            //'db_name' =>['required'],
            //'db_code'  =>['required'],
        ]);
        
        if (!$validator->fails()) {
            
            $userId =$request->outlet_id;
           
							    $userExist=Store::where('unique_code',$userId)->first();
								if(!$userExist){
									return response()->json(['error'=>false, 'resp'=>'Outlet is invalid']);
								}else{
								  
									$user=Store::where('unique_code',$userId)->first();
									$user->wallet += $request->amount;
									$user->save();
									
									$userAmount=RetailerWalletTxn::where('user_id',$userId)->orderby('id','desc')->first();
									$walletTxn=new RetailerWalletTxn();
									$walletTxn->user_id = $userId;
									
									$walletTxn->amount = $request->amount;
									$walletTxn->type = 1 ?? '';
									if (!$userAmount) {
                                        $walletTxn->final_amount = $request->amount;
                                    } else {
                                        $walletTxn->final_amount = $userAmount->final_amount + $request->amount;
                                    }
                                    $walletTxn->db_name = $request->db_name;
                                    $walletTxn->db_code = $request->db_code;
                                    $walletTxn->entry_date = $request->entry_date;
									$walletTxn->created_at = date('Y-m-d H:i:s');
									$walletTxn->updated_at = date('Y-m-d H:i:s');
									$walletTxn->save();
									$userwalletTxn=new RetailerUserTxnHistory();
									$userwalletTxn->user_id = $userId;
									
									$userwalletTxn->amount = $request->amount;
									$userwalletTxn->db_name = $request->db_name ??'';
									$userwalletTxn->db_code = $request->db_code??'';
									$userwalletTxn->type = 'Earn' ?? '';
									$userwalletTxn->title = $request->amount.' points earn';
									$userwalletTxn->description = $request->remarks;
									$userwalletTxn->amount_type = $request->type;
									$userwalletTxn->status = 'increment';
									$userwalletTxn->entry_date = $request->entry_date;
									$userwalletTxn->created_at = date('Y-m-d H:i:s');
									$userwalletTxn->updated_at = date('Y-m-d H:i:s');
									$userwalletTxn->save();
									
								}
						    
					    
                return response()->json(['error'=>false, 'resp'=>'You have earned ' .$request->amount.' Sales Drive currency']);
            
        
		} else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
   
    }
    
    
     public function debit(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            
            'outlet_id' =>['required'],
            'amount' => ['required'],
            'remarks' => ['required'],
            'entry_date' =>['required'],
            //'db_code'  =>['required'],
            'type' => ['required'],
        ]);
        
        if (!$validator->fails()) {
            
            $userId =$request->outlet_id;
           
							    $userExist=Store::where('unique_code',$userId)->first();
								if(!$userExist){
									return response()->json(['error'=>false, 'resp'=>'Outlet is invalid']);
								}else{
								  
									$user=Store::where('unique_code',$userId)->first();
									$user->wallet -= $request->amount;
									$user->save();
									
									$userAmount=RetailerWalletTxn::where('user_id',$userId)->orderby('id','desc')->first();
									$walletTxn=new RetailerWalletTxn();
									$walletTxn->user_id = $userId;
									
									$walletTxn->amount = $request->amount;
									$walletTxn->type = 2 ?? '';
									if (!$userAmount) {
                                        $walletTxn->final_amount = $request->amount;
                                    } else {
                                        $walletTxn->final_amount = $userAmount->final_amount - $request->amount;
                                    }
                                    $walletTxn->db_name = $request->db_name;
                                    $walletTxn->db_code = $request->db_code;
                                    $walletTxn->entry_date = $request->entry_date;
									$walletTxn->created_at = date('Y-m-d H:i:s');
									$walletTxn->updated_at = date('Y-m-d H:i:s');
									$walletTxn->save();
									$userwalletTxn=new RetailerUserTxnHistory();
									$userwalletTxn->user_id = $userId;
									
									$userwalletTxn->amount = $request->amount;
									$userwalletTxn->type = 'Debit' ?? '';
									$userwalletTxn->title = $request->amount.' points debit for sales return';
									$userwalletTxn->description = $request->remarks;
									$userwalletTxn->amount_type = $request->type;
									$userwalletTxn->db_name = $request->db_name;
									$userwalletTxn->db_code = $request->db_code;
									$userwalletTxn->status = 'increment';
									$userwalletTxn->entry_date = $request->entry_date;
									$userwalletTxn->created_at = date('Y-m-d H:i:s');
									$userwalletTxn->updated_at = date('Y-m-d H:i:s');
									$userwalletTxn->save();
									
								}
						    
					    
                return response()->json(['error'=>false, 'resp'=>$request->amount.' Sales Drive currency debited from your wallet']);
            
        
		} else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
   
    }
    
    


public function retailerSave()
{
    $page = 1;
    $limit = 10;

    do {
        $response = Http::get("https://api.mysalesdrive.in/api/v1/outletApproved/paginated-list", [
            'page' => $page,
            'limit' => $limit
        ]);

        if (!$response->successful()) {
            return response()->json(['error' => "API failed at page $page"], 500);
        }

        $outlets = $response->json('data');
        //dd($outlets);
        // Break the loop if no more data
        if (empty($outlets)) {
            break;
        }

        foreach ($outlets as $outlet) {
            
            // Check if store already exists
            $exists = DB::table('stores')
                ->where('api_id', $outlet['_id'])
                ->exists();

            if ($exists) {
                continue; // Skip and move to next
            }
           
            $user=User::where('api_id',$outlet['employeeId']['_id'])->first();
            $state=State::where('name',$outlet['stateId']['name'])->first();
            $beat =Area::where('name',$outlet['beatId']['name'])->first();
            DB::table('stores')->updateOrInsert(
                ['unique_code' => $outlet['outletUID']],
                [
                     'api_id'   => $outlet['_id'] ?? '',
                    'name'   => $outlet['outletName'] ?? '',
                    'owner_fname'    => $outlet['ownerName'] ?? '',
                    'contact'        => $outlet['mobile1'] ?? '',
                    'address'       => $outlet['address1'] ?? '',
                    'user_id'       => $user->id ??'',
                    'city'          => $outlet['city'] ?? '',
                    'state_id'         => $state->id ?? '',
                    'area_id' => $beat->id ?? '',
                     'pin' => $outlet['pin'] ?? '',
                      'district' => $outlet['district'] ?? '',
                    'gst_no' =>$outlet['gstin'] ?? '',
                     'pan_no' => $outlet['panNumber'] ?? '',
                      'aadhar' => $outlet['aadharNumber'] ?? '',
                    'password' => Hash::make($outlet['mobile1'].''.'@2025') ?? '',
                    'status'        => 1,
                    'created_at'    => now(),
                    'updated_at'    => now()
                ]
            );
        }

        $page++; // Move to next page

    } while (!empty($outlets));

    return response()->json(['message' => 'Paginated outlet data imported successfully']);
}



public function stateSave(Request $request)
{
    $response = Http::get("https://api.mysalesdrive.in/api/v1/State/list");

    if (!$response->successful()) {
        return response()->json(['error' => "API failed"], 500);
    }

    $outlets = $response->json('data');

    if (empty($outlets)) {
        return response()->json(['message' => 'No state data found']);
    }

    foreach ($outlets as $outlet) {
        DB::table('states')->updateOrInsert(
            // Unique condition (find by api_id OR name, depending on your data rule)
            [
                'api_id' => $outlet['_id'] ?? '',
            ],
            // Values to update/insert
            [
                'name'       => $outlet['name'] ?? '',
                'code'       => $outlet['code'] ?? '',
                'status'     => $outlet['status'] ?? false,
                'updated_at' => now(),
                'created_at' => now(), // only used when inserting
            ]
        );
    }

    return response()->json(['message' => 'State data imported successfully']);
}



public function beatSave(Request $request)
{
    $page = 1;
    $limit = 10;

    do {
        $response = Http::get('https://api.mysalesdrive.in/api/v1/beat/beat-list-paginated', [
            'page' => $page,
            'limit' => $limit,
        ]);

        if (!$response->successful()) break;

        $outlets = $response->json('data');

        if (empty($outlets)) break;

        foreach ($outlets as $outlet) {
            $state = State::where('name', $outlet['regionId']['name'])->first();

            // ✅ Skip if name already exists
            $exists = DB::table('areas')->where('name', $outlet['name'])->exists();
            if ($exists) {
                continue; // skip this outlet
            }

            DB::table('areas')->updateOrInsert([
                'api_id' => $outlet['_id'] ?? '',
            ], [
                'name' => $outlet['name'] ?? '',
                'state_id' => $state->id ?? null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $page++;
        sleep(1); // throttle to prevent timeout

    } while (true);
    return response()->json(['message' => 'Paginated beat data imported successfully']);
}




public function employeeSave(Request $request)
{
    $page = 1;
    $limit = 10;

    do {
        $response = Http::get("https://api.mysalesdrive.in/api/v1/employee/all-list-paginated", [
            'page' => $page,
            'limit' => $limit
        ]);

        if (!$response->successful()) {
            return response()->json(['error' => "API failed at page $page"], 500);
        }

        $outlets = $response->json('data');

        if (empty($outlets)) {
            break;
        }

        foreach ($outlets as $outlet) {
            DB::table('users')->updateOrInsert(
                // Unique condition (use api_id to check existing record)
                [
                    'api_id' => $outlet['_id'] ?? '',
                ],
                // Values to update/insert
                [
                    'name'        => $outlet['name'] ?? '',
                    'employee_id' => $outlet['empId'] ?? '',
                    'designation' => $outlet['desgId']['name'] ?? '',
                    'password'    => $outlet['password'] ?? '', // ⚠️ plain password? not secure
                    'state'       => $outlet['regionId']['name'] ?? '',
                    'status'      => 1,
                    'updated_at'  => now(),
                    'created_at'  => now(), // used only on insert
                ]
            );
        }

        $page++;
        sleep(1); // throttle API calls

    } while (!empty($outlets));

    return response()->json(['message' => 'Paginated employee data imported successfully']);
}



public function wallet(Request $request)
{
    $wallet=Store::where('api_id',$request->id)->first();
     if ($wallet) {
		    return response()->json(['error'=>false, 'resp'=>'Wallet data fetched successfully','balance'=>$wallet->wallet]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }
}


	
/*public function ledger(Request $request)
{
    $validator = Validator::make($request->all(), [
        'retailer_uid'   => ['required'],
        'startDate' => ['required', 'date'],
        'endDate'   => ['required', 'date'],
    ]);

    if ($validator->fails()) {
        return response()->json([
            'error'   => true,
            'message' => $validator->errors()->first()
        ]);
    }

    $arr=[];
    $userId = $request->retailer_uid;

    // Check for user by ID or unique_code
    $user = Store::where('unique_code', $userId)
                 
                 ->first();

    if (!$user) {
        return response()->json([
            'error' => true,
            'message' => 'User is invalid'
        ]);
    }

    // Fetch transaction history with date filter + pagination
    $resp = RetailerUserTxnHistory
        ::where('user_id', $user->id)->orWhere('user_id', $user->unique_code)
        ->whereBetween('created_at', [$request->startDate, $request->endDate])
        ->orderBy('id', 'asc')->get();
        
        
        foreach($resp as  $item){
            
           $store=Store::where('id',$item->user_id)->orWhere('unique_code',$item->user_id)->first();
          $openingBalance = DB::table('retailer_wallet_txns')->where('user_id', $item->user_id)
            ->where('created_at', '<', $item->created_at)
            ->orWhere('created_at', '=', $item->created_at)
            ->orderBy('created_at', 'desc')
            ->first();
            $closingBalance = DB::table('retailer_wallet_txns')->where('user_id', $item->user_id)
            ->where('created_at', '=', $item->created_at)
            
            ->orderBy('id', 'desc')
            ->first();
            if ($item->type == "Earn") {
                if ($item->amount_type == "SALES") {
                 $billamount = $item->amount;
                } elseif ($item->amount_type == "Sales Multiplier") {
                     $multiplierbillamount = $item->amount;
                }
            }
            
            if ($item->type == "manual-adjustment") {
                
                 $manualadj = $item->amount;
               
            }else{
                $manualadj ='';
            }
            
            if ($item->type == "Sales Return") {
                
                 $salescancel = $item->amount;
               
            }else{
                 $salescancel ='';
            }
            if(!empty($item->orders)){
                if($item->orders->status==5){
                    $redemcancel=$item->orders->final_amount;
                }else{
                    $redemcancel='';
                }
            }else{
                $redemcancel='';
            }
              $arr[] = [
                "Date" => $item->created_at,
                "Retailer code" =>$store->unique_code ??'',
                "Retailer name" =>$store->name ??'',
                "Retailer state" =>$store->states->name ??'',
                "Retailer city" =>$store->areas->name ??'',
               
                 "Opening balance" => $openingBalance->final_amount ??'',
                 "Point credited (Bill)" => $billamount ??'',
                 "Point credited (Multiplier)" => $multiplierbillamount ??'',
                 "Sales cancellation debit" => $salescancel ??'',
                 "Redemption cancellation debit" => $redemcancel ,
                 "Redemption gift" => $item->orders->orderProduct->product_name ??'',
                 "Manual adjustment" => $manualadj ??'',
                 "Closing balance" => $closingBalance->final_amount ??'',
             ];
        }   

    return response()->json([
        'error'   => false,
        'message' => 'Transaction history fetched successfully',
       
        'data'    => $arr,
    ]);
}*/


/*public function ledger(Request $request)
{
    $validator = Validator::make($request->all(), [
        'retailer_uid'   => ['required'],
        'startDate' => ['required'],
        'endDate'   => ['required'],
    ]);

    if ($validator->fails()) {
        return response()->json([
            'error'   => true,
            'message' => $validator->errors()->first()
        ]);
    }

    $arr = [];
    $userId = $request->retailer_uid;

    // Check for user
    $user = Store::where('unique_code', $userId)->first();

    if (!$user) {
        return response()->json([
            'error' => true,
            'message' => 'User is invalid'
        ]);
    }

    // Fetch all txn history date wise
    $resp = RetailerUserTxnHistory::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('user_id', $user->unique_code);
            })
        ->whereBetween('created_at', [$request->startDate, $request->endDate])
        ->orderBy('created_at', 'asc')
        ->get()
        ->groupBy(function($item) {
            return \Carbon\Carbon::parse($item->created_at)->format('Y-m-d'); // group by date
        });
    
    foreach ($resp as $date => $transactions) {
       
        $billamount = 0;
        $multiplierbillamount = 0;
        $manualadj = 0;
        $salescancel = 0;
        $redemcancel = 0;
        $redemptionpoint=0;
        $redemgift = '';
        $salesmulticancel=0;
        $manualadjdebit=0;
        // Opening balance = last balance before that date
        DB::enableQueryLog();
        $openingBalance = DB::table('retailer_wallet_txns')
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('user_id', $user->unique_code);
            })
            ->where('created_at', '<', $date)
            ->orderBy('id', 'desc')
            ->value('final_amount');
       $queries = DB::getQueryLog();
       
        if (!$openingBalance) {
            $openingBalance = 0;
        }
        // Closing balance = last balance on that date
        DB::enableQueryLog();
        $closingBalance = DB::table('retailer_wallet_txns')
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('user_id', $user->unique_code);
            })
            ->whereDate('created_at', '=', $date)
            ->orderBy('id', 'desc')
            ->value('final_amount') ?? $openingBalance;
        $queries = DB::getQueryLog();
     // Dumps an array 
        foreach ($transactions as $item) {
            if ($item->type == "Earn") {
                if ($item->amount_type == "SALES") {
                    $billamount += $item->amount;
                } elseif ($item->amount_type == "Sales Multiplier") {
                    $multiplierbillamount += $item->amount;
                }
            }

            if ($item->type == "manual-adjustment" && $item->status =="increment") {
                $manualadj += $item->amount;
            }
            
            if ($item->type == "manual-adjustment" && $item->status =="decrement") {
                $manualadjdebit += $item->amount;
            }

            if ($item->amount_type == "Sales Return" && $item->type == "Debit") {
                $salescancel += $item->amount;
            }
            
            if ($item->amount_type == "Sales Multiplier" && $item->type == "Debit") {
                $salesmulticancel += $item->amount;
            }

            if (!empty($item->orders) && $item->orders->status == 5) {
                $redemcancel += $item->orders->final_amount;
            }

            // if (!empty($item->orders) && !empty($item->orders->orderProduct)) {
            //     $redemgift = $item->orders->orderProduct->product_name;
            // }
            if (!empty($item->orders)) {
                $redemptionpoint+=$item->orders->final_amount;
            }
        }

        $arr[] = [
            "Date" => $date,
            "Retailer code" => $user->unique_code ?? '',
            "Retailer name" => $user->name ?? '',
            "Retailer state" => $user->states->name ?? '',
            "Retailer city" => $user->areas->name ?? '',
            "DB Name" => $user->db_name ?? '',
            "DB Code" => $user->db_code ?? '',
            "Opening balance" => $openingBalance,
            "Sales Point Credit" => $billamount,
            "Multiplier Point Credit" => $multiplierbillamount,
            "Sales Return Point Debit" => $salescancel,
            "Sales Return Multiplier Point Debit" => $salesmulticancel,
             "Gift Redemption Point Debit" => $redemptionpoint,
            "Redemption Cancellation Point Credit" => $redemcancel,
           
            
            "Manual Adjustment Point Credit" => $manualadj,
            "Manual Adjustment Point Debit" => $manualadjdebit,
            "Closing balance" => $closingBalance,
        ];
    }

    return response()->json([
        'error'   => false,
        'message' => 'Transaction history fetched successfully (date wise)',
        'data'    => $arr,
    ]);
}*/



/*public function ledger(Request $request)
{
    $validator = Validator::make($request->all(), [
        'retailer_uid' => ['nullable'],
        'startDate'    => ['required', 'date'],
        'endDate'      => ['required', 'date'],
    ]);

    if ($validator->fails()) {
        return response()->json([
            'error'   => true,
            'message' => $validator->errors()->first()
        ]);
    }

    $arr = [];
    $userId = $request->retailer_uid;

    // Check for user
    $user = Store::where('unique_code', $userId)->first();

    if (!$user) {
        return response()->json([
            'error' => true,
            'message' => 'User is invalid'
        ]);
    }

    // Create date loop
    $period = new \DatePeriod(
        new \DateTime($request->startDate),
        new \DateInterval('P1D'),
        (new \DateTime($request->endDate))->modify('+1 day') // include endDate
    );

    foreach ($period as $dateObj) {
        $date = $dateObj->format('Y-m-d');

        $billamount = 0;
        $multiplierbillamount = 0;
        $manualadj = 0;
        $salescancel = 0;
        $redemcancel = 0;
        $redemptionpoint = 0;
        $salesmulticancel = 0;
        $manualadjdebit = 0;

        // Opening balance = last balance before that date
        $openingBalance = DB::table('retailer_wallet_txns')
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('user_id', $user->unique_code);
            })
            ->where('created_at', '<', $date)
            ->orderBy('id', 'desc')
            ->value('final_amount') ?? 0;

        // Fetch all transactions for that date
        $transactions = RetailerUserTxnHistory::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('user_id', $user->unique_code);
            })
            ->whereDate('created_at', $date)
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($transactions as $item) {
            if ($item->type == "Earn") {
                if ($item->amount_type == "SALES") {
                    $billamount += $item->amount;
                } elseif ($item->amount_type == "Sales Multiplier") {
                    $multiplierbillamount += $item->amount;
                }
            }

            if ($item->type == "manual-adjustment" && $item->status == "increment") {
                $manualadj += $item->amount;
            }

            if ($item->type == "manual-adjustment" && $item->status == "decrement") {
                $manualadjdebit += $item->amount;
            }

            if ($item->amount_type == "Sales Return" && $item->type == "Debit") {
                $salescancel += $item->amount;
            }

            if ($item->amount_type == "Sales Multiplier" && $item->type == "Debit") {
                $salesmulticancel += $item->amount;
            }

            if (!empty($item->orders) && $item->orders->status == 5) {
                $redemcancel += $item->orders->final_amount;
            }

            if (!empty($item->orders)) {
                $redemptionpoint += $item->orders->final_amount;
            }
        }

        // Closing balance = last balance on that date OR opening if no txn
        $closingBalance = DB::table('retailer_wallet_txns')
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('user_id', $user->unique_code);
            })
            ->whereDate('created_at', '=', $date)
            ->orderBy('id', 'desc')
            ->value('final_amount') ?? $openingBalance;

        $arr[] = [
            "Date" => $date,
            "Retailer code" => $user->unique_code ?? '',
            "Retailer name" => $user->name ?? '',
            "Retailer state" => $user->states->name ?? '',
            "Retailer city" => $user->areas->name ?? '',
            "DB Name" => $user->db_name ?? '',
            "DB Code" => $user->db_code ?? '',
            "Opening balance" => $openingBalance,
            "Sales Point Credit" => $billamount,
            "Multiplier Point Credit" => $multiplierbillamount,
            "Sales Return Point Debit" => $salescancel,
            "Sales Return Multiplier Point Debit" => $salesmulticancel,
            "Gift Redemption Point Debit" => $redemptionpoint,
            "Redemption Cancellation Point Credit" => $redemcancel,
            "Manual Adjustment Point Credit" => $manualadj,
            "Manual Adjustment Point Debit" => $manualadjdebit,
            "Closing balance" => $closingBalance,
        ];
    }

    return response()->json([
        'error'   => false,
        'message' => 'Transaction history fetched successfully (date wise)',
        'data'    => $arr,
    ]);
}*/



public function ledger(Request $request)
{
    $validator = Validator::make($request->all(), [
        'retailer_uid' => ['nullable', 'array'], // accept array
        'retailer_uid.*' => ['string'],          // each should be string (or numeric if needed)
        'startDate'    => ['required', 'date'],
        'endDate'      => ['required', 'date'],
    ]);

    if ($validator->fails()) {
        return response()->json([
            'error'   => true,
            'message' => $validator->errors()->first()
        ]);
    }

    $arr = [];

    // Fetch retailers
    if ($request->has('retailer_uid') && !empty($request->retailer_uid)) {
        $retailers = Store::whereIn('unique_code', $request->retailer_uid)->get();

        if ($retailers->isEmpty()) {
            return response()->json([
                'error' => true,
                'message' => 'No valid retailer(s) found'
            ]);
        }
    } else {
        // All retailers
        $retailers = Store::all();
    }

    // Date loop
    $period = new \DatePeriod(
        new \DateTime($request->startDate),
        new \DateInterval('P1D'),
        (new \DateTime($request->endDate))->modify('+1 day') // include endDate
    );

    foreach ($retailers as $user) {
        foreach ($period as $dateObj) {
            $date = $dateObj->format('Y-m-d');

            $billamount = 0;
            $multiplierbillamount = 0;
            $manualadj = 0;
            $salescancel = 0;
            $redemcancel = 0;
            $redemptionpoint = 0;
            $salesmulticancel = 0;
            $manualadjdebit = 0;
            $openingStock = 0;
            // Opening balance
            $openingBalance = DB::table('retailer_wallet_txns')
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhere('user_id', $user->unique_code);
                })
                ->where('created_at', '<', $date)
                ->orderBy('id', 'desc')
                ->value('final_amount') ?? 0;

            // Transactions on that date
            $transactions = RetailerUserTxnHistory::where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhere('user_id', $user->unique_code);
                })
                ->whereDate('created_at', $date)
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($transactions as $item) {
                if ($item->type == "Earn") {
                    if ($item->amount_type == "SALES") {
                        $billamount += $item->amount;
                    } elseif ($item->amount_type == "Sales Multiplier") {
                        $multiplierbillamount += $item->amount;
                    }
                }

                if ($item->type == "manual-adjustment" && $item->status == "increment") {
                    $manualadj += $item->amount;
                }

                if ($item->type == "manual-adjustment" && $item->status == "decrement") {
                    $manualadjdebit += $item->amount;
                }

                if ($item->amount_type == "Sales Return" && $item->type == "Debit") {
                    $salescancel += $item->amount;
                }

                if ($item->amount_type == "Sales Multiplier" && $item->type == "Debit") {
                    $salesmulticancel += $item->amount;
                }
                
                if ($item->type == "Earn" && $item->amount_type == "Opening Stock" && $item->status == "increment") {
                    $openingStock += $item->amount;
                }

                if (!empty($item->orders) && $item->orders->status == 5) {
                    $redemcancel += $item->orders->final_amount;
                }

                if (!empty($item->orders)) {
                    $redemptionpoint += $item->orders->final_amount;
                }
            }

            // Closing balance
            $closingBalance = DB::table('retailer_wallet_txns')
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhere('user_id', $user->unique_code);
                })
                ->whereDate('created_at', '=', $date)
                ->orderBy('id', 'desc')
                ->value('final_amount') ?? $openingBalance;

            $arr[] = [
                "Date" => $date,
                "Retailer code" => $user->unique_code ?? '',
                "Retailer name" => $user->name ?? '',
                "Retailer state" => $user->states->name ?? '',
                "Retailer city" => $user->areas->name ?? '',
                "DB Name" => $user->db_name ?? '',
                "DB Code" => $user->db_code ?? '',
                "Opening balance" => $openingBalance,
                "Opening Stcok Point Credit" => $openingStock,
                "Sales Point Credit" => $billamount,
                "Multiplier Point Credit" => $multiplierbillamount,
                "Sales Return Point Debit" => $salescancel,
                "Sales Return Multiplier Point Debit" => $salesmulticancel,
                "Gift Redemption Point Debit" => $redemptionpoint,
                "Redemption Cancellation Point Credit" => $redemcancel,
                "Manual Adjustment Point Credit" => $manualadj,
                "Manual Adjustment Point Debit" => $manualadjdebit,
                "Closing balance" => $closingBalance,
            ];
        }
    }

    return response()->json([
        'error'   => false,
        'message' => 'Transaction history fetched successfully (date wise)',
        'data'    => $arr,
    ]);
}


public function balance(Request $request)
{
    // Fetch all active stores
    $wallets = Store::where('status', 1)
        
        ->select('api_id', 'name', 'wallet') // Only needed columns
        ->get();

    if ($wallets->isNotEmpty()) {
        $data = [];

        foreach ($wallets as $store) {
            $data[] = [
                'retailer_id' => $store->api_id,
                'retailer_name' => $store->name,
                'wallet_balance' => $store->wallet ?? 0,
            ];
        }

        return response()->json([
            'error' => false,
            'resp' => 'Wallet data fetched successfully',
            'data' => $data
        ]);
    } else {
        return response()->json([
            'error' => true,
            'resp' => 'No retailer found'
        ]);
    }
}



    
    
    
}
