<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Cart;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use DB;
class OrderController extends Controller
{
   
    
    /**
     * This method is for show order details
     * @param  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($orderId): JsonResponse
    {

        $data = Order::select('orders.id as id','orders.order_no as order_no','orders.created_at as created_at','orders.store_id as store_id','orders.status as status')->where('orders.store_id', $orderId)->orderby('id','desc')->with('orderProducts')->get();
         // dd($data);      

        return response()->json(['error'=>false, 'resp'=>'Order data fetched successfully','data'=>$data]);
    }
	
	public function orderDetails($orderId): JsonResponse
    {

        $data = OrderProduct::where('order_id', $orderId)->with('colorDetails')->get();
         // dd($data);      

        return response()->json(['error'=>false, 'resp'=>'Order data fetched successfully','data'=>$data]);
    }

    

    public function placeOrder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'store_id' => ['required'],
            'order_type' => ['required', 'string', 'min:1'],
            'order_lat' => ['required', 'string', 'min:1'],
            'order_lng' => ['required', 'string', 'min:1'],
            'comment' => ['nullable', 'string', 'min:1'],
           
        ]);

        if (!$validator->fails()) {
            $params = $request->except('_token');
            $collectedData = collect($params);
        
			$order_no = generateOrderNumber('secondary', $collectedData['store_id'])[0];
            $sequence_no = generateOrderNumber('secondary', $collectedData['store_id'])[1];
            // 1 order
            $newEntry = new Order;
            $newEntry->sequence_no = $sequence_no;
            $newEntry->order_no = $order_no;
            $newEntry->store_id = $collectedData['store_id'];
            $user=$newEntry->store_id;
			$result = DB::select("select * from stores where id='".$user."'");
            $item=$result[0];
            $name = $item->store_name;
			
            //$newEntry->ip = $this->ip;
            //$newEntry->store_id = $collectedData['store_id'];
            $newEntry->order_type = $collectedData['order_type'] ?? null;
            $newEntry->order_lat = $collectedData['order_lat'] ?? null;
            $newEntry->order_lng = $collectedData['order_lng'] ?? null;

            // if ASE found then
            /*if (Auth::guard('web')->user()->user_type==4) {
                $newEntry->email = $collectedData['email'] ?? null;
                $newEntry->mobile = $collectedData['mobile'] ?? null;
                
            } else {
                $newEntry->email = $user->users->email ?? null;
                $newEntry->mobile = $user->users->mobile ?? null;
            }*/
			
			$newEntry->email = $item->email;
			$newEntry->mobile = $item->contact;

            $newEntry->fname = $collectedData['fname'] ?? null;
            $newEntry->lname = $collectedData['lname'] ?? null;
            $newEntry->billing_country = $collectedData['billing_country'] ?? null;
            $newEntry->billing_address = $collectedData['billing_address'] ?? null;
            $newEntry->billing_landmark = $collectedData['billing_landmark'] ?? null;
            $newEntry->billing_city = $collectedData['billing_city'] ?? null;
            $newEntry->billing_state = $collectedData['billing_state'] ?? null;
            $newEntry->billing_pin = $collectedData['billing_pin'] ?? null;

            // shipping & billing address check
            $shippingSameAsBilling = $collectedData['shippingSameAsBilling'] ?? 0;
            $newEntry->shippingSameAsBilling = $shippingSameAsBilling;
            if ($shippingSameAsBilling == 0) {
                $newEntry->shipping_country = $collectedData['shipping_country'] ?? null;
                $newEntry->shipping_address = $collectedData['shipping_address'] ?? null;
                $newEntry->shipping_landmark = $collectedData['shipping_landmark'] ?? null;
                $newEntry->shipping_city = $collectedData['shipping_city'] ?? null;
                $newEntry->shipping_state = $collectedData['shipping_state'] ?? null;
                $newEntry->shipping_pin = $collectedData['shipping_pin'] ?? null;
            } else {
                $newEntry->shipping_country = $collectedData['billing_country'] ?? null;
                $newEntry->shipping_address = $collectedData['billing_address'] ?? null;
                $newEntry->shipping_landmark = $collectedData['billing_landmark'] ?? null;
                $newEntry->shipping_city = $collectedData['billing_city'] ?? null;
                $newEntry->shipping_state = $collectedData['billing_state'] ?? null;
                $newEntry->shipping_pin = $collectedData['billing_pin'] ?? null;
            }

            $newEntry->shipping_method = $collectedData['shipping_method'] ?? null;

            // fetch cart details
            //if(Auth::guard('web')->user()->user_type==4){
            $cartData = Cart::where('store_id', $newEntry->store_id)->get();
            $subtotal = $totalOrderQty = 0;
            foreach($cartData as $cartValue) {
                $totalOrderQty += $cartValue->qty;
                $subtotal += $cartValue->product->offer_price * $cartValue->qty;
                $store_id = $cartValue->store_id;
                $order_type = $cartValue->order_type;
            }
            $newEntry->amount = $subtotal;
            $newEntry->shipping_charges = $collectedData['shipping_charges'] ?? null;
            $newEntry->tax_amount = $collectedData['tax_amount'] ?? null;
            $newEntry->comment = $collectedData['comment'] ?? null;
            $total = (int) $subtotal +$newEntry->shipping_charges + $newEntry->tax_amount ;
            $newEntry->final_amount = $total;
            $newEntry->save();
            // 2 insert cart data into order products
            $orderProducts = [];
            foreach($cartData as $cartValue) {
                $orderProducts[] = [
                    'order_id' => $newEntry->id,
                    'product_id' => $cartValue->product_id,
                    'product_name' => $cartValue->product_name,
                    'product_image' => $cartValue->product_image,
                    'product_slug' => $cartValue->product_slug,
                    'product_variation_id' => $cartValue->product_variation_id,
                    'product_style_no' => $cartValue->product_style_no,
                    'price' => $cartValue->product->price,
                    'offer_price' => $cartValue->product->offer_price,
                    'color' => $cartValue->color,
                    'size' => $cartValue->size,
                    'qty' => $cartValue->qty,
                ];
            }
            $orderProductsNewEntry = OrderProduct::insert($orderProducts);
              Cart::where('store_id', $newEntry->store_id)->delete();

			

            return response()->json(
                [
                    'error' => false,
                    'resp' => 'Order placed successfully',
                    'data' => $newEntry
                ],
                Response::HTTP_CREATED
            );
        } else {
            return response()->json(['status' => 400, 'message' => $validator->errors()->first()]);
        }

    }
	
	
	public function orderPlacePDF_URL(Request $request, $id)
    {
        return response()->json([
            'error' => false,
            'message' => 'URL generated',
            'data' => url('/').'/retailer/order/place/pdf/view/'.$id,
        ]);
    }

    public function orderPlacePDF_view(Request $request, $orderId)
    {
        $orderProducts = DB::select("SELECT * FROM order_products WHERE order_id = '$orderId'");
        return view('front.order.store-pdf', compact('orderProducts'));
    }

}
