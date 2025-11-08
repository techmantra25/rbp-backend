<?php

namespace App\Http\Controllers\Retailer;

use App\Models\RetailerProduct;
use App\Models\RetailerOrder;
use App\Models\Offer;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RetailerProductController extends Controller
{
    /**
      * This method is to get product details
      *
      */
     public function index(Request $request): JsonResponse
     {
 
         $products = RetailerProduct::where('status',1)->orderby('amount','ASC')->get();
 
         return response()->json(['error'=>false, 'resp'=>'Product data fetched successfully','data'=>$products]);
 
     }
	/**
      * This method is to get top 5 product
      *
      */
    public function view(Request $request)
    {
  
          $products = RetailerProduct::where('status',1)->orderby('amount','ASC')->take(5)->get();
  
          return response()->json(['error'=>false, 'resp'=>'Product data fetched successfully','data'=>$products]);
  
    }
     /**
      * This method is for show product details
      * @param  $id
      *
      */
    public function show(Request $request,$id): JsonResponse
     {
		 
         $products = RetailerProduct::where('id',$id)->first();
		 $productSpec=DB::table('product_specifications')->where('product_id',$id)->get();
		 $data[] = [
                'product' => $products,
                'productSpecification' => $productSpec,
            ];
         return response()->json(['error'=>false, 'resp'=>'Product data fetched successfully','data'=>$products,'productSpecification'=>$productSpec]);
     }
     
	/**
      * This method is for show brochure details
      * 
      *
      */
    public function brochureindex(Request $request)
	{
        $brochure = Offer::where('is_current',1)->get();
        return response()->json(['error'=>false, 'resp'=>'Product data fetched successfully','data'=>$brochure]);
    }
	
	   /**
      * This method is to get 5 order details
      *
      */
    public function order(Request $request,$userId)
    {
		//dd($request->all());
        $resp = $orderDetails = [];
        $order = RetailerOrder::where('user_id',$userId)->orderby('created_at','desc')->get();
        foreach ($order as $data) {
            $orderDetails = RetailerOrder::where('created_at', $data->created_at)
            ->orderby('id', 'desc')
            ->get();
            $resp[] = [
                'date' => date('Y-m-d H:i:s', strtotime($data->created_at)),
                'order_details' => $orderDetails,
            ];
        }
        return response()->json([
                'error' => false,
                'message' => 'Order history with quanity',
                'data' => $resp,
            ]);
    }
}
