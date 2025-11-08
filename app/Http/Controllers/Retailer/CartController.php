<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Models\RewardCart;
use App\Models\Cart;
use App\Models\RetailerProduct;
use Illuminate\Support\Facades\Validator;
use DB;

class CartController extends Controller
{
    


    /**
     * This method is for show reward cart list
     * @return \Illuminate\Http\JsonResponse
     */

    public function list(Request $request,$id): JsonResponse
    {

        $cart = RewardCart::where('store_id', $id)->get();
		
		$cart_count = DB::select("select ifnull(sum(qty),0) as total_qty,ifnull(sum(final_amount),0) as total_amount from reward_carts where store_id='$id'");
		
		if(count($cart_count)>0){
			$total_quantity = $cart_count[0]->total_qty;
		}else{
			$total_quantity = 0;
		}
		
		if(count($cart_count)>0){
			$total_amount = $cart_count[0]->total_amount;
		}else{
			$total_amount = 0;
		}

        return response()->json(['error'=>false, 'resp'=>'Cart data fetched successfully', 'data'=>$cart,'total_quantity'=>$total_quantity,'total_amount'=>$total_amount]);
    }
	
    /**
     * This method is for show reward cart delete
     * @return \Illuminate\Http\JsonResponse
     */
	public function clear(Request $request, $id)
    {
        $data = RewardCart::findOrFail($id)->delete();

        if ($data) {
            return response()->json(['error' => false, 'resp' => 'Product removed from cart']);
            // return response()->json(null, Response::HTTP_NO_CONTENT);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
            # code...
        }
        
        
    }
	
	/**
     * This method is for reward product add to cart
     * @return \Illuminate\Http\JsonResponse
     */

    public function rewardbulkAddTocart(Request $request)
    {
		
        $validator = Validator::make($request->all(), [
            'store_id' => ['required', 'integer', 'min:1'],
            'device_id' => ['nullable'],
            'product_id' => ['required', 'integer'],
            'qty' => ['required'],
           
        ]);

        if (!$validator->fails()) {
            $params = $request->except('_token');
            $collectedData = $params;
            
            $cartExists = RewardCart::where('product_id', $collectedData['product_id'])->where('store_id', $collectedData['store_id'])->first();
        if ($cartExists) {
                $cartExists->qty = $cartExists->qty + $collectedData['qty'];
			    $cartExists->final_amount = $cartExists->price * $cartExists->qty;
                $cartExists->save();
        } else {
            
            $productDetails=RetailerProduct::where('id',$collectedData['product_id'])->first();
            $newEntry = new RewardCart;
            $newEntry->device_id = $collectedData['device_id'] ?? null;
            $newEntry->store_id = $collectedData['store_id'] ?? null;
            $newEntry->product_id = $collectedData['product_id'];
            $newEntry->product_name = $productDetails->title;
            $newEntry->product_image = $productDetails->image;
            $newEntry->price = $productDetails->amount;
			$newEntry->final_amount = $productDetails->amount * $collectedData['qty'];
            $newEntry->qty = $collectedData['qty'];
            $newEntry->save();
          }
			
        
        return response()->json(['error' => false, 'resp' => 'Product successfully added to cart'], Response::HTTP_CREATED);
        
        } else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
    }
	
	/**
     * This method is for update quantity for reward cart
     * @param  $id
     * @return \Illuminate\Http\JsonResponse
     */
   

    // $type = "incr"/ "decr"
    public function qtyUpdate(Request $request, $cartId,$q)
    {
        $cart = RewardCart::findOrFail($cartId);

        if ($cart) {
			 $cart->qty = $q;
			 $cart->final_amount = $cart->price * $q;
			 $cart->save();
            return response()->json([
                'error' => false,
                'message' => 'Quantity updated'
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Something Happened'
            ]);
        }
    }
	
    /**
     * This method is for show cart details
     * @param  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id): JsonResponse
    {

        $cart = Cart::where('store_id',$id)->get();
		
		$cart_count = DB::select("select sum(qty) as total_qty from carts where store_id='$id'");
		
		if(count($cart_count)>0){
			$total_quantity = $cart_count[0]->total_qty;
		}else{
			$total_quantity = 0;
		}

        return response()->json(['error'=>false, 'resp'=>'Cart data fetched successfully','data'=>$cart,'total_quantity'=>$total_quantity]);
    }

    public function showByUser($id): JsonResponse
    {

        // $cart = $this->CartRepository->listById($userId);
        $cart = Cart::where('store_id', $id)->with('colorDetails', 'sizeDetails')->get();
		
		$cart_count = DB::select("select ifnull(sum(qty),0) as total_qty from carts where store_id='$id'");
		
		if(count($cart_count)>0){
			$total_quantity = $cart_count[0]->total_qty;
		}else{
			$total_quantity = 0;
		}

        return response()->json(['error'=>false, 'resp'=>'Cart data fetched successfully', 'data'=>$cart,'total_quantity'=>$total_quantity]);
    }
	
	public function clearCart($userId){
		DB::select("delete from carts where store_id='$userId'");
		
		return response()->json(['error'=>false, 'resp'=>'Cart data has been updated successfully']);
	}

    
    /**
     * This method is for add cart details
     *
     * @return \Illuminate\Http\JsonResponse
     */
   
   
	 //multiple item added to cart

