<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RetailerUser;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Hash;
use App\Models\Store;
use App\Models\Branding;
use App\Models\StoreFormSubmit;
use App\Models\Team;
use App\Models\RetailerWalletTxn;
use Illuminate\Support\Str;
use Carbon\Carbon;
class UserController extends Controller
{

    // retailer create aadhar document API
	public function retailerCreateAadhar(Request $request) {
		$validator = Validator::make($request->all(), [
            'aadhar' => 'required'
        ]);
        if (!$validator->fails()) {
				$imageName = mt_rand().'.'.$request->aadhar->extension();
				$uploadPath = 'public/uploads/retailer/document';
				$request->aadhar->move($uploadPath, $imageName);
				$total_path = $uploadPath.'/'.$imageName;
			     $resp = [
                       'data' => $total_path,
                       ];
			return response()->json(['error' => false, 'message' => 'Document added', 'data' => $resp]);
		} else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
		
	}
	
	public function demoretailerCreateAadhar(Request $request) {
		$validator = Validator::make($request->all(), [
            'aadhar' => 'required'
        ]);
        if (!$validator->fails()) {
				$imageName = mt_rand().'.'.$request->aadhar->extension();
				$uploadPath = 'public/uploads/retailer/document';
				$request->aadhar->move($uploadPath, $imageName);
				$total_path = $uploadPath.'/'.$imageName;
			     $resp = [
                       'data' => $total_path,
                       ];
			return response()->json(['error' => false, 'message' => 'Document added', 'data' => $resp]);
		} else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
		
	}
    // retailer create pan document API
	public function retailerCreatePan(Request $request) {
		$validator = Validator::make($request->all(), [
            'pan' => 'required'
        ]);
        if (!$validator->fails()) {
				$imageName = mt_rand().'.'.$request->pan->extension();
				$uploadPath = 'public/uploads/retailer/document';
				$request->pan->move($uploadPath, $imageName);
				$total_path = $uploadPath.'/'.$imageName;
			     $resp = [
                       'data' => $total_path,
                       ];
			return response()->json(['error' => false, 'message' => 'Document added', 'data' => $resp]);
		} else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
		
	}
	
	public function demoretailerCreatePan(Request $request) {
		$validator = Validator::make($request->all(), [
            'pan' => 'required'
        ]);
        if (!$validator->fails()) {
				$imageName = mt_rand().'.'.$request->pan->extension();
				$uploadPath = 'public/uploads/retailer/document';
				$request->pan->move($uploadPath, $imageName);
				$total_path = $uploadPath.'/'.$imageName;
			     $resp = [
                       'data' => $total_path,
                       ];
			return response()->json(['error' => false, 'message' => 'Document added', 'data' => $resp]);
		} else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
		
	}
    // retailer create gst document API
	public function retailerCreateGst(Request $request) {
		$validator = Validator::make($request->all(), [
            'gst' => 'required'
        ]);
        if (!$validator->fails()) {
				$imageName = mt_rand().'.'.$request->gst->extension();
				$uploadPath = 'public/uploads/retailer/document';
				$request->gst->move($uploadPath, $imageName);
				$total_path = $uploadPath.'/'.$imageName;
			     $resp = [
                       'data' => $total_path,
                       ];
			return response()->json(['error' => false, 'message' => 'Document added', 'data' => $resp]);
		} else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
		
	}
	
	public function demoretailerCreateGst(Request $request) {
		$validator = Validator::make($request->all(), [
            'gst' => 'required'
        ]);
        if (!$validator->fails()) {
				$imageName = mt_rand().'.'.$request->gst->extension();
				$uploadPath = 'public/uploads/retailer/document';
				$request->gst->move($uploadPath, $imageName);
				$total_path = $uploadPath.'/'.$imageName;
			     $resp = [
                       'data' => $total_path,
                       ];
			return response()->json(['error' => false, 'message' => 'Document added', 'data' => $resp]);
		} else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
		
	}
	 // retailer create image API
	public function retailerCreateImage(Request $request) {
		$validator = Validator::make($request->all(), [
            'image' => 'required'
        ]);
        if (!$validator->fails()) {
				$imageName = mt_rand().'.'.$request->image->extension();
				$uploadPath = 'public/uploads/retailer/document';
				$request->image->move($uploadPath, $imageName);
				$total_path = $uploadPath.'/'.$imageName;
			     $resp = [
                       'data' => $total_path,
                       ];
			return response()->json(['error' => false, 'message' => 'Document added', 'data' => $resp]);
		} else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
		
	}
	
