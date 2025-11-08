<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Cart;
use App\Models\User;
use App\Models\Store;
use Illuminate\Support\Facades\Validator;
use DB;
class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id,$userId)
    {
        $order=Order::where('store_id',$id)->where('user_id',$userId)->orderby('id','desc')->with('stores:id,name')->get();
        if ($order) {
            return response()->json(['error'=>false, 'resp'=>'order List fetched successfully','data'=>$order]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }
    }
    
     public function storeOrderList($id,$userId)
    {
        $order=Order::where('store_id',$id)->orderby('id','desc')->with('stores:id,name')->get();
        if ($order) {
            return response()->json(['error'=>false, 'resp'=>'order List fetched successfully','data'=>$order]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_id' => ['required'],
            'user_id' => ['required'],
            'order_type' => ['required', 'string', 'min:1'],
            'order_lat' => ['required', 'string', 'min:1'],
            'order_lng' => ['required', 'string', 'min:1'],
            'comment' => ['nullable', 'string', 'min:1'],
           
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
                $newEntry->distributor_id = $collectedData['distributor_id'] ?? '';
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
    
    
    			// notification to ASM
    			$loggedInUser = $aseName;
    				$asm = DB::select("SELECT u.id as asm_id FROM `teams` t  INNER JOIN users u ON u.id = t.asm_id where t.ase_id = '".$collectedData['user_id']."' GROUP BY t.asm_id");
    			foreach($asm as $value){
    				sendNotification($collectedData['user_id'], $value->asm_id, 'secondary-order-place', 'front.user.order', $totalOrderQty.' new order placed by ' .$loggedInUser ,$totalOrderQty.' new order placed from  '.$name);
    			}
    
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order=OrderProduct::where('order_id',$id)->with('product','color','size','orders')->get();
        if ($order) {
            return response()->json(['error'=>false, 'resp'=>'order details fetched successfully','data'=>$order]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
    public function PDF_URL(Request $request, $id)
    {
        return response()->json([
            'error' => false,
            'resp' => 'URL generated',
            'data' => url('/').'/api/order/pdf/view/'.$id,
        ]);
    }

    

    public function PDF_view(Request $request, $id)
    {
        $orderData =OrderProduct::where('order_id',$id)->with('product','color','size','orders')->get()->toArray();
		
        return view('api.order-pdf', compact('orderData','id'));
    }
    
     public function dashboardCount(Request $request) {
        //dd($request->all());
            $validator = Validator::make($request->all(), [
                "user_id" => "required",
                "date_from" => "nullable",
                "date_to" => "nullable",
            ]);
            if (!$validator->fails()) {
                $ase = $request->user_id;
                // DB::enableQueryLog();
                if ( request()->input('date_from') || request()->input('date_to') ) {
                    // date from
                    if (!empty(request()->input('date_from'))) {
                        $from = date('Y-m-d', strtotime(request()->input('date_from')));
                    } else {
                        $from = date('Y-m-01');
                    }
        
                    // date to
                    if (!empty(request()->input('date_to'))) {
                        $to = date('Y-m-d', strtotime(request()->input('date_to').' +1 day'));
                    } else {
                        $to = date('Y-m-d', strtotime('+1 day'));
                    }
                    $ase_name = User::where('id',$ase)->first();
        
                    $secondaryreport =Store::select('id','name','address','state_id','area_id','pin')->where('user_id',$ase)->with('states','areas')->get();
                    //dd($secondaryreport);
                    $respArr = [];
        
                    foreach ($secondaryreport as $key => $value) {
                       
                        $report = Order::select(DB::raw("(SUM(orders.final_amount)) as amount"),DB::raw("SUM(order_products.qty) as qty"))
                                 ->join('order_products', 'orders.id', '=', 'order_products.order_id')->where('orders.store_id','=',$value->id)->where('orders.user_id','=',$ase)
                                 ->whereBetween('orders.created_at', [$from, $to])->get();
                       //dd($value);
                        $respArr[] = [
                            'retailer_id' => $value->id,
                            'store_name' => $value->name,
                            'address' => $value->address,
                            'area' => $value->areas->name,
                            'state' => $value->states->name,
                            'pin' => $value->pin,
                            'amount' => $report[0]->amount ?? 0,
                            'qty' => $report[0]->qty ?? 0,
                        ];
        
                    }
                } else {
                    $ase_name = User::where('id',$ase)->first();
                    
                    $secondaryreport = Store::select('id','name')->where('user_id',$ase)->get();
                    $respArr = [];
        
                    foreach ($secondaryreport as $key => $value) {
                       
                        $report = Order::select(DB::raw("(SUM(orders.final_amount)) as amount"),DB::raw("SUM(order_products.qty) as qty"))
                                 ->join('order_products', 'orders.id', '=', 'order_products.order_id')->where('orders.store_id','=',$value->id)->where('orders.user_id','=',$ase)
                                 ->where('orders.created_at', '>', date('Y-m-d'))->get();
                        $respArr[] = [
                            'retailer_id' => $value->id,
                            'store_name' => $value->name,
                            'amount' => $report[0]->amount ?? 0,
                            'qty' => $report[0]->qty ?? 0,
                        ];
        
                    }
                }
                return response()->json(['error' => false, 'resp' => 'ASE wise Secondary Sales|Retailer wise Order Count' ,'data'=> $respArr]);
            }else {
                return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
            }
        }
        
         //my order for ASE
        public function myOrdersFilter(Request $request){
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
            
            return response()->json(['error' => false, 'resp' => 'Store orders with filter', 'data' => $orders]);
        }
}