    public function bulkAddTocart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_id' => ['required', 'integer', 'min:1'],
            'device_id' => ['nullable'],
            'product_id' => ['required', 'integer'],
            'product_name' => ['required', 'string'],
            'product_style_no' => ['required', 'string'],
            //'product_slug' => ['required', 'string'],
            //'product_variation_id' => ['nullable', 'integer'],
            'color' => ['required'],
            'size' => ['nullable'],
            'price' => ['nullable'],
            'qty' => ['nullable'],
            // 'price' => ['required', 'integer'],
            // 'offer_price' => ['required', 'integer'],
            // 'qty' => ['required', 'integer'],
        ]);

        if (!$validator->fails()) {
            // $data = $this->CartRepository->bulkAddCart($request->all());
            $params = $request->except('_token');
            $collectedData = $params;
        // $multiColorSizeQty = explode('|', $collectedData['color']);
        $multiColorSizeQty = explode("|", $collectedData['color']);
        //print_r($multiColorSizeQty);
        $colors = array();
        $sizes = array();
        $qtys = array();
        $multiPrice =array();
        foreach($multiColorSizeQty as $m){
            //echo $m."<br/>";
            $str_arr = explode("*",$m);
            array_push($colors,$str_arr[0]);
            array_push($sizes,$str_arr[1]);
            array_push($qtys,$str_arr[2]);
			array_push($multiPrice,$str_arr[3]);
			//array_push($multiPrice,$str_arr[3]);
        }

        for($i=0;$i<count($colors);$i++){
            $cartExists = Cart::where('product_id', $collectedData['product_id'])->where('store_id', $collectedData['store_id'])->where('color', $colors[$i])->where('size', $sizes[$i])->first();
               
   
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
            
            $newEntry->device_id = $collectedData['device_id'] ?? null;
            $newEntry->store_id = $collectedData['store_id'] ?? null;
            $newEntry->order_type = $orderType;
            $newEntry->product_id = $collectedData['product_id'];
            $newEntry->product_name = $collectedData['product_name'];
            $newEntry->product_style_no = $collectedData['product_style_no'];
            $newEntry->product_image = null;
            $newEntry->product_slug = $collectedData['product_slug'];
            $newEntry->product_variation_id = $collectedData['product_variation_id'] ?? null;
            $newEntry->color = $colors[$i];
            $newEntry->price = 0;
            //$newEntry->ip = $this->ip;

            $newEntry->size = $sizes[$i];
           // $newEntry->offer_price = $multiPrice[$i] ?? '';
			$newEntry->offer_price = $multiPrice[$i] ?? null;
            $newEntry->qty = $qtys[$i];

            $newEntry->save();
          }
			
        }

        //    if ($newEntry) {
                return response()->json(['error' => false, 'resp' => 'Product successfully added to cart'], Response::HTTP_CREATED);
         //   } else {
         //       return response()->json(['error' => true, 'resp' => 'Something happened'], Response::HTTP_CREATED);
         //   }
        } else {
            return response()->json(['status' => 400, 'message' => $validator->errors()->first()]);
        }
    }
	
    

    public function delete(Request $request, $id)
    {
        $data = Cart::findOrFail($id)->delete();

        if ($data) {
            return response()->json(['error' => false, 'resp' => 'Product removed from cart']);
            // return response()->json(null, Response::HTTP_NO_CONTENT);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
            # code...
        }
        
        
    }
    /**
     * This method is for update quantity
     * @param  $id
     * @return \Illuminate\Http\JsonResponse
     */
   

    // $type = "incr"/ "decr"
    public function qtyUpdateLatest(Request $request, $cartId,$q)
    {
        $cart = Cart::findOrFail($cartId);

        if ($cart) {
            /*if ($type == "incr") {
                $cart->qty = $q;
                $cart->save();
            } else {
                $cart->qty = $q;
                $cart->save();
            }*/
			 $cart->qty = $q;
			 $cart->save();
            return response()->json([
                'error' => false,
                'message' => 'Quantity updated'
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Something Happened'
            ]);
        }
    }

    public function cartPlacePDF_URL(Request $request, $id)
    {
        return response()->json([
            'error' => false,
            'message' => 'URL generated',
            'data' => url('/').'/retailer/cart/pdf/view/'.$id,
        ]);
    }

    

    public function cartPreviewPDF_view(Request $request, $id)
    {
        $cartData = DB::select("SELECT * FROM carts WHERE store_id = '$id'");
		
        return view('front.cart.cart-pdf', compact('cartData'));
    }
	
	
	
	

}