	public function demoretailerCreateImage(Request $request) {
		$validator = Validator::make($request->all(), [
            'image' => 'required'
        ]);
        if (!$validator->fails()) {
				$imageName = mt_rand().'.'.$request->image->extension();
				$uploadPath = 'public/uploads/retailer/document';
				$request->image->move($uploadPath, $imageName);
				$total_path = $uploadPath.'/'.$imageName;
			     $resp = [
                       'data' => $total_path,
                       ];
			return response()->json(['error' => false, 'message' => 'Document added', 'data' => $resp]);
		} else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
		
	}
    public function demoregister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'owner_name' => ['required', 'string', 'min:1'],
			'owner_lname' => ['nullable', 'string', 'min:1'],
            'shop_name' => ['required', 'string', 'max:255'],
            'shop_address' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'min:1'],
            'mobile' => ['required', 'integer','digits:10'],
            'pin' => ['required', 'integer','digits:6'],
            'state' => ['required', 'string','max:255'],
            'district' => ['required', 'string','max:255'],
            'city' => ['required', 'string','max:255'],
            'aadhar' => ['nullable'],
      //  ], [
        //    'aadhar.*' => 'Please enter minimum one document'
        ]);

        if (!$validator->fails()) {
			
            //$upload_path = "uploads/retailer/document";
			$retailer_id = "LuxCozi".mt_rand();
			/*$storeExist=Store::where('store_name',$request['store_name'])->where('area',$request['area'])->where('contact',$request['contact'])->where('state',$request['state'])->first();
			//dd($storeExist);
			if(($storeExist)){
				
				  return response()->json(['error' => true, 'message' => 'Store/Retailer already exist']);
				}else{*/
				$user= new RetailerUser;
				$user->retailer_id = $retailer_id;
				$user->owner_name = $request->owner_name;
				$user->shop_name = $request->shop_name;
				$user->shop_address = $request->shop_address;
				$user->email = $request->email ?? '';
				$user->mobile = $request->mobile ?? '';
				$user->whatsapp_no = $request->whatsapp_no ?? '';
				$user->pin = $request->pin ?? '';
				$user->state = $request->state ?? '';
				$user->city = $request->city ?? '';
				$user->district = $request->district ?? '';
				$user->password = bcrypt($request['password']);
				$user->created_at = date('Y-m-d g:i:s');
				$user->updated_at = date('Y-m-d g:i:s');
				if (isset($request['aadhar'])) {
					$user->aadhar = $request->aadhar;
				}
				if (isset($request['pan'])) {
					$user->pan = $request->pan;
				}
				if (isset($request['gst'])) {
					$user->gst = $request->gst;
				}
				$user->save();
				/*$result1 = DB::select("select * from retailer_list_of_occ where distributor_name='$request->distributor_name' AND area='".$request['area']."'");
			$store = new Store;
			//$store->user_id = $request['user_id'];
			$store->store_name = $request['store_name'];
			$store->bussiness_name	 = $request['bussiness_name'] ?? null;
			$store->owner_name	 = $request['owner_name'] ?? null;
		    $store->owner_lname	 = $request['owner_lname'] ?? null;
			$store->store_OCC_number = $request['store_OCC_number'] ?? null;
			$store->gst_no = $request['gst_no'] ?? null;
			$store->contact = $request['contact'];
			$store->whatsapp = $request['whatsapp']?? null;
			$store->email	 = $request['email']?? null;
			$store->address	 = $request['address']?? null;
			$store->state	 = $request['state']?? null;
			$store->city	 = $request['city']?? null;
			$store->pin	 = $request['pin']?? null;
			$store->area	 = $request['area']?? null;
			$store->date_of_birth	 = $request['date_of_birth']?? null;
			$store->date_of_anniversary	 = $request['date_of_anniversary']?? null;
			$store->contact_person	 = $request['contact_person']?? null;
	    	$store->contact_person_lname = $request['contact_person_lname'] ?? null;
			$store->contact_person_phone	= $request['contact_person_phone']?? null;
			$store->contact_person_whatsapp	 = $request['contact_person_whatsapp']?? null;
			$store->contact_person_date_of_birth	 = $request['contact_person_date_of_birth']?? null;
			$store->contact_person_date_of_anniversary	 = $request['contact_person_date_of_anniversary']?? null;
			$store->password = bcrypt($request['password']) ?? null;
			$store->status = '0';
			//$store->gst_no = '';
			if (!empty($request['image'])) {
				
				$store->image= $request['image'];
			}
			// if (!empty($collection['slug'])) {
				$slug = Str::slug($request['store_name'], '-');
				$slugExistCount = Store::where('slug', $slug)->count();
				if ($slugExistCount > 0) $slug = $slug.'-'.($slugExistCount+1);
				$store->slug = $slug;
			// }
			$store->created_at = date('Y-m-d H:i:s');
			$store->updated_at = date('Y-m-d H:i:s');
			$store->save();

			$vp = $result1[0]->vp;
			$state = $result1[0]->state;
			$vp = $result1[0]->vp;
			$vp = $result1[0]->vp;

			$retailerListOfOcc = new RetailerListOfOcc;
			$retailerListOfOcc->vp = $result1[0]->vp;
			$retailerListOfOcc->state = $request['state'];
			$retailerListOfOcc->store_id = $store->id;
			$retailerListOfOcc->distributor_name = $request['distributor_name'];
			$retailerListOfOcc->area = $request['area'];
			$retailerListOfOcc->retailer = $request['store_name'];
			$retailerListOfOcc->rsm = $result1[0]->rsm;
			$retailerListOfOcc->asm = $result1[0]->asm;
			$retailerListOfOcc->ase = $result1[0]->ase;
			$retailerListOfOcc->is_active = '1';
			$retailerListOfOcc->is_deleted = '0';
			$retailerListOfOcc->asm_rsm = $result1[0]->rsm;
			$retailerListOfOcc->code = '';
			$retailerListOfOcc->created_at = date('Y-m-d H:i:s');
			$retailerListOfOcc->updated_at = date('Y-m-d H:i:s');
			$retailerListOfOcc->save();
		   
			// notification to Admin
			
				sendNotification(0, 'admin', 'store-add', 'admin.store.index', $store->store_name. '  added by self' , '  Store ' .$store->store_name.' added');


				// notification to ASM
				
				$asm = DB::select("SELECT u.id as asm_id FROM `retailer_list_of_occ` rlo  INNER JOIN users u ON u.name = rlo.asm where rlo.distributor_name = '$request->distributor_name' GROUP BY rlo.asm ");
				foreach($asm as $value){
					sendNotification(0, $value->asm_id, 'store-add', 'front.store.index', $store->store_name. '  added by self' , '  Store ' .$store->store_name.' added');
				}


				// notification to RSM
				
				$rsm = DB::select("SELECT u.id as rsm_id FROM `retailer_list_of_occ` rlo  INNER JOIN users u ON u.name = rlo.rsm where rlo.distributor_name = '$request->distributor_name' GROUP BY rlo.rsm ");
				foreach($rsm as $value){
					sendNotification(0, $value->rsm_id, 'store-add', '', $store->store_name. '  added by self',' Store ' .$store->store_name. ' added');
				}

				// notification to VP
				
				$vp = DB::select("SELECT u.id as vp_id FROM `retailer_list_of_occ` rlo  INNER JOIN users u ON u.name = rlo.vp Where rlo.distributor_name = '$request->distributor_name' GROUP BY rlo.vp ");
				foreach($vp as $value){
					sendNotification(0, $value->vp_id, 'store-add', '', $store->store_name. '  added by self','Store ' .$store->store_name.' added  ');
				}*/

				 return response()->json(['error' => false, 'message' => 'Registration Successful','data'=>$user]);
			
        } else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
    }
	
	 public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'owner_name' => ['required', 'string', 'min:1'],
			'owner_lname' => ['nullable', 'string', 'min:1'],
			'distributor_name' => ['required'],
            'store_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'min:1'],
            'contact' => ['required', 'integer','digits:10'],
            'pin' => ['required', 'integer','digits:6'],
            'state_id' => ['required', 'string','max:255'],
            'area_id' => ['required', 'string','max:255'],
            'city' => ['required', 'string','max:255'],
            'aadhar' => ['nullable'],
      //  ], [
        //    'aadhar.*' => 'Please enter minimum one document'
        ]);

        if (!$validator->fails()) {
			
            $upload_path = "uploads/retailer/document";
			$retailer_id = "ONN".mt_rand();
			$storeExist=Store::where('name',$request['name'])->where('area_id',$request['area_id'])->where('contact',$request['contact'])->where('state_id',$request['state_id'])->where('status',1)->first();
			//dd($storeExist);
			if(($storeExist)){
				
				  return response()->json(['error' => true, 'message' => 'Store/Retailer already exist']);
				}else{
				/*$user= new RetailerUser;
				$user->retailer_id = $retailer_id;
				$user->owner_name = $request->owner_name;
				$user->shop_name = $request->shop_name;
				$user->shop_address = $request->shop_address;
				$user->email = $request->email ?? '';
				$user->mobile = $request->mobile ?? '';
				$user->whatsapp_no = $request->whatsapp_no ?? '';
				$user->pin = $request->pin ?? '';
				$user->state = $request->state ?? '';
				$user->city = $request->city ?? '';
				$user->district = $request->district ?? '';
				$user->password = bcrypt($request['password']);
				$user->created_at = date('Y-m-d g:i:s');
				$user->updated_at = date('Y-m-d g:i:s');
				if (isset($request['aadhar'])) {
					$user->aadhar = $request->aadhar;
				}
				if (isset($request['pan'])) {
					$user->pan = $request->pan;
				}
				if (isset($request['gst'])) {
					$user->gst = $request->gst;
				}
				$user->save();*/
			//$result1 = DB::select("select * from retailer_list_of_occ where distributor_name='$request->distributor_name' AND area='".$request['area']."'");
			$store = new Store;
			//$store->user_id = $request['user_id'];
			$store->name = $request['name'];
			$store->bussiness_name	 = $request['bussiness_name'] ?? null;
			$store->owner_name	 = $request['owner_name'] ?? null;
		    $store->owner_lname	 = $request['owner_lname'] ?? null;
			$store->store_OCC_number = $request['store_OCC_number'] ?? null;
			$store->gst_no = $request['gst_no'] ?? null;
			$store->contact = $request['contact'];
			$store->whatsapp = $request['whatsapp']?? null;
			$store->email	 = $request['email']?? null;
			$store->address	 = $request['address']?? null;
			$store->state_id	 = $request['state_id']?? null;
			$store->city	 = $request['city']?? null;
			$store->pin	 = $request['pin']?? null;
			$store->area_id	 = $request['area_id']?? null;
		    $store->device_id	 = $request['device_id']?? null;
			$store->date_of_birth	 = $request['date_of_birth']?? null;
			$store->date_of_anniversary	 = $request['date_of_anniversary']?? null;
			$store->contact_person	 = $request['contact_person']?? null;
	    	$store->contact_person_lname = $request['contact_person_lname'] ?? null;
			$store->contact_person_phone	= $request['contact_person_phone']?? null;
			$store->contact_person_whatsapp	 = $request['contact_person_whatsapp']?? null;
			$store->contact_person_date_of_birth	 = $request['contact_person_date_of_birth']?? null;
			$store->contact_person_date_of_anniversary	 = $request['contact_person_date_of_anniversary']?? null;
			$store->password = bcrypt($request['password']) ?? null;
				if (isset($request['aadhar'])) {
					$store->aadhar = $request->aadhar;
				}
				if (isset($request['pan'])) {
					$store->pan = $request->pan;
				}
				if (isset($request['gst'])) {
					$store->gst = $request->gst;
				}
			$store->status = '0';
			//$store->gst_no = '';
			if (!empty($request['image'])) {
				
				$store->image= $request['image'];
			}
			// if (!empty($collection['slug'])) {
				$slug = Str::slug($request['store_name'], '-');
				$slugExistCount = Store::where('slug', $slug)->count();
				if ($slugExistCount > 0) $slug = $slug.'-'.($slugExistCount+1);
				$store->slug = $slug;
			// }
			$store->created_at = date('Y-m-d H:i:s');
			$store->updated_at = date('Y-m-d H:i:s');
			$store->save();
			
			
			

			
		   
			
			
				 return response()->json(['error' => false, 'message' => 'Registration Successful','data'=>$store]);
			}
        } else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
    }
    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'contact' => ['required'],
		//	'password' => ['required'],
        ], [
			'contact.integer' => 'Please enter a valid mobile number',
			'contact.digits' => 'Please enter a valid mobile number',
			'contact.exists' => 'We could not find your phone number',
		]);
        if (!$validator->fails()) {
            $login_otp = mt_rand(1111,9999);
            $mobile = $request->contact;
			
			if($mobile== 1234567899){
			    $userCheck = Store::where('contact', $mobile)->with('states','areas')->first();
            
                Store::where('contact', $mobile)->update(['login_otp' => 1234]);
			}else{
              $userCheck = Store::where('contact', $mobile)->with('states','areas')->first();
            
              Store::where('contact', $mobile)->update(['login_otp' => $login_otp]);
			}
            if ($userCheck) {
                if ($userCheck->status != 1) {
                    return response()->json([
                        'status' => 400,
                        'message' => 'You cannot access your account. Please contact Administrator',
                    ]);
                }
            }else{
                 return response()->json([
                        'status' => 400,
                        'message' => 'User is invalid',
                    ]);
            }
        
            // --- Send OTP via cURL to RML Connect ---
                $url = "https://cloudsms.digialaya.com/ApiSmsHttp";

                $params = [
                    "UserId"        => "smspragati@rupa.com",
                    "pwd"           => "pwd2025",
                    "Message"       => "Your PASSCODE for logging into the Rupa Pragati Application is " . $login_otp,
                    "Contacts"      => $mobile,
                    "SenderId"      => "RUPACO",
                    "ServiceName"   => "SMSOTP",
                    "MessageType"   => 1,
                    "DLTTemplateId" => "1707175577377622652",
                ];
            
                // Convert array into query string
                $postData = http_build_query($params);
            
                // Initialize cURL
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url . "?" . $postData);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
                // Execute request
                $response = curl_exec($ch);
            
                // Check for errors
                if (curl_errno($ch)) {
                    return "cURL Error: " . curl_error($ch);
                }
            
                curl_close($ch);
        
           
        
            return response()->json([
                'error' => false,
                'resp' => 'Mobile number matched',
                'data' => $userCheck,
            ]);
			//dd($userCheck);
    //         if ($userCheck) {
    //              if (Hash::check($password, $userCheck->password)) {
    //                   $status = $userCheck->status;
				// 	 if ($status == 0) {
				// 		return response()->json(['error' => true, 'resp' =>  'Your account is temporary blocked. Contact Admin']);
				// 	}else{
				// 		 $store=Store::findOrfail($userCheck->id);
				// 		 $store->device_id =$device_id;
				// 		 $store->save();
    //                  return response()->json(['error' => false, 'resp' => 'Login successful', 'data' => $userCheck]);
				// 	 }
    //                 // return response()->json(['error' => false, 'resp' => 'Login successful', 'data' => $userCheck]);
    //              } else {
    //                  return response()->json(['error' => true, 'resp' => 'You have entered wrong login credential. Please try with the correct one.', 'data' => $userCheck->password]);
    //              }
    //             //return response()->json(['error' => false, 'resp' => 'Login successful', 'data' => $userCheck->mobile]);
    //         } else {
    //             return response()->json(['error' => true, 'resp' => 'You have entered wrong login credential. Please try with the correct one.']);
    //         }
        }
     else {
        return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
    }
    
    
    public function checkCode(Request $request)
{
    $validate = Validator::make($request->all(), [
        'contact' => 'required|integer|digits:10|exists:stores,contact',
        'otp' => 'required|integer',
       
    ], [
        'contact.integer' => 'Please enter a valid mobile number',
        'contact.digits' => 'Please enter a valid mobile number',
        'contact.exists' => 'We could not find your phone number',
    ]);

    if ($validate->fails()) {
        return response()->json([
            'error' => true,
            'message' => $validate->errors()->first()
        ]);
    }

    $user = Store::where('contact', $request->contact)->first();

    if ($user && $user->login_otp == $request->otp) {
        // ✅ Login the user
       

        return response()->json([
            'error' => false,
            'resp' => 'OTP matched',
           
        ]);
    } else {
        return response()->json([
            'error' => true,
            'resp' => 'Please enter valid OTP'
        ]);
    }
}
		 
	 public function demologin(Request $request) {
        $validator = Validator::make($request->all(), [
            'mobile' => ['required', 'integer','digits:10'],
			'password' => ['required'],
        ]);
        if (!$validator->fails()) {
            $mobile = $request->mobile;
			$password = $request->password;
			$device_id = $request->device_id;
            $userCheck = Store::where('contact', $mobile)->first();
			//dd($userCheck);
            if ($userCheck) {
                 if (Hash::check($password, $userCheck->password)) {
					 $status = $userCheck->status;
					 if ($status == 0) {
						return response()->json(['error' => true, 'resp' =>  'Your account is temporary blocked. Contact Admin']);
					}else{
						 $store=Store::findOrfail($userCheck->id);
						 $store->device_id =$device_id;
						 $store->save();
                     return response()->json(['error' => false, 'resp' => 'Login successful', 'data' => $userCheck]);
					 }
                    // return response()->json(['error' => false, 'resp' => 'Login successful', 'data' => $userCheck]);
                 } else {
                     return response()->json(['error' => true, 'resp' => 'You have entered wrong login credential. Please try with the correct one.', 'data' => $userCheck->password]);
                 }
                //return response()->json(['error' => false, 'resp' => 'Login successful', 'data' => $userCheck->mobile]);
            } else {
                return response()->json(['error' => true, 'resp' => 'You have entered wrong login credential. Please try with the correct one.']);
            }
        }
     else {
        return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
    }
	
	
	//contact no wise store list
	
	public function storeList(Request $request)
    {
        //$userId = $_GET['user_id'];
        $contact = $_GET['contact'];
        $stores =Store::where('contact',$contact)->where('status','=',1)->orderby('name')->with('states:id,name','areas:id,name')->get();
        if ($stores) {
		    return response()->json(['error'=>false, 'resp'=>'Store data fetched successfully','data'=>$stores]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }

    }
	//login with pin
	public function loginPin(Request $request) {
        $validator = Validator::make($request->all(), [
            'mobile' => ['required'],
			'secret_pin' => ['required'],
        ]);
        if (!$validator->fails()) {
            $uniqueCode = $request->mobile;
			$password = $request->secret_pin;
            $userCheck = Store::where('contact', $uniqueCode)->with('states','areas')->first();
			//dd($userCheck);
            if ($userCheck) {
                 if($password==$userCheck->secret_pin) {
					 $status = $userCheck->status;
					 if ($status == 0) {
						return response()->json(['error' => true, 'resp' =>  'Your account is temporary blocked. Contact Admin']);
					}else{
                     return response()->json(['error' => false, 'resp' => 'Login successful', 'data' => $userCheck]);
					 }
                    // return response()->json(['error' => false, 'resp' => 'Login successful', 'data' => $userCheck]);
                 } else {
                     return response()->json(['error' => true, 'resp' => 'You have entered wrong login credential. Please try with the correct one.', 'data' => $userCheck->secret_pin]);
                 }
                //return response()->json(['error' => false, 'resp' => 'Login successful', 'data' => $userCheck->mobile]);
            } else {
                return response()->json(['error' => true, 'resp' => 'You have entered wrong login credential. Please try with the correct one.']);
            }
        }
     else {
        return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
    }
	
	/**
     * This method is for show user profile details
     * @param  $id
     *
     */
    public function myprofile($id)
    {
        $is_transac=0;
        $is_submit=0;
        $user = Store::where('id', $id)->with('states','areas')->first();
        $transac=RetailerWalletTxn::where('user_id',$id)->where('type',1)->count();
       
        if($transac==0){
            $is_transac=0;
        }else{
            $is_transac=1;
        }
        $formSubmit=StoreFormSubmit::where('retailer_id',$id)->first();
        if(!empty($formSubmit)){
            $is_submit=1;
        }else{
            $is_submit=0;
        }
        return response()->json(['error'=>false, 'resp'=>'User data fetched successfully','data'=>$user,'is_scan'=>$is_transac,'is_submit'=>$is_submit]);

    }
	
	public function demomyprofile($id)
    {
        $user = Store::where('id', $id)->first();
        return response()->json(['error'=>false, 'resp'=>'User data fetched successfully','data'=>$user]);

    }
	
	/**
     * This method is to update user profile details
     * @param  $id
     */
    public function demoupdateProfile(Request $request,$id)
    {
        $updatedEntry = Store::findOrFail($id);
        if ($request['owner_name']) {
        $updatedEntry->owner_name = $request->owner_name;
        }
        if ($request['shop_name']) {
        $updatedEntry->shop_name = $request->shop_name;
        }
        if ($request['shop_address']) {
        $updatedEntry->shop_address = $request->shop_address;
        }
        if ($request['mobile']) {
        $updatedEntry->mobile = $request->mobile;
        }
        if ($request['email']) {
        $updatedEntry->email = $request->email;
        }
        if ($request['whatsapp_no']) {
        $updatedEntry->whatsapp_no = $request->whatsapp_no;
        }
        if ($request['pin']) {
        $updatedEntry->pin = $request->pin;
        }
        if ($request['district']) {
        $updatedEntry->district = $request->district;
        }
        if ($request['address']) {
        $updatedEntry->state = $request->state;
        }
        if ($request['city']) {
        $updatedEntry->city = $request->city;
        }
        if ($request['state']) {
        $updatedEntry->state = $request->state;
        }
        if ($request['image']) {
            $updatedEntry->image = $request->image;
        }
        if ($request['aadhar']) {
            $updatedEntry->aadhar = $request->aadhar;
        }
        if ($request['pan']) {
            $updatedEntry->pan = $request->pan;
        }
        if ($request['gst']) {
            $updatedEntry->gst = $request->gst;
        }
        $updatedEntry->save();
        if($updatedEntry){
            return response()->json(['error' => false, 'message' => 'Update Successful','data'=>$updatedEntry]);
        } else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
    }
		 
	public function updateProfile(Request $request,$id)
    {
        $updatedEntry = Store::with('states','areas')->findOrFail($id);
        if ($request['owner_name']) {
        $updatedEntry->owner_name = $request->owner_name;
        }
		if ($request['owner_lname']) {
        $updatedEntry->owner_lname = $request->owner_lname;
        }
        if ($request['store_name']) {
        $updatedEntry->store_name = $request->store_name;
        }
        if ($request['address']) {
        $updatedEntry->address = $request->address;
        }
        if ($request['contact']) {
        $updatedEntry->contact = $request->contact;
        }
        if ($request['email']) {
        $updatedEntry->email = $request->email;
        }
        if ($request['whatsapp_no']) {
        $updatedEntry->whatsapp_no = $request->whatsapp_no;
        }
        if ($request['pin']) {
        $updatedEntry->pin = $request->pin;
        }
        if ($request['area']) {
        $updatedEntry->area = $request->area;
        }
        if ($request['address']) {
        $updatedEntry->state = $request->state;
        }
        if ($request['city']) {
        $updatedEntry->city = $request->city;
        }
        if ($request['state']) {
        $updatedEntry->state = $request->state;
        }
        if ($request['image']) {
            $updatedEntry->image = $request->image;
        }
        if ($request['aadhar']) {
            $updatedEntry->aadhar = $request->aadhar;
        }
        if ($request['pan']) {
            $updatedEntry->pan = $request->pan;
        }
        if ($request['gst']) {
            $updatedEntry->gst = $request->gst;
        }
        $updatedEntry->save();
        if($updatedEntry){
            return response()->json(['error' => false, 'message' => 'Update Successful','data'=>$updatedEntry]);
        } else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
    }
	
	//for change password
    public function demochangePassword(Request $request)
    {
        //dd($request->all());
        $validator = Validator::make($request->all(), [
             'mobile'  => 'required',
            'new_password' => 'required'
        ]);
        if (!$validator->fails()) {
        $check_old_pass = RetailerUser::where('mobile',$request->mobile)->first();

        if (!$check_old_pass) {
            return response()->json(['error' => true, 'message' =>'Old Password is not correct']);
        }

        $new_pass = Hash::make($request->new_password);

        $updatedEntry = Store::where('mobile', $request->mobile)->update(['password' => $new_pass]);

            return response()->json(['error' => false, 'message' => 'Update Successful','data'=>$updatedEntry]);
        } else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
    }
		 
	 public function changePassword(Request $request)
    {
        //dd($request->all());
        $validator = Validator::make($request->all(), [
             'mobile'  => 'required',
            'new_password' => 'required'
        ]);
        if (!$validator->fails()) {
        $check_old_pass = Store::where('contact',$request->mobile)->first();

        if (!$check_old_pass) {
            return response()->json(['error' => true, 'message' =>'Old Password is not correct']);
        }

        $new_pass = Hash::make($request->new_password);

        $updatedEntry = Store::where('mobile', $request->mobile)->update(['password' => $new_pass]);

            return response()->json(['error' => false, 'message' => 'Update Successful','data'=>$updatedEntry]);
        } else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
    }
	
	/**
     * This method is to get user wallet balance
    *
    */
    public function demowalletBalance(Request $request,$id)
    {
        $data = RetailerUser::where('id',$id)->first();
        if($data){
            return response()->json(['error'=>false, 'resp'=>'wallet balance data fetched successfully','data'=>$data->wallet]);
        } else {
            return response()->json(['error' => true, 'message' => 'No user found']);
        }
  
    }
		 
	 public function walletBalance(Request $request,$id)
    {
        $data = Store::where('id',$id)->first();
        if($data){
            return response()->json(['error'=>false, 'resp'=>'wallet balance data fetched successfully','data'=>$data->wallet]);
        } else {
            return response()->json(['error' => true, 'message' => 'No user found']);
        }
  
    }
	
	/**
     * This method is to get remove profile
    *
    */
    public function demoremoveProfile(Request $request,$id)
    {
        $data = RetailerUser::where('id',$id)->delete();
        if($data){
            return response()->json(['error'=>false, 'resp'=>'Profile deleted successfully','data'=>$data]);
        } else {
            return response()->json(['error' => true, 'message' => 'Something happend']);
        }
  
    }
		 
		 
	public function removeProfile(Request $request,$id)
    {
        $data = Store::where('id',$id)->delete();
        if($data){
            return response()->json(['error'=>false, 'resp'=>'Profile deleted successfully','data'=>$data]);
        } else {
            return response()->json(['error' => true, 'message' => 'Something happend']);
        }
  
    }
	
	
	public function pinGenerate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret_pin' => ['required', 'integer', 'min:1'],
			'id'   => ['required', 'integer', 'min:1'],
      //  ], [
        //    'aadhar.*' => 'Please enter minimum one document'
        ]);

        if (!$validator->fails()) {
			
				$user= Store::with('states','areas')->findOrFail($request->id);
				$user->secret_pin = $request->secret_pin;
				$user->save();
				 return response()->json(['error' => false, 'message' => 'Pin Generated Successfully','data'=>$user]);
			
        } else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
    }
	
	
	public function terms(Request $request)
    {
        $data=DB::table('reward_terms')->latest('id')->first();
         return response()->json(['error' => false, 'message' => 'Terms & condition fetched Successfully','data'=>$data]);
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
    
    
    
    //form submit
    
    public function branding(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'store_id' => ['required', 'integer'],
      //  ], [
        //    'aadhar.*' => 'Please enter minimum one document'
        ]);
         if (!$validator->fails()) {
			
			$store = new Branding;
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
    
    
     public function brandingList(Request $request,$id)
    {
        $data=Branding::where('store_id',$id)->orderby('id','desc')->first();
        return response()->json(['error' => false, 'message' => 'Branding List fetched  Successfully','data'=>$data]);
    }
    
    
    public function videoDownload(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'store_id' => ['required', 'integer'],
            'is_download'=> ['required', 'integer'],
      //  ], [
        //    'aadhar.*' => 'Please enter minimum one document'
        ]);
         if (!$validator->fails()) {
			
			$store =  StoreFormSubmit::where('retailer_id',$request->store_id)->first();
			$store->is_download = $request->is_download;
			$store->updated_at = date('Y-m-d H:i:s');
			$store->save();
			 return response()->json(['error' => false, 'message' => 'updated successfully','data'=>$store]);
         } else {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()]);
        }
	 
    }
    
    
    //monthly scan limit
    public function monthlyScan(Request $request,$id)
    {
        $currentMonth = Carbon::now()->format('Y-m');
       // dd($currentMonth);
        $storeExist=Store::where('id',$id)->first();
        $storeLimits = DB::table('store_limits')
        ->where('month', $currentMonth)
        ->get()->toArray();
        $storeLimitMap = [];
        foreach ($storeLimits as $storeLimit) {
            $storeLimitMap[$storeLimit->store_id] = $storeLimit->limit;
        }
        $extraStore=['61775','75805','65237','63448','26544','25831','37882','26479','26755','26764','37221','26017','38673','41938','26135','26805','39883','43319','26922','26918','26499','26849','41969','44802','52227','52502','62464','62841','65378','74949','77875','60859','76553','40178','38479','39664','39664','19801','20005','77610'];
        $stateData=['14'];
        $distributorStore = ['1745'];
        $distributorId = implode(',', $distributorStore); // Convert array to string
                        
        $distributorStore = Team::
            whereRaw("find_in_set(?, teams.distributor_id)", [$distributorId])
            ->pluck('store_id')
            ->toArray();
        
        if(in_array($id, $extraStore)){
            $scanLimit=10;
        } elseif (isset($storeLimitMap[$id])) {
            $scanLimit = $storeLimitMap[$id];
        }elseif(in_array($storeExist->state_id, $stateData)){
            $scanLimit=20;
            
        }elseif(in_array($id, $distributorStore)){
           $scanLimit=75;
        }else{
           $scanLimit=10;
        }
        $data=RetailerWalletTxn::where('user_id',$id)->where('type',1)->whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->count();
        return response()->json(['error' => false, 'message' => 'Monthly Scan Limit History','Monthly Scan Limit'=>$scanLimit,'Scan history by retailer'=>$data,'Monthly_Scan_Limit'=>$scanLimit,'Scan_history_by_retailer'=>$data]);
    }
	
}
