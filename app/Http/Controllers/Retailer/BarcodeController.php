<?php

namespace App\Http\Controllers\Retailer;

use App\Models\RetailerBarcode;
use App\Models\RetailerUser;
use App\Models\Invoice;
use App\Models\RetailerWalletTxn;
use App\Models\Store;
use App\Models\Team;
use App\Models\RetailerUserTxnHistory;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class BarcodeController extends Controller
{
    /**
      * This method is to get barcode details
      *
      */
 public function demoindex(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => ['required'],
            'user_id' =>['required'],
        ]);

        if (!$validator->fails()) {
            $code = $request->code;
            $userId =$request->user_id;
            $barcode=RetailerBarcode::where('code',$code)->first();
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
							    $userExist=RetailerUser::where('id',$userId)->first();
								if(!$userExist){
									return response()->json(['error'=>false, 'resp'=>'User is invalid']);
								}else{
									$user=RetailerUser::findOrFail($userId);
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
									$barcodeDetails=RetailerBarcode::findOrFail($barcode->id);
									$barcodeDetails->no_of_usage = $barcode->no_of_usage+1;
									$barcodeDetails->save();
								}
						    }
					    }
					}
				}
                return response()->json(['error'=>false, 'resp'=>'QR code data fetched successfully,you have earned' .$barcode->amount.' Luxcozi currency','data'=>$barcode]);
            }
        
		} else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
   
    }
	
	public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => ['required'],
            'user_id' =>['required'],
        ]);

        if (!$validator->fails()) {
            $codeExp=explode(',', $request->code);
			$code=$codeExp[0];
            $userId =$request->user_id;
            $state1=['2','22','13','14'];
            $state2=['17','7','25'];
            $storeExist=Store::where('id',$userId)->where('status',1)->first();
            if (in_array($storeExist->state_id, $state1)) {
                
                $barcode=RetailerBarcode::where('code',$code)->first();
                $distributor=$barcode->distributor_id;
                $givenState=$barcode->state_id;
                if(!empty($givenState)){
                    if($storeExist->state_id != $givenState){
                            return response()->json(['error'=>true, 'resp'=>'Sorry,Wrong State! Store is mapped with different state']);
                    }else{
                        if(!empty($distributor)){
                            $storeDistributor=Store::select('teams.distributor_id')->join('teams','teams.store_id','=','stores.id')->where('stores.id','=',$userId)->orderby('teams.id','desc')->first();
                            if (!in_array($distributor, explode(',', $storeDistributor->distributor_id))) {
                                return response()->json(['error'=>true, 'resp'=>'Sorry,Wrong Distributor! Store is mapped with different distributor']);
                            }else{
                                
                                if(!$barcode){
                                    return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is invalid']);
                                }else{
                    				if ($barcode->start_date > \Carbon\Carbon::now()) {
                                        return response()->json(['error'=>true, 'resp'=>'QRCodes is not valid now']);
                                    }else{
                                    // coupon code validity check
                    					if ($barcode->end_date < \Carbon\Carbon::now() || $barcode->status == 0) {
                    						return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is expired']);
                    					}else{
                    					    $maxtimeusage = RetailerWalletTxn::where('user_id',$userId)->where('type',1)->whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->count();
                            					    $stateData=['14'];
                            					    $distributorStore = ['1745'];
                            					    $distributorId = implode(',', $distributorStore); // Convert array to string
                        
                                                    $distributorStore = Team::whereRaw("find_in_set(?, teams.distributor_id)", [$distributorId])
                                                        ->pluck('store_id')
                                                        ->toArray();
                            					    $storeId=['32823','1862','26687','27854','2742','25758','38792','32328','12432','3932','26544','38125','26135','26940','39883','26805','26922','37221','26918','38673','37996','12432','61351','39827','46570','46218','61352','61354','61355','61356','32328','40962','40981','35707','30312','54054','52086'];
                            					    $extraStore=['49746','61775','75805','65237','63448','26544','25831','37882','26479','26755','26764','37221','26017','38673','41938','26135','26805','39883','43319','26922','26918','26499','26849','41969','44802','52227','52502','62464','62841','65378','74949','77875','60859','76553','40178','38479','39664','39664','19801','20005','77610'];
                            					    $currentMonth = Carbon::now()->format('Y-m');
                                                    $storeLimits = DB::table('store_limits')
                                                    ->where('month', $currentMonth)
                                                    ->get()->toArray();
                                                    $storeLimitMap = [];
                                                    foreach ($storeLimits as $storeLimit) {
                                                        $storeLimitMap[$storeLimit->store_id] = $storeLimit->limit;
                                                    }
                            					    $limit='';
                            					   // if (in_array($storeExist->state_id, $stateData)) {
                            					   //     $limit=20;
                            					   // }else if(in_array($storeExist->id, $storeId)){
                            					   //     $limit=20;
                            					    if(in_array($storeExist->id, $extraStore)){
                            					        $limit=10;
                            					    }elseif (isset($storeLimitMap[$userId])) {
                                                        $limit = $storeLimitMap[$userId];
                            					    }elseif(in_array($storeExist->state_id, $stateData)){
                            					        $limit=20;
                            					    }elseif(in_array($storeExist->id, $distributorStore)){
                            					        $limit=75;
                            					    }else{
                            					        $limit=10;
                            					    }
                    					    if ($maxtimeusage >= $limit) {
                                                     return response()->json(['error'=>true, 'resp'=>'Sorry! You have reached your monthly limit']);
                                            }else{
                    						//no of usage check
                        						if ($barcode->no_of_usage == $barcode->max_time_of_use || $barcode->no_of_usage >= $barcode->max_time_of_use){
                        							return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                        						}else{
                        							 $usage = RetailerWalletTxn::where('barcode_id',$barcode->id)->where('user_id',$userId)->count();
                                                     if ($usage == $barcode->max_time_one_can_use || $usage >= $barcode->max_time_one_can_use) {
                                                         return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                                                    }else{
                        							    $userExist=Store::where('id',$userId)->where('status',1)->first();
                        								if(!$userExist){
                        									return response()->json(['error'=>true, 'resp'=>'User is invalid']);
                        								}else{
                        									
                        									$userAmount=RetailerWalletTxn::where('user_id',$userId)->orderby('id','desc')->first();
                        									$walletTxn=new RetailerWalletTxn();
                        									$walletTxn->user_id = $userId;
                        									$walletTxn->barcode_id = $barcode->id;
                        									$walletTxn->barcode = $barcode->code;
                        									$walletTxn->amount = 400;
                        									$walletTxn->type = 1 ?? '';
                        									if(!$userAmount)
                        										$walletTxn->final_amount += 400 ?? '';
                        									else
                        									$walletTxn->final_amount = $userAmount->final_amount+ 400 ?? '';
                        									$walletTxn->created_at = date('Y-m-d H:i:s');
                        									$walletTxn->updated_at = date('Y-m-d H:i:s');
                        									$walletTxn->save();
                        									$userwalletTxn=new RetailerUserTxnHistory();
                        									$userwalletTxn->user_id = $userId;
                        									$userwalletTxn->barcode_id = $barcode->id;
                        									$userwalletTxn->barcode = $barcode->code;
                        									$userwalletTxn->amount = 400;
                        									$userwalletTxn->type = 'Qrcode scan' ?? '';
                        									$userwalletTxn->title = '400 points earn';
                        									$userwalletTxn->description = 'Using '.$barcode->code.' code';
                        									$userwalletTxn->status = 'increment';
                        									$userwalletTxn->created_at = date('Y-m-d H:i:s');
                        									$userwalletTxn->updated_at = date('Y-m-d H:i:s');
                        									$userwalletTxn->save();
                        									$barcodeDetails=RetailerBarcode::findOrFail($barcode->id);
                        									$barcodeDetails->no_of_usage = $barcode->no_of_usage+1;
                        									$barcodeDetails->save();
                        									$user=Store::findOrFail($userId);
                        									$user->wallet += 400;
                        									$user->save();
                        						    	}
                        							}
                    						    }
                    					    }
                    					}
                    				}
                                
                                
                               return response()->json(['error'=>false, 'resp'=>'Coupon scanned successfully ; 400 Cozi currency has been added to your wallet','data'=>$barcode]);
                                }
                            }
                        }else{
                                    //barcode exist check
                                    if(!$barcode){
                                        return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is invalid']);
                                    }else{
                        				if ($barcode->start_date > \Carbon\Carbon::now()) {
                                            return response()->json(['error'=>true, 'resp'=>'QRCodes is not valid now']);
                                        }else{
                                        // coupon code validity check
                        					if ($barcode->end_date < \Carbon\Carbon::now() || $barcode->status == 0) {
                        						return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is expired']);
                        					}else{
                        					    $maxtimeusage = RetailerWalletTxn::where('user_id',$userId)->where('type',1)->whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->count();
                        					        $stateData=['14'];
                        					        $distributorStore = ['1745'];
                            					    $distributorId = implode(',', $distributorStore); // Convert array to string
                        
                                                    $distributorStore = Team::whereRaw("find_in_set(?, teams.distributor_id)", [$distributorId])
                                                        ->pluck('store_id')
                                                        ->toArray();
                        					        $storeId=['32823','1862','26687','27854','2742','25758','38792','32328','12432','3932','26544','38125','26135','26940','39883','26805','26922','37221','26918','38673','37996','12432','61351','39827','46570','46218','61352','61354','61355','61356','32328','40962','40981','35707','30312','54054','52086'];
                            					    $extraStore=['49746','61775','75805','65237','63448','26544','25831','37882','26479','26755','26764','37221','26017','38673','41938','26135','26805','39883','43319','26922','26918','26499','26849','41969','44802','52227','52502','62464','62841','65378','74949','77875','60859','76553','40178','38479','39664','39664','19801','20005','77610'];
                            					    $currentMonth = Carbon::now()->format('Y-m');
                                                    $storeLimits = DB::table('store_limits')
                                                    ->where('month', $currentMonth)
                                                    ->get()->toArray();
                                                    $storeLimitMap = [];
                                                    foreach ($storeLimits as $storeLimit) {
                                                        $storeLimitMap[$storeLimit->store_id] = $storeLimit->limit;
                                                    }
                            					    $limit='';
                            					   // if (in_array($storeExist->state_id, $stateData)) {
                            					   //     $limit=20;
                            					   // }else if(in_array($storeExist->id, $storeId)){
                                    //                     $limit=20;
                            					    if(in_array($storeExist->id, $extraStore)){
                                                         $limit=10;
                            					    }elseif (isset($storeLimitMap[$userId])) {
                                                        $limit = $storeLimitMap[$userId];
                            					    }elseif(in_array($storeExist->state_id, $stateData)){
                            					        $limit=20;
                            					    }elseif(in_array($storeExist->id, $distributorStore)){
                            					        $limit=75;
                            					    }else{
                            					        $limit=10;
                            					    }
                        					    if ($maxtimeusage >= $limit) {
                                                         return response()->json(['error'=>true, 'resp'=>'Sorry! You have reached your monthly limit']);
                                                }else{
                        						//no of usage check
                            						if ($barcode->no_of_usage == $barcode->max_time_of_use || $barcode->no_of_usage >= $barcode->max_time_of_use){
                            							return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                            						}else{
                            							 $usage = RetailerWalletTxn::where('barcode_id',$barcode->id)->where('user_id',$userId)->count();
                                                         if ($usage == $barcode->max_time_one_can_use || $usage >= $barcode->max_time_one_can_use) {
                                                             return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                                                        }else{
                            							    $userExist=Store::where('id',$userId)->where('status',1)->first();
                            								if(!$userExist){
                            									return response()->json(['error'=>true, 'resp'=>'User is invalid']);
                            								}else{
                            									
                            									$userAmount=RetailerWalletTxn::where('user_id',$userId)->orderby('id','desc')->first();
                            									$walletTxn=new RetailerWalletTxn();
                            									$walletTxn->user_id = $userId;
                            									$walletTxn->barcode_id = $barcode->id;
                            									$walletTxn->barcode = $barcode->code;
                            									$walletTxn->amount = 400;
                            									$walletTxn->type = 1 ?? '';
                            									if(!$userAmount)
                            										$walletTxn->final_amount += 400 ?? '';
                            									else
                            									$walletTxn->final_amount = $userAmount->final_amount+ 400 ?? '';
                            									$walletTxn->created_at = date('Y-m-d H:i:s');
                            									$walletTxn->updated_at = date('Y-m-d H:i:s');
                            									$walletTxn->save();
                            									$userwalletTxn=new RetailerUserTxnHistory();
                            									$userwalletTxn->user_id = $userId;
                            									$userwalletTxn->barcode_id = $barcode->id;
                            									$userwalletTxn->barcode = $barcode->code;
                            									$userwalletTxn->amount = 400;
                            									$userwalletTxn->type = 'Qrcode scan' ?? '';
                            									$userwalletTxn->title = '400 points earn';
                            									$userwalletTxn->description = 'Using '.$barcode->code.' code';
                            									$userwalletTxn->status = 'increment';
                            									$userwalletTxn->created_at = date('Y-m-d H:i:s');
                            									$userwalletTxn->updated_at = date('Y-m-d H:i:s');
                            									$userwalletTxn->save();
                            									$barcodeDetails=RetailerBarcode::findOrFail($barcode->id);
                            									$barcodeDetails->no_of_usage = $barcode->no_of_usage+1;
                            									$barcodeDetails->save();
                            									$user=Store::findOrFail($userId);
                            									$user->wallet += 400;
                            									$user->save();
                            						    	}
                            							}
                        						    }
                        					    }
                        					}
                        				}
                                    
                                   return response()->json(['error'=>false, 'resp'=>'Coupon scanned successfully ; 400 Cozi currency has been added to your wallet','data'=>$barcode]);
                                }
                        }
                    }
                }else{
                     if(!empty($distributor)){
                            $storeDistributor=Store::select('teams.distributor_id')->join('teams','teams.store_id','=','stores.id')->where('stores.id','=',$userId)->orderby('teams.id','desc')->first();
                            if (!in_array($distributor, explode(',', $storeDistributor->distributor_id))) {
                                return response()->json(['error'=>true, 'resp'=>'Sorry, Wrong Distributor! Store is mapped with different distributor']);
                            }else{
                                
                                if(!$barcode){
                                    return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is invalid']);
                                }else{
                    				if ($barcode->start_date > \Carbon\Carbon::now()) {
                                        return response()->json(['error'=>true, 'resp'=>'QRCodes is not valid now']);
                                    }else{
                                    // coupon code validity check
                    					if ($barcode->end_date < \Carbon\Carbon::now() || $barcode->status == 0) {
                    						return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is expired']);
                    					}else{
                    					    $maxtimeusage = RetailerWalletTxn::where('user_id',$userId)->where('type',1)->whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->count();
                            					    $stateData=['14'];
                            					    $distributorStore = ['1745'];
                            					    $distributorId = implode(',', $distributorStore); // Convert array to string
                        
                                                    $distributorStore = Team::whereRaw("find_in_set(?, teams.distributor_id)", [$distributorId])
                                                       ->pluck('store_id')
                                                        ->toArray();
                            					    $storeId=['32823','1862','26687','27854','2742','25758','38792','32328','12432','3932','26544','38125','26135','26940','39883','26805','26922','37221','26918','38673','37996','12432','61351','39827','46570','46218','61352','61354','61355','61356','32328','40962','40981','35707','30312','54054','52086'];
                            					     $extraStore=['49746','61775','75805','65237','63448','26544','25831','37882','26479','26755','26764','37221','26017','38673','41938','26135','26805','39883','43319','26922','26918','26499','26849','41969','44802','52227','52502','62464','62841','65378','74949','77875','60859','76553','40178','38479','39664','39664','19801','20005','77610'];
                            					     $currentMonth = Carbon::now()->format('Y-m');
                                                     $storeLimits = DB::table('store_limits')
                                                    ->where('month', $currentMonth)
                                                    ->get()->toArray();
                                                    $storeLimitMap = [];
                                                    foreach ($storeLimits as $storeLimit) {
                                                        $storeLimitMap[$storeLimit->store_id] = $storeLimit->limit;
                                                    }
                            					    $limit='';
                            					   // if (in_array($storeExist->state_id, $stateData)) {
                            					   //     $limit=20;
                            					   // }else if(in_array($storeExist->id, $storeId)){
                            					   //     $limit=20;
                            					    if(in_array($storeExist->id, $extraStore)){
                            					        $limit=10;
                            					    }elseif (isset($storeLimitMap[$userId])) {
                                                        $limit = $storeLimitMap[$userId];
                            					    }elseif(in_array($storeExist->state_id, $stateData)){
                            					        $limit=20;
                            					    }elseif(in_array($storeExist->id, $distributorStore)){
                            					        $limit=75;
                            					    }else{
                            					        $limit=10;
                            					    }
                    					    if ($maxtimeusage >= $limit) {
                                                     return response()->json(['error'=>true, 'resp'=>'Sorry! You have reached your monthly limit']);
                                            }else{
                    						//no of usage check
                        						if ($barcode->no_of_usage == $barcode->max_time_of_use || $barcode->no_of_usage >= $barcode->max_time_of_use){
                        							return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                        						}else{
                        							 $usage = RetailerWalletTxn::where('barcode_id',$barcode->id)->where('user_id',$userId)->count();
                                                     if ($usage == $barcode->max_time_one_can_use || $usage >= $barcode->max_time_one_can_use) {
                                                         return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                                                    }else{
                        							    $userExist=Store::where('id',$userId)->where('status',1)->first();
                        								if(!$userExist){
                        									return response()->json(['error'=>true, 'resp'=>'User is invalid']);
                        								}else{
                        									
                        									$userAmount=RetailerWalletTxn::where('user_id',$userId)->orderby('id','desc')->first();
                        									$walletTxn=new RetailerWalletTxn();
                        									$walletTxn->user_id = $userId;
                        									$walletTxn->barcode_id = $barcode->id;
                        									$walletTxn->barcode = $barcode->code;
                        									$walletTxn->amount = 400;
                        									$walletTxn->type = 1 ?? '';
                        									if(!$userAmount)
                        										$walletTxn->final_amount += 400 ?? '';
                        									else
                        									$walletTxn->final_amount = $userAmount->final_amount+ 400 ?? '';
                        									$walletTxn->created_at = date('Y-m-d H:i:s');
                        									$walletTxn->updated_at = date('Y-m-d H:i:s');
                        									$walletTxn->save();
                        									$userwalletTxn=new RetailerUserTxnHistory();
                        									$userwalletTxn->user_id = $userId;
                        									$userwalletTxn->barcode_id = $barcode->id;
                        									$userwalletTxn->barcode = $barcode->code;
                        									$userwalletTxn->amount = 400;
                        									$userwalletTxn->type = 'Qrcode scan' ?? '';
                        									$userwalletTxn->title = '400 points earn';
                        									$userwalletTxn->description = 'Using '.$barcode->code.' code';
                        									$userwalletTxn->status = 'increment';
                        									$userwalletTxn->created_at = date('Y-m-d H:i:s');
                        									$userwalletTxn->updated_at = date('Y-m-d H:i:s');
                        									$userwalletTxn->save();
                        									$barcodeDetails=RetailerBarcode::findOrFail($barcode->id);
                        									$barcodeDetails->no_of_usage = $barcode->no_of_usage+1;
                        									$barcodeDetails->save();
                        									$user=Store::findOrFail($userId);
                        									$user->wallet += 400;
                        									$user->save();
                        						    	}
                        							}
                    						    }
                    					    }
                    					}
                    				}
                                
                                
                               return response()->json(['error'=>false, 'resp'=>'Coupon scanned successfully ; 400 Cozi currency has been added to your wallet','data'=>$barcode]);
                                }
                            }
                        }else{
                                    //barcode exist check
                                    if(!$barcode){
                                        return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is invalid']);
                                    }else{
                        				if ($barcode->start_date > \Carbon\Carbon::now()) {
                                            return response()->json(['error'=>true, 'resp'=>'QRCodes is not valid now']);
                                        }else{
                                        // coupon code validity check
                        					if ($barcode->end_date < \Carbon\Carbon::now() || $barcode->status == 0) {
                        						return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is expired']);
                        					}else{
                        					    $maxtimeusage = RetailerWalletTxn::where('user_id',$userId)->where('type',1)->whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->count();
                        					        $stateData=['14'];
                        					        $distributorStore = ['1745'];
                            					    $distributorId = implode(',', $distributorStore); // Convert array to string
                        
                                                    $distributorStore = Team::whereRaw("find_in_set(?, teams.distributor_id)", [$distributorId])
                                                        ->pluck('store_id')
                                                        ->toArray();
                        					        $storeId=['32823','1862','26687','27854','2742','25758','38792','32328','12432','3932','26544','38125','26135','26940','39883','26805','26922','37221','26918','38673','37996','12432','61351','39827','46570','46218','61352','61354','61355','61356','32328','40962','40981','35707','30312','54054','52086'];
                            					    $extraStore=['49746','61775','75805','65237','63448','26544','25831','37882','26479','26755','26764','37221','26017','38673','41938','26135','26805','39883','43319','26922','26918','26499','26849','41969','44802','52227','52502','62464','62841','65378','74949','77875','60859','76553','40178','38479','39664','39664','19801','20005','77610'];
                            					    $currentMonth = Carbon::now()->format('Y-m');
                                                    $storeLimits = DB::table('store_limits')
                                                    ->where('month', $currentMonth)
                                                    ->get()->toArray();
                                                    $storeLimitMap = [];
                                                    foreach ($storeLimits as $storeLimit) {
                                                        $storeLimitMap[$storeLimit->store_id] = $storeLimit->limit;
                                                    }
                            					    $limit='';
                            					   // if (in_array($storeExist->state_id, $stateData)) {
                            					   //     $limit=20;
                            					   // }else if(in_array($storeExist->id, $storeId)){
                                    //                     $limit=20;
                            					    if(in_array($storeExist->id, $extraStore)){
                                                         $limit=10;
                            					    }elseif (isset($storeLimitMap[$userId])) {
                                                        $limit = $storeLimitMap[$userId];
                            					    }elseif(in_array($storeExist->state_id, $stateData)){
                            					        $limit=20;
                            					    }elseif(in_array($storeExist->id, $distributorStore)){
                            					        $limit=75;
                            					    }else{
                            					        $limit=10;
                            					    }
                        					    if ($maxtimeusage >= $limit) {
                                                         return response()->json(['error'=>true, 'resp'=>'Sorry! You have reached your monthly limit']);
                                                }else{
                        						//no of usage check
                            						if ($barcode->no_of_usage == $barcode->max_time_of_use || $barcode->no_of_usage >= $barcode->max_time_of_use){
                            							return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                            						}else{
                            							 $usage = RetailerWalletTxn::where('barcode_id',$barcode->id)->where('user_id',$userId)->count();
                                                         if ($usage == $barcode->max_time_one_can_use || $usage >= $barcode->max_time_one_can_use) {
                                                             return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                                                        }else{
                            							    $userExist=Store::where('id',$userId)->where('status',1)->first();
                            								if(!$userExist){
                            									return response()->json(['error'=>true, 'resp'=>'User is invalid']);
                            								}else{
                            									
                            									$userAmount=RetailerWalletTxn::where('user_id',$userId)->orderby('id','desc')->first();
                            									$walletTxn=new RetailerWalletTxn();
                            									$walletTxn->user_id = $userId;
                            									$walletTxn->barcode_id = $barcode->id;
                            									$walletTxn->barcode = $barcode->code;
                            									$walletTxn->amount = 400;
                            									$walletTxn->type = 1 ?? '';
                            									if(!$userAmount)
                            										$walletTxn->final_amount += 400 ?? '';
                            									else
                            									$walletTxn->final_amount = $userAmount->final_amount+ 400 ?? '';
                            									$walletTxn->created_at = date('Y-m-d H:i:s');
                            									$walletTxn->updated_at = date('Y-m-d H:i:s');
                            									$walletTxn->save();
                            									$userwalletTxn=new RetailerUserTxnHistory();
                            									$userwalletTxn->user_id = $userId;
                            									$userwalletTxn->barcode_id = $barcode->id;
                            									$userwalletTxn->barcode = $barcode->code;
                            									$userwalletTxn->amount = 400;
                            									$userwalletTxn->type = 'Qrcode scan' ?? '';
                            									$userwalletTxn->title = '400 points earn';
                            									$userwalletTxn->description = 'Using '.$barcode->code.' code';
                            									$userwalletTxn->status = 'increment';
                            									$userwalletTxn->created_at = date('Y-m-d H:i:s');
                            									$userwalletTxn->updated_at = date('Y-m-d H:i:s');
                            									$userwalletTxn->save();
                            									$barcodeDetails=RetailerBarcode::findOrFail($barcode->id);
                            									$barcodeDetails->no_of_usage = $barcode->no_of_usage+1;
                            									$barcodeDetails->save();
                            									$user=Store::findOrFail($userId);
                            									$user->wallet += 400;
                            									$user->save();
                            						    	}
                            							}
                        						    }
                        					    }
                        					}
                        				}
                                    
                                   return response()->json(['error'=>false, 'resp'=>'Coupon scanned successfully ; 400 Cozi currency has been added to your wallet','data'=>$barcode]);
                                }
                        }
                }
            }else if (in_array($storeExist->state_id, $state2)) {
                $barcode=RetailerBarcode::where('code',$code)->first();
                $distributor=$barcode->distributor_id;
                $givenState=$barcode->state_id;
                if(!empty($givenState)){
                    if($storeExist->state_id != $givenState){
                            return response()->json(['error'=>true, 'resp'=>'Sorry, Wrong State! Store is mapped with different state']);
                    }else{
                        if(!empty($distributor)){
                            $storeDistributor=Store::select('teams.distributor_id')->join('teams','teams.store_id','=','stores.id')->where('stores.id','=',$userId)->orderby('teams.id','desc')->first();
                            if (!in_array($distributor, explode(',', $storeDistributor->distributor_id))) {
                                return response()->json(['error'=>true, 'resp'=>'Sorry, Wrong Distributor! Store is mapped with different distributor']);
                            }else{
                                
                                if(!$barcode){
                                    return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is invalid']);
                                }else{
                    				if ($barcode->start_date > \Carbon\Carbon::now()) {
                                        return response()->json(['error'=>true, 'resp'=>'QRCodes is not valid now']);
                                    }else{
                                    // coupon code validity check
                    					if ($barcode->end_date < \Carbon\Carbon::now() || $barcode->status == 0) {
                    						return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is expired']);
                    					}else{
                    					    $maxtimeusage = RetailerWalletTxn::where('user_id',$userId)->where('type',1)->whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->count();
                    					            $stateData=['14'];
                    					            $distributorStore = ['1745'];
                            					    $distributorId = implode(',', $distributorStore); // Convert array to string
                        
                                                    $distributorStore = Team::whereRaw("find_in_set(?, teams.distributor_id)", [$distributorId])
                                                        ->pluck('store_id')
                                                        ->toArray();
                    					            $storeId=['32823','1862','26687','27854','2742','25758','38792','32328','12432','3932','26544','38125','26135','26940','39883','26805','26922','37221','26918','38673','37996','12432','61351','39827','46570','46218','61352','61354','61355','61356','32328','40962','40981','35707','30312','54054','52086'];
                            					    $extraStore=['49746','61775','75805','65237','63448','26544','25831','37882','26479','26755','26764','37221','26017','38673','41938','26135','26805','39883','43319','26922','26918','26499','26849','41969','44802','52227','52502','62464','62841','65378','74949','77875','60859','76553','40178','38479','39664','39664','19801','20005','77610'];
                            					    $currentMonth = Carbon::now()->format('Y-m');
                                                    $storeLimits = DB::table('store_limits')
                                                    ->where('month', $currentMonth)
                                                    ->get()->toArray();
                                                    $storeLimitMap = [];
                                                    foreach ($storeLimits as $storeLimit) {
                                                        $storeLimitMap[$storeLimit->store_id] = $storeLimit->limit;
                                                    }
                            					    $limit='';
                            					   // if (in_array($storeExist->state_id, $stateData)) {
                            					   //     $limit=20;
                            					   // }else if(in_array($storeExist->id, $storeId)){
                                    //                     $limit=20;
                            					    if(in_array($storeExist->id, $extraStore)){
                                                         $limit=10;
                            					    }elseif (isset($storeLimitMap[$userId])) {
                                                        $limit = $storeLimitMap[$userId];
                            					    }elseif(in_array($storeExist->state_id, $stateData)){
                            					        $limit=20;
                            					    }elseif(in_array($storeExist->id, $distributorStore)){
                            					        $limit=75;
                            					    }else{
                            					        $limit=10;
                            					    }
                    					    if ($maxtimeusage >= $limit) {
                                                     return response()->json(['error'=>true, 'resp'=>'Sorry! You have reached your monthly limit']);
                                            }else{
                    						//no of usage check
                        						if ($barcode->no_of_usage == $barcode->max_time_of_use || $barcode->no_of_usage >= $barcode->max_time_of_use){
                        							return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                        						}else{
                        							 $usage = RetailerWalletTxn::where('barcode_id',$barcode->id)->where('user_id',$userId)->count();
                                                     if ($usage == $barcode->max_time_one_can_use || $usage >= $barcode->max_time_one_can_use) {
                                                         return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                                                    }else{
                        							    $userExist=Store::where('id',$userId)->where('status',1)->first();
                        								if(!$userExist){
                        									return response()->json(['error'=>true, 'resp'=>'User is invalid']);
                        								}else{
                        									
                        									$userAmount=RetailerWalletTxn::where('user_id',$userId)->orderby('id','desc')->first();
                        									$walletTxn=new RetailerWalletTxn();
                        									$walletTxn->user_id = $userId;
                        									$walletTxn->barcode_id = $barcode->id;
                        									$walletTxn->barcode = $barcode->code;
                        									$walletTxn->amount = 500;
                        									$walletTxn->type = 1 ?? '';
                        									if(!$userAmount)
                        										$walletTxn->final_amount += 500 ?? '';
                        									else
                        									$walletTxn->final_amount = $userAmount->final_amount+ 500 ?? '';
                        									$walletTxn->created_at = date('Y-m-d H:i:s');
                        									$walletTxn->updated_at = date('Y-m-d H:i:s');
                        									$walletTxn->save();
                        									$userwalletTxn=new RetailerUserTxnHistory();
                        									$userwalletTxn->user_id = $userId;
                        									$userwalletTxn->barcode_id = $barcode->id;
                        									$userwalletTxn->barcode = $barcode->code;
                        									$userwalletTxn->amount = 500;
                        									$userwalletTxn->type = 'Qrcode scan' ?? '';
                        									$userwalletTxn->title = '500 points earn';
                        									$userwalletTxn->description = 'Using '.$barcode->code.' code';
                        									$userwalletTxn->status = 'increment';
                        									$userwalletTxn->created_at = date('Y-m-d H:i:s');
                        									$userwalletTxn->updated_at = date('Y-m-d H:i:s');
                        									$userwalletTxn->save();
                        									$barcodeDetails=RetailerBarcode::findOrFail($barcode->id);
                        									$barcodeDetails->no_of_usage = $barcode->no_of_usage+1;
                        									$barcodeDetails->save();
                        									$user=Store::findOrFail($userId);
                        									$user->wallet += 500;
                        									$user->save();
                        						    	}
                        							}
                    						    }
                    					    }
                    					}
                    				}
                                
                                
                               return response()->json(['error'=>false, 'resp'=>'Coupon scanned successfully ; 500 Cozi currency has been added to your wallet','data'=>$barcode]);
                                }
                            }
                        }else{
                                    //barcode exist check
                                    if(!$barcode){
                                        return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is invalid']);
                                    }else{
                        				if ($barcode->start_date > \Carbon\Carbon::now()) {
                                            return response()->json(['error'=>true, 'resp'=>'QRCodes is not valid now']);
                                        }else{
                                        // coupon code validity check
                        					if ($barcode->end_date < \Carbon\Carbon::now() || $barcode->status == 0) {
                        						return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is expired']);
                        					}else{
                        					    $maxtimeusage = RetailerWalletTxn::where('user_id',$userId)->where('type',1)->whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->count();
                        					        $stateData=['14'];
                        					        $distributorStore = ['1745'];
                            					    $distributorId = implode(',', $distributorStore); // Convert array to string
                        
                                                    $distributorStore = Team::whereRaw("find_in_set(?, teams.distributor_id)", [$distributorId])
                                                        ->pluck('store_id')
                                                        ->toArray();
                        					        $storeId=['32823','1862','26687','27854','2742','25758','38792','32328','12432','3932','26544','38125','26135','26940','39883','26805','26922','37221','26918','38673','37996','12432','61351','39827','46570','46218','61352','61354','61355','61356','32328','40962','40981','35707','30312','54054','52086'];
                            					    $extraStore=['49746','61775','75805','65237','63448','26544','25831','37882','26479','26755','26764','37221','26017','38673','41938','26135','26805','39883','43319','26922','26918','26499','26849','41969','44802','52227','52502','62464','62841','65378','74949','77875','60859','76553','40178','38479','39664','39664','19801','20005','77610'];
                            					    $currentMonth = Carbon::now()->format('Y-m');
                                                    $storeLimits = DB::table('store_limits')
                                                    ->where('month', $currentMonth)
                                                    ->get()->toArray();
                                                    $storeLimitMap = [];
                                                    foreach ($storeLimits as $storeLimit) {
                                                        $storeLimitMap[$storeLimit->store_id] = $storeLimit->limit;
                                                    }
                            					    $limit='';
                            					   // if (in_array($storeExist->state_id, $stateData)) {
                            					   //     $limit=20;
                            					   // }else if(in_array($storeExist->id, $storeId)){
                                    //                     $limit=20;
                            					    if(in_array($storeExist->id, $extraStore)){
                                                         $limit=10;
                            					    }elseif (isset($storeLimitMap[$userId])) {
                                                        $limit = $storeLimitMap[$userId];
                            					    }elseif(in_array($storeExist->state_id, $stateData)){
                            					        $limit=20;
                            					    }elseif(in_array($storeExist->id, $distributorStore)){
                            					        $limit=75;
                            					    }else{
                            					        $limit=10;
                            					    }
                        					    if ($maxtimeusage >= $limit) {
                                                         return response()->json(['error'=>true, 'resp'=>'Sorry! You have reached your monthly limit']);
                                                }else{
                        						//no of usage check
                            						if ($barcode->no_of_usage == $barcode->max_time_of_use || $barcode->no_of_usage >= $barcode->max_time_of_use){
                            							return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                            						}else{
                            							 $usage = RetailerWalletTxn::where('barcode_id',$barcode->id)->where('user_id',$userId)->count();
                                                         if ($usage == $barcode->max_time_one_can_use || $usage >= $barcode->max_time_one_can_use) {
                                                             return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                                                        }else{
                            							    $userExist=Store::where('id',$userId)->where('status',1)->first();
                            								if(!$userExist){
                            									return response()->json(['error'=>true, 'resp'=>'User is invalid']);
                            								}else{
                            									
                            									$userAmount=RetailerWalletTxn::where('user_id',$userId)->orderby('id','desc')->first();
                            									$walletTxn=new RetailerWalletTxn();
                            									$walletTxn->user_id = $userId;
                            									$walletTxn->barcode_id = $barcode->id;
                            									$walletTxn->barcode = $barcode->code;
                            									$walletTxn->amount = 500;
                            									$walletTxn->type = 1 ?? '';
                            									if(!$userAmount)
                            										$walletTxn->final_amount += 500 ?? '';
                            									else
                            									$walletTxn->final_amount = $userAmount->final_amount+ 500 ?? '';
                            									$walletTxn->created_at = date('Y-m-d H:i:s');
                            									$walletTxn->updated_at = date('Y-m-d H:i:s');
                            									$walletTxn->save();
                            									$userwalletTxn=new RetailerUserTxnHistory();
                            									$userwalletTxn->user_id = $userId;
                            									$userwalletTxn->barcode_id = $barcode->id;
                            									$userwalletTxn->barcode = $barcode->code;
                            									$userwalletTxn->amount = 500;
                            									$userwalletTxn->type = 'Qrcode scan' ?? '';
                            									$userwalletTxn->title ='500 points earn';
                            									$userwalletTxn->description = 'Using '.$barcode->code.' code';
                            									$userwalletTxn->status = 'increment';
                            									$userwalletTxn->created_at = date('Y-m-d H:i:s');
                            									$userwalletTxn->updated_at = date('Y-m-d H:i:s');
                            									$userwalletTxn->save();
                            									$barcodeDetails=RetailerBarcode::findOrFail($barcode->id);
                            									$barcodeDetails->no_of_usage = $barcode->no_of_usage+1;
                            									$barcodeDetails->save();
                            									$user=Store::findOrFail($userId);
                            									$user->wallet += 500;
                            									$user->save();
                            						    	}
                            							}
                        						    }
                        					    }
                        					}
                        				}
                                    
                                   return response()->json(['error'=>false, 'resp'=>'Coupon scanned successfully ; 500 Cozi currency has been added to your wallet','data'=>$barcode]);
                                }
                        }
                    }
                }else{
                    
                     if(!empty($distributor)){
                            $storeDistributor=Store::select('teams.distributor_id')->join('teams','teams.store_id','=','stores.id')->where('stores.id','=',$userId)->orderby('teams.id','desc')->first();
                            if (!in_array($distributor, explode(',', $storeDistributor->distributor_id))) {
                                return response()->json(['error'=>true, 'resp'=>'Sorry, Wrong Distributor! Store is mapped with different distributor']);
                            }else{
                                
                                if(!$barcode){
                                    return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is invalid']);
                                }else{
                    				if ($barcode->start_date > \Carbon\Carbon::now()) {
                                        return response()->json(['error'=>true, 'resp'=>'QRCodes is not valid now']);
                                    }else{
                                    // coupon code validity check
                    					if ($barcode->end_date < \Carbon\Carbon::now() || $barcode->status == 0) {
                    						return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is expired']);
                    					}else{
                    					    $maxtimeusage = RetailerWalletTxn::where('user_id',$userId)->where('type',1)->whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->count();
                    					            $stateData=['14'];
                    					            $distributorStore = ['1745'];
                            					    $distributorId = implode(',', $distributorStore); // Convert array to string
                        
                                                    $distributorStore = Team::whereRaw("find_in_set(?, teams.distributor_id)", [$distributorId])
                                                       ->pluck('store_id')
                                                        ->toArray();
                    					            $storeId=['32823','1862','26687','27854','2742','25758','38792','32328','12432','3932','26544','38125','26135','26940','39883','26805','26922','37221','26918','38673','37996','12432','61351','39827','46570','46218','61352','61354','61355','61356','32328','40962','40981','35707','30312','54054','52086'];
                            					    $extraStore=['49746','61775','75805','65237','63448','26544','25831','37882','26479','26755','26764','37221','26017','38673','41938','26135','26805','39883','43319','26922','26918','26499','26849','41969','44802','52227','52502','62464','62841','65378','74949','77875','60859','76553','40178','38479','39664','39664','19801','20005','77610'];
                            					    $currentMonth = Carbon::now()->format('Y-m');
                                                    $storeLimits = DB::table('store_limits')
                                                    ->where('month', $currentMonth)
                                                    ->get()->toArray();
                                                    $storeLimitMap = [];
                                                    foreach ($storeLimits as $storeLimit) {
                                                        $storeLimitMap[$storeLimit->store_id] = $storeLimit->limit;
                                                    }
                            					    $limit='';
                            					   // if (in_array($storeExist->state_id, $stateData)) {
                            					   //     $limit=20;
                            					   // }else if(in_array($storeExist->id, $storeId)){
                                    //                     $limit=20;
                            					    if(in_array($storeExist->id, $extraStore)){
                                                         $limit=10;
                            					    }elseif (isset($storeLimitMap[$userId])) {
                                                        $limit = $storeLimitMap[$userId];
                            					    }elseif(in_array($storeExist->state_id, $stateData)){
                            					        $limit=20;
                            					    }elseif(in_array($storeExist->id, $distributorStore)){
                            					        $limit=75;
                            					    }else{
                            					        $limit=10;
                            					    }
                    					    if ($maxtimeusage >= $limit) {
                                                     return response()->json(['error'=>true, 'resp'=>'Sorry! You have reached your monthly limit']);
                                            }else{
                    						//no of usage check
                        						if ($barcode->no_of_usage == $barcode->max_time_of_use || $barcode->no_of_usage >= $barcode->max_time_of_use){
                        							return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                        						}else{
                        							 $usage = RetailerWalletTxn::where('barcode_id',$barcode->id)->where('user_id',$userId)->count();
                                                     if ($usage == $barcode->max_time_one_can_use || $usage >= $barcode->max_time_one_can_use) {
                                                         return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                                                    }else{
                        							    $userExist=Store::where('id',$userId)->where('status',1)->first();
                        								if(!$userExist){
                        									return response()->json(['error'=>true, 'resp'=>'User is invalid']);
                        								}else{
                        									
                        									$userAmount=RetailerWalletTxn::where('user_id',$userId)->orderby('id','desc')->first();
                        									$walletTxn=new RetailerWalletTxn();
                        									$walletTxn->user_id = $userId;
                        									$walletTxn->barcode_id = $barcode->id;
                        									$walletTxn->barcode = $barcode->code;
                        									$walletTxn->amount = 500;
                        									$walletTxn->type = 1 ?? '';
                        									if(!$userAmount)
                        										$walletTxn->final_amount += 500 ?? '';
                        									else
                        									$walletTxn->final_amount = $userAmount->final_amount+ 500 ?? '';
                        									$walletTxn->created_at = date('Y-m-d H:i:s');
                        									$walletTxn->updated_at = date('Y-m-d H:i:s');
                        									$walletTxn->save();
                        									$userwalletTxn=new RetailerUserTxnHistory();
                        									$userwalletTxn->user_id = $userId;
                        									$userwalletTxn->barcode_id = $barcode->id;
                        									$userwalletTxn->barcode = $barcode->code;
                        									$userwalletTxn->amount = 500;
                        									$userwalletTxn->type = 'Qrcode scan' ?? '';
                        									$userwalletTxn->title = '500 points earn';
                        									$userwalletTxn->description = 'Using '.$barcode->code.' code';
                        									$userwalletTxn->status = 'increment';
                        									$userwalletTxn->created_at = date('Y-m-d H:i:s');
                        									$userwalletTxn->updated_at = date('Y-m-d H:i:s');
                        									$userwalletTxn->save();
                        									$barcodeDetails=RetailerBarcode::findOrFail($barcode->id);
                        									$barcodeDetails->no_of_usage = $barcode->no_of_usage+1;
                        									$barcodeDetails->save();
                        									$user=Store::findOrFail($userId);
                        									$user->wallet += 500;
                        									$user->save();
                        						    	}
                        							}
                    						    }
                    					    }
                    					}
                    				}
                                
                                
                               return response()->json(['error'=>false, 'resp'=>'Coupon scanned successfully ; 500 Cozi currency has been added to your wallet','data'=>$barcode]);
                                }
                            }
                        }else{
                                    //barcode exist check
                                    if(!$barcode){
                                        return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is invalid']);
                                    }else{
                        				if ($barcode->start_date > \Carbon\Carbon::now()) {
                                            return response()->json(['error'=>true, 'resp'=>'QRCodes is not valid now']);
                                        }else{
                                        // coupon code validity check
                        					if ($barcode->end_date < \Carbon\Carbon::now() || $barcode->status == 0) {
                        						return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is expired']);
                        					}else{
                        					    $maxtimeusage = RetailerWalletTxn::where('user_id',$userId)->where('type',1)->whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->count();
                        					        $stateData=['14'];
                        					        $distributorStore = ['1745'];
                            					    $distributorId = implode(',', $distributorStore); // Convert array to string
                        
                                                    $distributorStore = Team::whereRaw("find_in_set(?, teams.distributor_id)", [$distributorId])
                                                        ->pluck('store_id')
                                                        ->toArray();
                        					        $storeId=['32823','1862','26687','27854','2742','25758','38792','32328','12432','3932','26544','38125','26135','26940','39883','26805','26922','37221','26918','38673','37996','12432','61351','39827','46570','46218','61352','61354','61355','61356','32328','40962','40981','35707','30312','54054','52086'];
                            					   $extraStore=['49746','61775','75805','65237','63448','26544','25831','37882','26479','26755','26764','37221','26017','38673','41938','26135','26805','39883','43319','26922','26918','26499','26849','41969','44802','52227','52502','62464','62841','65378','74949','77875','60859','76553','40178','38479','39664','39664','19801','20005','77610'];
                            					    $currentMonth = Carbon::now()->format('Y-m');
                                                    $storeLimits = DB::table('store_limits')
                                                    ->where('month', $currentMonth)
                                                    ->get()->toArray();
                                                    $storeLimitMap = [];
                                                    foreach ($storeLimits as $storeLimit) {
                                                        $storeLimitMap[$storeLimit->store_id] = $storeLimit->limit;
                                                    }
                            					    $limit='';
                            					   // if (in_array($storeExist->state_id, $stateData)) {
                            					   //     $limit=20;
                            					   // }else if(in_array($storeExist->id, $storeId)){
                                    //                     $limit=20;
                            					    if(in_array($storeExist->id, $extraStore)){
                                                         $limit=10;
                            					    }elseif (isset($storeLimitMap[$userId])) {
                                                        $limit = $storeLimitMap[$userId];
                            					    }elseif(in_array($storeExist->state_id, $stateData)){
                            					        $limit=20;
                            					    }elseif(in_array($storeExist->id, $distributorStore)){
                            					        $limit=75;
                            					    }else{
                            					        $limit=10;
                            					    }
                        					    if ($maxtimeusage >= $limit) {
                                                         return response()->json(['error'=>true, 'resp'=>'Sorry! You have reached your monthly limit']);
                                                }else{
                        						//no of usage check
                            						if ($barcode->no_of_usage == $barcode->max_time_of_use || $barcode->no_of_usage >= $barcode->max_time_of_use){
                            							return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                            						}else{
                            							 $usage = RetailerWalletTxn::where('barcode_id',$barcode->id)->where('user_id',$userId)->count();
                                                         if ($usage == $barcode->max_time_one_can_use || $usage >= $barcode->max_time_one_can_use) {
                                                             return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                                                        }else{
                            							    $userExist=Store::where('id',$userId)->where('status',1)->first();
                            								if(!$userExist){
                            									return response()->json(['error'=>true, 'resp'=>'User is invalid']);
                            								}else{
                            									
                            									$userAmount=RetailerWalletTxn::where('user_id',$userId)->orderby('id','desc')->first();
                            									$walletTxn=new RetailerWalletTxn();
                            									$walletTxn->user_id = $userId;
                            									$walletTxn->barcode_id = $barcode->id;
                            									$walletTxn->barcode = $barcode->code;
                            									$walletTxn->amount = 500;
                            									$walletTxn->type = 1 ?? '';
                            									if(!$userAmount)
                            										$walletTxn->final_amount += 500 ?? '';
                            									else
                            									$walletTxn->final_amount = $userAmount->final_amount+ 500 ?? '';
                            									$walletTxn->created_at = date('Y-m-d H:i:s');
                            									$walletTxn->updated_at = date('Y-m-d H:i:s');
                            									$walletTxn->save();
                            									$userwalletTxn=new RetailerUserTxnHistory();
                            									$userwalletTxn->user_id = $userId;
                            									$userwalletTxn->barcode_id = $barcode->id;
                            									$userwalletTxn->barcode = $barcode->code;
                            									$userwalletTxn->amount = 500;
                            									$userwalletTxn->type = 'Qrcode scan' ?? '';
                            									$userwalletTxn->title ='500 points earn';
                            									$userwalletTxn->description = 'Using '.$barcode->code.' code';
                            									$userwalletTxn->status = 'increment';
                            									$userwalletTxn->created_at = date('Y-m-d H:i:s');
                            									$userwalletTxn->updated_at = date('Y-m-d H:i:s');
                            									$userwalletTxn->save();
                            									$barcodeDetails=RetailerBarcode::findOrFail($barcode->id);
                            									$barcodeDetails->no_of_usage = $barcode->no_of_usage+1;
                            									$barcodeDetails->save();
                            									$user=Store::findOrFail($userId);
                            									$user->wallet += 500;
                            									$user->save();
                            						    	}
                            							}
                        						    }
                        					    }
                        					}
                        				}
                                    
                                   return response()->json(['error'=>false, 'resp'=>'Coupon scanned successfully ; 500 Cozi currency has been added to your wallet','data'=>$barcode]);
                                }
                        }
                }
            // if($userId==13){
            //      $barcode=RetailerBarcode::where('code',$code)->first();
            //     if(!$barcode){
            //                     return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is invalid']);
            //                 }else{
            //     				if ($barcode->start_date > \Carbon\Carbon::now()) {
            //                         return response()->json(['error'=>true, 'resp'=>'QRCodes is not valid now']);
            //                     }else{
            //                     // coupon code validity check
            //     					if ($barcode->end_date < \Carbon\Carbon::now() || $barcode->status == 0) {
            //     						return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is expired']);
            //     					}else{
            //     					    $maxtimeusage = RetailerWalletTxn::where('user_id',$userId)->whereMonth('created_at', Carbon::now()->month)->count();
                					   
            //     					    if ($maxtimeusage >= 10) {
            //                                      return response()->json(['error'=>true, 'resp'=>'Sorry! You have reached your monthly limit']);
            //                             }else{
            //     						//no of usage check
            //         						if ($barcode->no_of_usage == $barcode->max_time_of_use || $barcode->no_of_usage >= $barcode->max_time_of_use){
            //         							return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes Already scanned']);
            //         						}else{
            //         							 $usage = RetailerWalletTxn::where('barcode_id',$barcode->id)->where('user_id',$userId)->count();
            //                                      if ($usage == $barcode->max_time_one_can_use || $usage >= $barcode->max_time_one_can_use) {
            //                                          return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes Already scanned']);
            //                                     }else{
            //         							    $userExist=Store::where('id',$userId)->where('status',1)->first();
            //         								if(!$userExist){
            //         									return response()->json(['error'=>true, 'resp'=>'User is invalid']);
            //         								}else{
                    									
            //         									$userAmount=RetailerWalletTxn::where('user_id',$userId)->orderby('id','desc')->first();
            //         									$walletTxn=new RetailerWalletTxn();
            //         									$walletTxn->user_id = $userId;
            //         									$walletTxn->barcode_id = $barcode->id;
            //         									$walletTxn->barcode = $barcode->code;
            //         									$walletTxn->amount = 500;
            //         									$walletTxn->type = 1 ?? '';
            //         									if(!$userAmount)
            //         										$walletTxn->final_amount += 500 ?? '';
            //         									else
            //         									$walletTxn->final_amount = $userAmount->final_amount+ 500 ?? '';
            //         									$walletTxn->created_at = date('Y-m-d H:i:s');
            //         									$walletTxn->updated_at = date('Y-m-d H:i:s');
            //         									$walletTxn->save();
            //         									$userwalletTxn=new RetailerUserTxnHistory();
            //         									$userwalletTxn->user_id = $userId;
            //         									$userwalletTxn->barcode_id = $barcode->id;
            //         									$userwalletTxn->barcode = $barcode->code;
            //         									$userwalletTxn->amount = 500;
            //         									$userwalletTxn->type = 'Qrcode scan' ?? '';
            //         									$userwalletTxn->title = '500 points earn';
            //         									$userwalletTxn->description = 'Using '.$barcode->code.' code';
            //         									$userwalletTxn->status = 'increment';
            //         									$userwalletTxn->created_at = date('Y-m-d H:i:s');
            //         									$userwalletTxn->updated_at = date('Y-m-d H:i:s');
            //         									$userwalletTxn->save();
            //         									$barcodeDetails=RetailerBarcode::findOrFail($barcode->id);
            //         									$barcodeDetails->no_of_usage = $barcode->no_of_usage+1;
            //         									$barcodeDetails->save();
            //         									$user=Store::findOrFail($userId);
            //         									$user->wallet += 500;
            //         									$user->save();
            //         						    	}
            //         							}
            //     						    }
            //     					    }
            //     					}
            //     				}
                            
            //               return response()->json(['error'=>false, 'resp'=>'Coupon scanned successfully ;  500 Cozi currency has been added to your wallet','data'=>$barcode]);
            //             }
                    
                
            // }
            }else if($userId==23263 || $userId==28583 || $userId==11937 || $userId==32882|| $userId==32894){
                    $barcode=RetailerBarcode::where('code',$code)->first();
                    //distributor check
                    $distributor=$barcode->distributor_id;
                    $givenState=$barcode->state_id;
                    if(!empty($givenState)){
                        if($storeExist->state_id != $givenState){
                                return response()->json(['error'=>true, 'resp'=>'Sorry, Wrong State! Store is mapped with different state']);
                        }else{
                                $storeDistributor=Store::select('teams.distributor_id')->join('teams','teams.store_id','=','stores.id')->where('stores.id','=',$userId)->orderby('teams.id','desc')->first();
                                if (!in_array($distributor, explode(',', $storeDistributor->distributor_id))) {
                                    return response()->json(['error'=>true, 'resp'=>'Sorry, Wrong Distributor! Store is mapped with different distributor']);
                                }else{
                                    //barcode exist check
                                    if(!$barcode){
                                        return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is invalid']);
                                    }else{
                        				if ($barcode->start_date > \Carbon\Carbon::now()) {
                                            return response()->json(['error'=>true, 'resp'=>'QRCodes is not valid now']);
                                        }else{
                                        // coupon code validity check
                        					if ($barcode->end_date < \Carbon\Carbon::now() || $barcode->status == 0) {
                        						return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is expired']);
                        					}else{
                        					    $maxtimeusage = RetailerWalletTxn::where('user_id',$userId)->where('type',1)->whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->count();
                        					        $stateData=['14'];
                        					        $distributorStore = ['1745'];
                            					    $distributorId = implode(',', $distributorStore); // Convert array to string
                        
                                                    $distributorStore = Team::whereRaw("find_in_set(?, teams.distributor_id)", [$distributorId])
                                                        ->pluck('store_id')
                                                        ->toArray();
                        					        $storeId=['32823','1862','26687','27854','2742','25758','38792','32328','12432','3932','26544','38125','26135','26940','39883','26805','26922','37221','26918','38673','37996','12432','61351','39827','46570','46218','61352','61354','61355','61356','32328','40962','40981','35707','30312','54054','52086'];
                                					$extraStore=['49746','61775','75805','65237','63448','26544','25831','37882','26479','26755','26764','37221','26017','38673','41938','26135','26805','39883','43319','26922','26918','26499','26849','41969','44802','52227','52502','62464','62841','65378','74949','77875','60859','76553','40178','38479','39664','39664','19801','20005','77610'];
                                					$currentMonth = Carbon::now()->format('Y-m');
                                                    $storeLimits = DB::table('store_limits')
                                                    ->where('month', $currentMonth)
                                                    ->get()->toArray();
                                                    $storeLimitMap = [];
                                                    foreach ($storeLimits as $storeLimit) {
                                                        $storeLimitMap[$storeLimit->store_id] = $storeLimit->limit;
                                                    }
                                					    $limit='';
                                					   // if (in_array($storeExist->state_id, $stateData)) {
                                					   //     $limit=20;
                                					   // }else if(in_array($storeExist->id, $storeId)){
                                        //                     $limit=20;
                                					    if(in_array($storeExist->id, $extraStore)){
                                                             $limit=10;
                                					    }elseif (isset($storeLimitMap[$userId])) {
                                                          $limit = $storeLimitMap[$userId];
                                					    }elseif(in_array($storeExist->state_id, $stateData)){
                            					          $limit=20;
                                					    }elseif(in_array($storeExist->id, $distributorStore)){
                            					          $limit=75;
                                					    }else{
                                					        $limit=10;
                                					    }
                        					    if ($maxtimeusage >= $limit) {
                                                         return response()->json(['error'=>true, 'resp'=>'Sorry! You have reached your monthly limit']);
                                                }else{
                        						//no of usage check
                            						if ($barcode->no_of_usage == $barcode->max_time_of_use || $barcode->no_of_usage >= $barcode->max_time_of_use){
                            							return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                            						}else{
                            							 $usage = RetailerWalletTxn::where('barcode_id',$barcode->id)->where('user_id',$userId)->count();
                                                         if ($usage >= $barcode->max_time_of_use || $usage >= $barcode->max_time_one_can_use) {
                                                             return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                                                        }else{
                            							    $userExist=Store::where('id',$userId)->where('status',1)->first();
                            								if(!$userExist){
                            									return response()->json(['error'=>true, 'resp'=>'User is invalid']);
                            								}else{
                            									$user=Store::findOrFail($userId);
                            									$user->wallet += $barcode->amount;
                            									$user->save();
                            									try {
                                                                    // Start a transaction
                                                                    DB::beginTransaction();
                                                                    
                                                                    // Check if the entry already exists within the last second
                                                                    $existingTxn = RetailerWalletTxn::where('user_id', $userId)
                                                                        ->where('barcode_id', $barcode->id)
                                                                        ->where('created_at', '>=', now()->subSeconds(1))
                                                                        ->first();
                                                                    
                                                                    // If the entry doesn't exist, insert it
                                                                    if (!$existingTxn) {
                                                                        // Retrieve the latest user amount
                                                                        $userAmount = RetailerWalletTxn::where('user_id', $userId)->orderBy('id', 'desc')->first();
                                                                        
                                                                        // Create a new entry in the RetailerWalletTxn table
                                                                        $walletTxn = new RetailerWalletTxn();
                                                                        $walletTxn->user_id = $userId;
                                                                        $walletTxn->barcode_id = $barcode->id;
                                                                        $walletTxn->barcode = $barcode->code;
                                                                        $walletTxn->amount = $barcode->amount;
                                                                        $walletTxn->type = 1 ?? '';
                                                                        $walletTxn->final_amount = ($userAmount ? $userAmount->final_amount : 0) + $barcode->amount;
                                                                        $walletTxn->created_at = now();
                                                                        $walletTxn->updated_at = now();
                                                                        $walletTxn->save();
                                                                        
                                                                        // Create a new entry in the RetailerUserTxnHistory table
                                                                        $userwalletTxn = new RetailerUserTxnHistory();
                                                                        $userwalletTxn->user_id = $userId;
                                                                        $userwalletTxn->barcode_id = $barcode->id;
                                                                        $userwalletTxn->barcode = $barcode->code;
                                                                        $userwalletTxn->amount = $barcode->amount;
                                                                        $userwalletTxn->type = 'Qrcode scan' ?? '';
                                                                        $userwalletTxn->title = $barcode->amount . ' points earn';
                                                                        $userwalletTxn->description = 'Using ' . $barcode->code . ' code';
                                                                        $userwalletTxn->status = 'increment';
                                                                        $userwalletTxn->created_at = now();
                                                                        $userwalletTxn->updated_at = now();
                                                                        $userwalletTxn->save();
                                                                        
                                                                        // Update the no_of_usage field in the RetailerBarcode table
                                                                        $barcodeDetails = RetailerBarcode::findOrFail($barcode->id);
                                                                        $barcodeDetails->no_of_usage = $barcode->no_of_usage + 1;
                                                                        $barcodeDetails->save();
                                                                    }
                                                                    
                                                                    // Commit the transaction
                                                                    DB::commit();
                                                                } catch (Exception $e) {
                                                                    // Rollback the transaction if an error occurs
                                                                    DB::rollBack();
                                                                    // Handle the error
                                                                }
            
                            						    	}
                            							}
                        						    }
                        					    }
                        					}
                        				}
                                    }
                                   return response()->json(['error'=>false, 'resp'=>'Coupon scanned successfully ; ' .$barcode->amount.' Cozi currency has been added to your wallet','data'=>$barcode]);
                                }
                        }
                    }else{
                        $storeDistributor=Store::select('teams.distributor_id')->join('teams','teams.store_id','=','stores.id')->where('stores.id','=',$userId)->orderby('teams.id','desc')->first();
                                if (!in_array($distributor, explode(',', $storeDistributor->distributor_id))) {
                                    return response()->json(['error'=>true, 'resp'=>'Sorry, Wrong Distributor! Store is mapped with different distributor']);
                                }else{
                                    //barcode exist check
                                    if(!$barcode){
                                        return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is invalid']);
                                    }else{
                        				if ($barcode->start_date > \Carbon\Carbon::now()) {
                                            return response()->json(['error'=>true, 'resp'=>'QRCodes is not valid now']);
                                        }else{
                                        // coupon code validity check
                        					if ($barcode->end_date < \Carbon\Carbon::now() || $barcode->status == 0) {
                        						return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is expired']);
                        					}else{
                        					    $maxtimeusage = RetailerWalletTxn::where('user_id',$userId)->where('type',1)->whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->count();
                        					        $stateData=['14'];
                        					        $distributorStore = ['1745'];
                            					    $distributorId = implode(',', $distributorStore); // Convert array to string
                        
                                                    $distributorStore = Team::whereRaw("find_in_set(?, teams.distributor_id)", [$distributorId])
                                                        ->pluck('store_id')
                                                        ->toArray();
                        					        $storeId=['32823','1862','26687','27854','2742','25758','38792','32328','12432','3932','26544','38125','26135','26940','39883','26805','26922','37221','26918','38673','37996','12432','61351','39827','46570','46218','61352','61354','61355','61356','32328','40962','40981','35707','30312','54054','52086'];
                                					$extraStore=['49746','61775','75805','65237','63448','26544','25831','37882','26479','26755','26764','37221','26017','38673','41938','26135','26805','39883','43319','26922','26918','26499','26849','41969','44802','52227','52502','62464','62841','65378','74949','77875','60859','76553','40178','38479','39664','39664','19801','20005','77610'];
                                					$currentMonth = Carbon::now()->format('Y-m');
                                                    $storeLimits = DB::table('store_limits')
                                                    ->where('month', $currentMonth)
                                                    ->get()->toArray();
                                                    $storeLimitMap = [];
                                                    foreach ($storeLimits as $storeLimit) {
                                                        $storeLimitMap[$storeLimit->store_id] = $storeLimit->limit;
                                                    }
                                					    $limit='';
                                					   // if (in_array($storeExist->state_id, $stateData)) {
                                					   //     $limit=20;
                                					   // }else if(in_array($storeExist->id, $storeId)){
                                        //                     $limit=20;
                                					    if(in_array($storeExist->id, $extraStore)){
                                                             $limit=10;
                                					    }elseif (isset($storeLimitMap[$userId])) {
                                                         $limit = $storeLimitMap[$userId];
                                					    }elseif(in_array($storeExist->state_id, $stateData)){
                            					          $limit=20;
                                					    }elseif(in_array($storeExist->id, $distributorStore)){
                            					            $limit=75;
                                					    }else{
                                					       $limit=10;
                                					    }
                        					    if ($maxtimeusage >= $limit) {
                                                         return response()->json(['error'=>true, 'resp'=>'Sorry! You have reached your monthly limit']);
                                                }else{
                        						//no of usage check
                            						if ($barcode->no_of_usage == $barcode->max_time_of_use || $barcode->no_of_usage >= $barcode->max_time_of_use){
                            							return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                            						}else{
                            							 $usage = RetailerWalletTxn::where('barcode_id',$barcode->id)->where('user_id',$userId)->count();
                                                         if ($usage >= $barcode->max_time_of_use || $usage >= $barcode->max_time_one_can_use) {
                                                             return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                                                        }else{
                            							    $userExist=Store::where('id',$userId)->where('status',1)->first();
                            								if(!$userExist){
                            									return response()->json(['error'=>true, 'resp'=>'User is invalid']);
                            								}else{
                            									$user=Store::findOrFail($userId);
                            									$user->wallet += $barcode->amount;
                            									$user->save();
                            									try {
                                                                    // Start a transaction
                                                                    DB::beginTransaction();
                                                                    
                                                                    // Check if the entry already exists within the last second
                                                                    $existingTxn = RetailerWalletTxn::where('user_id', $userId)
                                                                        ->where('barcode_id', $barcode->id)
                                                                        ->where('created_at', '>=', now()->subSeconds(1))
                                                                        ->first();
                                                                    
                                                                    // If the entry doesn't exist, insert it
                                                                    if (!$existingTxn) {
                                                                        // Retrieve the latest user amount
                                                                        $userAmount = RetailerWalletTxn::where('user_id', $userId)->orderBy('id', 'desc')->first();
                                                                        
                                                                        // Create a new entry in the RetailerWalletTxn table
                                                                        $walletTxn = new RetailerWalletTxn();
                                                                        $walletTxn->user_id = $userId;
                                                                        $walletTxn->barcode_id = $barcode->id;
                                                                        $walletTxn->barcode = $barcode->code;
                                                                        $walletTxn->amount = $barcode->amount;
                                                                        $walletTxn->type = 1 ?? '';
                                                                        $walletTxn->final_amount = ($userAmount ? $userAmount->final_amount : 0) + $barcode->amount;
                                                                        $walletTxn->created_at = now();
                                                                        $walletTxn->updated_at = now();
                                                                        $walletTxn->save();
                                                                        
                                                                        // Create a new entry in the RetailerUserTxnHistory table
                                                                        $userwalletTxn = new RetailerUserTxnHistory();
                                                                        $userwalletTxn->user_id = $userId;
                                                                        $userwalletTxn->barcode_id = $barcode->id;
                                                                        $userwalletTxn->barcode = $barcode->code;
                                                                        $userwalletTxn->amount = $barcode->amount;
                                                                        $userwalletTxn->type = 'Qrcode scan' ?? '';
                                                                        $userwalletTxn->title = $barcode->amount . ' points earn';
                                                                        $userwalletTxn->description = 'Using ' . $barcode->code . ' code';
                                                                        $userwalletTxn->status = 'increment';
                                                                        $userwalletTxn->created_at = now();
                                                                        $userwalletTxn->updated_at = now();
                                                                        $userwalletTxn->save();
                                                                        
                                                                        // Update the no_of_usage field in the RetailerBarcode table
                                                                        $barcodeDetails = RetailerBarcode::findOrFail($barcode->id);
                                                                        $barcodeDetails->no_of_usage = $barcode->no_of_usage + 1;
                                                                        $barcodeDetails->save();
                                                                    }
                                                                    
                                                                    // Commit the transaction
                                                                    DB::commit();
                                                                } catch (Exception $e) {
                                                                    // Rollback the transaction if an error occurs
                                                                    DB::rollBack();
                                                                    // Handle the error
                                                                }
            
                            						    	}
                            							}
                        						    }
                        					    }
                        					}
                        				}
                                    }
                                   return response()->json(['error'=>false, 'resp'=>'Coupon scanned successfully ; ' .$barcode->amount.' Cozi currency has been added to your wallet','data'=>$barcode]);
                                }
                    }
            
            }else{
                $barcode=RetailerBarcode::where('code',$code)->first();
                if(!$barcode){
                        return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is invalid']);
                }else{
                $distributor=$barcode->distributor_id;
                $givenState=$barcode->state_id;
                if(!empty($givenState)){
                    if($storeExist->state_id != $givenState){
                            return response()->json(['error'=>true, 'resp'=>'Kindly Purchase from Your Authorised Area Distributor']);
                    }else{
                        if(!empty($distributor)){
                            $storeDistributor=Store::select('teams.distributor_id')->join('teams','teams.store_id','=','stores.id')->where('stores.id','=',$userId)->orderby('teams.id','desc')->first();
                            if (!in_array($distributor, explode(',', $storeDistributor->distributor_id))) {
                                return response()->json(['error'=>true, 'resp'=>'Kindly Purchase from Your Authorised Area Distributor']);
                            }else{
                                
                                
                    				if ($barcode->start_date > \Carbon\Carbon::now()) {
                                        return response()->json(['error'=>true, 'resp'=>'QRCodes is not valid now']);
                                    }else{
                                    // coupon code validity check
                    					if ($barcode->end_date < \Carbon\Carbon::now() || $barcode->status == 0) {
                    						return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is expired']);
                    					}else{
                    					    $maxtimeusage = RetailerWalletTxn::where('user_id',$userId)->where('type',1)->whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->count();
                    					        $stateData=['14'];
                    					        $distributorStore = ['1745'];
                            					    $distributorId = implode(',', $distributorStore); // Convert array to string
                        
                                                    $distributorStore = Team::whereRaw("find_in_set(?, teams.distributor_id)", [$distributorId])
                                                        ->pluck('store_id')
                                                        ->toArray();
                    					        $storeId=['32823','1862','26687','27854','2742','25758','38792','32328','12432','3932','26544','38125','26135','26940','39883','26805','26922','37221','26918','38673','37996','12432','61351','39827','46570','46218','61352','61354','61355','61356','32328','40962','40981','35707','30312','54054','52086'];
                            					$extraStore=['49746','61775','75805','65237','63448','26544','25831','37882','26479','26755','26764','37221','26017','38673','41938','26135','26805','39883','43319','26922','26918','26499','26849','41969','44802','52227','52502','62464','62841','65378','74949','77875','60859','76553','40178','38479','39664','39664','19801','20005','77610'];
                            					$currentMonth = Carbon::now()->format('Y-m');
                                                    $storeLimits = DB::table('store_limits')
                                                    ->where('month', $currentMonth)
                                                    ->get()->toArray();
                                                    $storeLimitMap = [];
                                                    foreach ($storeLimits as $storeLimit) {
                                                        $storeLimitMap[$storeLimit->store_id] = $storeLimit->limit;
                                                    }
                            					    $limit='';
                            					    /*if (in_array($storeExist->state_id, $stateData)) {
                            					        $limit=20;
                            					    }else if(in_array($storeExist->id, $storeId)){
                                                         $limit=20;*/
                            					    if(in_array($storeExist->id, $extraStore)){
                                                         $limit=10;
                            					    }elseif (isset($storeLimitMap[$userId])) {
                                                        $limit = $storeLimitMap[$userId];
                            					    }elseif(in_array($storeExist->state_id, $stateData)){
                            					          $limit=20;
                            					    }elseif(in_array($storeExist->id, $distributorStore)){
                            					        $limit=75;
                            					    }else{
                            					        $limit=10;
                            					    }
                    					    if ($maxtimeusage >= $limit) {
                                                     return response()->json(['error'=>true, 'resp'=>'Sorry! You have reached your monthly limit']);
                                            }else{
                    						//no of usage check
                        						if ($barcode->no_of_usage == $barcode->max_time_of_use || $barcode->no_of_usage >= $barcode->max_time_of_use){
                        							return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                        						}else{
                        							 $usage = RetailerWalletTxn::where('barcode_id',$barcode->id)->where('user_id',$userId)->count();
                                                     if ($usage == $barcode->max_time_one_can_use || $usage >= $barcode->max_time_one_can_use) {
                                                         return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                                                    }else{
                        							    $userExist=Store::where('id',$userId)->where('status',1)->first();
                        								if(!$userExist){
                        									return response()->json(['error'=>true, 'resp'=>'User is invalid']);
                        								}else{
                        									
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
                        									$barcodeDetails=RetailerBarcode::findOrFail($barcode->id);
                        									$barcodeDetails->no_of_usage = $barcode->no_of_usage+1;
                        									$barcodeDetails->save();
                        									$user=Store::findOrFail($userId);
                        									$user->wallet += $barcode->amount;
                        									$user->save();
                        						    	}
                        							}
                    						    }
                    					    }
                    					}
                    				
                                
                                
                               return response()->json(['error'=>false, 'resp'=>'Coupon scanned successfully ; ' .$barcode->amount.' Cozi currency has been added to your wallet','data'=>$barcode]);
                                }
                            }
                        }else{
                                    //barcode exist check
                                    if(!$barcode){
                                        return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is invalid']);
                                    }else{
                        				if ($barcode->start_date > \Carbon\Carbon::now()) {
                                            return response()->json(['error'=>true, 'resp'=>'QRCodes is not valid now']);
                                        }else{
                                        // coupon code validity check
                        					if ($barcode->end_date < \Carbon\Carbon::now() || $barcode->status == 0) {
                        						return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is expired']);
                        					}else{
                        					    $maxtimeusage = RetailerWalletTxn::where('user_id',$userId)->where('type',1)->whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->count();
                        					         $stateData=['14'];
                        					         $distributorStore = ['1745'];
                            					    $distributorId = implode(',', $distributorStore); // Convert array to string
                        
                                                    $distributorStore = Team::whereRaw("find_in_set(?, teams.distributor_id)", [$distributorId])
                                                        ->pluck('store_id')
                                                        ->toArray();
                        					         $storeId=['32823','1862','26687','27854','2742','25758','38792','32328','12432','3932','26544','38125','26135','26940','39883','26805','26922','37221','26918','38673','37996','12432','61351','39827','46570','46218','61352','61354','61355','61356','32328','40962','40981','35707','30312','54054','52086'];
                            					    $extraStore=['49746','61775','75805','65237','63448','26544','25831','37882','26479','26755','26764','37221','26017','38673','41938','26135','26805','39883','43319','26922','26918','26499','26849','41969','44802','52227','52502','62464','62841','65378','74949','77875','60859','76553','40178','38479','39664','39664','19801','20005','77610'];
                            					    $currentMonth = Carbon::now()->format('Y-m');
                                                    $storeLimits = DB::table('store_limits')
                                                    ->where('month', $currentMonth)
                                                    ->get()->toArray();
                                                    $storeLimitMap = [];
                                                    foreach ($storeLimits as $storeLimit) {
                                                        $storeLimitMap[$storeLimit->store_id] = $storeLimit->limit;
                                                    }
                            					    $limit='';
                            					    /*if (in_array($storeExist->state_id, $stateData)) {
                            					        $limit=20;
                            					    }else if(in_array($storeExist->id, $storeId)){
                                                        $limit=20;*/
                            					    if(in_array($storeExist->id, $extraStore)){
                                                        $limit=10;
                            					    }elseif (isset($storeLimitMap[$userId])) {
                                                        $limit = $storeLimitMap[$userId];
                            					    }elseif(in_array($storeExist->state_id, $stateData)){
                            					          $limit=20;
                            					    }elseif(in_array($storeExist->id, $distributorStore)){
                            					        $limit=75;
                            					    }else{
                            					        $limit=10;
                            					    }
                        					    if ($maxtimeusage >= $limit) {
                                                         return response()->json(['error'=>true, 'resp'=>'Sorry! You have reached your monthly limit']);
                                                }else{
                        						//no of usage check
                            						if ($barcode->no_of_usage == $barcode->max_time_of_use || $barcode->no_of_usage >= $barcode->max_time_of_use){
                            							return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                            						}else{
                            							 $usage = RetailerWalletTxn::where('barcode_id',$barcode->id)->where('user_id',$userId)->count();
                                                         if ($usage == $barcode->max_time_one_can_use || $usage >= $barcode->max_time_one_can_use) {
                                                             return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                                                        }else{
                            							    $userExist=Store::where('id',$userId)->where('status',1)->first();
                            								if(!$userExist){
                            									return response()->json(['error'=>true, 'resp'=>'User is invalid']);
                            								}else{
                            									
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
                            									$barcodeDetails=RetailerBarcode::findOrFail($barcode->id);
                            									$barcodeDetails->no_of_usage = $barcode->no_of_usage+1;
                            									$barcodeDetails->save();
                            									$user=Store::findOrFail($userId);
                            									$user->wallet += $barcode->amount;
                            									$user->save();
                            						    	}
                            							}
                        						    }
                        					    }
                        					}
                        				}
                                    
                                   return response()->json(['error'=>false, 'resp'=>'Coupon scanned successfully ; ' .$barcode->amount.' Cozi currency has been added to your wallet','data'=>$barcode]);
                                }
                        }
                    }
                }else{
                     if(!empty($distributor)){
                            $storeDistributor=Store::select('teams.distributor_id')->join('teams','teams.store_id','=','stores.id')->where('stores.id','=',$userId)->orderby('teams.id','desc')->first();
                            if (!in_array($distributor, explode(',', $storeDistributor->distributor_id))) {
                                return response()->json(['error'=>true, 'resp'=>'Kindly Purchase from Your Authorised Area Distributor']);
                            }else{
                                
                                if(!$barcode){
                                    return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is invalid']);
                                }else{
                    				if ($barcode->start_date > \Carbon\Carbon::now()) {
                                        return response()->json(['error'=>true, 'resp'=>'QRCodes is not valid now']);
                                    }else{
                                    // coupon code validity check
                    					if ($barcode->end_date < \Carbon\Carbon::now() || $barcode->status == 0) {
                    						return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is expired']);
                    					}else{
                    					    $maxtimeusage = RetailerWalletTxn::where('user_id',$userId)->where('type',1)->whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->count();
                    					        $stateData=['14'];
                    					        $distributorStore = ['1745'];
                            					    $distributorId = implode(',', $distributorStore); // Convert array to string
                        
                                                    $distributorStore = Team::whereRaw("find_in_set(?, teams.distributor_id)", [$distributorId])
                                                        ->pluck('store_id')
                                                        ->toArray();
                    					        $storeId=['32823','1862','26687','27854','2742','25758','38792','32328','12432','3932','26544','38125','26135','26940','39883','26805','26922','37221','26918','38673','37996','12432','61351','39827','46570','46218','61352','61354','61355','61356','32328','40962','40981','35707','30312','54054','52086'];
                            					$extraStore=['49746','61775','75805','65237','63448','26544','25831','37882','26479','26755','26764','37221','26017','38673','41938','26135','26805','39883','43319','26922','26918','26499','26849','41969','44802','52227','52502','62464','62841','65378','74949','77875','60859','76553','40178','38479','39664','39664','19801','20005','77610'];
                            					$currentMonth = Carbon::now()->format('Y-m');
                                                    $storeLimits = DB::table('store_limits')
                                                    ->where('month', $currentMonth)
                                                    ->get()->toArray();
                                                    $storeLimitMap = [];
                                                    foreach ($storeLimits as $storeLimit) {
                                                        $storeLimitMap[$storeLimit->store_id] = $storeLimit->limit;
                                                    }
                            					    $limit='';
                            					    /*if (in_array($storeExist->state_id, $stateData)) {
                            					        $limit=20;
                            					    }else if(in_array($storeExist->id, $storeId)){
                                                         $limit=20;*/
                            					    if(in_array($storeExist->id, $extraStore)){
                                                         $limit=10;
                            					    }elseif (isset($storeLimitMap[$userId])) {
                                                        $limit = $storeLimitMap[$userId];
                            					    }elseif(in_array($storeExist->state_id, $stateData)){
                            					          $limit=20;
                            					    }elseif(in_array($storeExist->id, $distributorStore)){
                            					        $limit=75;
                            					    }else{
                            					        $limit=10;
                            					    }
                    					    if ($maxtimeusage >= $limit) {
                                                     return response()->json(['error'=>true, 'resp'=>'Sorry! You have reached your monthly limit']);
                                            }else{
                    						//no of usage check
                        						if ($barcode->no_of_usage == $barcode->max_time_of_use || $barcode->no_of_usage >= $barcode->max_time_of_use){
                        							return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                        						}else{
                        							 $usage = RetailerWalletTxn::where('barcode_id',$barcode->id)->where('user_id',$userId)->count();
                                                     if ($usage == $barcode->max_time_one_can_use || $usage >= $barcode->max_time_one_can_use) {
                                                         return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                                                    }else{
                        							    $userExist=Store::where('id',$userId)->where('status',1)->first();
                        								if(!$userExist){
                        									return response()->json(['error'=>true, 'resp'=>'User is invalid']);
                        								}else{
                        									
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
                        									$barcodeDetails=RetailerBarcode::findOrFail($barcode->id);
                        									$barcodeDetails->no_of_usage = $barcode->no_of_usage+1;
                        									$barcodeDetails->save();
                        									$user=Store::findOrFail($userId);
                        									$user->wallet += $barcode->amount;
                        									$user->save();
                        						    	}
                        							}
                    						    }
                    					    }
                    					}
                    				}
                                
                                
                               return response()->json(['error'=>false, 'resp'=>'Coupon scanned successfully ; ' .$barcode->amount.' Cozi currency has been added to your wallet','data'=>$barcode]);
                                }
                            }
                        }else{
                                    //barcode exist check
                                    if(!$barcode){
                                        return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is invalid']);
                                    }else{
                        				if ($barcode->start_date > \Carbon\Carbon::now()) {
                                            return response()->json(['error'=>true, 'resp'=>'QRCodes is not valid now']);
                                        }else{
                                        // coupon code validity check
                        					if ($barcode->end_date < \Carbon\Carbon::now() || $barcode->status == 0) {
                        						return response()->json(['error'=>true, 'resp'=>'Sorry! QRCodes is expired']);
                        					}else{
                        					    $maxtimeusage = RetailerWalletTxn::where('user_id',$userId)->where('type',1)->whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->count();
                        					         $stateData=['14'];
                        					         $distributorStore = ['1745'];
                            					    $distributorId = implode(',', $distributorStore); // Convert array to string
                        
                                                    $distributorStore = Team::whereRaw("find_in_set(?, teams.distributor_id)", [$distributorId])
                                                       ->pluck('store_id')
                                                        ->toArray();
                        					         $storeId=['32823','1862','26687','27854','2742','25758','38792','32328','12432','3932','26544','38125','26135','26940','39883','26805','26922','37221','26918','38673','37996','12432','61351','39827','46570','46218','61352','61354','61355','61356','32328','40962','40981','35707','30312','54054','52086'];
                            					    $extraStore=['49746','61775','75805','65237','63448','26544','25831','37882','26479','26755','26764','37221','26017','38673','41938','26135','26805','39883','43319','26922','26918','26499','26849','41969','44802','52227','52502','62464','62841','65378','74949','77875','60859','76553','40178','38479','39664','39664','19801','20005','77610'];
                            					    $currentMonth = Carbon::now()->format('Y-m');
                                                    $storeLimits = DB::table('store_limits')
                                                    ->where('month', $currentMonth)
                                                    ->get()->toArray();
                                                    $storeLimitMap = [];
                                                    foreach ($storeLimits as $storeLimit) {
                                                        $storeLimitMap[$storeLimit->store_id] = $storeLimit->limit;
                                                    }
                            					    $limit='';
                            					    /*if (in_array($storeExist->state_id, $stateData)) {
                            					        $limit=20;
                            					    }else if(in_array($storeExist->id, $storeId)){
                                                        $limit=20;*/
                            					    if(in_array($storeExist->id, $extraStore)){
                                                        $limit=10;
                            					    }elseif (isset($storeLimitMap[$userId])) {
                                                        $limit = $storeLimitMap[$userId];
                            					    }elseif(in_array($storeExist->state_id, $stateData)){
                            					          $limit=20;
                            					          
                                                    }elseif(in_array($storeExist->id, $distributorStore)){
                            					        $limit=75;
                            					    }else{
                            					        $limit=10;
                            					    }
                        					    if ($maxtimeusage >= $limit) {
                                                         return response()->json(['error'=>true, 'resp'=>'Sorry! You have reached your monthly limit']);
                                                }else{
                        						//no of usage check
                            						if ($barcode->no_of_usage == $barcode->max_time_of_use || $barcode->no_of_usage >= $barcode->max_time_of_use){
                            							return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                            						}else{
                            							 $usage = RetailerWalletTxn::where('barcode_id',$barcode->id)->where('user_id',$userId)->count();
                                                         if ($usage == $barcode->max_time_one_can_use || $usage >= $barcode->max_time_one_can_use) {
                                                             return response()->json(['error'=>true, 'resp'=>'Sorry! QRCode is already scanned']);
                                                        }else{
                            							    $userExist=Store::where('id',$userId)->where('status',1)->first();
                            								if(!$userExist){
                            									return response()->json(['error'=>true, 'resp'=>'User is invalid']);
                            								}else{
                            									
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
                            									$barcodeDetails=RetailerBarcode::findOrFail($barcode->id);
                            									$barcodeDetails->no_of_usage = $barcode->no_of_usage+1;
                            									$barcodeDetails->save();
                            									$user=Store::findOrFail($userId);
                            									$user->wallet += $barcode->amount;
                            									$user->save();
                            						    	}
                            							}
                        						    }
                        					    }
                        					}
                        				}
                                    
                                   return response()->json(['error'=>false, 'resp'=>'Coupon scanned successfully ; ' .$barcode->amount.' Cozi currency has been added to your wallet','data'=>$barcode]);
                                }
                        }
                }
                }
            }
        
		} else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
   
    }
	
	
	// retailer create invoice document API
	public function invoiceIndex(Request $request) {
		$validator = Validator::make($request->all(), [
            'invoice' => 'required'
        ]);
        if (!$validator->fails()) {
				$imageName = mt_rand().'.'.$request->invoice->extension();
				$uploadPath = 'public/uploads/retailer/document';
				$request->invoice->move($uploadPath, $imageName);
				$total_path = $uploadPath.'/'.$imageName;
			     $resp = [
                       $total_path,
                       ];
			return response()->json(['error' => false, 'message' => 'Document added', 'data' => $total_path]);
		} else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
		
	}


     // retailer create invoice store  API
	public function invoiceStore(Request $request) {
		$validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'invoice' => 'required'
        ]);
        if (!$validator->fails()) {
            $user= new Invoice;
			$user->user_id = $request->user_id;
            $user->amount = $request->amount;
            $user->date = $request->date;
            if (isset($request['invoice'])) {
                $user->invoice = $request->invoice;
            }
            $user->save();
			return response()->json(['error' => false, 'message' => 'Invoice added', 'data' => $user]);
		} else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
		
	}
	
	
	
	
	
	
	public function duplicateCheck(Request $request)
    {
        
            $duplicateRecords=DB::select("SELECT * FROM `retailer_wallet_txns` where barcode_id != NULL GROUP BY barcode_id HAVING COUNT(*)>1");
            
		    //$duplicateRecords = DB::selectRaw('count(`*`) as `occurences`')
             // ->from('retailer_wallet_txns')
              //->groupBy('barcode_id')
              //->having('occurences', '>', 1)
              //->orderBy('id','desc')
             // ->get();
                        //duplicate exist check
                        if(!empty($duplicateRecords)){
                       foreach($duplicateRecords as $record) {
                            $store=Store::where('id',$record->user_id)->first();
                            $store->wallet-=$record->amount;
                            $store->save();
                            RetailerWalletTxn::where('id', $record->id)->delete();
                            
                        }
                        }
                        $data = [
                                   
                                    "command" => 'yes',
                                   
                                    "created_at" => date('Y-m-d H:i:s'),
                                    "updated_at" => date('Y-m-d H:i:s'),
                                ];

                                $resp = DB::table('cron_logs')->insertGetId($data); 
         dd('done');
                    
    }
        
		
   
    
}
