<?php

namespace App\Http\Controllers\Retailer;

use App\Models\RetailerOrder;
use App\Models\RetailerUser;
use App\Models\RetailerProduct;
use App\Models\RetailerWalletTxn;
use App\Models\Store;
use App\Models\RewardCart;
use App\Models\RewardOrder;
use App\Models\MailActivity;
use App\Models\RewardOrderProduct;
use App\Models\RetailerUserTxnHistory;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\File;
class RetailerOrderController extends Controller
{
    /**
      * This method is to get 5 order details
      *
      */
    public function index(Request $request,$userId)
    {
        $order = RetailerOrder::where('user_id',$userId)->orderby('id','desc')->take(5)->get();
        
        return response()->json(['error'=>false, 'resp'=>'Order history fetched successfully','data'=>$order]);
    }
	
	 /**
      * This method is to get  order details
      *
      */
    public function demoorder(Request $request,$id)
    {
		$resp=[];
        $order = RetailerOrder::where('id',$id)->with('product')->first();
        $product = RetailerProduct::where('id',$order->product_id)->first();
		
        return response()->json(['error'=>false, 'resp'=>'Order history fetched successfully','data'=>$order]);
    }
	
	public function order(Request $request,$id)
    {
		$resp=[];
        $order = RetailerOrder::where('id',$id)->with('orderProduct','user')->first();
        
        $product = RetailerProduct::where('id',$order->product_id)->first();
		
        return response()->json(['error'=>false, 'resp'=>'Order history fetched successfully','data'=>$order]);
    }

