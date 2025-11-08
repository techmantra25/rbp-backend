<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\User;
use App\Models\Team;
use App\Models\Area;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;
class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //$userId = $_GET['user_id'];
        $areaId = $_GET['area_id'];
        $stores =Store::where('area_id',$areaId)->where('status','=',1)->orderby('name')->with('states:id,name','areas:id,name')->get();
        if ($stores) {
		    return response()->json(['error'=>false, 'resp'=>'Store data fetched successfully','data'=>$stores]);
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
        $validator = Validator::make($request->all(),[
            'user_id' => 'required|integer',
            'contact' => 'required|integer|unique:stores|min:1|digits:10',
            'whatsapp' => 'nullable|integer|unique:stores|min:1|digits:10',
            'name' => 'required',
            'distributor_id' => 'required',
            'owner_fname' => 'required|regex:/^[\pL\s\-]+$/u',
           'owner_lname' => 'required|regex:/^[\pL\s\-]+$/u',
           'image' => 'required',
           'contact_person_fname' => 'required|regex:/^[\pL\s\-]+$/u',
           'pin' => 'required|integer|digits:6',
           'contact_person_lname' => 'required|regex:/^[\pL\s\-]+$/u',
           'area_id' => 'required',
           'state_id' => 'required',
           'district' => 'required',
           'contact_person_phone' => 'required|integer|',
            'pan_no' => 'nullable|min:10|',
        ]);

        if(!$validator->fails()){
            $distributorPincode = $request->pin;
            if (in_array($request->state_id, [23, 14, 17, 25, 19, 9, 7,20,29,24,2,22,39,38,18,5,13,28])) {
                $wrongPin=User::where('id', $request->distributor_id) // Assuming '7' is the type for distributors
                    ->whereRaw("FIND_IN_SET(?, postal_code)", [$distributorPincode])->first();
            }else{
                $wrongPin='default';
            }
            if(empty($wrongPin))  {     
                return response()->json(['error'=>true, 'resp'=>'The provided PIN does not match the distributor\'s postal code. Store cannot be added.']);
            }else{
                    //dd($distributorPincode);
                    // Check if the distributor pincode is present in any other distributors
                    if(!empty($distributorPincode)){
                    $matchingDistributors = User::where('type', 7) // Assuming '7' is the type for distributors
                    ->whereRaw("FIND_IN_SET(?, postal_code)", [$distributorPincode]) // Check if the pin exists in postal_code
                    ->where('id', '!=', $request->distributor_id) // Exclude the current distributor
                    ->pluck('id') // Get the IDs of the matching distributors
                    ->toArray();
                    //dd($matchingDistributors);
                    // If there are matching distributors, append their IDs to the current distributor ID
                    $distributorIds = !empty($matchingDistributors)
                        ? implode(',', array_merge([$request->distributor_id], $matchingDistributors))
                        : $request->distributor_id;
                    }else{
                        $distributorIds=$request->distributor_id;
                    }
                    //dd($distributorIds);
                    $result = Team::where('ase_id',$request->user_id)->where('area_id',$request->area_id)->where('distributor_id',$request->distributor_id)->first();
                    $user = User::where('id',$request->user_id)->first();
                    $name = $user->name;
                    $store=new Store();
                    $store->user_id = $request->user_id;
        			$store->name = $request->name;
        			$store->business_name	 = $request->business_name ?? null;
        			$store->owner_fname	 = $request->owner_fname ?? null;
        		    $store->owner_lname	 = $request->owner_lname ?? null;
        			$store->store_OCC_number = $request->store_OCC_number ?? null;
        			$store->gst_no = $request->gst_no ?? null;
        			$store->pan_no = $request->pan_no ?? null;
        			$store->contact = $request->contact;
        			$store->whatsapp = $request->whatsapp?? null;
        			$store->email	 = $request->email?? null;
        			$store->address	 = strtoupper($request->address)?? null;
        			$store->state_id	 = $request->state_id?? null;
        			$store->city	 = $request->city?? null;
        			$store->district	 = $request->district ?? null;
        			$store->pin	 = $request->pin?? null;
        			$store->area_id	 = $request->area_id?? null;
        			$store->date_of_birth	 = $request->date_of_birth?? null;
        			$store->date_of_anniversary	 = $request->date_of_anniversary?? null;
        			$store->contact_person_fname	 = $request->contact_person_fname ?? null;
        	    	$store->contact_person_lname = $request->contact_person_lname ?? null;
        			$store->contact_person_phone	= $request->contact_person_phone ?? null;
        			$store->contact_person_whatsapp	 = $request->contact_person_whatsapp ?? null;
        			$store->contact_person_date_of_birth	 = $request->contact_person_date_of_birth ?? null;
        			$store->contact_person_date_of_anniversary	 = $request->contact_person_date_of_anniversary ?? null;
                    $orderData = Store::select('sequence_no')->latest('sequence_no')->first();
        				
        				    if (empty($store->sequence_no)) {
        						if (!empty($orderData->sequence_no)) {
        							$new_sequence_no = (int) $orderData->sequence_no + 1;
        							
        						} else {
        							$new_sequence_no = 1;
        							
        						}
        					}
        			$uniqueNo = sprintf("%'.06d",$new_sequence_no);
        		    $store->sequence_no = $new_sequence_no;
        			$store->unique_code = 'ST'.$uniqueNo;
        			$store->status = '0';
        			if (!empty($request['image'])) {
        				$store->image= $request->image;
        			}
        			
                    $slug = Str::slug($request->name, '-');
                    $slugExistCount = Store::where('slug', $slug)->count();
                    if ($slugExistCount > 0) $slug = $slug.'-'.($slugExistCount+1);
                    $store->slug = $slug;
        			
        			$store->created_at = date('Y-m-d H:i:s');
        			$store->updated_at = date('Y-m-d H:i:s');
        			$store->save();
        
        			$nsm_id = $result->nsm_id;
        			$state_id = $result->state_id;
        			$zsm_id = $result->zsm_id;
        			$rsm_id = $result->rsm_id;
                    $asm_id = $result->asm_id;
                    $sm_id = $result->sm_id;
        
        			$team = new Team;
        			$team->nsm_id = $nsm_id;
        			$team->state_id = $state_id;
        			$team->zsm_id = $zsm_id;
        			$team->rsm_id = $rsm_id;
        			$team->asm_id = $asm_id;
        			$team->sm_id = $sm_id;
        			$team->ase_id = $request->user_id;
        			$team->area_id = $request->area_id;
        			if (in_array($request->state_id, [23, 14, 17, 25, 19, 9, 7,20,29,24,2,22,39,38,18,5,13,28])) {
        			  $team->distributor_id = $distributorIds;
                    
        			}else{
        			    $team->distributor_id = $request->distributor_id;
        			}
        			$team->store_id = $store->id;
        			$team->status = '1';
        			$team->is_deleted = '0';
        			$team->created_at = date('Y-m-d H:i:s');
        			$team->updated_at = date('Y-m-d H:i:s');
        			$team->save();
        			// notification to Admin
        			$loggedInUser = $name;
        				sendNotification($store->user_id, 'admin', 'store-add', 'admin.store.index', $store->name. '  added by ' .$loggedInUser , '  Store ' .$store->name.' added');
        				// notification to ASM
        				$loggedInUser = $name;
        				$asm = DB::select("SELECT u.id as asm_id FROM `teams` t  INNER JOIN users u ON u.id = t.asm_id where t.ase_id = '$request->user_id' GROUP BY t.asm_id");
        				foreach($asm as $value){
        					sendNotification($store->user_id, $value->asm_id, 'store-add', 'front.store.index', $store->name. '  added by ' .$loggedInUser , '  Store ' .$store->name.' added');
        				}
        				// notification to RSM
        				$loggedInUser = $name;
        				$rsm = DB::select("SELECT u.id as rsm_id FROM `teams` t  INNER JOIN users u ON u.id = t.rsm_id where t.ase_id = '$request->user_id' GROUP BY t.rsm_id");
        				foreach($rsm as $value){
        					sendNotification($store->user_id, $value->rsm_id, 'store-add', '', $store->name. '  added by '  .$loggedInUser ,' Store ' .$store->name. ' added');
        				}
        
        				// notification to SM
        				$loggedInUser = $name;
        				$sm = DB::select("SELECT u.id as sm_id FROM `teams` t  INNER JOIN users u ON u.id = t.sm_id where t.ase_id = '$request->user_id' GROUP BY t.sm_id");
        				foreach($sm as $value){
        					sendNotification($store->user_id, $value->sm_id, 'store-add', '', $store->name. '  added by ' .$loggedInUser ,'Store ' .$store->name.' added  ');
        				}
                        // notification to ZSM
        				$loggedInUser = $name;
        				$zsm = DB::select("SELECT u.id as zsm_id FROM `teams` t  INNER JOIN users u ON u.id = t.zsm_id where t.ase_id = '$request->user_id'  GROUP BY t.zsm_id");
        				foreach($zsm as $value){
        					sendNotification($store->user_id, $value->zsm_id, 'store-add', '', $store->name. '  added by ' .$loggedInUser ,'Store ' .$store->name.' added  ');
        				}
                        // notification to NSM
        				$loggedInUser = $name;
        				$nsm = DB::select("SELECT u.id as nsm_id FROM `teams` t  INNER JOIN users u ON u.id = t.nsm_id where t.ase_id = '$request->user_id' GROUP BY t.nsm_id");
        				foreach($nsm as $value){
        					sendNotification($store->user_id, $value->nsm_id, 'store-add', '', $store->name. '  added by ' .$loggedInUser ,'Store ' .$store->name.' added  ');
        				}
                        return response()->json(['error'=>false, 'resp'=>'Store data created successfully','data'=>$store]);
            }
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
    public function show($id)
    {
        //
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

     /**
     * Store image a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function imageCreate(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'image' => ['required', 'image', 'max:1000000']
        ]);

        if(!$validator->fails()){
            $imageName = mt_rand().'.'.$request->image->extension();
			$uploadPath = 'public/uploads/store';
			$request->image->move($uploadPath, $imageName);
			$total_path = $uploadPath.'/'.$imageName;
            
			return response()->json(['error' => false, 'resp' => 'Image added', 'data' => $total_path]);

        }else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }

    }

    //ase wise distributor list
    public function distributorList(Request $request)
    {
        $ase = $_GET['user_id'];
        $area = $_GET['area_id'];
        $data= Team::select('distributor_id','area_id')->where('ase_id',$ase)->where('area_id',$area)->where('store_id',NULL)->with('distributors:id,name,mobile,email,address,city,state')->distinct('distributor_id')->get();
        if($data)
        {
            return response()->json(['error' => false, 'resp' => 'Distributor data fetched successfully','data' => $data]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }
   }

   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function stateList(Request $request)
    {
        $areaId = $_GET['area_id'];
        $stores =Area::where('id',$areaId)->where('status',1)->orderby('id','desc')->with('states:id,name')->first();
        if ($stores) {
		    return response()->json(['error'=>false, 'resp'=>'State data fetched successfully','data'=>$stores]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }

    }

    //all store search area wise 
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'area_id' => 'required',
            'keyword' => 'required'
        ]);

        if(!$validator->fails()){
            $areaId = $_GET['area_id'];
            $search = $_GET['keyword'];
            $data = Store::select('*');
            
            if(!empty($search)){
                $data = $data->where('status','=',1)->where('contact', '=',$search)->orWhere('name', 'like', '%'.$search.'%')->where('area_id',$areaId)->with('states:id,name','areas:id,name');
            }        

            $data = $data->get();
           
            if(!empty($data)){
                foreach($data as $item){
                    $retailer=Team::select('id','distributor_id')->where('store_id',$item->id)->with('distributors:id,name')->first();
                    $item->team = $retailer;
                }
            }
            return response()->json([
                'error'=>false,
                'resp'=>"Store List",
                'data'=> $data
                
            ]);
        }else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }

    }

    //store search for individual ASE's store
    public function searchuserStore(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'user_id' => 'required',
            'area_id' => 'required',
            'keyword' => 'required'
        ]);

        if(!$validator->fails()){
            $userId = $_GET['user_id'];
            $areaId = $_GET['area_id'];
            $search = $_GET['keyword'];
            $data = Store::select('*');
            
            if(!empty($search)){
                $data = $data->where('status','=',1)->where('contact', '=',$search)->orWhere('name', 'like', '%'.$search.'%')->where('area_id',$areaId)->with('states:id,name','areas:id,name');
            }        

            $data = $data->get();
            if(!empty($data)){
                foreach($data as $item){
                    $retailer=Team::select('id','distributor_id')->where('store_id',$item->id)->with('distributors:id,name')->first();
                    $item->team = $retailer;
                }
            }
            return response()->json([
                'error'=>false,
                'resp'=>"Store List",
                'data'=> $data
                
            ]);
        }else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }

    }
    
    //inactive store list user wise
    public function inactiveStorelist(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'user_id' => 'required',
            'area_id' => 'required'
        ]);
        if(!$validator->fails()){
    		$ase = $_GET['user_id'];
    		$area = $_GET['area_id'];
            $stores = Store::where('area_id',$area)->where('status',0)->get();
            if ($stores) {
                return response()->json(['error'=>false, 'resp'=>'Store data fetched successfully','data'=>$stores]);
            } else {
                return response()->json(['error' => true, 'resp' => 'Something happened']);
            }
        }else {
                return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
            }  
    }
	
	
	//all store search area wise 
    // public function searchStoreAll(Request $request)
    // {
    //     $validator = Validator::make($request->all(),[
    //         'keyword' => 'required'
    //     ]);

    //     if(!$validator->fails()){
    //         $search = $_GET['keyword'];
    //         $data = Store::select('*');
            
    //         if(!empty($search)){
    //             $data = $data->where('contact', '=',$search)->orWhere('name', 'like', '%'.$search.'%')->with('states:id,name','areas:id,name')->where('status','=',1);
    //         }        

    //         $data = $data->get();
           
    //         if(!empty($data)){
    //             foreach($data as $item){
    //                 $retailer=Team::select('id','distributor_id')->where('store_id',$item->id)->with('distributors:id,name')->first();
    //                 $item->team = $retailer;
    //             }
    //         }
    //         return response()->json([
    //             'error'=>false,
    //             'resp'=>"Store List",
    //             'data'=> $data
                
    //         ]);
    //     }else {
    //         return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
    //     }

    // }
     public function distributorListArea(Request $request)
     {
        $areaId = $_GET['area_id'];
        $stores =Team::where('area_id',$areaId)->where('store_id',NULL)->groupby('distributor_id')->with('distributors:id,name')->get();
        if ($stores) {
		    return response()->json(['error'=>false, 'resp'=>'Distributor data fetched successfully','data'=>$stores]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }
     }
     
}
