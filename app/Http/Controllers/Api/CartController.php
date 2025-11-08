<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use DB;
use Illuminate\Support\Facades\Validator;
class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,$userId)
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cart=Cart::destroy($id);
        if ($cart) {
            return response()->json(['error'=>false, 'resp'=>'Product removed from cart']);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }
    }
    
    
     public function delete($id)
    {
        $cart=Cart::where('user_id',$id)->delete();
        if ($cart) {
            return response()->json(['error'=>false, 'resp'=>'Product removed from cart']);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }
    }

    public function PDF_URL(Request $request, $id,$userId)
    {
        return response()->json([
            'error' => false,
            'resp' => 'URL generated',
            'data' => url('/').'/api/cart/pdf/view/'.$id.'/'.$userId,
        ]);
    }

    

    public function PDF_view(Request $request, $id,$userId)
    {
        $cartData =Cart::where('store_id',$id)->where('user_id',$userId)->with('product','stores','color','size')->get()->toArray();
		
        return view('api.cart-pdf', compact('cartData'));
    }
}
