<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Activity;
use App\Models\Team;
use App\Models\UserNoOrderReason;
use App\Models\User;
use App\Models\Cart;
use App\Models\Store;
use App\Models\Order;
use App\Models\OrderProduct;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class ASMController extends Controller
{
    //inactive ASE report for ASM in dashboard
    public function inactiveAseListASM(Request $request)
    {
        $userId = $_GET['user_id'];
        $aseDetails = Team::select('users.id')->join('users', 'teams.ase_id', '=', 'users.id')->where('teams.asm_id', '=', $userId)->groupby('teams.ase_id')->orderby('teams.ase_id')->get()->pluck('id')->toArray();
                
        $activeASEreport=Activity::where('type','Visit Started')->whereDate('created_at', '=', Carbon::now())->whereIn('user_id',$aseDetails)->pluck('user_id')->toArray();
                
        $inactiveASE=Team::select(DB::raw("users.id as id"),DB::raw("users.name as name"),DB::raw("users.mobile as mobile"),DB::raw("users.state as state"),DB::raw("users.city as city"))->join('users', 'teams.ase_id', '=', 'users.id')->where('teams.asm_id', '=', $userId)->whereNotIn('users.id',$activeASEreport)->groupby('teams.ase_id')->orderby('teams.ase_id')->get();
            
        return response()->json(['error' => false, 'resp' => 'Inactive ASE report - Team wise', 'data' => $inactiveASE]);
        
    }

    //area list
    public function areaList(Request $request,$id)
    {
        $data=Team::where('asm_id',$id)->groupby('area_id')->with('areas:id,name')->get();
        if (count($data)==0) {
                 return response()->json(['error'=>true, 'resp'=>'No data found']);
        } else {
                 return response()->json(['error'=>false, 'resp'=>'Area List','data'=>$data]);
        } 
    }
    
    //distributor list
   public function distributorList(Request $request)
    {
        $asm = $_GET['user_id'];
        $area = $_GET['area_id'];
        $data= Team::select('distributor_id','area_id')->where('asm_id',$asm)->where('area_id',$area)->where('store_id',NULL)->with('distributors:id,name,mobile,email,address,city,state')->distinct('distributor_id')->get();
        if($data)
        {
            return response()->json(['error' => false, 'resp' => 'Distributor data fetched successfully','data' => $data]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }
   }

   //store image create
    public function imageCreate(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'image' => ['required', 'image', 'max:1000000']
        ]);

        if(!$validator->fails()){
            $imageName = mt_rand().'.'.$request->image->extension();
			$uploadPath = 'public/uploads/store';
			$request->image->move($uploadPath, $imageName);
			$total_path = $uploadPath.'/'.$imageName;
            
			return response()->json(['error' => false, 'resp' => 'Image added', 'data' => $total_path]);

        }else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }

    }
    //store create
    public function storeCreate(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'user_id' => 'required|integer',
            'contact' => 'required|integer|unique:stores|min:1|digits:10',
            'whatsapp' => 'required|integer|unique:stores|min:1|digits:10',
            'name' => 'required',
            'distributor_id' => 'required',
            'owner_fname' => 'required|regex:/^[\pL\s\-]+$/u',
            'owner_lname' => 'required|regex:/^[\pL\s\-]+$/u',
            'image' => 'required',
            'contact_person_fname' => 'required|regex:/^[\pL\s\-]+$/u',
            'pin' => 'required|integer|digits:6',
           'contact_person_lname' => 'required|regex:/^[\pL\s\-]+$/u',
           'area_id' => 'required',
           'state_id' => 'required',
           'contact_person_phone' => 'required|integer|',
           'district' => 'required',
           ]);

        if(!$validator->fails()){
            $result = Team::where('asm_id',$request->user_id)->where('area_id',$request->area_id)->where('distributor_id',$request->distributor_id)->first();
            $user = User::where('id',$request->user_id)->first();
            $name = $user->name;
            $store=new Store();
            $store->user_id = $request->user_id;
			$store->name = $request->name;
			$store->business_name	 = $request->business_name ?? null;
			$store->owner_fname	 = $request->owner_fname ?? null;
		    $store->owner_lname	 = $request->owner_lname ?? null;
			$store->store_OCC_number = $request->store_OCC_number ?? null;
			$store->gst_no = $request->gst_no ?? null;
			$store->contact = $request->contact;
			$store->whatsapp = $request->whatsapp?? null;
			$store->email	 = $request->email?? null;
			$store->address	 = $request->address?? null;
			$store->state_id	 = $request->state_id?? null;
			$store->city	 = $request->city?? null;
			$store->district	 = $request->district?? null;
			$store->pin	 = $request->pin?? null;
			$store->area_id	 = $request->area_id?? null;
			$store->date_of_birth	 = $request->date_of_birth?? null;
			$store->date_of_anniversary	 = $request->date_of_anniversary?? null;
			$store->contact_person_fname	 = $request->contact_person_fname ?? null;
	    	$store->contact_person_lname = $request->contact_person_lname ?? null;
			$store->contact_person_phone	= $request->contact_person_phone ?? null;
			$store->contact_person_whatsapp	 = $request->contact_person_whatsapp ?? null;
			$store->contact_person_date_of_birth	 = $request->contact_person_date_of_birth ?? null;
			$store->contact_person_date_of_anniversary	 = $request->contact_person_date_of_anniversary ?? null;
            $orderData = Store::select('sequence_no')->latest('sequence_no')->first();
				
				    if (empty($store->sequence_no)) {
						if (!empty($orderData->sequence_no)) {
							$new_sequence_no = (int) $orderData->sequence_no + 1;
							
						} else {
							$new_sequence_no = 1;
							
						}
					}
			$uniqueNo = sprintf("%'.06d", $new_sequence_no);
		    $store->sequence_no = $new_sequence_no;
			$store->unique_code = $uniqueNo;
			$store->status = '0';
			if (!empty($request['image'])) {
				$store->image= $request->image;
			}
			
            $slug = Str::slug($request->name, '-');
            $slugExistCount = Store::where('slug', $slug)->count();
            if ($slugExistCount > 0) $slug = $slug.'-'.($slugExistCount+1);
            $store->slug = $slug;
			
			$store->created_at = date('Y-m-d H:i:s');
			$store->updated_at = date('Y-m-d H:i:s');
			$store->save();

			$nsm_id = $result->nsm_id;
			$state_id = $result->state_id;
			$zsm_id = $result->zsm_id;
			$rsm_id = $result->rsm_id;
            $ase_id = $result->ase_id;
            $sm_id = $result->sm_id;

			$team = new Team;
			$team->nsm_id = $nsm_id;
			$team->state_id = $state_id;
			$team->zsm_id = $zsm_id;
			$team->rsm_id = $rsm_id;
			$team->asm_id = $request->user_id;
			$team->sm_id = $sm_id;
			$team->ase_id = $ase_id;
			$team->area_id = $request->area_id;
            $team->distributor_id = $request->distributor_id;
			$team->store_id = $store->id;
			$team->status = '1';
			$team->is_deleted = '0';
			$team->created_at = date('Y-m-d H:i:s');
			$team->updated_at = date('Y-m-d H:i:s');
			$team->save();
			// notification to Admin
			$loggedInUser = $name;
				sendNotification($store->user_id, 'admin', 'store-add', 'admin.store.index', $store->name. '  added by ' .$loggedInUser , '  Store ' .$store->name.' added');
				
				// notification to RSM
				$loggedInUser = $name;
				$rsm = DB::select("SELECT u.id as rsm_id FROM `teams` t  INNER JOIN users u ON u.id = t.rsm_id where t.ase_id = '$request->user_id' GROUP BY t.rsm_id");
				foreach($rsm as $value){
					sendNotification($store->user_id, $value->rsm_id, 'store-add', '', $store->name. '  added by '  .$loggedInUser ,' Store ' .$store->name. ' added');
				}

				// notification to SM
				$loggedInUser = $name;
				$sm = DB::select("SELECT u.id as sm_id FROM `teams` t  INNER JOIN users u ON u.id = t.sm_id where t.ase_id = '$request->user_id' GROUP BY t.sm_id");
				foreach($sm as $value){
					sendNotification($store->user_id, $value->sm_id, 'store-add', '', $store->name. '  added by ' .$loggedInUser ,'Store ' .$store->name.' added  ');
				}
                // notification to ZSM
				$loggedInUser = $name;
				$zsm = DB::select("SELECT u.id as zsm_id FROM `teams` t  INNER JOIN users u ON u.id = t.zsm_id where t.ase_id = '$request->user_id'  GROUP BY t.zsm_id");
				foreach($zsm as $value){
					sendNotification($store->user_id, $value->zsm_id, 'store-add', '', $store->name. '  added by ' .$loggedInUser ,'Store ' .$store->name.' added  ');
				}
                // notification to NSM
				$loggedInUser = $name;
				$nsm = DB::select("SELECT u.id as nsm_id FROM `teams` t  INNER JOIN users u ON u.id = t.nsm_id where t.ase_id = '$request->user_id' GROUP BY t.nsm_id");
				foreach($nsm as $value){
					sendNotification($store->user_id, $value->nsm_id, 'store-add', '', $store->name. '  added by ' .$loggedInUser ,'Store ' .$store->name.' added  ');
				}
                return response()->json(['error'=>false, 'resp'=>'Store data created successfully','data'=>$store]);
        }else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }

    }
    
    //store list
    public function storeList(Request $request)
    {
        $areaId = $_GET['area_id'];
        $stores =Store::where('area_id',$areaId)->where('status',1)->orderby('name')->with('states:id,name','areas:id,name')->get();
        if ($stores) {
		    return response()->json(['error'=>false, 'resp'=>'Store data fetched successfully','data'=>$stores]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }
    }
    
       //store search for ASM area wise
    public function searchStore(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'area_id' => 'required',
            'keyword' => 'required'
        ]);

        if(!$validator->fails()){
            $areaId = $_GET['area_id'];
            $search = $_GET['keyword'];
            $data = Store::select('*');
            
            if(!empty($search)){
                $data = $data->where('area_id',$areaId)->where('contact', '=',$search)->orWhere('name', 'like', '%'.$search.'%')->with('states:id,name','areas:id,name')->where('status',1);
            }        

            $data = $data->get();
            if(!empty($data)){
                foreach($data as $item){
                    $retailer=Team::where('store_id',$item->id)->with('distributors:id,name')->first();
                    $item->team = $retailer;
                }
            }
            return response()->json([
                'error'=>false,
                'resp'=>"Store List",
                'data'=> $data
                
            ]);
        }else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }

    }
    
    //inactive store list user wise
    public function inactiveStorelist(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'user_id' => 'required',
            'area_id' => 'required'
        ]);
        if(!$validator->fails()){
            $ase = $_GET['user_id'];
            $area = $_GET['area_id'];
            $stores = Store::where('user_id',$ase)->where('area_id',$area)->where('status',0)->get();
            if ($stores) {
                return response()->json(['error'=>false, 'resp'=>'Store data fetched successfully','data'=>$stores]);
            } else {
                return response()->json(['error' => true, 'resp' => 'Something happened']);
            }
        }else {
                return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
            }  
    }
    
    
      //cart list user wise
    public function cartList($id,$userId)
    {
        $cart=Cart::where('store_id',$id)->where('user_id',$userId)->with('product:id,name,style_no','color:id,name','size:id,name')->get();
        $cart_count = DB::select("select ifnull(sum(qty),0) as total_qty from carts where store_id='$id' and user_id='$userId'");
            
        if(count($cart_count)>0){
            $total_quantity = $cart_count[0]->total_qty;
        }else{
            $total_quantity = 0;
        }
        if ($cart) {
            return response()->json(['error'=>false, 'resp'=>'cart List fetched successfully','data'=>$cart,'total_quantity'=>$total_quantity]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }
    }

    //add to cart
    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'user_id' => 'required',
            'store_id' => 'required',
            'product_id' => 'required',
            'order_type' => 'required',
            'color' => 'required'
        ]);
        if(!$validator->fails()){
            $collectedData = $request->except('_token');
            $multiColorSizeQty = explode("|", $collectedData['color']);
            $colors = array();
            $sizes = array();
            $qtys = array();
            $multiPrice =array();
            foreach($multiColorSizeQty as $m){
                $str_arr = explode("*",$m);
                array_push($colors,$str_arr[0]);
                array_push($sizes,$str_arr[1]);
                array_push($qtys,$str_arr[2]);
                
            }

            for($i=0;$i<count($colors);$i++)
            {
                $cartExists = Cart::where('product_id', $collectedData['product_id'])->where('user_id', $collectedData['user_id'])->where('color_id', $colors[$i])->where('size_id', $sizes[$i])->first();
                
    
                if ($cartExists) {
                        $cartExists->qty = $cartExists->qty + $qtys[$i];
                        $cartExists->save();
                } else {
                    if ($collectedData['order_type']) {
                        if ($collectedData['order_type'] == 'store-visit') {
                            $orderType = 'Store visit';
                        } else {
                            $orderType = 'Order on call';
                        }
                    } else {
                        $orderType = null;
                    }
                    
                    $newEntry = new Cart;
                    $newEntry->user_id = $collectedData['user_id'];
                    $newEntry->store_id = $collectedData['store_id'] ?? null;
                    $newEntry->order_type = $orderType;
                    $newEntry->product_id = $collectedData['product_id'];
                    $newEntry->color_id = $colors[$i];
                    $newEntry->size_id = $sizes[$i];
                    $newEntry->qty = $qtys[$i];

                    $newEntry->save();
                }
            }
            return response()->json(['error'=>false, 'resp'=>'Product added to cart successfully','data'=>$newEntry]);
        }else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }
    }
    
     //cart quantity update
    public function cartUpdate(Request $request, $cartId,$q)
    {
        $cart = Cart::findOrFail($cartId);

        if ($cart) {
			 $cart->qty = $q;
			 $cart->save();
            return response()->json([
                'error' => false,
                'resp' => 'Quantity updated'
            ]);
        } else {
            return response()->json([
                'error' => true,
                'resp' => 'Something Happened'
            ]);
        }
    }
    //cart delete
    public function cartDestroy($id)
    {
        $cart=Cart::destroy($id);
        if ($cart) {
            return response()->json(['error'=>false, 'resp'=>'Product removed from cart']);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }
    }
    //cart preview url
    public function CartPDF_URL(Request $request, $id,$userId)
    {
        return response()->json([
            'error' => false,
            'resp' => 'URL generated',
            'data' => url('/').'/api/cart/pdf/view/'.$id.'/'.$userId,
        ]);
    }

    
    //cart preview
    public function CartPDF_view(Request $request, $id,$userId)
    {
        $cartData =Cart::where('store_id',$id)->where('user_id',$userId)->with('product','stores','color','size')->get()->toArray();
		
        return view('api.cart-pdf', compact('cartData'));
    }
    
    //place order
   
    public function placeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_id' => ['required'],
            'user_id' => ['required'],
            'order_type' => ['required', 'string', 'min:1'],
            'order_lat' => ['required', 'string', 'min:1'],
            'order_lng' => ['required', 'string', 'min:1'],
            'comment' => ['nullable', 'string', 'min:1'],
            'distributor_id' => ['required'],
        ]);

        if (!$validator->fails()) {
            $params = $request->except('_token');
            $collectedData = collect($params);
            $cart_count = Cart::where('store_id', $collectedData['store_id'])->where('user_id',$collectedData['user_id'])->get();
            if (!empty($cart_count) ) {
			    $order_no = generateOrderNumber('secondary', $collectedData['store_id'])[0];
                $sequence_no = generateOrderNumber('secondary', $collectedData['store_id'])[1];
                // 1 order
                $newEntry = new Order;
                $newEntry->sequence_no = $sequence_no;
                $newEntry->order_no = $order_no;
                $newEntry->store_id = $collectedData['store_id'];
                $newEntry->user_id = $collectedData['user_id'];
                $newEntry->distributor_id = $collectedData['distributor_id'] ??'';
                $aseDetails=DB::select("select * from users where id='".$collectedData['user_id']."'");
                $aseName=$aseDetails[0]->name;
                $user=$newEntry->store_id;
    			$result = DB::select("select * from stores where id='".$user."'");
                $item=$result[0];
                $name = $item->name;
                $newEntry->order_type = $collectedData['order_type'] ?? null;
                $newEntry->order_lat = $collectedData['order_lat'] ?? null;
                $newEntry->order_lng = $collectedData['order_lng'] ?? null;
    
    			$newEntry->email = $item->email;
    			$newEntry->mobile = $item->contact;
                // fetch cart details
                $cartData = Cart::where('store_id', $newEntry->store_id)->where('user_id',$newEntry->user_id)->get();
                $subtotal = $totalOrderQty = 0;
                foreach($cartData as $cartValue) {
                    $totalOrderQty += $cartValue->qty;
                    $subtotal += $cartValue->product->offer_price * $cartValue->qty;
                    $store_id = $cartValue->store_id;
                    $order_type = $cartValue->order_type;
                }
                $newEntry->amount = $subtotal;
                $newEntry->comment = $collectedData['comment'] ?? null;
                $total = (int) $subtotal;
                $newEntry->final_amount = $total;
                $newEntry->save();
                // 2 insert cart data into order products
                $orderProducts = [];
                foreach($cartData as $cartValue) {
                    $orderProducts[] = [
                        'order_id' => $newEntry->id,
                        'product_id' => $cartValue->product_id,
                        'color_id' => $cartValue->color_id,
                        'size_id' => $cartValue->size_id,
                        'qty' => $cartValue->qty,
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ];
                }
                $orderProductsNewEntry = OrderProduct::insert($orderProducts);
                  Cart::where('store_id', $newEntry->store_id)->where('user_id',$newEntry->user_id)->delete();
    
    			// notification: sender, receiver, type, route, title
                // notification to ASE
                sendNotification($collectedData['user_id'], 'admin', 'secondary-order-place', 'front.user.order', $totalOrderQty.' New order placed',$totalOrderQty.' new order placed  '.$name);
    
    
    			
    
                // notification to SM
    			$loggedInUser = $aseName;
                $sm = DB::select("SELECT u.id as sm_id FROM `teams` t  INNER JOIN users u ON u.id = t.sm_id where t.ase_id = '".$collectedData['user_id']."' GROUP BY t.sm_id");
                foreach($sm as $value){
                    sendNotification($collectedData['user_id'], $value->sm_id, 'secondary-order-place', 'front.user.order', $totalOrderQty.' new order placed by ' .$loggedInUser ,$totalOrderQty.' new order placed from  '.$name);
                }
    			// notification to RSM
    			$loggedInUser = $aseName;
    			$rsm = DB::select("SELECT u.id as rsm_id FROM `teams` t  INNER JOIN users u ON u.id = t.rsm_id where t.ase_id = '".$collectedData['user_id']."' GROUP BY t.rsm_id");
    			foreach($rsm as $value){
    				sendNotification($collectedData['user_id'], $value->rsm_id, 'secondary-order-place', 'front.user.order', $totalOrderQty.' new order placed by ' .$loggedInUser ,$totalOrderQty.' new order placed from  '.$name);
    			}
    			
    			// notification to ZSM
    			$loggedInUser = $aseName;
    			$zsm = DB::select("SELECT u.id as zsm_id FROM `teams` t  INNER JOIN users u ON u.id = t.zsm_id where t.ase_id = '".$collectedData['user_id']."' GROUP BY t.zsm_id");
    			foreach($zsm as $value){
    				sendNotification($collectedData['user_id'], $value->zsm_id, 'secondary-order-place', 'front.user.order', $totalOrderQty.' new order placed by ' .$loggedInUser ,$totalOrderQty.' new order placed from  '.$name);
    			}
    
                // notification to NSM
    			$loggedInUser = $aseName;
    			$nsm = DB::select("SELECT u.id as nsm_id FROM `teams` t  INNER JOIN users u ON u.id = t.nsm_id where t.ase_id = '".$collectedData['user_id']."' GROUP BY t.nsm_id");
    			foreach($nsm as $value){
    				sendNotification($collectedData['user_id'], $value->nsm_id, 'secondary-order-place', 'front.user.order', $totalOrderQty.' new order placed by ' .$loggedInUser ,$totalOrderQty.' new order placed from  '.$name);
    			}
    
                return response()->json(['error'=>false, 'resp'=>'Order placed successfully','data'=>$newEntry]);
            }else{
                return response()->json(['error'=>true, 'resp'=>'cart empty']);
            }
        } else {
            return response()->json(['status' => 400, 'resp' => $validator->errors()->first()]);
        }
    }
    
      //order list
    public function orderList($id)
    {
        $order=Order::where('store_id',$id)->with('stores:id,name')->get();
        if ($order) {
            return response()->json(['error'=>false, 'resp'=>'order List fetched successfully','data'=>$order]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }
    }
    //order details
    public function orderDetails($id)
    {
        $order=OrderProduct::where('order_id',$id)->with('product','color','size','orders')->get();
        if ($order) {
            return response()->json(['error'=>false, 'resp'=>'order details fetched successfully','data'=>$order]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }
    }

    public function orderPDF_URL(Request $request, $id)
    {
        return response()->json([
            'error' => false,
            'resp' => 'URL generated',
            'data' => url('/').'/api/order/pdf/view/'.$id,
        ]);
    }

    

    public function orderPDF_view(Request $request, $id)
    {
        $orderData =OrderProduct::where('order_id',$id)->with('product','color','size','orders')->get()->toArray();
		
        return view('api.order-pdf', compact('orderData','id'));
    }
    
    //my order
    public function myOrders(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => ['required'],
            'store_id' => ['nullable'],
            'date_from' => ['nullable'],
            'date_to' => ['nullable'],
        ]);

        $user_id = $request->user_id;

        if (!$validator->fails()) {
                // date from
                if (!empty($request->date_from)) {
                    $from = date('Y-m-d', strtotime($request->date_from));
                } else {
                    $from = date('Y-m-01');
                }

                // date to
                if (!empty($request->date_to)) {
                    //$to = date('Y-m-d', strtotime($request->date_to. '+1 day'));
                    $to = $request->date_to;
                } else {
                    $to = date('Y-m-d');
                }
                
                $orderByQuery = 'o.id DESC';

                $orders = array();

                if(!empty($request->store_id)){
                    $store_id = $request->store_id;
                    $ordersData = DB::select("SELECT * FROM `orders` AS o
                    WHERE o.user_id = '".$user_id."' AND o.store_id = '".$store_id."'
                    AND (date(o.created_at) BETWEEN '".$from."' AND '".$to."')
                    ORDER BY ".$orderByQuery);
                }else{
                    $ordersData = DB::select("SELECT * FROM `orders` AS o
                    WHERE o.user_id = '".$user_id."'
                    AND (date(o.created_at) BETWEEN '".$from."' AND '".$to."')
                    ORDER BY ".$orderByQuery);
                }
                
                
                foreach($ordersData as $o){
                    $store_id = $o->store_id;
                    $user_id = $o->user_id;
                    $order_id = $o->id;

                    $storesData = Store::where('id',$store_id)->with('states','areas')->first();
                    $usersData = User::where('id',$user_id)->first();
                    $orderResult = OrderProduct::select(DB::raw("IFNULL(SUM(qty),0) as product_count"))->where('order_id',$order_id)->get();
                    $o->stores = $storesData;
                    $o->users = $usersData;
                    $o->product_count = $orderResult[0]->product_count;
                    array_push($orders,$o);
                }
            
        }else{
            $orders = array();
        }
        
        return response()->json(['error' => false, 'resp' => 'ASM orders with filter', 'data' => $orders]);
    }
    
          //ase wise store order count
      private function aseWiseStoreData($ase_id,$date_from,$date_to,$collection,$category,$style_no){
		$retailers = DB::table('stores')->select('id','name')->where('user_id',$ase_id)->orderby('name')->get();

		$total_quantity = 0;
		if($ase_id!=0 && count($retailers)>0){
			foreach($retailers as $retailer) {
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
					/*if (!isset($collection) || $collection == '10000') {
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
                    }*/
                    
                    
                    // Handle collection filter
                    $collectionQuery = $collection != '10000' && $collection 
                        ? " AND p.collection_id = " . (int)$collection 
                        : "";
                    
                    // Handle category filter
                    $categoryQuery = $category != '10000' && $category 
                        ? " AND p.cat_id = " . (int)$category 
                        : "";
                    
                    // Handle style_no filter
                    $styleNoQuery = $style_no
                        ? " AND p.style_no LIKE '%" . addslashes($style_no) . "%'" 
                        : "";
					// order by
					$orderByQuery = "op.id ASC";

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


				$quantity = $report[0]->qty;

				//echo $quantity."<br>";
				$total_quantity+=$quantity;

			}
		}
		
		return $total_quantity;
	}
    // store wise team report
    /*public function storeReportASM(Request $request)
    {
       $validator = Validator::make($request->all(), [
			'user_id' => ['required'],
			'date_from' => ['nullable'],
			'date_to' => ['nullable'],
			'collection' => ['nullable'],
			'category' => ['nullable'],
			'orderBy' => ['nullable'],
			'style_no' => ['nullable'],
		]);

		$selfResp = $aseResp = $resp = [];

		if (!$validator->fails()) {
			$userdata = User::findOrFail($request->user_id);
			$userName = $userdata->name;

			$ase_arr_result = DB::select("SELECT DISTINCT ase_id as ase_n from teams where asm_id='$request->user_id' and ase_id is not null");

			foreach($ase_arr_result as $ase){
				$ase_id = $ase->ase_n;
				$user_result = DB::select("SELECT IFNULL(id, 0) as id,name from users where id='$ase_id'");

				if(count($user_result)>0){
					$ase_id = $user_result[0]->id;
                    $ase_name=$user_result[0]->name;
				}else{
					$ase_id = 0;
				}
				
				$total_quantity = 0;
				
				if($ase_id!=0){
					$total_quantity = $this->aseWiseStoreData($ase_id,$request->date_from,$request->date_to,$request->collection,$request->category,$request->style_no);
				}
				
				$aseResp[] = [
							'ase_id' => $ase_id,
							'ase_name' => $ase_name,
							'quantity' => $total_quantity
						];
				
			}

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

                    // Handle collection filter
                    $collectionQuery = $request->collection != '10000' && $request->collection 
                        ? " AND p.collection_id = " . (int)$request->collection 
                        : "";
                    
                    // Handle category filter
                    $categoryQuery = $request->category != '10000' && $request->category 
                        ? " AND p.cat_id = " . (int)$request->category 
                        : "";
                    
                    // Handle style_no filter
                    $styleNoQuery = $request->style_no 
                        ? " AND p.style_no LIKE '%" . addslashes($request->style_no) . "%'" 
                        : "";

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
                    WHERE o.user_id = '".$request->user_id."'
                    ".$collectionQuery."
                    ".$categoryQuery."
                    ".$styleNoQuery."
                    AND (date(o.created_at) BETWEEN '".$from."' AND '".$to."')
                    ORDER BY ".$orderByQuery);
                } else {
                    $report = DB::select("SELECT IFNULL(SUM(op.qty), 0) AS qty FROM `orders` AS o INNER JOIN order_products AS op ON op.order_id = o.id WHERE o.user_id = '".$request->user_id."' AND (date(o.created_at) BETWEEN '".date('Y-m-01')."' AND '".date('Y-m-d', strtotime('+1 day'))."')");
                }
				
                $selfResp[] = [
                    'id' => $request->user_id,
					'name' => $userName,
					'quantity' => $report[0]->qty
					
                ];
			$resp[] = [
				'self_sales' => $selfResp,
				'secondary_sales' => $aseResp,
			];

			return response()->json(['error' => false, 'message' => 'ASM report - Team wise', 'data' => $resp]);
		} else {
			return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
		}
    }*/
    
    // public function storeReportASM(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'user_id' => ['required'],
    //         'date_from' => ['nullable'],
    //         'date_to' => ['nullable'],
    //         'collection' => ['nullable'],
    //         'category' => ['nullable'],
    //         'orderBy' => ['nullable'],
    //         'style_no' => ['nullable'],
    //     ]);
    
    //     if ($validator->fails()) {
    //         return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
    //     }
    
    //     $user = User::findOrFail($request->user_id);
    //     $aseIds = Team::where('asm_id', $request->user_id)
    //         ->whereNotNull('ase_id')
    //         ->pluck('ase_id');
    
    //     // Fetch ASE Data in One Query
    //     $ases = User::whereIn('id', $aseIds)->get();
    
    //     // Prepare date range
    //     $from = $request->date_from ? date('Y-m-d', strtotime($request->date_from)) : date('Y-m-01');
    //     $to = $request->date_to ? date('Y-m-d', strtotime($request->date_to)) : date('Y-m-d', strtotime('+1 day'));
    
    //     // Prepare filters
    //     $filters = [
    //         'collection' => $request->collection != '10000' ? $request->collection : null,
    //         'category' => $request->category != '10000' ? $request->category : null,
    //         'style_no' => $request->style_no,
    //     ];
    //   // dd($filters);
    //     // Fetch ASE Sales in Bulk
    //     $aseSales = $this->fetchASESales($aseIds, $filters, $from, $to);
    
    //     // Fetch Self Sales
    //     $selfSales = $this->fetchUserSales($request->user_id, $filters, $from, $to);
    
    //     $aseResp = $ases->map(function ($ase) use ($aseSales) {
    //         return [
    //             'ase_id' => $ase->id,
    //             'ase_name' => $ase->name,
    //             'quantity' => $aseSales[$ase->id] ?? 0,
    //         ];
    //     });
    
    //     return response()->json([
    //         'error' => false,
    //         'message' => 'ASM report - Team wise',
    //         'data' => [
    //             'self_sales' => [
    //                 [
    //                     'id' => $user->id,
    //                     'name' => $user->name,
    //                     'quantity' => $selfSales,
    //                 ]
    //             ],
    //             'secondary_sales' => $aseResp,
    //         ],
    //     ]);
    // }

    // private function fetchASESales($aseIds, $filters, $from, $to)
    // {
    //     if (empty($aseIds)) {
    //         return [];
    //     }
    
    //     $query = DB::table('orders as o')
    //         ->join('order_products as op', 'op.order_id', '=', 'o.id')
    //         ->join('products as p', 'p.id', '=', 'op.product_id')
    //         ->select('o.user_id', DB::raw('SUM(op.qty) as total_qty'))
    //         ->whereIn('o.user_id', $aseIds)
    //         ->whereBetween('o.created_at', [$from, $to])
    //         ->groupBy('o.user_id');
    
    //     // Apply filters
    //     if ($filters['collection']) {
    //         $query->where('p.collection_id', $filters['collection']);
    //     }
    //     if ($filters['category']) {
    //         $query->where('p.cat_id', $filters['category']);
    //     }
    //     if ($filters['style_no']) {
    //         $query->where('p.style_no', 'LIKE', '%' . addslashes($filters['style_no']) . '%');
    //     }
    
    //     return $query->pluck('total_qty', 'o.user_id')->toArray();
    // }

    // private function fetchUserSales($userId, $filters, $from, $to)
    // {
    //     $query = DB::table('orders as o')
    //         ->join('order_products as op', 'op.order_id', '=', 'o.id')
    //         ->join('products as p', 'p.id', '=', 'op.product_id')
    //         ->where('o.user_id', $userId)
    //         ->whereBetween('o.created_at', [$from, $to])
    //         ->select(DB::raw('SUM(op.qty) as total_qty'));
    
    //     // Apply filters
    //     if ($filters['collection']) {
    //         $query->where('p.collection_id', $filters['collection']);
    //     }
    //     if ($filters['category']) {
    //         $query->where('p.cat_id', $filters['category']);
    //     }
    //     if ($filters['style_no']) {
    //         $query->where('p.style_no', 'LIKE', '%' . addslashes($filters['style_no']) . '%');
    //     }
    
    //     return $query->value('total_qty') ?? 0;
    // }
    
    
    public function storeReportASM(Request $request)
{
    $validator = Validator::make($request->all(), [
        'user_id' => ['required'],
        'date_from' => ['nullable'],
        'date_to' => ['nullable'],
        'collection' => ['nullable'],
        'category' => ['nullable'],
        'orderBy' => ['nullable'],
        'style_no' => ['nullable'],
    ]);
    
    if ($validator->fails()) {
        return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
    }
    
    $user = User::findOrFail($request->user_id);
    $aseIds = Team::where('asm_id', $request->user_id)
        ->whereNotNull('ase_id')
        ->pluck('ase_id');
    
    // Fetch ASE Data in One Query with Eager Loading (If applicable)
    $ases = User::whereIn('id', $aseIds)->get();

    // Prepare date range
    $from = $request->date_from ? date('Y-m-d', strtotime($request->date_from)) : date('Y-m-01');
    $to = $request->date_to ? date('Y-m-d', strtotime($request->date_to)) : date('Y-m-d', strtotime('+1 day'));
    
    // Prepare filters
    $filters = [
        'collection' => $request->collection != '10000' ? $request->collection : null,
        'category' => $request->category != '10000' ? $request->category : null,
        'style_no' => $request->style_no,
    ];

    // Fetch ASE Sales in Bulk (Optimize Query)
    $aseSales = $this->fetchASESales($aseIds, $filters, $from, $to);

    // Fetch Self Sales
    $selfSales = $this->fetchUserSales($request->user_id, $filters, $from, $to);

    // Map ASE data to response format
    $aseResp = $ases->map(function ($ase) use ($aseSales) {
        return [
            'ase_id' => $ase->id,
            'ase_name' => $ase->name,
            'quantity' => $aseSales[$ase->id] ?? 0,
        ];
    });
    $selfResp=[
                    'id' => $user->id,
                    'name' => $user->name,
                    'quantity' => $selfSales,
                ];
    $resp[] = [
				'self_sales' =>[ $selfResp],
				'secondary_sales' => $aseResp,
			];
    return response()->json([
        'error' => false,
        'message' => 'ASM report - Team wise',
        'data' =>$resp
        // 'data' => [
        //     'self_sales' => [
        //         [
        //             'id' => $user->id,
        //             'name' => $user->name,
        //             'quantity' => $selfSales,
        //         ]
        //     ],
        //     'secondary_sales' => $aseResp,
        // ],
    ]);
}

private function fetchASESales($aseIds, $filters, $from, $to)
{
    if (empty($aseIds)) {
        return [];
    }

    // Bulk Fetch ASE Sales with optimized query
    $query = DB::table('orders as o')
        ->join('order_products as op', 'op.order_id', '=', 'o.id')
        ->join('products as p', 'p.id', '=', 'op.product_id')
        ->select('o.user_id', DB::raw('SUM(op.qty) as total_qty'))
        ->whereIn('o.user_id', $aseIds)
        ->whereBetween('o.created_at', [$from, $to])
        ->groupBy('o.user_id');

    // Apply filters
    if ($filters['collection']) {
        $query->where('p.collection_id', $filters['collection']);
    }
    if ($filters['category']) {
        $query->where('p.cat_id', $filters['category']);
    }
    if ($filters['style_no']) {
        $query->where('p.style_no', 'LIKE', '%' . addslashes($filters['style_no']) . '%');
    }

    return $query->pluck('total_qty', 'o.user_id')->toArray();
}

private function fetchUserSales($userId, $filters, $from, $to)
{
    $query = DB::table('orders as o')
        ->join('order_products as op', 'op.order_id', '=', 'o.id')
        ->join('products as p', 'p.id', '=', 'op.product_id')
        ->where('o.user_id', $userId)
        ->whereBetween('o.created_at', [$from, $to])
        ->select(DB::raw('SUM(op.qty) as total_qty'));

    // Apply filters
    if ($filters['collection']) {
        $query->where('p.collection_id', $filters['collection']);
    }
    if ($filters['category']) {
        $query->where('p.cat_id', $filters['category']);
    }
    if ($filters['style_no']) {
        $query->where('p.style_no', 'LIKE', '%' . addslashes($filters['style_no']) . '%');
    }

    return $query->value('total_qty') ?? 0;
}

    //product wise team report
    public function productReportASM(Request $request)
    {
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
            //$userName = User::findOrFail($request->ase_id);
            //$userName = $userName->name;

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
                /*if ($request->collection == '10000' || !isset($request->collection)) {
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
                }*/
                $collectionQuery = $request->collection != '10000' && $request->collection 
                        ? " AND p.collection_id = " . (int)$request->collection 
                        : "";
                    
                    // Handle category filter
                $categoryQuery = $request->category != '10000' && $request->category 
                        ? " AND p.cat_id = " . (int)$request->category 
                        : "";
                    
                    // Handle style_no filter
                $styleNoQuery = $request->style_no 
                        ? " AND p.style_no LIKE '%" . addslashes($request->style_no) . "%'" 
                        : "";

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
                WHERE o.user_id = ".$request->user_id."
                AND (DATE(o.created_at) BETWEEN '".$from."' AND '".$to."')
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
                WHERE o.user_id = ".$request->user_id."
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
			
			
         	return response()->json(['error' => false, 'resp' => 'ASM report - Product wise', 'data' => $resp]);
        } else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }
    }

    //ASM wise ASE List
    public function aseList(Request $request,$id)
    {
       $data=Team::where('asm_id',$id)->groupby('ase_id')->with('ase:id,name')->get();
       if (count($data)==0) {
                return response()->json(['error'=>true, 'resp'=>'No data found']);
       } else {
                return response()->json(['error'=>false, 'resp'=>'ASE List','data'=>$data]);
       } 
        
    }
    //activity log ase wise
    public function activityList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required",
            "date" => "required",
        ]);
        if (!$validator->fails()) 
        {
         $data = (object)[];
         $user_id = $_GET['user_id'];
         $date = $_GET['date'];
		 $data->activity=Activity::where('user_id',$user_id)->whereDate('date',$date)->orderby('id','desc')->get();
        if (count($data->activity)==0) {
                return response()->json(['error'=>true, 'resp'=>'No data found']);
            } else {
                return response()->json(['error'=>false, 'resp'=>'Activity List','data'=>$data->activity]);
            } 
        } else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }
		
	}

    // no order reason history
    public function noOrderReasonDetail(Request $request,$id,$userId)
    {
        $noOrder=UserNoOrderReason::where('store_id', $id)->where('user_id',$userId)->with('stores')->orderby('id','desc')->get();
		if ($noOrder) {
        return response()->json(['error'=>false, 'resp'=>'no order list data fetched successfully','data'=>$noOrder]);
		}else{
			  return response()->json(['error' => true, 'message' => 'No data found']);
		}
    }

    //no order reason update
    public function noOrderReasonUpdate(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'user_id' => 'required|integer',
            'store_id' => 'required|integer',
            'no_order_reason_id' => 'required|integer',
            'comment' => 'required',
            'location' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'date' => 'required',
            'time' => 'required',
        ]);

        if(!$validator->fails()){
            $noorder = new UserNoorderreason();
            $noorder->user_id = $request['user_id'];
            $noorder->store_id = $request['store_id'];
            $noorder->no_order_reason_id = $request['no_order_reason_id'];
            $noorder->comment	 = $request['comment'];
            $noorder->description	 = $request['description'];
            $noorder->location = $request['location'];
            $noorder->lat = $request['lat'];
            $noorder->lng = $request['lng'];
            $noorder->date	 = $request['date'];
            $noorder->time	 = $request['time'];
            $noorder->save();
            return response()->json(['error'=>false, 'resp'=>'No order Reason data created successfully','data'=>$noorder]);
        }else {
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
