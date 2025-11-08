<?php

namespace App\Http\Controllers\Admin;

use App\Models\RetailerOrder;
use App\Models\Store;
use App\Models\RetailerProduct;
use App\Models\User;
use App\Models\State;
use App\Models\Area;
use App\Models\MailActivity;
use App\Models\RewardOrderProduct;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\RetailerUserTxnHistory;
use App\Models\RetailerWalletTxn;
use Illuminate\Http\Request;
//use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use Dompdf\Dompdf;
use Dompdf\Options;

use Illuminate\Support\Facades\Response;
use DB;
class RetailerOrderController extends Controller
{
    public function index(Request $request)
    {
       $data = (object) [];
        $from =  date('Y-m-01');
        $to =  date('Y-m-d', strtotime('+1 day'));
      if(Auth()->guard('admin')->user()->email=='admin@admin.com'){
       if (isset($request->date_from) || isset($request->date_to) ||isset($request->product) ||isset($request->term) || isset($request->distributor)|| isset($request->state)|| isset($request->status)) {
            $from = $request->date_from ? $request->date_from : date('Y-m-01');
            $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
            $term = $request->term ? $request->term : '';
            $product = $request->product ?? '';
 			$user_id = $request->distributor ? $request->distributor : '';
 			$state_id = $request->state ? $request->state : '';
 			$status = $request->status ? $request->status : '';
            // all order products
            $query1 = RewardOrderProduct::select('retailer_orders.status AS status','retailer_orders.final_amount AS finalamount','retailer_orders.user_id AS user_id','retailer_orders.admin_status AS admin_status','reward_order_products.qty AS qty','retailer_orders.order_no AS order_no','retailer_orders.id AS id','retailer_orders.shop_name AS shop_name','retailer_orders.email AS email','retailer_orders.mobile AS mobile','retailer_orders.created_at AS created_at','retailer_orders.asm_approval AS asm_approval','retailer_orders.rsm_approval AS rsm_approval','retailer_orders.zsm_approval AS zsm_approval','retailer_orders.nsm_approval AS nsm_approval','retailer_orders.distributor_approval AS distributor_approval','retailer_orders.asm_note AS asm_note','retailer_orders.rsm_note AS rsm_note','retailer_orders.nsm_note AS nsm_note','retailer_orders.distributor_note AS distributor_note','retailer_orders.zsm_note AS zsm_note','stores.wallet','stores.unique_code','stores.email AS storeemail')->join('retailer_products', 'retailer_products.id', 'reward_order_products.product_id')
             ->join('retailer_orders', 'retailer_orders.id', 'reward_order_products.order_id')->join('stores', 'stores.id', 'retailer_orders.user_id')
            ;
           
            $query1->when($product, function($query1) use ($product) {
                $query1->where('reward_order_products.product_id', $product);
            });
            
			
          
            $query1->when($status, function($query1) use ($status) {
                $query1->where('retailer_orders.status', $status);
            });
            
            $query1->when($term, function($query1) use ($term) {
                $query1->Where('retailer_orders.order_no', 'like', '%' . $term . '%')->orWhere('stores.name', 'like', '%' . $term . '%')->orWhere('stores.contact', 'like', '%' . $term . '%');
            })->whereBetween('retailer_orders.created_at', [$from, $to]);

            $data = $query1->latest('retailer_orders.id')->groupby('reward_order_products.order_id')
            ->paginate(25);
			
       }else{
            $data = RewardOrderProduct::select('retailer_orders.status AS status','retailer_orders.final_amount AS finalamount','retailer_orders.user_id AS user_id','retailer_orders.admin_status AS admin_status','reward_order_products.qty AS qty','retailer_orders.order_no AS order_no','retailer_orders.id AS id','retailer_orders.shop_name AS shop_name','retailer_orders.email AS email','retailer_orders.mobile AS mobile','retailer_orders.created_at AS created_at','retailer_orders.asm_approval AS asm_approval','retailer_orders.rsm_approval AS rsm_approval','retailer_orders.zsm_approval AS zsm_approval','retailer_orders.nsm_approval AS nsm_approval','retailer_orders.distributor_approval AS distributor_approval','retailer_orders.asm_note AS asm_note','retailer_orders.rsm_note AS rsm_note','retailer_orders.nsm_note AS nsm_note','retailer_orders.distributor_note AS distributor_note','retailer_orders.zsm_note AS zsm_note','stores.wallet','stores.unique_code','stores.email AS storeemail')->join('retailer_products', 'retailer_products.id', 'reward_order_products.product_id')
             ->join('retailer_orders', 'retailer_orders.id', 'reward_order_products.order_id')->join('retailer_user_txn_histories', 'retailer_orders.id', 'retailer_user_txn_histories.order_id')->join('stores', 'stores.id', 'retailer_orders.user_id')->join('retailer_wallet_txns', 'stores.id', 'retailer_wallet_txns.user_id')->whereBetween('reward_order_products.created_at', [$from, $to])->latest('retailer_orders.id')->groupby('reward_order_products.order_id')->paginate(25);
           
       }
      }else{
          if (isset($request->date_from) || isset($request->date_to) ||isset($request->product) ||isset($request->term) || isset($request->distributor)|| isset($request->state)|| isset($request->status)) {
            $from = $request->date_from ? $request->date_from : date('Y-m-01');
            $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
            $term = $request->term ? $request->term : '';
            $product = $request->product ?? '';
 			$user_id = $request->distributor ? $request->distributor : '';
 			$state_id = $request->state ? $request->state : '';
 			$status = $request->status ? $request->status : '';
            // all order products
            $query1 = RewardOrderProduct::select('retailer_orders.status AS status','retailer_orders.user_id AS user_id','retailer_orders.admin_status AS admin_status','reward_order_products.qty AS qty','retailer_orders.order_no AS order_no','retailer_orders.id AS id','retailer_orders.shop_name AS shop_name','retailer_orders.email AS email','retailer_orders.mobile AS mobile','retailer_orders.created_at AS created_at','retailer_orders.asm_approval AS asm_approval','retailer_orders.rsm_approval AS rsm_approval','retailer_orders.zsm_approval AS zsm_approval','retailer_orders.nsm_approval AS nsm_approval','retailer_orders.distributor_approval AS distributor_approval','retailer_orders.asm_note AS asm_note','retailer_orders.rsm_note AS rsm_note','retailer_orders.nsm_note AS nsm_note','retailer_orders.distributor_note AS distributor_note','retailer_orders.zsm_note AS zsm_note','teams.distributor_id AS distributor_id','teams.state_id AS state_id','teams.area_id AS area_id','teams.ase_id AS ase_id')->join('retailer_products', 'retailer_products.id', 'reward_order_products.product_id')
             ->join('retailer_orders', 'retailer_orders.id', 'reward_order_products.order_id')->join('stores', 'stores.id', 'retailer_orders.user_id')->join('teams', 'teams.store_id', 'stores.id')
            ;
           
            $query1->when($product, function($query1) use ($product) {
                $query1->where('reward_order_products.product_id', $product);
            });
            
			 $query1->when($user_id, function($query1) use ($user_id) {
                $query1->where('teams.distributor_id', 'like', '%'.$user_id.'%');
            });
            $query1->when($state_id, function($query1) use ($state_id) {
                $query1->where('teams.state_id', $state_id);
            });
            $query1->when($status, function($query1) use ($status) {
                $query1->where('retailer_orders.status', $status);
            });
            
            $query1->when($term, function($query1) use ($term) {
                $query1->Where('retailer_orders.order_no', 'like', '%' . $term . '%')->orWhere('stores.name', 'like', '%' . $term . '%')->orWhere('stores.contact', 'like', '%' . $term . '%');
            })->whereBetween('retailer_orders.created_at', [$from, $to]);

            $data = $query1->where('retailer_orders.status',2)->latest('retailer_orders.id')->groupby('reward_order_products.order_id')
            ->paginate(25);
			//dd($data);
       }else{
            $data = RewardOrderProduct::select('retailer_orders.status AS status','retailer_orders.user_id AS user_id','retailer_orders.admin_status AS admin_status','reward_order_products.qty AS qty','retailer_orders.order_no AS order_no','retailer_orders.id AS id','retailer_orders.shop_name AS shop_name','retailer_orders.email AS email','retailer_orders.mobile AS mobile','retailer_orders.created_at AS created_at','retailer_orders.asm_approval AS asm_approval','retailer_orders.rsm_approval AS rsm_approval','retailer_orders.zsm_approval AS zsm_approval','retailer_orders.nsm_approval AS nsm_approval','retailer_orders.distributor_approval AS distributor_approval','retailer_orders.asm_note AS asm_note','retailer_orders.rsm_note AS rsm_note','retailer_orders.nsm_note AS nsm_note','retailer_orders.distributor_note AS distributor_note','retailer_orders.zsm_note AS zsm_note','teams.distributor_id AS distributor_id','teams.state_id AS state_id','teams.area_id AS area_id','teams.ase_id AS ase_id','retailer_wallet_txns.final_amount AS final_amount')->join('retailer_products', 'retailer_products.id', 'reward_order_products.product_id')
             ->join('retailer_orders', 'retailer_orders.id', 'reward_order_products.order_id')->join('retailer_user_txn_histories', 'retailer_orders.id', 'retailer_user_txn_histories.order_id')->join('stores', 'stores.id', 'retailer_orders.user_id')->join('retailer_wallet_txns', 'stores.id', 'retailer_wallet_txns.user_id')->join('teams', 'teams.store_id', 'stores.id')->whereBetween('reward_order_products.created_at', [$from, $to])->where('retailer_orders.status',2)->latest('retailer_orders.id')->groupby('reward_order_products.order_id')->paginate(25);
           
       }
      }
       
        $allUser=Store::orderby('name')->get();
        $allDistributor=User::where('type',7)->orderby('name')->get();
        $allState=State::orderby('name')->get();
        $products=RetailerProduct::orderby('title')->get();
        return view('admin.reward.order.index', compact('data','allUser','products','request','allDistributor','allState'));
    }
	