    /**
      * This method is to get all order 
      *
      */
    public function demoview(Request $request)
    {
         $validator = Validator::make($request->all(), [
        'user_id' => ['required'],
        'pageNo' => ['required', 'integer', 'min:1'],
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
    }

    $page = $request->pageNo ?? 1;
    $limit = 20;
    $offset = ($page - 1) * $limit;
    $userId = $request->user_id;

    // Check for user by ID or unique_code
    $user = Store::where('id', $userId)
               
                ->first();

    if (!$user) {
        return response()->json(['error' => false, 'resp' => 'User is invalid']);
    }

    // Use query builder for security instead of raw DB::select
    $resp = DB::table('retailer_user_txn_histories')
        ->where('user_id', $userId)
        ->orWhere('user_id', $user->unique_code)
        ->orderByDesc('id')
        ->offset($offset)
        ->limit($limit)
        ->get();

    $total = DB::table('retailer_user_txn_histories')
        ->where('user_id', $userId)
        ->orWhere('user_id', $user->unique_code)
        ->count();

    $count = (int) ceil($total / $limit);

    return response()->json([
        'error' => false,
        'message' => 'Transaction history fetched successfully',
        'data' => $resp,
        'count' => $count,
    ]);
        
    }
	
	public function view(Request $request)
{
    $validator = Validator::make($request->all(), [
        'user_id' => ['required'],
        'pageNo' => ['required', 'integer', 'min:1'],
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
    }

    $page = $request->pageNo ?? 1;
    $limit = 20;
    $offset = ($page - 1) * $limit;
    $userId = $request->user_id;

    // Check for user by ID or unique_code
    $user = Store::where('id', $userId)
               
                ->first();

    if (!$user) {
        return response()->json(['error' => false, 'resp' => 'User is invalid']);
    }

    // Use query builder for security instead of raw DB::select
    $resp = DB::table('retailer_user_txn_histories')
        ->where('user_id', $userId)
        ->orWhere('user_id', $user->unique_code)
        ->orderByDesc('id')
        ->offset($offset)
        ->limit($limit)
        ->get();

    $total = DB::table('retailer_user_txn_histories')
        ->where('user_id', $userId)
        ->orWhere('user_id', $user->unique_code)
        ->count();

    $count = (int) ceil($total / $limit);

    return response()->json([
        'error' => false,
        'message' => 'Transaction history fetched successfully',
        'data' => $resp,
        'count' => $count,
    ]);
}



      /**
      * This method is to get reward 
      *
      */
      public function demoreward(Request $request)
      {
        //dd($userId);
        $validator = Validator::make($request->all(), [
            'user_id' =>['required'],
			'pageNo' => ['required'],
            
        ]);
        if (!$validator->fails()) {
          $resp = [];
          $pageNo =$request->pageNo;
          $userId =$request->user_id;
          $userExist=RetailerUser::where('id','=',$userId)->first();
            if(!$userExist){
                return response()->json(['error'=>false, 'resp'=>'User is invalid']);
            }else{
                if(!$pageNo){
                    $page=1;
                }else{
                    $page=$pageNo;
				}
                    $limit=20;
                    $offset=($page-1)*$limit;
                   // $resp = RetailerWalletTxn::where('user_id',$userId)->orderby('id','desc')->offset($offset)->take($limit)->get();
                    $resp= DB::select("SELECT * FROM retailer_user_txn_histories WHERE user_id = ".$userId." and type= 'barcode scan' ORDER BY id DESC LIMIT ".$limit." OFFSET ".$offset."");
                $notificationCount=DB::table('retailer_user_txn_histories')->where('user_id','=',$userId)->where('type','=','barcode scan')->count();
                $count= (int) ceil($notificationCount / $limit);
            }
            return response()->json([
                'error' => false,
                'message' => 'Reward history with quanity',
                'data' => $resp,
				'count'=>$count,
            ]);

            } else {
                return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
            }
        
        }
	
	   public function reward(Request $request)
      {
        //dd($userId);
        $validator = Validator::make($request->all(), [
            'user_id' =>['required'],
			'pageNo' => ['required'],
            
        ]);
        if (!$validator->fails()) {
          $resp = [];
          $pageNo =$request->pageNo;
          $userId =$request->user_id;
          $userExist=Store::where('id','=',$userId)->first();
			//dd($userExist);
            if(!$userExist){
                return response()->json(['error'=>false, 'resp'=>'User is invalid']);
            }else{
                if(!$pageNo){
                    $page=1;
                }else{
                    $page=$pageNo;
				}
                    $limit=20;
                    $offset=($page-1)*$limit;
                   // $resp = RetailerWalletTxn::where('user_id',$userId)->orderby('id','desc')->offset($offset)->take($limit)->get();
                    //$resp= DB::select("SELECT * FROM retailer_user_txn_histories WHERE user_id = ".$userId." and type= 'Qrcode scan' ORDER BY id DESC LIMIT ".$limit." OFFSET ".$offset."");
             $resp=   DB::table('retailer_user_txn_histories')
        
        ->where('user_id', $userExist->unique_code)
        ->orWhere('user_id', $userExist->id)
        ->where('type','Earn')
        ->orderByDesc('id')
        ->offset($offset)
        ->limit($limit)
        ->get();
            
                $notificationCount=DB::table('retailer_user_txn_histories')->where('user_id','=',$userId)->orWhere('user_id', $userExist->unique_code)->orWhere('user_id', $userExist->id)->where('type','=','Earn')->count();
                $count= (int) ceil($notificationCount / $limit);
            }
            return response()->json([
                'error' => false,
                'message' => 'Reward history with quanity',
                'data' => $resp,
				'count'=>$count,
            ]);

            } else {
                return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
            }
        
        }
        
        
         public function redemptionHistory(Request $request)
      {
        //dd($userId);
        $validator = Validator::make($request->all(), [
            'user_id' =>['required'],
			'pageNo' => ['required'],
            
        ]);
        if (!$validator->fails()) {
          $resp = [];
          $pageNo =$request->pageNo;
          $userId =$request->user_id;
          $userExist=Store::where('id','=',$userId)->first();
			//dd($userExist);
            if(!$userExist){
                return response()->json(['error'=>false, 'resp'=>'User is invalid']);
            }else{
                if(!$pageNo){
                    $page=1;
                }else{
                    $page=$pageNo;
				}
                    $limit=20;
                    $offset=($page-1)*$limit;
                   // $resp = RetailerWalletTxn::where('user_id',$userId)->orderby('id','desc')->offset($offset)->take($limit)->get();
                    //$resp= DB::select("SELECT * FROM retailer_user_txn_histories WHERE user_id = ".$userId." and type= 'Qrcode scan' ORDER BY id DESC LIMIT ".$limit." OFFSET ".$offset."");
                $resp = DB::table('retailer_user_txn_histories')
                ->where(function($q) use ($userExist) {
                    $q->where('user_id', $userExist->unique_code)
                      ->orWhere('user_id', $userExist->id);
                })
                ->where('type', 'points redeem')
                ->orderByDesc('id')
                ->offset($offset)
                ->limit($limit)
                ->get();
            
                $notificationCount = DB::table('retailer_user_txn_histories')
                ->where(function($q) use ($userId, $userExist) {
                $q->where('user_id', $userId)
                  ->orWhere('user_id', $userExist->unique_code)
                  ->orWhere('user_id', $userExist->id);
            })
            ->where('type', 'points redeem')
            ->count();
                $count= (int) ceil($notificationCount / $limit);
            }
            return response()->json([
                'error' => false,
                'message' => 'Reedem history with quanity',
                'data' => $resp,
				'count'=>$count,
            ]);

            } else {
                return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
            }
        
        }

        public function demoplaceOrder(Request $request): JsonResponse
        {
          $validator = Validator::make($request->all(), [
            'user_id' => ['required'],
            'shop_name' => ['required'],
            'email' => ['nullable'],
            'mobile' => ['required', 'integer','digits:10'],
            'shipping_country' => ['nullable', 'string'],
            'shipping_address' => ['nullable', 'string'],
            'shipping_landmark' => ['nullable', 'string'],
            'shipping_city' => ['nullable', 'string'],
            'shipping_state' => ['nullable', 'string'],
            'shipping_pin' => ['nullable', 'integer','digits:6'],
          ]);
  
          if (!$validator->fails()) {
                $userExist=RetailerUser::where('id',$request['user_id'])->first();
            if(!$userExist){
                return response()->json(['error'=>false, 'resp'=>'User is invalid']);
            }else{
                $userBalance=RetailerUser::where('id',$request['user_id'])->first();
                if ((int) ($request['amount'] * $request['qty']) > (int) $userExist->wallet ) {
                    return response()->json(['error'=>false, 'resp'=>'Wallet balance is low','data'=>$userExist->wallet]);
                }else{
                    //$order_no = "ONN".date('y').'/'.mt_rand();
                    // 1 order sequence
                    $OrderChk = RetailerOrder::select('order_sequence_int')->latest('id')->first();
                    if($OrderChk->order_sequence_int == 0) $orderSeq = 1;
                    else $orderSeq = (int) $OrderChk->order_sequence_int + 1;

                    $ordNo = sprintf("%'.05d", $orderSeq);
                    $order_no = "ONNREWARD".date('y').'/'.$ordNo;
                    $newEntry = new RetailerOrder;
                    $newEntry->order_sequence_int = $orderSeq;
                    $newEntry->order_no = $order_no;
                    $newEntry->user_id = $request['user_id'];
                    $newEntry->product_id = $request['product_id'];
				    $product=DB::select("select * from retailer_products where id='".$request['product_id']."'");
					$productName= $product[0]->title;
                    //$newEntry->product_name = $request['product_name'] ?? null;
                    $user=$newEntry->user_id;
                    $result = DB::select("select * from retailer_users where id='".$user."'");
                    $item=$result[0];
                    $name = $item->shop_name ?? null;
                    $newEntry->email = $item->email ?? null ;
                    $newEntry->mobile = $item->mobile  ?? null;
                    $newEntry->billing_address = $request['billing_address'] ?? null;
                    $newEntry->billing_landmark = $request['billing_landmark'] ?? null;
                    $newEntry->billing_city = $request['billing_city'] ?? null;
                    $newEntry->billing_state = $request['billing_state'] ?? null;
                    $newEntry->billing_pin = $request['billing_pin'] ?? null;

                    // shipping & billing address check
                    
                    $subtotal = $totalOrderQty = 0;
                    $newEntry->qty = $request['qty'];
                    $subtotal += $request['amount'] * $request['qty'];
                    $newEntry->amount =$request['amount'];
                    $newEntry->final_amount = $subtotal;
                    $newEntry->save();
                    $user=RetailerUser::findOrFail($userBalance->id);
                    $user->wallet -= $newEntry->final_amount;
                    $user->save();
                    $userAmount=RetailerWalletTxn::where('user_id',$request['user_id'])->orderby('id','desc')->first();
                    $walletTxn=new RetailerWalletTxn();
                    $walletTxn->user_id = $newEntry->user_id;
                    $walletTxn->barcode_id = NULL;
                    $walletTxn->barcode = NULL;
                    $walletTxn->amount = $newEntry->final_amount;
                    $walletTxn->type = 2 ?? '';
                    if(!$userAmount)
                        $walletTxn->final_amount -=  $newEntry->final_amount ?? '';
                    else
                        $walletTxn->final_amount =  $userAmount->final_amount - $newEntry->final_amount ?? '';
                    $walletTxn->created_at = date('Y-m-d H:i:s');
                    $walletTxn->updated_at = date('Y-m-d H:i:s');
                    $walletTxn->save();
                    $userwalletTxn=new RetailerUserTxnHistory;
                    $userwalletTxn->user_id = $request['user_id'];
                    $userwalletTxn->order_id = $newEntry->id;
                    $userwalletTxn->amount = $newEntry->final_amount;
                    $userwalletTxn->type = 'points redeem' ?? '';
                    $userwalletTxn->title = 'Redeem points';
                    $userwalletTxn->description = 'You Purchase '.$productName;
                    $userwalletTxn->status = 'decrement';
                    $userwalletTxn->created_at = date('Y-m-d H:i:s');
                    $userwalletTxn->updated_at = date('Y-m-d H:i:s');
                    $userwalletTxn->save();
                
                }
            }
            return response()->json([
                'error' => false,
                'message' => 'Order placed successfully',
                'data' => $newEntry,
            ]);
            } else {
              return response()->json(['status' => 400, 'message' => $validator->errors()->first()]);
            }
  
        }
	
	
	public function placeOrder(Request $request): JsonResponse
        {
          $validator = Validator::make($request->all(), [
            'user_id' => ['required'],
            'shop_name' => ['nullable'],
            'email' => ['nullable'],
            'mobile' => ['required', 'integer','digits:10'],
            'shipping_country' => ['nullable', 'string'],
            'shipping_address' => ['nullable', 'string'],
            'shipping_landmark' => ['nullable', 'string'],
            'shipping_city' => ['nullable', 'string'],
            'shipping_state' => ['nullable', 'string'],
            'shipping_pin' => ['nullable', 'integer','digits:6'],
          ]);
  
          if (!$validator->fails()) {
                $userExist=Store::where('id',$request['user_id'])->first();
            if(!$userExist){
                return response()->json(['error'=>false, 'resp'=>'User is invalid']);
            }else{
                $userBalance=Store::where('id',$request['user_id'])->first();
                if ((int) ($request['amount'] * $request['qty']) > (int) $userExist->wallet ) {
                    return response()->json(['error'=>false, 'resp'=>'Wallet balance is low','data'=>$userExist->wallet]);
                }else{
                    //$order_no = "ONN".date('y').'/'.mt_rand();
                    // 1 order sequence
                    $OrderChk = RetailerOrder::select('order_sequence_int')->latest('id')->first();
					//if (!empty($OrderChk)) {
                    	if (empty($OrderChk->order_sequence_int))  $orderSeq = 1;
						else $orderSeq = (int) $OrderChk->order_sequence_int + 1;
					//}

                    $ordNo = sprintf("%'.05d", $orderSeq);
                    $order_no = "ONNREWARD".date('y').'/'.$ordNo;
                    $newEntry = new RetailerOrder;
                    $newEntry->order_sequence_int = $orderSeq;
                    $newEntry->order_no = $order_no;
                    $newEntry->user_id = $request['user_id'];
                    $newEntry->product_id = $request['product_id'];
				    $product=DB::select("select * from retailer_products where id='".$request['product_id']."'");
					$productName= $product[0]->title;
                    //$newEntry->product_name = $request['product_name'] ?? null;
                    $user=$newEntry->user_id;
                    $result = DB::select("select * from stores where id='".$user."'");
                    $item=$result[0];
                    $name = $item->store_name ?? null;
                    $newEntry->email = $item->email ?? null ;
                    $newEntry->mobile = $item->contact  ?? null;
                    $newEntry->billing_address = $item->address ?? null;
                    $newEntry->billing_city = $item->area ?? null;
                    $newEntry->billing_state = $item->state ?? null;
                    $newEntry->billing_pin = $item->pin ?? null;

                    // shipping & billing address check
                    
                    $subtotal = $totalOrderQty = 0;
                    $newEntry->qty = $request['qty'];
                    $subtotal += $request['amount'] * $request['qty'];
                    $newEntry->amount =$request['amount'];
                    $newEntry->final_amount = $subtotal;
                    $newEntry->save();
                    $user=Store::findOrFail($userBalance->id);
                    $user->wallet -= $newEntry->final_amount;
                    $user->save();
                    $userAmount=RetailerWalletTxn::where('user_id',$request['user_id'])->orderby('id','desc')->first();
                    $walletTxn=new RetailerWalletTxn();
                    $walletTxn->user_id = $newEntry->user_id;
                    $walletTxn->amount = $newEntry->final_amount;
                    $walletTxn->type = 2 ?? '';
                    if(!$userAmount)
                        $walletTxn->final_amount -=  $newEntry->final_amount ?? '';
                    else
                        $walletTxn->final_amount =  $userAmount->final_amount - $newEntry->final_amount ?? '';
                    $walletTxn->created_at = date('Y-m-d H:i:s');
                    $walletTxn->updated_at = date('Y-m-d H:i:s');
                    $walletTxn->save();
                    $userwalletTxn=new RetailerUserTxnHistory;
                    $userwalletTxn->user_id = $request['user_id'];
                    $userwalletTxn->order_id = $newEntry->id;
                    $userwalletTxn->amount = $newEntry->final_amount;
                    $userwalletTxn->type = 'points redeem' ?? '';
                    $userwalletTxn->title = 'Redeem points';
                    $userwalletTxn->description = 'You Purchase '.$productName;
                    $userwalletTxn->status = 'decrement';
                    $userwalletTxn->created_at = date('Y-m-d H:i:s');
                    $userwalletTxn->updated_at = date('Y-m-d H:i:s');
                    $userwalletTxn->save();
                
                }
            }
            return response()->json([
                'error' => false,
                'message' => 'Order placed successfully',
                'data' => $newEntry,
            ]);
            } else {
              return response()->json(['status' => 400, 'message' => $validator->errors()->first()]);
            }
  
        }
	
public function convertHtmlToPdf($url, $pdfPath)
{
    try {
        $response = Http::get($url);
        if (!$response->successful()) {
            \Log::error("Failed to fetch URL: " . $url);
            return false;
        }

        $html = $response->body();
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); // Enable external assets

        $dompdf = new Dompdf($options);
        
        

        
        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait'); // Corrected page size
        $dompdf->render();

        file_put_contents($pdfPath, $dompdf->output());

        if (!file_exists($pdfPath)) {
            \Log::error("PDF generation failed: File not saved.");
            return false;
        }

        return true;
    } catch (\Exception $e) {
        \Log::error('PDF generation error: ' . $e->getMessage());
        return false;
    }
}

    
    
private function SendMail($emailData, $attachmentPath = null, $ccEmails = [])
{
    try {
        Mail::send($emailData['blade_file'], ['emailData' => $emailData], function ($message) use ($emailData, $attachmentPath, $ccEmails) {
            $message->to($emailData['email'])
                    ->subject($emailData['subject']);

            // Add CC emails if provided (array support)
            if (!empty($ccEmails)) {
                $message->cc($ccEmails);
            }

            // Attach the PDF if it exists
            if ($attachmentPath && file_exists($attachmentPath)) {
                $message->attach($attachmentPath, [
                    'as' => 'Order-NOC-'.$emailData['retailers'], // Custom nameNOC for Order '.$newOrder->order_no.') of- ' . $userExist->name
                    'mime' => 'application/pdf'
                ]);
            }
        });

        \Log::info("Email sent successfully to: " . implode(', ', $emailData['email']) . " | CC: " . implode(', ', $ccEmails));
        return true;
    } catch (\Exception $e) {
         dd('Exception:', $e->getMessage());
        \Log::error('Email sending failed: ' . $e->getMessage());
        return false;
    }
}


		public function rewardplaceOrder(Request $request): JsonResponse
        {
         /* $validator = Validator::make($request->all(), [
            'user_id' => ['required'],
          ]);
  
          if (!$validator->fails()) {
                $userExist=Store::where('id',$request['user_id'])->where('status',1)->first();
            if(!$userExist){
                return response()->json(['error'=>true, 'message'=>'User is invalid']);
            }else{
                $userBalance=Store::where('id',$request['user_id'])->where('status',1)->first();
				//$cartData = RewardCart::where('store_id', $request['user_id'])->get();
				$cart_count = DB::select("select sum(final_amount) as total_amount from reward_carts where store_id='$request->user_id'");
				$total_amount = $cart_count[0]->total_amount;
				//foreach($cartData as $cartValue) {
					if ((int) $total_amount > (int) $userExist->wallet ) {
						return response()->json(['error'=>true, 'message'=>'Wallet balance is low','data'=>(int) ($total_amount)-($userExist->wallet)]);
					
					}else{
					    
					    
						
						//$order_no = "ONN".date('y').'/'.mt_rand();
						// 1 order sequence
						$OrderChk = RetailerOrder::select('order_sequence_int')->latest('id')->first();
						if(empty($OrderChk->order_sequence_int )) $orderSeq = 1;
						else $orderSeq = (int) $OrderChk->order_sequence_int + 1;

						$ordNo = sprintf("%'.05d", $orderSeq);
						$order_no = "COZIREWARD".date('y').'/'.$ordNo;
						$newEntry = new RetailerOrder;
						$newEntry->order_sequence_int = $orderSeq;
						$newEntry->order_no = $order_no;
						$newEntry->user_id = $request['user_id'];
						//$newEntry->product_id = $cartValue->product_id;
						//$product=DB::select("select * from retailer_products where id='".$cartValue->product_id."'");
						//$productName= $product[0]->title;
						$user=$newEntry->user_id;
						$result = DB::select("select * from stores where id='".$user."'");
						$item=$result[0];
						$newEntry->shop_name = $item->name ?? null;
						//$newEntry->product_name= $cartValue->product_name;
						//$newEntry->product_image= $cartValue->product_image;
						$newEntry->email = $item->email ?? null ;
						$newEntry->mobile = $item->contact  ?? null;
						$newEntry->billing_address = $item->address ?? null;
						$newEntry->billing_city = $item->area ?? null;
						$newEntry->billing_state = $item->state ?? null;
						$newEntry->billing_pin = $item->pin ?? null;

						// shipping & billing address check
						$cartData = RewardCart::where('store_id', $request['user_id'])->get();
						$subtotal = $totalOrderQty = 0;
						foreach($cartData as $cartValue) {
							$totalOrderQty += $cartValue->qty;
							
							$subtotal += $cartValue->price * $cartValue->qty;
						}
							$newEntry->amount =$subtotal;
						    $newEntry->qty = $totalOrderQty;
							$newEntry->final_amount = $subtotal;
							$newEntry->save();
					 // }
					$orderProducts = [];
					 foreach($cartData as $cartValue) {
						
						
							$orderProducts[] = [
								'order_id' => $newEntry->id,
								'product_id' => $cartValue->product_id,
								'product_name' => $cartValue->product_name,
								'product_image' => $cartValue->product_image,
								'product_slug' => $cartValue->product_slug,
								'price' => $cartValue->price,
								'offer_price' => $cartValue->price,
								'qty' => $cartValue->qty,
							];
					   }
                         $orderProductsNewEntry = RewardOrderProduct::insert($orderProducts);
						if($newEntry){
							RewardCart::where('store_id', $request['user_id'])->delete();
						}
						//$product=DB::select("select * from retailer_products where id='".$cartValue->product_id."'");
						//$productName= $product[0]->title;
						$user=Store::findOrFail($userBalance->id);
						$user->wallet -= $newEntry->final_amount;
						$user->save();
						$userAmount=RetailerWalletTxn::where('user_id',$request['user_id'])->orderby('id','desc')->first();
						$walletTxn=new RetailerWalletTxn();
						$walletTxn->user_id = $newEntry->user_id;
						$walletTxn->amount = $newEntry->final_amount;
						$walletTxn->type = 2 ?? '';
						if(!$userAmount)
							$walletTxn->final_amount -=  $newEntry->final_amount ?? '';
						else
							$walletTxn->final_amount =  $userAmount->final_amount - $newEntry->final_amount ?? '';
						$walletTxn->created_at = date('Y-m-d H:i:s');
						$walletTxn->updated_at = date('Y-m-d H:i:s');
						$walletTxn->save();
						$userwalletTxn=new RetailerUserTxnHistory;
						$userwalletTxn->user_id = $request['user_id'];
						$userwalletTxn->order_id = $newEntry->id;
						$userwalletTxn->amount = $newEntry->final_amount;
						$userwalletTxn->type = 'points redeem' ?? '';
						$userwalletTxn->title = 'Redeem points';
						$userwalletTxn->description = 'You Purchased gift';
						$userwalletTxn->status = 'decrement';
						$userwalletTxn->created_at = date('Y-m-d H:i:s');
						$userwalletTxn->updated_at = date('Y-m-d H:i:s');
						$userwalletTxn->save();*/
						
						   $validator = Validator::make($request->all(), [
                                'user_id' => ['required'],
                            ]);
                        
                            if ($validator->fails()) {
                                return response()->json(['error' => true, 'message' => 'Validation failed', 'errors' => $validator->errors()]);
                            }
                        
                            // Check if user exists and is active
                            $userExist = Store::where('id', $request['user_id'])->where('status', 1)->first();
                        
                            if (!$userExist) {
                                return response()->json(['error' => true, 'message' => 'User is invalid']);
                            }
                        
                            // Fetch wallet balance and cart total
                            $userBalance = $userExist->wallet;
                            $cartTotal = RewardCart::where('store_id', $request['user_id'])->sum('final_amount');
                        
                            if ((int)$cartTotal > (int)$userBalance) {
                                return response()->json([
                                    'error' => true,
                                    'message' => 'Wallet balance is low',
                                    'data' => (int)($cartTotal - $userBalance)
                                ]);
                            }
                           // Start transaction
                        DB::beginTransaction();
                        try {
                            // Generate Order Number
                            $lastOrder = RetailerOrder::latest('id')->first();
                            $orderSeq = $lastOrder ? (int)$lastOrder->order_sequence_int + 1 : 1;
                            $orderNo = sprintf("SDREWARD%'.05d", $orderSeq);
                        
                            // Create new order entry
                            $newOrder = new RetailerOrder();
                            $newOrder->order_sequence_int = $orderSeq;
                            $newOrder->order_no = $orderNo;
                            $newOrder->user_id = $request['user_id'];
                            $newOrder->shop_name = $userExist->name ?? null;
                            $newOrder->email = $userExist->email ?? null;
                            $newOrder->mobile = $userExist->contact ?? null;
                            $newOrder->billing_address = $userExist->address ?? null;
                            $newOrder->billing_city = $userExist->area ?? null;
                            $newOrder->billing_state = $userExist->state ?? null;
                            $newOrder->billing_pin = $userExist->pin ?? null;
                        
                            // Get cart items
                            $cartItems = RewardCart::where('store_id', $request['user_id'])->get();
                            $subtotal = $cartItems->map(function ($cart) {
                                return $cart->price * $cart->qty;
                            })->sum();
                            $totalQty = $cartItems->sum('qty');
                        
                            $newOrder->amount = $subtotal;
                            $newOrder->qty = $totalQty;
                            $newOrder->final_amount = $subtotal;
                            $newOrder->save();
                        
                            // Insert Order Products
                            $orderProducts = $cartItems->map(function ($cart) use ($newOrder) {
                                return [
                                    'order_id' => $newOrder->id,
                                    'product_id' => $cart->product_id,
                                    'product_name' => $cart->product_name,
                                    'product_image' => $cart->product_image,
                                    'product_slug' => $cart->product_slug,
                                    'price' => $cart->price,
                                    'offer_price' => $cart->price,
                                    'qty' => $cart->qty,
                                ];
                            })->toArray();
                        
                            RewardOrderProduct::insert($orderProducts);
                        
                            // Clear Cart
                            RewardCart::where('store_id', $request['user_id'])->delete();
                        
                            // Deduct wallet balance
                            $userExist->decrement('wallet', $subtotal);
                        
                            // Wallet Transaction Entry
                            $lastWalletTxn = RetailerWalletTxn::where('user_id', $request['user_id'])->orWhere('user_id', $userExist->unique_code)->latest('id')->first();
                            $finalWalletBalance = $lastWalletTxn ? $lastWalletTxn->final_amount - $subtotal : -$subtotal;
                           
                           // Check for recent WalletTxn
                                $recentWalletTxn = RetailerWalletTxn::where('user_id', $request['user_id'])
                                ->where('amount', $subtotal)
                                ->where('type', 2)
                                ->whereBetween('created_at', [now()->subSeconds(10), now()])
                                ->first();
                        
                                if (!$recentWalletTxn) {
                                    RetailerWalletTxn::create([
                                        'user_id' => $request['user_id'],
                                        'amount' => $subtotal,
                                        'type' => 2,
                                        'final_amount' => $finalWalletBalance,
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]);
                                }
                        
                            // User Transaction History
                            // RetailerUserTxnHistory::create([
                            //     'user_id' => $request['user_id'],
                            //     'order_id' => $newOrder->id,
                            //     'amount' => $subtotal,
                            //     'type' => 'points redeem',
                            //     'title' => 'Redeem points',
                            //     'description' => 'You Purchased gift',
                            //     'status' => 'decrement',
                            //     'created_at' => now(),
                            //     'updated_at' => now(),
                            // ]);

                                $recentUserTxn = RetailerUserTxnHistory::where('user_id', $request['user_id'])
                                ->where('amount', $subtotal)
                                ->where('type', 'points redeem')
                                ->where('order_id', $newOrder->id)
                                ->whereBetween('created_at', [now()->subSeconds(10), now()])
                                ->first();
                            
                                 if (!$recentUserTxn) {
                                    RetailerUserTxnHistory::create([
                                        'user_id' => $request['user_id'],
                                        'order_id' => $newOrder->id,
                                        'amount' => $subtotal,
                                        'type' => 'points redeem',
                                        'title' => 'Redeem points',
                                        'description' => 'You Purchased gift',
                                        'status' => 'decrement',
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]);
                                 }
						// notification: sender, receiver, type, route, title
						// notification to ASE
						sendNotification('admin', '', 'reward-order-place', 'front.user.order', $totalQty.' New order placed',$totalQty.' new order placed  '.$userExist->name);
					//	if($request['user_id']==28583){
				//         if (is_float($newOrder->amount) && $newOrder->amount != 0) {
    //                 				  $distributorData=DB::table('teams')->where('store_id',$request['user_id'])->first();
                                     
    //                                   $distributorName=DB::table('users')->where('id',$distributorData->distributor_id)->first();
    //                                   $transactionH = DB::table('retailer_wallet_txns')->where('user_id', $request['user_id'])->get();
                                         
    //                                         $qr = [];
                                            
    //                                         foreach ($transactionH as $rec) {
    //                                             $qr[] = $rec->barcode;
    //                                         }
                                           
    //                                         // Fetch distributor IDs and count their occurrences
    //                                         // $distributorIdCounts = DB::table('retailer_barcodes')
    //                                         //     ->whereIn('code', $qr)
    //                                         //     ->select('distributor_id', DB::raw('COUNT(*) as count'))
    //                                         //     ->groupBy('distributor_id')
    //                                         //     ->orderByDesc('count')
    //                                         //     ->first();
                                            
    //                                         // if ($distributorIdCounts) {
    //                                         //     $maxDistributorId = $distributorIdCounts->distributor_id;
    //                                         //     $distributorDetails= DB::table('users')->where('id', $maxDistributorId)->first();
    //                                         //     $maxCount = $distributorIdCounts->count;
    //                                         // }else{
    //                                         //       $distributorIds = explode(',', $distributorData->distributor_id);
                                                      
    //                                         //           $teamDistributorIds = DB::table('teams')->where('area_id', $distributorData->area_id)->where('state_id', $distributorData->state_id)->where('ase_id',$distributorData->ase_id)->where('store_id',NULL)->pluck('distributor_id')->toArray();
                                                        
    //                                         //             // Find the matching distributor IDs that are both in the team table and $distributorIds array
    //                                         //             $matchingIds = array_intersect($distributorIds, $teamDistributorIds);
    //                                 			     // //$distributors=DB::table('users')->where('id',$matchingIds)->first();
    //                                         //   $distributorDetails = DB::table('users')->where('id', $matchingIds)->first();
    //                                         // }
                                            
    //                                         $distributorIdCounts = DB::table('retailer_barcodes')
    //                                             ->whereIn('code', $qr)
    //                                             ->select('distributor_id', DB::raw('COUNT(*) as count'))
    //                                             ->groupBy('distributor_id')
    //                                             ->orderByDesc('count')
    //                                             ->get();
    //                                             $distributorIdCounts = $distributorIdCounts->filter(function($item) {
    //                                                 return $item->distributor_id !== null;
    //                                             });
    //                                         if (isset($distributorIdCounts[1])) {
    //                                         //dd($distributorIdCounts[1]);
    //                                             if ($distributorIdCounts[1]->distributor_id) {
                                                
    //                                                 $maxDistributorId = $distributorIdCounts[1]->distributor_id;
    //                                                 $distributorDetails= DB::table('users')->where('id', $maxDistributorId)->where('status',1)->first();
    //                                                 $maxCount = $distributorIdCounts[1]->count;
    //                                             }else{
                                                   
    //                                     			      $distributorIds = explode(',', $distributorData->distributor_id);
    //                                                       $teamDistributorIds = DB::table('teams')->where('area_id', $distributorData->area_id)->where('state_id', $distributorData->state_id)->where('ase_id',$distributorData->ase_id)->where('store_id',NULL)->pluck('distributor_id')->toArray();
    //                                                       // dd($teamDistributorIds);
    //                                                         // Find the matching distributor IDs that are both in the team table and $distributorIds array
    //                                                         $matchingIds = array_intersect($distributorIds, $teamDistributorIds);
    //                                     			      //$distributors=DB::table('users')->where('id',$matchingIds)->first();
                                        			    
    //                                               $distributorDetails = DB::table('users')->whereIn('id', $matchingIds)->where('status',1)->first();
    //                                             }
    //                                         } elseif (isset($distributorIdCounts[0])) {
    //                                           if ($distributorIdCounts[0]->distributor_id) {
                                                
    //                                                 $maxDistributorId = $distributorIdCounts[0]->distributor_id;
    //                                                 $distributorDetails= DB::table('users')->where('id', $maxDistributorId)->where('status',1)->first();
    //                                                 $maxCount = $distributorIdCounts[0]->count;
    //                                             }else{
                                                   
    //                                     			      $distributorIds = explode(',', $distributorData->distributor_id);
    //                                                       $teamDistributorIds = DB::table('teams')->where('area_id', $distributorData->area_id)->where('state_id', $distributorData->state_id)->where('ase_id',$distributorData->ase_id)->where('store_id',NULL)->pluck('distributor_id')->toArray();
    //                                                       // dd($teamDistributorIds);
    //                                                         // Find the matching distributor IDs that are both in the team table and $distributorIds array
    //                                                         $matchingIds = array_intersect($distributorIds, $teamDistributorIds);
    //                                     			      //$distributors=DB::table('users')->where('id',$matchingIds)->first();
                                        			    
    //                                               $distributorDetails = DB::table('users')->whereIn('id', $matchingIds)->where('status',1)->first();
    //                                             }
    //                                         }else{
    //                                               $distributorIds = explode(',', $distributorData->distributor_id);
                                                      
    //                                                   $teamDistributorIds = DB::table('teams')->where('area_id', $distributorData->area_id)->where('state_id', $distributorData->state_id)->where('ase_id',$distributorData->ase_id)->where('store_id',NULL)->pluck('distributor_id')->toArray();
                                                        
    //                                                     // Find the matching distributor IDs that are both in the team table and $distributorIds array
    //                                                     $matchingIds = array_intersect($distributorIds, $teamDistributorIds);
    //                                 			      //$distributors=DB::table('users')->where('id',$matchingIds)->first();
    //                                           $distributorDetails = DB::table('users')->whereIn('id', $matchingIds)->where('status',1)->first();
    //                                         }
    //                                         $emailAddresses = array_map('trim', explode(',', $distributorDetails->email));
    //                                         $ccmail=array_map('trim', explode(',', $distributorDetails->state_head_email));
    //                                         //dd($distributorDetails);
    //                                         $emailData = [
    //                                             'name' => $distributorDetails->name,
    //                                             'subject' => 'NOC for Order ('.$newOrder->order_no.') of- ' . $userExist->name,
    //                                             'email' => $emailAddresses,
    //                                             'retailers' => $userExist->name,
    //                                             'blade_file' => 'admin/mail/order-noc', // Ensure correct view path with dot notation
    //                                         ];
    //                                     //dd($emailData);
    //                                         $mailLog = MailActivity::create([
    //                                             'email' => implode(',', $emailAddresses),
    //                                             'type' => 'noc-for-order',
    //                                             'sent_at' => now(),
    //                                             'status' => 'pending',
    //                                         ]);
                                            
    //                                       try {
    //                                             // Check order fetching
    //                                             $data = RetailerOrder::findOrFail($newOrder->id);
    //                                             //dd('Order Data:', $data);
                                            
    //                                             // Check view rendering
    //                                             $html = View::make('pdf.invoice', ['data' => $data])->render();
    //                                             $url='https://luxcozi.club/page?id='.$newOrder->id;
    //                                             //dd('Generated HTML:', $html);
                                            
    //                                             // Check PDF generation
    //                                             $pdfPath = public_path('invoices/NOC_' . $newOrder->id . '.pdf');
    //                                           $pdfGenerated = $this->convertHtmlToPdf($url, $pdfPath);
    //                                             //dd('PDF Created at:', $pdfPath);
    //                                             if ($pdfGenerated) {
    //                                                 // Check email sending
    //                                                 $ccEmail = ['coziclubsupport@luxinnerwear.com','sanket.ghoble@luxinnerwear.com','cozisupport@luxcozi.club','koushik.oneness@gmail.com','priya.m@techmantra.co','koushik@techmantra.co'];
    //                                                 $allCcEmails = array_merge($ccmail, $ccEmail);

    //                                               // dd(SendMail($emailData, $pdfPath, $ccEmail));
    //                                               $result = $this->SendMail($emailData, $pdfPath, $allCcEmails);
    //                                                 //dd($result); // Check if the function is called
    //                                                 if ($result) {
    //                                                   // dd('Email Sent Successfully');
    //                                                     $mailLog->update(['status' => 'sent']);
    //                                                 } else {
    //                                                   //  dd('Email Sending Failed');
    //                                                     $mailLog->update(['status' => 'failed']);
    //                                                 }
    //                                             }
    //                                             else {
    //                                                         return response()->json(['message' => 'PDF generation failed.'], 500);
    //                                                     }
    //                                         } catch (\Exception $e) {
    //                                             dd('Exception:', $e->getMessage());
    //                                             $mailLog->update(['status' => 'failed']);
    //                                             \Log::error('Order NOC email failed: ' . $e->getMessage());
    //                                         }
				// 		}
				    DB::commit();
						return response()->json([
						'error' => false,
						'message' => 'Order placed successfully',
						'data' => $userExist->wallet,
					    ]);
                    } catch (\Exception $e) {
                        DB::rollback();
                        return response()->json(['error' => true, 'message' => 'Something went wrong', 'exception' => $e->getMessage()]);
                    }

				//	}
			//	}
            
            
            /*} else {
              return response()->json(['status' => 400, 'message' => $validator->errors()->first()]);
            }*/
  
        }
        
        
        
       

public function pdfGenerateFunction()
{
    $url = 'https://luxcozi.club/page?id=1784';

    // Fetch HTML content from the URL
    $response = Http::get($url);

    if ($response->successful()) {
        $htmlContent = $response->body();

        // Generate PDF
        $pdf = Pdf::loadHTML($htmlContent)
            ->setPaper([0, 0, 595.28, 842])
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        // Ensure the 'pdfs' directory exists
        $pdfDirectory = public_path('pdfs');
        if (!File::exists($pdfDirectory)) {
            File::makeDirectory($pdfDirectory, 0775, true, true);
        }

        // Define the file path
        $filePath = $pdfDirectory . '/generated_page.pdf';

        // Save PDF to public folder
        file_put_contents($filePath, $pdf->output());

        return response()->json(['message' => 'PDF generated successfully!', 'path' => url('pdfs/generated_page.pdf')]);
    }

    return response()->json(['error' => 'Failed to fetch the page content'], 500);
}


}
