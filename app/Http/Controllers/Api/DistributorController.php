<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Models\CartDistributor;
use App\Models\OrderDistributor;
use App\Models\OrderProductDistributor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Store;
use App\Models\Product;
use App\Models\State;
use App\Models\Area;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\RetailerOrder;
use App\Models\RewardOrderProduct;
use App\Models\Branding;
use Carbon\Carbon;
use App\Models\RetailerWalletTxn;
use App\Models\StoreFormSubmit;
use App\Models\DistributorProduct;
use Illuminate\Support\Facades\Validator;
class DistributorController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'user_id' => 'required',
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
                $cartExists = CartDistributor::where('product_id', $collectedData['product_id'])->where('user_id', $collectedData['user_id'])->where('color_id', $colors[$i])->where('size_id', $sizes[$i])->first();
                
    
                if ($cartExists) {
                        $cartExists->qty = $cartExists->qty + $qtys[$i];
                        $cartExists->save();
                } else {
                   
                        $orderType = 'distributor-visit';
                    
                    
                    $newEntry = new CartDistributor;
                    $newEntry->user_id = $collectedData['user_id'];
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($userId)
    {
        $cart=CartDistributor::where('user_id',$userId)->with('product:id,name,style_no','color:id,name','size:id,name')->get();
        $cart_count = DB::select("select ifnull(sum(qty),0) as total_qty from carts_distributors where  user_id='$userId'");
		
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
    public function update(Request $request, $cartId,$q)
    {
        $cart = CartDistributor::findOrFail($cartId);

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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cart=CartDistributor::destroy($id);
        if ($cart) {
            return response()->json(['error'=>false, 'resp'=>'Product removed from cart']);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }
    }
    
    
     public function delete($id)
    {
        $cart=CartDistributor::where('user_id',$id)->delete();
        if ($cart) {
            return response()->json(['error'=>false, 'resp'=>'Product removed from cart']);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }
    }

    public function PDF_URL(Request $request,$userId)
    {
        return response()->json([
            'error' => false,
            'resp' => 'URL generated',
            'data' => url('/').'/api/distributor/cart/pdf/view/'.$userId,
        ]);
    }

    

    public function PDF_view(Request $request,$userId)
    {
        $cartData =CartDistributor::where('user_id',$userId)->with('product','color','size')->get()->toArray();
		
        return view('api.distributor-cart-pdf', compact('cartData'));
    }
    
    
     public function orderList($userId)
    {
        $order=OrderDistributor::where('user_id',$userId)->orderby('id','desc')->get();
        if ($order) {
            return response()->json(['error'=>false, 'resp'=>'order List fetched successfully','data'=>$order]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }
    }
    
    public function orderDetail($id)
    {
        $order=OrderProductDistributor::where('order_id',$id)->with('product','color','size','orders')->get();
        if ($order) {
            return response()->json(['error'=>false, 'resp'=>'order details fetched successfully','data'=>$order]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }
    }
    
    
    public function placeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required'],
            'order_type' => ['required', 'string', 'min:1'],
            'order_lat' => ['required', 'string', 'min:1'],
            'order_lng' => ['required', 'string', 'min:1'],
            'comment' => ['nullable', 'string', 'min:1'],
           
        ]);

        if (!$validator->fails()) {
            $params = $request->except('_token');
            $collectedData = collect($params);
            $cart_count = CartDistributor::where('user_id',$collectedData['user_id'])->get();
            if (!empty($cart_count) ) {
			$order_no = generatePrimaryOrderNumber('primary', $collectedData['user_id'])[0];
            $sequence_no = generatePrimaryOrderNumber('primary', $collectedData['user_id'])[1];
                // 1 order
                $newEntry = new OrderDistributor;
                $newEntry->sequence_no = $sequence_no;
                $newEntry->order_no = $order_no;
                $newEntry->user_id = $collectedData['user_id'];
                $aseDetails=DB::select("select * from users where id='".$collectedData['user_id']."'");
                $aseName=$aseDetails[0]->name;
                
                $newEntry->order_type = $collectedData['order_type'] ?? null;
                $newEntry->order_lat = $collectedData['order_lat'] ?? null;
                $newEntry->order_lng = $collectedData['order_lng'] ?? null;
    
    			
                // fetch cart details
                $cartData = CartDistributor::where('user_id',$newEntry->user_id)->get();
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
                $orderProductsNewEntry = OrderProductDistributor::insert($orderProducts);
                  CartDistributor::where('user_id',$newEntry->user_id)->delete();
    
    			// notification: sender, receiver, type, route, title
                // notification to ASE
                sendNotification($collectedData['user_id'], 'admin', 'primary-order-place', 'front.user.order', $totalOrderQty.' New order placed',$totalOrderQty.' new order placed  '.$aseName);
    
    
    		
                return response()->json(['error'=>false, 'resp'=>'Order placed successfully','data'=>$newEntry]);
            }else{
                return response()->json(['error'=>true, 'resp'=>'cart empty']);
            }
        } else {
            return response()->json(['status' => 400, 'resp' => $validator->errors()->first()]);
        }
    }
    
    
    
    public function orderPDF_URL(Request $request, $id)
    {
        return response()->json([
            'error' => false,
            'resp' => 'URL generated',
            'data' => url('/').'/api/distributor/order/pdf/view/'.$id,
        ]);
    }

    

    public function orderPDF_view(Request $request, $id)
    {
        $orderData =OrderProductDistributor::where('order_id',$id)->with('product','color','size','orders')->get()->toArray();
		
        return view('api.distributor-order-pdf', compact('orderData','id'));
    }
    
    
    //retailer list
    public function retailerList(Request $request,$id)
    {
            $data =Store::select('stores.*',DB::raw('SUM(retailer_wallet_txns.amount) as total_amount'))->join('teams', 'stores.id', '=', 'teams.store_id')->join('retailer_wallet_txns', 'stores.id', '=', 'retailer_wallet_txns.user_id')->join('retailer_barcodes', 'retailer_wallet_txns.barcode_id', '=', 'retailer_barcodes.id')->whereRaw("FIND_IN_SET(?, retailer_barcodes.distributor_id)", [$id])->where('stores.status',1)->groupby('stores.id')->orderby('stores.name')->with('states:id,name','areas:id,name')->get();

            return response()->json(['error' => false, 'message' => 'Distributor wise Retailer report', 'data' => $data]);
       
    }
    
    
    public function storeOrder(Request $request): JsonResponse
    {
      // $params = $request->except('_token');
		$validator = Validator::make($request->all(), [
            'store_id' => ['required'],
            'date_from' => ['nullable'],
			'user_id' => ['nullable'],
           
        ]);
         DB::enableQueryLog();
        if (!$validator->fails()) {
         $store_id = $request->store_id;
		 
		 $coll_array= array();	
		 $product_arr= array();	
		
	
		if ( !empty($request->date_from)) {
		 			if (!empty($request->date_from)) {
                        $date = date('Y-m-d', strtotime($request->date_from));
                    } else {
                        $date = date('Y-m-d');
                    }
        $resp = Order::orderBy('id', 'desc')->where('store_id',$store_id)->whereDate('created_at', 'like', '%'.$date.'%')->get();
				if(!empty($resp)){
					foreach($resp as $item){
						$orderProduct = OrderProduct::where('order_id',$item->id)->with('color','size','product')->get();
						$item->orderProduct =$orderProduct;
					}
			  	}

		}else{
			 $resp = Order::orderBy('id', 'desc')->where('store_id',$store_id)->whereDate('created_at', '=', Carbon::now())->get();
			if(!empty($resp)){
					foreach($resp as $item){
						$orderProduct = OrderProduct::where('order_id',$item->id)->with('color','size','product')->get();
						$item->orderProduct =$orderProduct;
					}
			  	}

		}
       
        	return response()->json(['error'=>false, 'resp'=>'Order data fetched successfully','data'=>$resp]);
	} else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
    }
    
    
    public function storeOrderCsv(Request $request)
    {
     
		$validator = Validator::make($request->all(), [
            'store_id' => ['required'],
            'date_from' => ['required'],
			'date_to' => ['required']
           
        ]);
         DB::enableQueryLog();
        if (!$validator->fails()) {
          $store_id = $request->store_id;
		  
		  $coll_array= array();	
		  $product_arr= array();	
	
        if (!empty($request->date_from)) {
            $from = date('Y-m-d', strtotime($request->date_from));
        } else {
            $from = date('Y-m-d');
        }
        // date to
        if (!empty($request->date_to)) {
            $to = date('Y-m-d', strtotime($request->date_to));
        } else {
            $to = date('Y-m-d', strtotime('+1 day'));
        }
		if ( !empty($request->date_from) || !empty($request->date_to) ) {
		 			
				$data = OrderProduct::select('order_products.qty','orders.order_no', 'order_products.product_id',  'products.style_no','colors.name AS color','sizes.name AS size','stores.name AS store_name','teams.ase_id','teams.asm_id','teams.rsm_id','teams.zsm_id','teams.sm_id','teams.nsm_id','teams.state_id','teams.area_id','stores.pin','orders.created_at')
                        ->join('colors', 'colors.id', '=', 'order_products.color_id')->join('sizes', 'sizes.id', '=', 'order_products.size_id')->join('products', 'products.id', '=', 'order_products.product_id')->join('orders', 'orders.id', '=', 'order_products.order_id')->join('stores', 'stores.id', '=', 'orders.store_id')->join('teams', 'teams.store_id', '=', 'stores.id')
						 ->where('orders.store_id',$store_id)->whereBetween('orders.created_at', [$from, $to])->groupby('order_products.id')->orderby('orders.id','desc')->get();
        } else{
            $data = OrderProduct::select('order_products.qty','orders.order_no', 'order_products.product_id',  'products.style_no','sizes.name AS size','colors.name AS color','stores.name AS store_name','teams.ase_id','teams.asm_id','teams.rsm_id','teams.zsm_id','teams.sm_id','teams.nsm_id','teams.state_id','teams.area_id','stores.pin','orders.created_at')
                        ->join('colors', 'colors.id', '=', 'order_products.color_id')->join('sizes', 'sizes.id', '=', 'order_products.size_id')->join('products', 'products.id', '=', 'order_products.product_id')->join('orders', 'orders.id', '=', 'order_products.order_id')->join('stores', 'stores.id', '=', 'orders.store_id')->join('teams', 'teams.store_id', '=', 'stores.id')
						 ->where('orders.store_id',$store_id)->whereBetween('orders.created_at', [$from, $to])->groupby('order_products.id')->orderby('orders.id','desc')->get();
        }    
			
       
        if (count($data) > 0) {
            $delimiter = ",";
            $filename = "lux-secondary-order-".date('Y-m-d').".csv";

            // Create a file pointer
            $f = fopen('php://memory', 'w');

            // Set column headers
            $fields = array('SR', 'ORDER NO','PRODUCT', 'STYLE NO','COLOR', 'SIZE', 'QTY','STORE', 'ASE', 'ASM','RSM','SM','ZSM','NSM', 'STATE', 'AREA', 'PINCODE','DATETIME');
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($data as $row) {
                $datetime = date('j F, Y h:i A', strtotime($row['created_at']));
                $product=Product::where('id',$row['product_id'])->first();
                $store = Store::select('name')->where('id', $row['store_id'])->first();
                $ase = User::select('name', 'mobile', 'state', 'city', 'pin')->where('id', $row['ase_id'])->first();

                $ase = User::select('name', 'mobile', 'state', 'city', 'pin')->where('id', $row['ase_id'])->first();
                $asm = User::select('name')->where('id', $row['asm_id'])->first();
                $rsm = User::select('name')->where('id', $row['rsm_id'])->first();
                $sm = User::select('name')->where('id', $row['sm_id'])->first();
                $zsm = User::select('name', 'mobile', 'state', 'city', 'pin')->where('id', $row['zsm_id'])->first();
                $nsm = User::select('name', 'mobile', 'state', 'city', 'pin')->where('id', $row['nsm_id'])->first();
                  $state = State::select('name')->where('id', $row['state_id'])->first();
                    $area = Area::select('name')->where('id', $row['area_id'])->first();

                $lineData = array(
                    $count,
                    $row->order_no,
                    $product->name,
                    $product->style_no,
                    $row->color,
                    $row->size,
                    $row->qty,
                    $row->store_name ?? '',
                    $ase->name ?? '',
                    $asm->name ?? '',
                    $rsm->name ?? '',
                    $sm->name ?? '',
                    $zsm->name ?? '',
                    $nsm->name ?? '',
                    $state->name ?? '',
                    $area->name ?? '',
                    $row->pin ?? '',
                    
                    $datetime
                );

                fputcsv($f, $lineData, $delimiter);

                $count++;
            }
		
            // Move back to beginning of file
            fseek($f, 0);

            // Set headers to download file rather than displayed
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '";');

            //output all remaining data on a file pointer
            fpassthru($f);
            }else{
                return response()->json(['error'=>false, 'resp'=>'No data found']);
            }
        } else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
    }
    
    
    
     //product wise order details for distributor dashboard
	
	 public function productOrder(Request $request): JsonResponse
     {
       // $params = $request->except('_token');
         $validator = Validator::make($request->all(), [
             'date_to' => ['nullable'],
             'date_from' => ['nullable'],
             'user_id' => ['required'],
         ]);
          DB::enableQueryLog();
         if (!$validator->fails()) {
        
                  $repArray=array();
                  $coll_array= array();	
                  $product_arr= array();	
                
             $store_arr_result = DB::select("SELECT s.name,s.id from teams t  INNER JOIN stores s ON s.id = t.store_id where find_in_set('".$request->user_id."',t.distributor_id) and store_id is not null");
		
      foreach($store_arr_result as $store){
          
           array_push($coll_array, $store->id);
      }
		  if (!empty($request->date_from)) {
                         $from = date('Y-m-d', strtotime($request->date_from));
                     } else {
                         $from = date('Y-m-d');
                     }
					 // date to
				if (!empty($request->date_to)) {
					$to = date('Y-m-d', strtotime($request->date_to.'+1 day'));
					//dd($to);
				} else {
					$to = date('Y-m-d', strtotime('+1 day'));
				}
         if ( !empty($request->date_from) || !empty($request->date_to) ) {
                      
             $resp = OrderProduct::select(DB::raw("(SUM(order_products.qty)) as product_count"),'orders.order_no', 'order_products.product_id', 'products.name AS product_name',  'sizes.name AS size','colors.name AS color','stores.name','orders.created_at')
                     ->join('products', 'products.id', '=', 'order_products.product_id')->join('orders', 'orders.id', '=', 'order_products.order_id')->join('colors', 'colors.id', '=', 'order_products.color_id')->join('sizes', 'sizes.id', '=', 'order_products.size_id')->join('stores', 'stores.id', '=', 'orders.store_id')->join('teams', 'teams.store_id', '=', 'stores.id')
                     ->whereIN('orders.store_id',$coll_array)->whereBetween('orders.created_at', [$from, $to])->groupby('order_products.id')->orderby('order_products.id','desc')->get();
			
            }else{

                 $resp = OrderProduct::select(DB::raw("(SUM(order_products.qty)) as product_count"),'orders.order_no', 'order_products.product_id', 'products.name AS product_name', 'sizes.name AS size','colors.name AS color','stores.name','orders.created_at')
                     ->join('products', 'products.id', '=', 'order_products.product_id')->join('orders', 'orders.id', '=', 'order_products.order_id')->join('colors', 'colors.id', '=', 'order_products.color_id')->join('sizes', 'sizes.id', '=', 'order_products.size_id')->join('stores', 'stores.id', '=', 'orders.store_id')->join('teams', 'teams.store_id', '=', 'stores.id')
                     ->whereIN('orders.store_id',$coll_array)->whereBetween('orders.created_at', [$from, $to])->groupby('order_products.id')->orderby('order_products.id','desc')->get();
 
            }
            // $repArray=$resp;
            
        
             return response()->json(['error'=>false, 'resp'=>'Product wise Order data fetched successfully','data'=>$resp]);
         
     } else {
             return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
         }
     }
     
     
     public function productOrderCsv(Request $request)
    {
        // return Excel::download(new OrderExport, 'Secondary-sales-'.date('Y-m-d').'.csv');
	        $validator = Validator::make($request->all(), [
             'date_to' => ['nullable'],
             'date_from' => ['nullable'],
             'user_id' => ['required'],
            ]);
            DB::enableQueryLog();
        if (!$validator->fails()) {
        
                  
                  $coll_array= array();	
                  $product_arr= array();	
                
                  $store_arr_result = DB::select("SELECT s.name,s.id from teams t  INNER JOIN stores s ON s.id = t.store_id where find_in_set('".$request->user_id."',t.distributor_id) and store_id is not null");
			    //dd($store_arr_result);
                     foreach($store_arr_result as $store){
          
                        array_push($coll_array, $store->id);
                        }
        		        if (!empty($request->date_from)) {
                                 $from = date('Y-m-d', strtotime($request->date_from));
                             } else {
                                 $from = date('Y-m-d');
                             }
        					 // date to
        				if (!empty($request->date_to)) {
        					$to = date('Y-m-d', strtotime($request->date_to.'+1 day'));
        					//dd($to);
        				} else {
        					$to = date('Y-m-d', strtotime('+1 day'));
        				}
                         if ( !empty($request->date_from) || !empty($request->date_to) ) {
                                      
                             $resp = OrderProduct::select(DB::raw("(SUM(order_products.qty)) as product_count"),'orders.order_no', 'order_products.product_id',  'products.style_no','sizes.name AS size','colors.name AS color','stores.name','teams.ase_id','teams.asm_id','teams.rsm_id','teams.zsm_id','teams.sm_id','teams.nsm_id','teams.state_id','teams.area_id','stores.pin','orders.created_at')
                                     ->join('products', 'products.id', '=', 'order_products.product_id')->join('orders', 'orders.id', '=', 'order_products.order_id')->join('colors', 'colors.id', '=', 'order_products.color_id')->join('sizes', 'sizes.id', '=', 'order_products.size_id')->join('stores', 'stores.id', '=', 'orders.store_id')->join('teams', 'teams.store_id', '=', 'stores.id')
                                     ->whereIN('orders.store_id',$coll_array)->whereBetween('orders.created_at', [$from, $to])->groupby('order_products.id')->orderby('order_products.id','desc')->get();
                			
                            }else{
                
                                 $resp = OrderProduct::select(DB::raw("(SUM(order_products.qty)) as product_count"),'orders.order_no', 'order_products.product_id',  'products.style_no','sizes.name AS size','colors.name AS color','stores.name','teams.ase_id','teams.asm_id','teams.rsm_id','teams.zsm_id','teams.sm_id','teams.nsm_id','teams.state_id','teams.area_id','stores.pin','orders.created_at')
                                     ->join('products', 'products.id', '=', 'order_products.product_id')->join('orders', 'orders.id', '=', 'order_products.order_id')->join('colors', 'colors.id', '=', 'order_products.color_id')->join('sizes', 'sizes.id', '=', 'order_products.size_id')->join('stores', 'stores.id', '=', 'orders.store_id')->join('teams', 'teams.store_id', '=', 'stores.id')
                                     ->whereIN('orders.store_id',$coll_array)->whereBetween('orders.created_at', [$from, $to])->groupby('order_products.id')->orderby('order_products.id','desc')->get();
                 
                         }
                   
		
                   
                    if (count($resp) > 0) {
                        $delimiter = ",";
                        $filename = "luxcozi-secondary-order-".date('Y-m-d').".csv";
            
                        // Create a file pointer
                        $f = fopen('php://memory', 'w');
            
                        // Set column headers
                        $fields = array('SR', 'ORDER NO','PRODUCT','COLOR', 'SIZE', 'QTY','STORE', 'ASE', 'ASM','RSM','SM','ZSM','NSM', 'STATE', 'AREA', 'PINCODE','DATETIME');
                        fputcsv($f, $fields, $delimiter);
            
                        $count = 1;
            
                        foreach($resp as $row) {
                            $datetime = date('j F, Y h:i A', strtotime($row['created_at']));
                            $product=Product::where('id',$row['product_id'])->first();
                            $store = Store::select('name')->where('id', $row['store_id'])->first();
                            $ase = User::select('name', 'mobile', 'state', 'city', 'pin')->where('id', $row['ase_id'])->first();
            
                            $ase = User::select('name', 'mobile', 'state', 'city', 'pin')->where('id', $row['ase_id'])->first();
                            $asm = User::select('name')->where('id', $row['asm_id'])->first();
                            $rsm = User::select('name')->where('id', $row['rsm_id'])->first();
                            $sm = User::select('name')->where('id', $row['sm_id'])->first();
                            $zsm = User::select('name', 'mobile', 'state', 'city', 'pin')->where('id', $row['zsm_id'])->first();
                            $nsm = User::select('name', 'mobile', 'state', 'city', 'pin')->where('id', $row['nsm_id'])->first();
                              $state = State::select('name')->where('id', $row['state_id'])->first();
                                $area = Area::select('name')->where('id', $row['area_id'])->first();
            
                            // dd($store->store_name, $ase->name, $ase->mobile);
            
                            $lineData = array(
                                $count,
                                $row->order_no,
                                $product->name,
                                $row->color,
                                $row->size,
                                $row->product_count,
                                $row->name ?? '',
                                $ase->name ?? '',
                                $asm->name ?? '',
                                $rsm->name ?? '',
                                $sm->name ?? '',
                                $zsm->name ?? '',
                                $nsm->name ?? '',
                                $state->name ?? '',
                                $area->name ?? '',
                                $row->pin ?? '',
                                $datetime
                            );
            
                            fputcsv($f, $lineData, $delimiter);
            
                            $count++;
                        }
                    
                   
                    
                        // Move back to beginning of file
                        fseek($f, 0);
            
                        // Set headers to download file rather than displayed
                        header('Content-Type: text/csv');
                        header('Content-Disposition: attachment; filename="' . $filename . '";');
            
                        //output all remaining data on a file pointer
                        fpassthru($f);
                    }else{
                return response()->json(['error'=>false, 'resp'=>'No data found']);
            }
                    
        } else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
    }
    
    
    // reward store orders for vp
   /* public function rewardorderdistributorDetail(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required'],
            'date_from' => ['nullable'],
            'date_to' => ['nullable'],
        ]);

        if (!$validator->fails()) {
            $userName = $request->user_id;
                  $data =Store::select('stores.*',DB::raw('SUM(retailer_wallet_txns.amount) as total_amount'))->join('teams', 'stores.id', '=', 'teams.store_id')->join('retailer_wallet_txns', 'stores.id', '=', 'retailer_wallet_txns.user_id')->join('retailer_barcodes', 'retailer_wallet_txns.barcode_id', '=', 'retailer_barcodes.id')->whereRaw("FIND_IN_SET(?, retailer_barcodes.distributor_id)", [$userName])->where('stores.status',1)->groupby('stores.id')->orderby('stores.name')->with('states:id,name','areas:id,name')->get();

           
                $orders = DB::select("SELECT o.id AS id,o.order_no AS order_no,o.user_id AS user_id,s.name AS name,s.contact AS mobile,o.qty AS qty,o.final_amount AS final_amount,o.billing_address AS billing_address,o.billing_landmark AS billing_landmark,o.billing_country AS billing_country,o.billing_state AS billing_state,o.billing_city AS billing_city,o.billing_pin AS billing_pin,o.status AS status,o.asm_approval AS asm_approval,o.rsm_approval AS rsm_approval,o.zsm_approval AS zsm_approval,o.nsm_approval AS nsm_approval,o.distributor_approval AS distributor_approval,o.distributor_note AS distributor_note,o.admin_status AS admin_status,o.created_at AS created_at FROM `retailer_orders` o
				INNER JOIN reward_order_products rp ON o.id = rp.order_id
                INNER JOIN stores s ON s.id = o.user_id
				INNER JOIN teams t ON t.store_id = s.id
                WHERE FIND_IN_SET('".$userName."',t.distributor_id)
                GROUP BY o.id
                ORDER BY o.id DESC");
				if(!empty($data)){
              	foreach($data as $item){
				  $orderProduct = RewardOrderProduct::where('order_id',$item->id)->get();
				  $item->orderProduct =$orderProduct;
			  	}
			  }
			
            //}
			//dd($orders);
            

            return response()->json([
                'error' => false,
                'message' => 'Product orders with quanity',
                'data' => $data,
            ]);

        } else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
    }*/
	public function rewardorderdistributorDetail(Request $request)
{
    $validator = Validator::make($request->all(), [
        'user_id' => ['required'],
        'date_from' => ['nullable', 'date'],
        'date_to' => ['nullable', 'date'],
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
    }

    $userName = $request->user_id;

    // Step 1: Fetch store IDs associated with the distributor
    $storeIds = Store::select('stores.id')
        ->join('teams', 'stores.id', '=', 'teams.store_id')
        ->join('retailer_wallet_txns', 'stores.id', '=', 'retailer_wallet_txns.user_id')
        ->join('retailer_barcodes', 'retailer_wallet_txns.barcode_id', '=', 'retailer_barcodes.id')
        ->whereRaw("FIND_IN_SET(?, retailer_barcodes.distributor_id)", [$userName])
        ->where('stores.status', 1)
        ->groupBy('stores.id')
        ->pluck('stores.id')
        ->toArray();

    if (empty($storeIds)) {
        return response()->json([
            'error' => false,
            'message' => 'No stores found for the given distributor.',
            'data' => [],
        ]);
    }

    // Step 2: Fetch orders associated with the stores
    $orders = DB::table('retailer_orders as o')
        ->selectRaw('
            o.id AS id, 
            o.order_no AS order_no, 
            o.user_id AS user_id, 
            s.name AS name, 
            s.contact AS mobile, 
            o.qty AS qty, 
            o.final_amount AS final_amount, 
            o.billing_address AS billing_address, 
            o.billing_landmark AS billing_landmark, 
            o.billing_country AS billing_country, 
            o.billing_state AS billing_state, 
            o.billing_city AS billing_city, 
            o.billing_pin AS billing_pin, 
            o.status AS status, 
            o.asm_approval AS asm_approval, 
            o.rsm_approval AS rsm_approval, 
            o.zsm_approval AS zsm_approval, 
            o.nsm_approval AS nsm_approval, 
            o.distributor_approval AS distributor_approval, 
            o.distributor_note AS distributor_note, 
            o.admin_status AS admin_status, 
            o.created_at AS created_at
        ')
        ->join('reward_order_products as rp', 'o.id', '=', 'rp.order_id')
        ->join('stores as s', 's.id', '=', 'o.user_id')
        ->whereIn('s.id', $storeIds)
        ->groupBy('o.id')
        ->orderBy('o.id', 'DESC')
        ->get();

    // Step 3: Attach order products to each order
    foreach ($orders as $order) {
        $orderProducts = RewardOrderProduct::where('order_id', $order->id)->get();
        $order->orderProduct = $orderProducts;
    }

    return response()->json([
        'error' => false,
        'message' => 'Product orders with quantity',
        'data' => $orders,
    ]);
}

	
	//retailer wise reward order 
	
	 
	
	
	 // reward store orders for asm
    public function rewardorderdistributorStatus(Request $request) {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required'],
			'distributor_approval'=>['required'],
            'distributor_note' => ['nullable'],
        ]);

        if (!$validator->fails()) {
            
                $order = RetailerOrder::findOrFail($request->order_id);

                $order->distributor_approval = $request['distributor_approval'];
        		$order->distributor_note = $request['distributor_note'];
        		$order->status=1;
				$order->save();
			//dd($orders);
            

            return response()->json([
                'error' => false,
                'message' => 'Status updated',
                'data' => $order,
            ]);

        } else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
    }
    
    
    
    
     //retailer list
    public function retailerFetch(Request $request,$id)
    {
       // $data =Store::select('stores.*',DB::raw('SUM(retailer_wallet_txns.amount) as total_amount'))->join('teams', 'stores.id', '=', 'teams.store_id')->join('retailer_wallet_txns', 'stores.id', '=', 'retailer_wallet_txns.user_id')->join('retailer_barcodes', 'retailer_wallet_txns.barcode_id', '=', 'retailer_barcodes.id')->whereRaw("FIND_IN_SET(?, retailer_barcodes.distributor_id)", [$id])->where('stores.status',1)->groupby('stores.id')->orderby('stores.name')->with('states:id,name','areas:id,name')->get();

           $data=DB::select("SELECT 
                s.*, 
                o.name AS state_name, 
                a.name AS area_name
            FROM 
                `stores` s
            INNER JOIN 
                `teams` t ON s.id = t.store_id
            INNER JOIN 
                `retailer_wallet_txns` rt ON s.id = rt.user_id
            INNER JOIN 
                `retailer_barcodes` rb ON rt.barcode_id = rb.id
            INNER JOIN 
                `states` o ON o.id = s.state_id
            INNER JOIN 
                `areas` a ON a.id = s.area_id
            WHERE 
                FIND_IN_SET($id, t.distributor_id) AND s.status = 1
            ORDER BY 
                s.name ASC");
            //$data =Store::select('stores.*')->join('teams', 'stores.id', '=', 'teams.store_id')->where('teams.distributor_id',$id)->where('stores.status',1)->orderby('name')->with('states:id,name','areas:id,name')->get();

            return response()->json($data);
       
    }
    
    
    public function branding(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'store_id' => ['required', 'integer'],
      //  ], [
        //    'aadhar.*' => 'Please enter minimum one document'
        ]);
         if (!$validator->fails()) {
			
			$store = new Branding;
			$store->distributor_id=$request['distributor_id'];
			$store->store_id = $request['store_id'];
			$store->remarks = $request['remarks'];
			$store->created_at = date('Y-m-d H:i:s');
			$store->updated_at = date('Y-m-d H:i:s');
			$store->save();
			 return response()->json(['error' => false, 'message' => 'Branding Submission Successful','data'=>$store]);
         } else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
	 
    }
    
    
    public function couponData(Request $request,$id)
    {
        $arr=[];
        $store_id=[];
        $data=User::where('id',$id)->orderby('id','desc')->first();
        
        $reward = RetailerWalletTxn::join('stores', 'stores.id', 'retailer_wallet_txns.user_id')
        	->join('teams', 'stores.id', 'teams.store_id')
            ->join('retailer_barcodes', 'retailer_barcodes.id', 'retailer_wallet_txns.barcode_id')
			
			->whereRaw("find_in_set('".$id."',retailer_barcodes.distributor_id)")->latest('retailer_wallet_txns.id')
            ->count();
        
        $store=DB::select("SELECT s.*  
            FROM `stores` s
            INNER JOIN teams t ON s.id = t.store_id
            INNER JOIN retailer_wallet_txns rt ON s.id = rt.user_id
            INNER JOIN retailer_barcodes rb ON rt.barcode_id = rb.id
            WHERE FIND_IN_SET($id, rb.distributor_id) AND s.status = 1
            GROUP BY s.id;");
                
                foreach($store as $stores){
                     array_push($store_id, $stores->id);
                }
        //dd($store_id);
       // $reward=RetailerWalletTxn::whereIN('user_id',$store_id)->where('type',1)->where('barcode_id','!=',NULL)->whereBetween('created_at', ['2024-10-01', now()])->count();
        
        $remainingAmount=(($data->given_coupon)-($reward));
        $arr=[
           'distributor_coupon_data'=>  $data->given_coupon,
           'retailer_scan_count' =>$reward,
           'left_usage' => $remainingAmount
            ];
            //dd($arr);
        return response()->json(['error' => false, 'message' => 'Data fetched Successfully','data'=>$arr]);
    }
    
    
    
     public function retailerOrder(Request $request,$id) {
       
            //$userName = $id;

           
            $data = RetailerOrder::where('user_id',$id)->get();
			if(!empty($data)){
              	foreach($data as $item){
				  $orderProduct = RewardOrderProduct::where('order_id',$item->id)->get();
				  $item->orderProduct =$orderProduct;
			  	}
			  }
			
            //}
			//dd($orders);
            

            return response()->json([
                'error' => false,
                'message' => 'Product orders with quanity',
                'data' => $data,
            ]);

       
    }
    
    
    
    
    
    
    
    
     //form submit
    
    public function formSubmit(Request $request)
    {
         $validator = Validator::make($request->all(), [
             
            'target' => ['required', 'integer', 'min:200'],
      //  ], [
        //    'aadhar.*' => 'Please enter minimum one document'
        ]);
         if (!$validator->fails()) {
			
			$store = new StoreFormSubmit;
			$store->retailer_id = $request['retailer_id'];
			$store->retailer_name = $request['retailer_name'];
			$store->retailer_address	 = $request['retailer_address'] ?? null;
			$store->retailer_pincode	 = $request['retailer_pincode'] ?? null;
		    $store->retailer_contact_person1_name	 = $request['retailer_contact_person1_name'] ?? null;
			$store->retailer_contact_person1_mobile = $request['retailer_contact_person1_mobile'] ?? null;
			$store->retailer_contact_person2_name = $request['retailer_contact_person2_name'] ?? null;
			$store->retailer_contact_person2_mobile = $request['retailer_contact_person2_mobile'];
			$store->dob = $request['dob']?? null;
			$store->gst	 = $request['gst']?? null;
			$store->email	 = $request['email']?? null;
			$store->distributor1	 = $request['distributor1']?? null;
			$store->distributor2	 = $request['distributor2']?? null;
			$store->distributor_id = $request['distributor_id']?? null;
			$store->target	 = $request['target']?? null;
			$store->is_check	 = $request['is_check']?? null;
		    $store->is_submit	 = 1;
		
			$store->created_at = date('Y-m-d H:i:s');
			$store->updated_at = date('Y-m-d H:i:s');
			$store->save();
			 return response()->json(['error' => false, 'message' => 'Form Submission Successful','data'=>$store]);
         } else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
			
		
        
    }
    
    
    public function formSubmitCheck($id)
    {
        $is_transac=0;
        $is_submit=0;
       
       
       
        $formSubmit=StoreFormSubmit::where('distributor_id',$id)->first();
        if(!empty($formSubmit)){
            if($formSubmit->is_submit==1){
                $is_submit=1;
            }else{
                $is_submit=0;
            }
        }
        return response()->json(['error'=>false, 'resp'=>'Data fetched successfully','is_submit'=>$is_submit]);

    }
    
    
    public function videoDownload(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'distributor_id' => ['required', 'integer'],
            'is_download'=> ['required', 'integer'],
      //  ], [
        //    'aadhar.*' => 'Please enter minimum one document'
        ]);
         if (!$validator->fails()) {
			
			$store =  StoreFormSubmit::where('distributor_id',$request->distributor_id)->first();
			$store->is_download = $request->is_download;
			$store->updated_at = date('Y-m-d H:i:s');
			$store->save();
			 return response()->json(['error' => false, 'message' => 'updated successfully','data'=>$store]);
         } else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
	 
    }
    
    
    public function walletBalance(Request $request,$id)
    {
        $data = User::where('id',$id)->first();
        if($data){
            return response()->json(['error'=>false, 'resp'=>'wallet balance data fetched successfully','data'=>$data->wallet]);
        } else {
            return response()->json(['error' => true, 'message' => 'No user found']);
        }
  
    }
    
    
     public function view(Request $request)
    {
  
          $products = DistributorProduct::where('status',1)->orderby('amount','ASC')->take(5)->get();
  
          return response()->json(['error'=>false, 'resp'=>'Product data fetched successfully','data'=>$products]);
  
    }
    
    
}