	//details
	public function show(Request $request, $id)
    {
        $data = RetailerOrder::where('id',$id)->with('user')->first();
        return view('admin.reward.order.detail', compact('data'));
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
                        'as' => 'Gift-Order-'.$emailData['retailers'], // Custom nameNOC for Order '.$newOrder->order_no.') of- ' . $userExist->name
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
	
	public function approval(Request $request,$id,$status)
    {
		//dd($request->status);
		if($status == 1){
		    
            $updatedEntry = RetailerOrder::findOrFail($id);
            $updatedEntry->admin_status = $status;
            $updatedEntry->save();
            if (is_float($updatedEntry->amount) && $updatedEntry->amount != 0) {
                
        				  $distributorData=DB::table('teams')->where('store_id',$updatedEntry->user_id)->first();
                         
                          $distributorName=DB::table('users')->where('id',$distributorData->distributor_id)->first();
                          $transactionH = DB::table('retailer_wallet_txns')->where('user_id', $updatedEntry->user_id)->get();
                                         
                                            $qr = [];
                                            
                                            foreach ($transactionH as $rec) {
                                                $qr[] = $rec->barcode;
                                            }
                                           
                                            // Fetch distributor IDs and count their occurrences
                                            // $distributorIdCounts = DB::table('retailer_barcodes')
                                            //     ->whereIn('code', $qr)
                                            //     ->select('distributor_id', DB::raw('COUNT(*) as count'))
                                            //     ->groupBy('distributor_id')
                                            //     ->orderByDesc('count')
                                            //     ->first();
                                            
                                            // if ($distributorIdCounts) {
                                            //     $maxDistributorId = $distributorIdCounts->distributor_id;
                                            //     $distributorDetails= DB::table('users')->where('id', $maxDistributorId)->first();
                                            //     $maxCount = $distributorIdCounts->count;
                                            // }else{
                                            //       $distributorIds = explode(',', $distributorData->distributor_id);
                                                      
                                            //           $teamDistributorIds = DB::table('teams')->where('area_id', $distributorData->area_id)->where('state_id', $distributorData->state_id)->where('ase_id',$distributorData->ase_id)->where('store_id',NULL)->pluck('distributor_id')->toArray();
                                                        
                                            //             // Find the matching distributor IDs that are both in the team table and $distributorIds array
                                            //             $matchingIds = array_intersect($distributorIds, $teamDistributorIds);
                                    			     // //$distributors=DB::table('users')->where('id',$matchingIds)->first();
                                            //   $distributorDetails = DB::table('users')->where('id', $matchingIds)->first();
                                            // }
                                            
                                            $distributorIdCounts = DB::table('retailer_barcodes')
                                                ->whereIn('code', $qr)
                                                ->select('distributor_id', DB::raw('COUNT(*) as count'))
                                                ->groupBy('distributor_id')
                                                ->orderByDesc('count')
                                                ->get();
                                                $distributorIdCounts = $distributorIdCounts->filter(function($item) {
                                                    return $item->distributor_id !== null;
                                                });
                                            if (isset($distributorIdCounts[1])) {
                                            //dd($distributorIdCounts[1]);
                                                if ($distributorIdCounts[1]->distributor_id) {
                                                
                                                    $maxDistributorId = $distributorIdCounts[1]->distributor_id;
                                                    $distributorDetails= DB::table('users')->where('id', $maxDistributorId)->first();
                                                    $maxCount = $distributorIdCounts[1]->count;
                                                }else{
                                                   
                                        			      $distributorIds = explode(',', $distributorData->distributor_id);
                                                          $teamDistributorIds = DB::table('teams')->where('area_id', $distributorData->area_id)->where('state_id', $distributorData->state_id)->where('ase_id',$distributorData->ase_id)->where('store_id',NULL)->pluck('distributor_id')->toArray();
                                                           // dd($teamDistributorIds);
                                                            // Find the matching distributor IDs that are both in the team table and $distributorIds array
                                                            $matchingIds = array_intersect($distributorIds, $teamDistributorIds);
                                        			      //$distributors=DB::table('users')->where('id',$matchingIds)->first();
                                        			    
                                                   $distributorDetails = DB::table('users')->where('id', $matchingIds)->first();
                                                }
                                            } elseif (isset($distributorIdCounts[0])) {
                                               if ($distributorIdCounts[0]->distributor_id) {
                                                
                                                    $maxDistributorId = $distributorIdCounts[0]->distributor_id;
                                                    $distributorDetails= DB::table('users')->where('id', $maxDistributorId)->first();
                                                    $maxCount = $distributorIdCounts[0]->count;
                                                }else{
                                                   
                                        			      $distributorIds = explode(',', $distributorData->distributor_id);
                                                          $teamDistributorIds = DB::table('teams')->where('area_id', $distributorData->area_id)->where('state_id', $distributorData->state_id)->where('ase_id',$distributorData->ase_id)->where('store_id',NULL)->pluck('distributor_id')->toArray();
                                                           // dd($teamDistributorIds);
                                                            // Find the matching distributor IDs that are both in the team table and $distributorIds array
                                                            $matchingIds = array_intersect($distributorIds, $teamDistributorIds);
                                        			      //$distributors=DB::table('users')->where('id',$matchingIds)->first();
                                        			    
                                                   $distributorDetails = DB::table('users')->where('id', $matchingIds)->first();
                                                }
                                            }else{
                                                   $distributorIds = explode(',', $distributorData->distributor_id);
                                                      
                                                      $teamDistributorIds = DB::table('teams')->where('area_id', $distributorData->area_id)->where('state_id', $distributorData->state_id)->where('ase_id',$distributorData->ase_id)->where('store_id',NULL)->pluck('distributor_id')->toArray();
                                                        
                                                        // Find the matching distributor IDs that are both in the team table and $distributorIds array
                                                        $matchingIds = array_intersect($distributorIds, $teamDistributorIds);
                                    			      //$distributors=DB::table('users')->where('id',$matchingIds)->first();
                                               $distributorDetails = DB::table('users')->where('id', $matchingIds)->first();
                                            }
                                           
                                            $emailData = [
                                                'name' => $distributorDetails->name,
                                                'subject' => 'Place an Gift Order ('.$updatedEntry->order_no.') of- ' . $updatedEntry->shop_name,
                                                'email' => 'jyoti.singh@luxcozi.com',
                                                'retailers' => $updatedEntry->shop_name,
                                                'blade_file' => 'admin/mail/gift-order', // Ensure correct view path with dot notation
                                            ];
                                        //dd($emailData);
                                            $mailLog = MailActivity::create([
                                                'email' => 'jyoti.singh@luxcozi.com',
                                                'type' => 'gift-order-mail',
                                                'sent_at' => now(),
                                                'status' => 'pending',
                                            ]);
                                            
                                           try {
                                                // Check order fetching
                                                $data = RetailerOrder::findOrFail($updatedEntry->id);
                                                //dd('Order Data:', $data);
                                            
                                                // Check view rendering
                                                //$html = View::make('pdf.gift-order', ['data' => $data])->render();
                                                $url='https://luxcozi.club/page?id='.$updatedEntry->id;
                                                //dd('Generated HTML:', $html);
                                            
                                                // Check PDF generation
                                                $pdfPath = public_path('order/Gift_' . $updatedEntry->id . '.pdf');
                                               $pdfGenerated = $this->convertHtmlToPdf($url, $pdfPath);
                                               // dd('PDF Created at:', $pdfPath);
                                                if ($pdfGenerated) {
                                                    // Check email sending
                                                    $ccEmail = ['coziclubsupport@luxinnerwear.com','cozisupport@luxcozi.club','sanket.ghoble@luxinnerwear.com'];
                                                   // dd(SendMail($emailData, $pdfPath, $ccEmail));
                                                   $result = $this->SendMail($emailData, $pdfPath, $ccEmail);
                                                    //dd($result); // Check if the function is called
                                                    if ($result) {
                                                       // dd('Email Sent Successfully');
                                                        $mailLog->update(['status' => 'sent']);
                                                    } else {
                                                      //  dd('Email Sending Failed');
                                                        $mailLog->update(['status' => 'failed']);
                                                    }
                                                }
                                                else {
                                                            return response()->json(['message' => 'PDF generation failed.'], 500);
                                                        }
                                            } catch (\Exception $e) {
                                                dd('Exception:', $e->getMessage());
                                                $mailLog->update(['status' => 'failed']);
                                                \Log::error('Order NOC email failed: ' . $e->getMessage());
                                            }
						        }
		}else{
		    $updatedEntry = RetailerOrder::findOrFail($id);
            $updatedEntry->admin_status = $status;
            $updatedEntry->status=5;
            $updatedEntry->save();
			
		}
        
		$user_id=$updatedEntry->user_id;
		if($updatedEntry->admin_status == 0)
		{
		  $store=Store::findOrFail($user_id);
		  $store->wallet += $updatedEntry->final_amount;
		  $store->save();
		}
       return redirect()->back()->with('success', 'Order status updated');
    }
    public function status(Request $request, $id, $status)
    {
        
        $updatedEntry = RetailerOrder::findOrFail($id);
        $storeData=Store::findOrFail($updatedEntry->user_id);
        // If order is cancelled
        if ($updatedEntry->status == 5) {
            return redirect()->back()->with('failure', 'Order has been cancelled');
        }
    
        // Prevent moving back from 2, 3, or 4 to any smaller number
        if ($updatedEntry->status == 4 && in_array($status, [1,2, 3, 5,6,7])) {
            return redirect()->back()->with('failure', 'Order has been delivered');
        }
        if ($updatedEntry->status == 7 && in_array($status, [1,2, 3,5, 6])) {
            return redirect()->back()->with('failure', 'Gift Product Order has been dispatched');
        }
        
        if ($updatedEntry->status == 3 && in_array($status, [1,2, 5,6])) {
            return redirect()->back()->with('failure', 'Gift Product Order has been placed');
        }
        
        if ($updatedEntry->status == 2 && in_array($status, [1, 6])) {
            return redirect()->back()->with('failure', 'Address already confirmed');
        }
        
        if ($updatedEntry->status == 1 && in_array($status, [6])) {
            return redirect()->back()->with('failure', 'NOC already approved');
        }
    
        $updatedEntry->status = $status;
        if($status==2){
            $updatedEntry->	address_confirm_date=now();
        }else if($status==3){
            $updatedEntry->	gift_order_date=now();
        }
        $updatedEntry->save();
        if($updatedEntry->status == 5)
		{
		  $store=Store::findOrFail($updatedEntry->user_id);
		  $store->wallet += $updatedEntry->final_amount;
		  $store->save();
		  
		  $data=new RetailerUserTxnHistory();
            $data->user_id = $storeData->id;
            $data->amount = $updatedEntry->final_amount;
    		$data->type = 'Earn' ?? '';
    		$data->title = $updatedEntry->final_amount.' points credited to retailer';
    		$data->description = $updatedEntry->final_amount.' points credited to retailer for cancel gift reedemption';
    		$data->amount_type = 'gift-reedem-cancel';
    		
    		$data->status = 'increment';
    		$data->created_at = date('Y-m-d H:i:s');
    		$data->updated_at = date('Y-m-d H:i:s');
            $data->save();
            
            $userAmount=RetailerWalletTxn::where('user_id',$storeData->id)->orWhere('user_id',$storeData->unique_code)->orderby('id','desc')->first();
			$walletTxn=new RetailerWalletTxn();
			$walletTxn->user_id = $storeData->id;
			
			$walletTxn->amount = $updatedEntry->final_amount;
			
			$walletTxn->type = 1 ?? '';
			
			if (!$userAmount) {
                $walletTxn->final_amount =  $updatedEntry->final_amount;
            } else {
                $walletTxn->final_amount = $userAmount->final_amount +  $updatedEntry->final_amount;
            }
            
			$walletTxn->created_at = date('Y-m-d H:i:s');
			$walletTxn->updated_at = date('Y-m-d H:i:s');
			$walletTxn->save();
		}
        
    
        return redirect()->back()->with('success', 'Order status updated');
    }
    
    
   
    
    public function dispatchOrder(Request $request,$id,$status)
   {
       $validator = \Validator::make($request->all(), [
            
            'docket_no' => 'required|string|max:255',
            'gift_dispatch_date' => 'required',
        ]);
         if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $order = RetailerOrder::findOrFail($id);
    
        // Prevent change if already delivered
        if ($order->status == 4) {
            return response()->json(['success' => false, 'message' =>'Order has already been delivered']);
        }
        if ($order->status == 5) {
            return response()->json(['success' => false, 'message' =>'Order has already been cancelled']);
        }
    
        $order->status = $status; // Gift Dispatched
        $order->docket_no = $request->docket_no;
        $order->gift_dispatch_date = $request->gift_dispatch_date;
        $order->dispatch_remarks = $request->dispatch_remarks;
        $order->save();
        return response()->json(['success' => true]);
       // return redirect()->back()->with('success', 'Order dispatched successfully with docket number');
   }
   
   
    public function deliverOrder(Request $request,$id,$status)
   {
         $validator = \Validator::make($request->all(), [
           
            'delivery_date' => 'required',
            
        ]);
         if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $order = RetailerOrder::findOrFail($id);
    
        // Prevent change if already delivered
        
        if ($order->status == 5) {
             return response()->json(['success' => false, 'message' =>'Order has already been cancelled']);
        }
    
        $order->status = $status; // Gift Dispatched
        
        $order->delivery_date = $request->delivery_date;
        $order->delivery_remarks = $request->delivery_remarks;
        $order->save();
        return response()->json(['success' => true]);
        //return redirect()->back()->with('success', 'Order delivery status changed successfully');
   }

	
	public function orderProductStatus(Request $request,$id,$status)
    {
        
        $statusUpdate = DB::table('reward_order_products')->where('id', $id)->update([
            'status' => $status
        ]);

        // send email
        // fetching ordered products
        

        switch ($status) {
            case 1:
                $statusTitle = 'NOC Approved';
               // $statusDesc = 'We are currently processing your order';
                break;
            case 2:
                $statusTitle = 'Address Confirmed';
               // $statusDesc = 'Your order is confirmed';
                break;
            case 3:
                $statusTitle = 'Gift Ordered';
                //$statusDesc = 'Your order is Shipped. It will reach you soon';
                break;
            case 4:
                $statusTitle = 'Gift Delivered';
                //$statusDesc = 'Your order is delivered';
                break;
            case 5:
                $statusTitle = 'Cancelled';
               // $statusDesc = 'Your order is cancelled';
                break;
            
            default:
                $statusTitle = 'Waiting for NOC';
               // $statusDesc = 'We are currently processing your order';
                break;
        }

        /*$email_data = [
            'name' => $orderedProducts->orderDetails->fname.' '.$orderedProducts->orderDetails->lname,
            'subject' => 'Onn - Order update for #'.$orderedProducts->orderDetails->order_no,
            'email' => $orderedProducts->orderDetails->email,
            'orderId' => $orderedProducts->orderDetails->id,
            'orderNo' => $orderedProducts->orderDetails->order_no,
            'orderAmount' => $orderedProducts->orderDetails->final_amount,
            'status' => $orderedProducts->orderDetails->status,
            'statusTitle' => $statusTitle,
            'statusDesc' => $statusDesc,
            'orderProducts' => $orderedProducts,
            'blade_file' => 'front/mail/order-update',
        ];*/

        // dd($email_data);

        //SendMail($email_data);

        if ($statusUpdate) {
            return redirect()->back()->with(['error' => false, 'message' => 'Order status updated']);
        } else {
             return redirect()->back()->with(['error' => true, 'message' => 'Something happened']);
        }
    }
      //export csv for reward order report

          //export csv for secondary order report

    /*public function exportCSV(Request $request)
    {
        $data = (object) [];
        $from = $request->date_from ? $request->date_from : date('Y-m-01');
        $to = date('Y-m-d', strtotime(request()->input('date_to')))? date('Y-m-d', strtotime(request()->input('date_to'))) : '';
        if (isset($request->date_from) || isset($request->date_to) ||isset($request->product) ||isset($request->term) || isset($request->distributor)|| isset($request->state)) {
            $from = $request->date_from ? $request->date_from : date('Y-m-01');
            $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
            $term = $request->term ? $request->term : '';
            $product = $request->product ?? '';
 			$user_id = $request->distributor ? $request->distributor : '';
 			$state_id = $request->state ? $request->state : '';
            // all order products
            $query1 = RewardOrderProduct::select('retailer_orders.status AS status','retailer_orders.admin_status AS admin_status','reward_order_products.qty AS qty','retailer_orders.order_no AS order_no','retailer_orders.final_amount AS final_amount','retailer_orders.id AS id','retailer_orders.shop_name AS shop_name','retailer_orders.email AS email','retailer_orders.mobile AS mobile','retailer_orders.created_at AS created_at','retailer_orders.asm_approval AS asm_approval','retailer_orders.rsm_approval AS rsm_approval','retailer_orders.zsm_approval AS zsm_approval','retailer_orders.nsm_approval AS nsm_approval','retailer_orders.distributor_approval AS distributor_approval','retailer_orders.asm_note AS asm_note','retailer_orders.rsm_note AS rsm_note','retailer_orders.nsm_note AS nsm_note','retailer_orders.distributor_note AS distributor_note','retailer_orders.zsm_note AS zsm_note','teams.distributor_id AS distributor_id','teams.state_id AS state_id','teams.area_id AS area_id','teams.ase_id AS ase_id','reward_order_products.product_name AS product_name')->join('retailer_products', 'retailer_products.id', 'reward_order_products.product_id')
             ->join('retailer_orders', 'retailer_orders.id', 'reward_order_products.order_id')->join('stores', 'stores.id', 'retailer_orders.user_id')->join('teams', 'teams.store_id', 'stores.id')
            ;
           
            $query1->when($product, function($query1) use ($product) {
                $query1->where('reward_order_products.product_id', $product);
            });
            
			 $query1->when($user_id, function($query1) use ($user_id) {
                $query1->where('teams.distributor_id', $user_id);
            });
            $query1->when($state_id, function($query1) use ($state_id) {
                $query1->where('teams.state_id', $state_id);
            });
            $query1->when($term, function($query1) use ($term) {
                $query1->Where('retailer_orders.order_no', 'like', '%' . $term . '%')->orWhere('stores.name', 'like', '%' . $term . '%')->orWhere('stores.contact', 'like', '%' . $term . '%');
            })->whereBetween('retailer_orders.created_at', [$from, $to]);

            $data = $query1->latest('retailer_orders.id')->groupby('reward_order_products.order_id')
            ->get();
		
       }else{
            $data = RewardOrderProduct::select('retailer_orders.status AS status','retailer_orders.admin_status AS admin_status','reward_order_products.qty AS qty','retailer_orders.order_no AS order_no','retailer_orders.final_amount AS final_amount','retailer_orders.id AS id','retailer_orders.shop_name AS shop_name','retailer_orders.email AS email','retailer_orders.mobile AS mobile','retailer_orders.created_at AS created_at','retailer_orders.asm_approval AS asm_approval','retailer_orders.rsm_approval AS rsm_approval','retailer_orders.zsm_approval AS zsm_approval','retailer_orders.nsm_approval AS nsm_approval','retailer_orders.distributor_approval AS distributor_approval','retailer_orders.asm_note AS asm_note','retailer_orders.rsm_note AS rsm_note','retailer_orders.nsm_note AS nsm_note','retailer_orders.distributor_note AS distributor_note','retailer_orders.zsm_note AS zsm_note','teams.distributor_id AS distributor_id','teams.state_id AS state_id','teams.area_id AS area_id','teams.ase_id AS ase_id','reward_order_products.product_name AS product_name')->join('retailer_products', 'retailer_products.id', 'reward_order_products.product_id')
             ->join('retailer_orders', 'retailer_orders.id', 'reward_order_products.order_id')->join('stores', 'stores.id', 'retailer_orders.user_id')->join('teams', 'teams.store_id', 'stores.id')->latest('retailer_orders.id')->groupby('reward_order_products.order_id')->get();
          
       }

        if (count($data) > 0) {
            $delimiter = ",";
            $filename = "luxcozi-reward-order-report-".$from.' to '.$to.".csv";

            // Create a file pointer 
            $f = fopen('php://memory', 'w');

            // Set column headers 
            $fields = array('SR', 'ORDER NUMBER','ORDER AMOUNT', 'PRODUCT NAME', 'QUANTITY', 'STORE','STORE CONTACT','DISTRIBUTOR','DISTRIBUTOR CODE','DISTRIBUTOR CITY','DISTRIBUTOR STATE','DISTRIBUTOR APPROVAL','ADMIN APPROVAL','PRODUCT DISPATCH STATUS','PREVIOUS CURRENCY','DATE','TIME');
            fputcsv($f, $fields, $delimiter); 

            $count = 1;

            foreach($data as $row) {
               $distributor=DB::table('users')->where('id',$row->distributor_id)->first();
               $state= State::where('id',$row->state_id)->first();
                $date = date('j M Y', strtotime($row['created_at']));
                $time = date('g:i A', strtotime($row['created_at']));
                
                $transactionH = DB::table('retailer_wallet_txns')->where('user_id', $row->user_id)->get();
                                            
                                            $qr = [];
                                            
                                            foreach ($transactionH as $rec) {
                                                $qr[] = $rec->barcode;
                                            }
                                           
                                            // Fetch distributor IDs and count their occurrences
                                            $distributorIdCounts = DB::table('retailer_barcodes')
                                                ->whereIn('code', $qr)
                                                ->select('distributor_id', DB::raw('COUNT(*) as count'))
                                                ->groupBy('distributor_id')
                                                ->orderByDesc('count')
                                                ->first();
                                             
                                            if (!empty($distributorIdCounts->distributor_id)) {
                                                $maxDistributorId = $distributorIdCounts->distributor_id;
                                                $distributorDetails= DB::table('users')->where('id', $maxDistributorId)->first();
                                                $maxCount = $distributorIdCounts->count;
                                            }else{
                                                $distributorIds = explode(',', $row->distributor_id);
                                                      $teamDistributorIds = DB::table('teams')->where('area_id', $row->area_id)->where('state_id', $row->state_id)->where('ase_id',$row->ase_id)->where('store_id',NULL)->pluck('distributor_id')->toArray();
                                                       // dd($teamDistributorIds);
                                                        // Find the matching distributor IDs that are both in the team table and $distributorIds array
                                                        $matchingIds = array_intersect($distributorIds, $teamDistributorIds);
                                    			      //$distributors=DB::table('users')->where('id',$matchingIds)->first();
                                               $distributorDetails = DB::table('users')->where('id', $matchingIds)->first();
                                            }
                $walletTran=DB::table('retailer_user_txn_histories')->where('order_id',$row->id)->first();
                 if(!empty($walletTran))    {               
                $walletBal=DB::table('retailer_wallet_txns')->where('amount',$walletTran->amount)->where('user_id',$walletTran->user_id)->where('created_at','=',$walletTran->created_at)->first();
                 }  
                 if(!empty($walletTran) && !empty($walletBal))    {
                $finalBal=DB::table('retailer_wallet_txns')->where('user_id',$walletTran->user_id)->where('id', '<', $walletBal->id)->orderBy('id', 'desc')->first();
                 }
				if($row->distributor_approval ==2)
                    $distributor_status='Wait for approval';
                    elseif($row->distributor_approval==1)
                    $distributor_status='Approved';
                    else
                    $distributor_status='Rejected';
				
				 
				 if($row->admin_status ==2)
                    $admin_status='Wait for approval';
                    elseif($row->admin_status==1)
                    $admin_status='Approved';
                    else
                    $admin_status='Rejected';
				
				      switch ($row->status) {
                    case 1:
                        $statusTitle = 'New';
                        $statusDesc = 'We are currently processing your order';
                        break;
                    case 2:
                        $statusTitle = 'Confirmed';
                        break;
                    case 3:
                        $statusTitle = 'Shipped';
                        $statusDesc = 'Your order is Shipped. It will reach you soon';
                        break;
                    case 4:
                        $statusTitle = 'Delivered';
                        $statusDesc = 'Your order is delivered';
                        break;
                    case 5:
                        $statusTitle = 'Cancelled';
                        $statusDesc = 'Your order is cancelled';
                        break;
                    case 6:
                        $statusTitle = 'Return request';
                        $statusDesc = 'You have requested return for the product';
                        break;
                    case 7:
                        $statusTitle = 'Return approved';
                        $statusDesc = 'You return request is approved';
                        break;
                    case 8:
                        $statusTitle = 'Return declined';
                        $statusDesc = 'You return request is declined';
                        break;
                    case 9:
                        $statusTitle = 'Products Returned';
                        $statusDesc = 'You have returned old products';
                        break;
                    case 10:
                        $statusTitle = 'Products Received';
                        $statusDesc = 'Your returned products are received';
                        break;
                    case 11:
                        $statusTitle = 'Products Shipped';
                        $statusDesc = 'Your new products are shipped';
                        break;
                    case 12:
                        $statusTitle = 'Products Delivered';
                        $statusDesc = 'Your new products are delivered';
                        break;
                    default:
                        $statusTitle = 'New';
                        $statusDesc = 'We are currently processing your order';
                        break;
                }
                $lineData = array(
                    $count,
                    $row['order_no'] ?? '',
                    $row['final_amount'] ?? '',
                    $row['product_name'] ?? '',
                    $row['qty'] ?? '',
                    $row['shop_name'] ?? '',
                    $row['mobile'] ?? '',
                    $distributorDetails->name ?? '',
                    $distributorDetails->employee_id ?? '',
                    $distributorDetails->city ?? '',
                    $state->name ??'',
					$distributor_status,
					$admin_status,
					$statusTitle,
					$finalBal->final_amount ??'',
                    $date,
                    $time
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
        }
    }*/
    
    
    
   public function exportCSV(Request $request)
{
     $from =  date('Y-m-01');
        $to =  date('Y-m-d', strtotime('+1 day'));
   if (isset($request->date_from) || isset($request->date_to) ||isset($request->product) ||isset($request->term) || isset($request->distributor)|| isset($request->state)|| isset($request->status)) {
            $from = $request->date_from ? $request->date_from : date('Y-m-01');
            $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
            $term = $request->term ? $request->term : '';
            $product = $request->product ?? '';
 			$user_id = $request->distributor ? $request->distributor : '';
 			$state_id = $request->state ? $request->state : '';
 			$status = $request->status ? $request->status : '';
            // all order products
            $query1 = RewardOrderProduct::select('retailer_products.title as product_name','retailer_orders.status AS status','retailer_orders.final_amount AS final_amount','retailer_orders.user_id AS user_id','retailer_orders.admin_status AS admin_status','reward_order_products.qty AS qty','retailer_orders.order_no AS order_no','retailer_orders.id AS id','retailer_orders.shop_name AS shop_name','retailer_orders.email AS email','retailer_orders.mobile AS mobile','retailer_orders.created_at AS created_at','retailer_orders.asm_approval AS asm_approval','retailer_orders.rsm_approval AS rsm_approval','retailer_orders.zsm_approval AS zsm_approval','retailer_orders.nsm_approval AS nsm_approval','retailer_orders.distributor_approval AS distributor_approval','retailer_orders.asm_note AS asm_note','retailer_orders.rsm_note AS rsm_note','retailer_orders.nsm_note AS nsm_note','retailer_orders.distributor_note AS distributor_note','retailer_orders.zsm_note AS zsm_note','address_confirm_date','gift_order_date','gift_dispatch_date','docket_no','delivery_date','delivery_remarks','stores.state_id','stores.area_id')->join('retailer_products', 'retailer_products.id', 'reward_order_products.product_id')
             ->join('retailer_orders', 'retailer_orders.id', 'reward_order_products.order_id')->join('stores', 'stores.id', 'retailer_orders.user_id')
            ;
           
            $query1->when($product, function($query1) use ($product) {
                $query1->where('reward_order_products.product_id', $product);
            });
            
			
          
            $query1->when($status, function($query1) use ($status) {
                $query1->where('retailer_orders.status', $status);
            });
            
            $query1->when($term, function($query1) use ($term) {
                $query1->Where('retailer_orders.order_no', 'like', '%' . $term . '%')->orWhere('stores.name', 'like', '%' . $term . '%')->orWhere('stores.contact', 'like', '%' . $term . '%');
            })->whereBetween('retailer_orders.created_at', [$from, $to]);

            $data = $query1->latest('retailer_orders.id')->groupby('reward_order_products.order_id')
            ->get();
			
       }else{
            $data = RewardOrderProduct::select('retailer_products.title as product_name','retailer_orders.status AS status','retailer_orders.final_amount AS final_amount','retailer_orders.user_id AS user_id','retailer_orders.admin_status AS admin_status','reward_order_products.qty AS qty','retailer_orders.order_no AS order_no','retailer_orders.id AS id','retailer_orders.shop_name AS shop_name','retailer_orders.email AS email','retailer_orders.mobile AS mobile','retailer_orders.created_at AS created_at','retailer_orders.asm_approval AS asm_approval','retailer_orders.rsm_approval AS rsm_approval','retailer_orders.zsm_approval AS zsm_approval','retailer_orders.nsm_approval AS nsm_approval','retailer_orders.distributor_approval AS distributor_approval','retailer_orders.asm_note AS asm_note','retailer_orders.rsm_note AS rsm_note','retailer_orders.nsm_note AS nsm_note','retailer_orders.distributor_note AS distributor_note','retailer_orders.zsm_note AS zsm_note','address_confirm_date','gift_order_date','gift_dispatch_date','docket_no','delivery_date','delivery_remarks','stores.state_id','stores.area_id')->join('retailer_products', 'retailer_products.id', 'reward_order_products.product_id')
             ->join('retailer_orders', 'retailer_orders.id', 'reward_order_products.order_id')->join('retailer_user_txn_histories', 'retailer_orders.id', 'retailer_user_txn_histories.order_id')->join('stores', 'stores.id', 'retailer_orders.user_id')->join('retailer_wallet_txns', 'stores.id', 'retailer_wallet_txns.user_id')->whereBetween('reward_order_products.created_at', [$from, $to])->latest('retailer_orders.id')->groupby('reward_order_products.order_id')->get();
          
       }

    $filename = "rupa-reward-order-report-{$from}_to_{$to}.csv";
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ];

    return Response::stream(function () use ($data) {
        $file = fopen('php://output', 'w');

        // Column Headers
        fputcsv($file, [
            'Outlet Name', 'Outlet Code', 'Points Redeemed', 'Gift Required', 'Redemption Request No',
            'Outlet Address', 'Pincode', 'city','State', 'Owner Name','Owner Mobile','','','','Outlet GST No','Outlet Owner PAN No','Outlet Owner Adhaar No','Order Date','Address Confirm Date','Gift Order Date',
            'Dispatch Date','Docket No','Delivery Date','Delivery Remarks'
        ]);

        $count = 1;

        // Process data in chunks to optimize memory usage
        $data->chunk(500)->each(function ($chunk) use (&$count, $file) {
            foreach ($chunk as $row) {
                
                $store=Store::find($row->user_id);
                $state = State::find($row->state_id);
                $area=Area::find($row->area_id);
                $date = date('j M Y', strtotime($row->created_at));
                $time = date('g:i A', strtotime($row->created_at));
                $address_confirm_date = !empty($row->address_confirm_date) 
                ? date('j M Y g:i A', strtotime($row->address_confirm_date)) 
                : null;
                $gift_order_date = !empty($row->gift_order_date) 
                ? date('j M Y g:i A', strtotime($row->gift_order_date)) 
                : null;
                 $gift_dispatch_date = !empty($row->gift_dispatch_date) 
                ? date('j M Y g:i A', strtotime($row->gift_dispatch_date)) 
                : null;
                 $delivery_date = !empty($row->delivery_date) 
                ? date('j M Y g:i A', strtotime($row->delivery_date)) 
                : null;
                // Fetch all transactions for the given user
               
                if($row->distributor_approval ==2)
                    $distributor_status='Wait for approval';
                    elseif($row->distributor_approval==1)
                    $distributor_status='Approved';
                    else
                    $distributor_status='Rejected';
                if($row->admin_status ==2)
                    $admin_status='Wait for approval';
                    elseif($row->admin_status==1)
                    $admin_status='Approved';
                    else
                    $admin_status='Rejected';
				
				      switch ($row->status) {
                    case 1:
                        $statusTitle = 'NOC Approved';
                        $statusDesc = 'We are currently processing your order';
                        break;
                    case 2:
                        $statusTitle = 'Address Confirmed';
                        break;
                    case 3:
                        $statusTitle = 'Gift Ordered';
                        $statusDesc = 'Your order is Shipped. It will reach you soon';
                        break;
                    case 4:
                        $statusTitle = 'Gift Delivered';
                        $statusDesc = 'Your order is delivered';
                        break;
                    case 5:
                        $statusTitle = 'Cancelled';
                        $statusDesc = 'Your order is cancelled';
                        break;
                   
                   
                    default:
                        $statusTitle = 'Waiting for NOC';
                        $statusDesc = 'We are currently processing your order';
                        break;
                }
                fputcsv($file, [
                    $store->name,
                    $store->unique_code,
                    $row->final_amount,
                    $row->product_name,
                    $row->order_no,
                    $store->address,
                    $store->pin,
                    $area->name ??'',
                    $state->name,
                    $store->owner_fname.' '.$store->owner_lname,
                    $store->contact,
                    '',
                    '',
                    '',
                    $store->gst_no,
                    $store->pan_no,
                    
                    $store->aadhar,
                    $date.' '.$time,
                    
                    $address_confirm_date,
                    $gift_order_date,
                    $gift_dispatch_date,
                    $row->docket_no,
                    $delivery_date,
                    $row->delivery_remarks,
                    
                ]);
            }
        });

        fclose($file);
    }, 200, $headers);
}





	public function addressUpdate(Request $request,$id)
    {
		//dd($request->status);
        $updatedEntry = RetailerOrder::findOrFail($id);
       
        $updatedEntry->shipping_address = $request->shipping_address;
        $updatedEntry->shipping_landmark = $request->shipping_landmark;
        $updatedEntry->shipping_country = $request->shipping_country;
        $updatedEntry->shipping_state = $request->shipping_state;
        $updatedEntry->shipping_city = $request->shipping_city;
        $updatedEntry->shipping_pin = $request->shipping_pin;
        $updatedEntry->save();
        if($updatedEntry){
        return redirect()->back()->with('success', 'Shipping address updated');
        }else{
            return redirect()->back()->with('failure', 'something happend');
        }
        
      
    }
    
   
    
    

    
    public function nocmailSend(Request $request,$id)
    {
        $order=RetailerOrder::findOrfail($id);
        $userExist = Store::where('id', $order->user_id)->where('status', 1)->first();
         $distributorData=DB::table('teams')->where('store_id',$order->user_id)->first();
                                     
                                      $distributorName=DB::table('users')->where('id',$distributorData->distributor_id)->first();
                                      $transactionH = DB::table('retailer_wallet_txns')->where('user_id', $order->user_id)->get();
                                         
                                            $qr = [];
                                            
                                            foreach ($transactionH as $rec) {
                                                $qr[] = $rec->barcode;
                                            }
                                           
                                            
                                            
                                            $distributorIdCounts = DB::table('retailer_barcodes')
                                                ->whereIn('code', $qr)
                                                ->select('distributor_id', DB::raw('COUNT(*) as count'))
                                                ->groupBy('distributor_id')
                                                ->orderByDesc('count')
                                                ->get();
                                                $distributorIdCounts = $distributorIdCounts->filter(function($item) {
                                                    return $item->distributor_id !== null;
                                                });
                                            if (isset($distributorIdCounts[1])) {
                                            //dd($distributorIdCounts[1]);
                                                if ($distributorIdCounts[1]->distributor_id) {
                                                
                                                    $maxDistributorId = $distributorIdCounts[1]->distributor_id;
                                                    $distributorDetails= DB::table('users')->where('id', $maxDistributorId)->where('status',1)->first();
                                                    $maxCount = $distributorIdCounts[1]->count;
                                                }else{
                                                   
                                        			      $distributorIds = explode(',', $distributorData->distributor_id);
                                                          $teamDistributorIds = DB::table('teams')->where('area_id', $distributorData->area_id)->where('state_id', $distributorData->state_id)->where('ase_id',$distributorData->ase_id)->where('store_id',NULL)->pluck('distributor_id')->toArray();
                                                           // dd($teamDistributorIds);
                                                            // Find the matching distributor IDs that are both in the team table and $distributorIds array
                                                            $matchingIds = array_intersect($distributorIds, $teamDistributorIds);
                                        			      //$distributors=DB::table('users')->where('id',$matchingIds)->first();
                                        			    
                                                   $distributorDetails = DB::table('users')->whereIn('id', $matchingIds)->where('status',1)->first();
                                                }
                                            } elseif (isset($distributorIdCounts[0])) {
                                               if ($distributorIdCounts[0]->distributor_id) {
                                                
                                                    $maxDistributorId = $distributorIdCounts[0]->distributor_id;
                                                    $distributorDetails= DB::table('users')->where('id', $maxDistributorId)->where('status',1)->first();
                                                    $maxCount = $distributorIdCounts[0]->count;
                                                }else{
                                                   
                                        			      $distributorIds = explode(',', $distributorData->distributor_id);
                                                          $teamDistributorIds = DB::table('teams')->where('area_id', $distributorData->area_id)->where('state_id', $distributorData->state_id)->where('ase_id',$distributorData->ase_id)->where('store_id',NULL)->pluck('distributor_id')->toArray();
                                                           // dd($teamDistributorIds);
                                                            // Find the matching distributor IDs that are both in the team table and $distributorIds array
                                                            $matchingIds = array_intersect($distributorIds, $teamDistributorIds);
                                        			      //$distributors=DB::table('users')->where('id',$matchingIds)->first();
                                        			    
                                                   $distributorDetails = DB::table('users')->whereIn('id', $matchingIds)->where('status',1)->first();
                                                }
                                            }else{
                                                   $distributorIds = explode(',', $distributorData->distributor_id);
                                                      
                                                      $teamDistributorIds = DB::table('teams')->where('area_id', $distributorData->area_id)->where('state_id', $distributorData->state_id)->where('ase_id',$distributorData->ase_id)->where('store_id',NULL)->pluck('distributor_id')->toArray();
                                                        
                                                        // Find the matching distributor IDs that are both in the team table and $distributorIds array
                                                        $matchingIds = array_intersect($distributorIds, $teamDistributorIds);
                                    			      //$distributors=DB::table('users')->where('id',$matchingIds)->first();
                                               $distributorDetails = DB::table('users')->whereIn('id', $matchingIds)->where('status',1)->first();
                                            }
                                            $emailAddresses = array_map('trim', explode(',', $distributorDetails->email));
                                           
                                            $ccmail=array_map('trim', explode(',', $distributorDetails->state_head_email));
                                             
                                            //dd($distributorDetails);
                                            $emailData = [
                                                'name' => $distributorDetails->name,
                                                'subject' => 'NOC for Order ('.$order->order_no.') of- ' . $userExist->name,
                                                'email' => $emailAddresses,
                                                'retailers' => $userExist->name,
                                                'blade_file' => 'admin/mail/order-noc', // Ensure correct view path with dot notation
                                            ];
                                        //dd($emailData);
                                            $mailLog = MailActivity::create([
                                                'email' => implode(',', $emailAddresses),
                                                'type' => 'noc-for-order',
                                                'sent_at' => now(),
                                                'status' => 'pending',
                                            ]);
                                            //dd($mailLog);
                                           try {
                                                // Check order fetching
                                                //$data = RetailerOrder::findOrFail($order->id);
                                                //dd('Order Data:', $data);
                                            
                                                // Check view rendering
                                                $html = View::make('pdf.invoice', ['data' => $order])->render();
                                                $url='https://luxcozi.club/page?id='.$order->id;
                                                //dd('Generated HTML:', $url);
                                            
                                                // Check PDF generation
                                                $pdfPath = public_path('invoices/NOC_' . $order->id . '.pdf');
                                               $pdfGenerated = $this->convertHtmlToPdf($url, $pdfPath);
                                                //dd('PDF Created at:', $pdfPath);
                                                if ($pdfGenerated) {
                                                    // Check email sending
                                                    $ccEmail = ['coziclubsupport@luxinnerwear.com','sanket.ghoble@luxinnerwear.com','cozisupport@luxcozi.club','koushik@techmantra.co','koushik.oneness@gmail.com','priya.m@techmantra.co'];
                                                    $allCcEmails = array_merge($ccmail, $ccEmail);
                                                    dd($allCcEmails);
                                                   // dd(SendMail($emailData, $pdfPath, $ccEmail));
                                                   $result = $this->SendMail($emailData, $pdfPath, $allCcEmails);
                                                    //dd($result); // Check if the function is called
                                                    if ($result) {
                                                       // dd('Email Sent Successfully');
                                                        $mailLog->update(['status' => 'sent']);
                                                    } else {
                                                      //  dd('Email Sending Failed');
                                                        $mailLog->update(['status' => 'failed']);
                                                    }
                                                }
                                                else {
                                                            return response()->json(['message' => 'PDF generation failed.'], 500);
                                                        }
                                            } catch (\Exception $e) {
                                                dd('Exception:', $e->getMessage());
                                                $mailLog->update(['status' => 'failed']);
                                                \Log::error('Order NOC email failed: ' . $e->getMessage());
                                            }
						
					 return redirect()->back()->with('success', 'Mail sended successfully');
        
    }
    
    

  
}
