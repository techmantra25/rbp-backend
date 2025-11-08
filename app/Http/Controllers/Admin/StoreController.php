<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\User;
use App\Models\State;
use App\Models\Area;
use App\Models\Team;
use App\Models\UserNoOrderReason;
use App\Models\RetailerWalletTxn;
use App\Models\RetailerUserTxnHistory;
use App\Models\NoOrderReason;
use App\Models\Activity;
use App\Models\Order;

use Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Hash;
class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
  public function index(Request $request)
{
    $allASEs = User::select('id', 'name')->whereNotNull('name')->where('status', 1)->groupBy('name')->orderBy('name')->get();
    $allASMs = User::select('id', 'name')->where('type', 5)->whereNotNull('name')->where('status', 1)->groupBy('name')->orderBy('name')->get();
    $allDistributors = User::select('id', 'name','employee_id','state')->where('type', 7)->whereNotNull('name')->where('status', 1)->groupBy('name')->orderBy('name')->get();
    $state = State::where('status', 1)->groupBy('name')->orderBy('name')->get();
    $inactiveStore = Store::where('status', 0)->groupBy('name')->get();

    $query = Store::selectRaw('stores.*')->with('states', 'areas', 'users');

    if (
        $request->filled('date_from') || 
        $request->filled('date_to') || 
       
        $request->filled('ase_id') || 
        
        $request->filled('state_id') || 
        $request->filled('keyword') || 
        $request->filled('area_id') || 
        $request->filled('status_id') 
        
    ) {
        $from = $request->date_from ?: null;
        $to = $request->date_to ? date('Y-m-d', strtotime($request->date_to . '+1 day')) : null;

        if ($from && $to) {
            $query->whereBetween('stores.created_at', [$from, $to]);
        }

       

        $query->when($request->ase_id, function ($query) use ($request) {
            $query->whereRaw("find_in_set(?, stores.user_id)", [$request->ase_id]);
        });

       

        $query->when($request->state_id, function ($query) use ($request) {
            $query->where('stores.state_id', $request->state_id);
        });

        $query->when($request->area_id, function ($query) use ($request) {
            $query->where('stores.area_id', $request->area_id);
        });

        $query->when($request->keyword, function ($query) use ($request) {
            $query->where('stores.name', 'like', '%' . $request->keyword . '%')
                ->orWhere('stores.business_name', 'like', '%' . $request->keyword . '%')
                ->orWhere('stores.owner_fname', 'like', '%' . $request->keyword . '%')
                ->orWhere('stores.contact', '=', $request->keyword)
                ->orWhere('stores.unique_code', 'like', '%' . $request->keyword . '%')
                ;
        });

        if ($request->filled('status_id')) {
            $query->where('stores.status', $request->status_id === 'active' ? 1 : 0);
        }

        

        $data = $query->latest('stores.id')->paginate(25);
    } else {
        $data = $query->latest('id')->paginate(25);
        
    }

    return view('admin.store.index', compact('data', 'request', 'allASEs', 'allASMs', 'allDistributors', 'state', 'inactiveStore'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = (object)[];
        $data->stores = Store::where('id',$id)->with('users','states','areas')->first();
		//dd($data->stores);
        $data->team = Team::where('store_id', $id)->with('distributors','rsm','zsm','nsm','asm','sm','ase')->first();
        $data->users = User::all();
        return view('admin.store.detail', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = (object)[];
        $data->stores = Store::with('users','states','areas')->findOrfail($id);
        $data->states=State::where('status',1)->groupby('name')->orderby('name')->get();
        $data->team = Team::where('store_id', $id)->with('distributors','rsm','zsm','nsm','asm','sm','ase')->first();
        $data->users = User::where('name', '!=', NULL)->where('status',1)->groupBy('name')->orderBy('name')->get();
        $data->asms = User::where('type',5)->where('name', '!=', NULL)->where('status',1)->groupBy('name')->orderBy('name')->get();
        $data->allDistributors = User::select('id','name','state')->where('type',7)->where('name', '!=', NULL)->where('status',1)->groupBy('name')->orderBy('name')->get();
        return view('admin.store.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       // dd($request->all());
           $request->validate([
            'name' => 'required|string|min:2|max:255',
            'business_name' => 'nullable|string|min:2|max:255',
            'distributor_id' => 'nullable',
			'owner_fname' =>'required|string|max:255',
			'owner_lname' =>'nullable|string|max:255',
            'gst_no' => 'nullable',
            'contact' => 'required|integer|digits:10',
            'whatsapp' => 'nullable|integer|digits:10',
            'email' => 'nullable|email',
			'date_of_birth' =>'nullable',
            'date_of_anniversary' =>'nullable',
            'address' => 'nullable',
            'area_id' => 'nullable',
            'state_id' => 'nullable',
            'city' => 'nullable',
            'pin' => 'nullable|integer|digits:6',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10000000',
        ]);

         
        $store=Store::where('id',$id)->first();
	
		
        // update store table
        $store = Store::findOrFail($id);
        $store->user_id = $request['ase_id'];
        $store->gst_no = $request->gst_no ?? null;
        $store->pan_no = $request->pan_no ?? null;
        // slug update
        if ($store->name != $request->name) {
            $slug = Str::slug($request->name, '-');
            $slugExistCount = Store::where('name', $request->name)->count();
            if ($slugExistCount > 0) $slug = $slug.'-'.($slugExistCount);
            $store->slug = $slug;
        }

        $store->name = $request->name ?? null;
        $store->business_name = $request->business_name ?? null;
        $store->store_OCC_number = $request->store_OCC_number ?? null;
		$store->owner_fname = $request->owner_fname ?? null;
		$store->owner_lname = $request->owner_lname ?? null;
        $store->contact = $request->contact ?? null;
        $store->email = $request->email ?? null;
        $store->whatsapp = $request->whatsapp ?? null;
		$store->date_of_birth = $request->date_of_birth ?? null;
		$store->date_of_anniversary = $request->date_of_anniversary ?? null;
        $store->address = $request->address ?? null;
        $store->area_id = $request->area_id;
        $store->state_id = $request->state_id;
        $store->city = $request->city;
        $store->pin = $request->pin ?? null;
        $store->district = $request->district ?? null;
		$store->contact_person_fname = $request->contact_person_fname ?? null;
		$store->contact_person_lname = $request->contact_person_lname ?? null;
        $store->contact_person_phone = $request->contact_person_phone ?? null;
        $store->contact_person_whatsapp = $request->contact_person_whatsapp ?? null;
        $store->contact_person_date_of_birth = $request->contact_person_date_of_birth ?? null;
        $store->contact_person_date_of_anniversary = $request->contact_person_date_of_anniversary ?? null;

        // image upload
        if($request->hasFile('image')) {
            $imageName = mt_rand().'.'.$request->image->extension();
            $uploadPath = 'public/uploads/store';
            $request->image->move($uploadPath, $imageName);
            $store->image = $uploadPath.'/'.$imageName;
        }
        
		$store->updated_at = now();
        $store->save();
        
        
          
        
        return redirect()->back()->with('success', 'Store information updated successfully');
    }

    

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $isReferenced = DB::table('retailer_wallet_txns')->where('user_id', $id)->exists();
    
        if ($isReferenced) {
            return redirect()->route('admin.stores.index')->with('error', 'Store cannot be deleted because it is referenced in another table.');
        }
        $data=Store::destroy($id);
        if ($data) {
            return redirect()->back()->with('success','Deleted successfully');
        } else {
            return redirect()->route('admin.stores.index')->with('failure', 'Failed to delete store.');
        }
    }

     /**
     * status change the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   public function status(Request $request, $id)
  {
    $category = Store::findOrFail($id);
    
    // Toggle status
    $status = ($category->status == 1) ? 0 : 1;
    
    // Check if the state ID matches the specific ones
    if (in_array($category->state_id, [23, 14, 17, 25, 19, 9, 7,20,29,24,2,22,39,38,18,5,13,28])) {
        
        // Get the distributor related to the store
        
                $distributor = Team::select('distributor_id')->where('store_id', $id)->first();
                
                if ($distributor) {
                    $state=State::where('id',$category->state_id)->first();
                    // Fetch matching distributors based on postal codes, excluding the current distributor ID
                    $matchingDistributors = User::where('type', 7) // Assuming '7' is the type for distributors
                         ->where('state',$state->name)
                        ->whereRaw("FIND_IN_SET(?, postal_code)", [$category->pin]) // Check if the pin exists in postal_code
                        //->where('id', '!=', $distributor->distributor_id) // Exclude the current distributor
                        
                        ->pluck('id') // Get the IDs of the matching distributors
                        ->toArray();
                    //dd($matchingDistributors);
                    // If there are matching distributors, merge them with the current distributor ID(s)
                    if (!empty($matchingDistributors)) {
                        // Get the current distributor_id(s) and convert them to an array if it's a comma-separated string
                        //$currentDistributorIds = explode(',', $distributor->distributor_id);
                        //$onlyFirst[]=$currentDistributorIds[0];
                        //dd($onlyFirst);
                        // Merge current distributor IDs with the new matching IDs, avoiding duplicates
                        $distributorIds = array_unique($matchingDistributors);
        
                        // Convert the distributor IDs back to a comma-separated string
                        $distributorIdsString = implode(',', $distributorIds);
                        //dd($distributorIdsString);
                        // Save the updated distributor IDs
                        $storeData = Team::where('store_id', $category->id)->first();
                        if(!empty($storeData->distributor_id)){
                            $storeData->distributor_id = $distributorIdsString; // Save as comma-separated
                            $storeData->save(); // Save the store data
                        }else{
                            $storeData->distributor_id = $distributorIdsString; // Save as comma-separated
                            $storeData->save();
                        }
                    }else{
                        $area= $category->area_id;
                        $state=$category->state_id;
                        $primaryDistributor=Team::select('distributor_id')->where('state_id', $state)->where('area_id', $area)->where('ase_id', $category->user_id)->orWhere('asm_id', $category->user_id)->where('store_id',NULL)->first();
                        $storeData = Team::where('store_id', $category->id)->first();
                        if(!empty($primaryDistributor)){
                            $storeData->distributor_id = $primaryDistributor->distributor_id; // Save as comma-separated
                            $storeData->save(); // Save the store data
                        }else{
                            $storeData->distributor_id = NULL; // Save as comma-separated
                            $storeData->save(); 
                        }
                    }
                }
            
        if ($status == 1) {
            // Fetch distributor IDs for the store
            $distributor = Team::select('distributor_id')->where('store_id', $id)->first();
            
            if (!empty($distributor)) {
                $discat = explode(",", $distributor->distributor_id);
                
                // Array to hold all postal codes
                $allPincodes = [];
                
                // Collect pincodes of all relevant distributors
                foreach ($discat as $cat) {
                    $user = User::where('id', $cat)->first();
                    
                    if ($user && !empty($user->postal_code)) {
                        // Merge distributor's pincodes into a single array
                        $pincodeArray = explode(',', $user->postal_code);
                        $allPincodes = array_merge($allPincodes, $pincodeArray);
                    }
                }
                
                // Now check if the store's pin code exists in the combined array of pincodes
                if (in_array($category->pin, $allPincodes)) {
                    $category->status = $status;
                    $category->save();
                } else {
                    return redirect()->back()->with('failure', 'Pincode is not authorized with any distributor');
                }
            }
        }else{
            $category->status = $status;
            $category->save();
        }
    } else {
        // Directly update status for states outside of the specified ones
        $category->status = $status;
        $category->save();
    }
    
    // Reset password after status update
    $category->password = Hash::make('Welcome@2023');
    $category->save();
    
    // Redirect with success or failure message
    if ($category) {
        return redirect()->back()->with('success', 'Status updated successfully');
    } else {
        return redirect()->route('admin.stores.create')->withInput($request->all());
    }
}

    //state wise area
    public function stateWiseArea(Request $request, $state)
    {
		$stateName=State::where('id',$state)->first();
		$region = Area::where('state_id',$state)->get();
        $resp = [
            'state' => $stateName->name,
            'area' => [],
        ];

        foreach($region as $area) {
            $resp['area'][] = [
                'area_id' => $area->id,
                'area' => $area->name,
            ];
        }
        
		return response()->json(['error' => false, 'resp' => 'State wise area list', 'data' => $resp]);
    }

    //export data into csv

  public function csvExport(Request $request)
  {
      $from = $request->date_from ? $request->date_from : '';
      $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
     if(isset($request->date_from) || isset($request->date_to) || isset($request->distributor_id)||isset($request->ase_id)||isset($request->asm_id)||isset($request->state_id)||isset($request->keyword)||isset($request->area_id)||isset($request->status_id)||isset($request->zsm_approval_id)) 
        {
            $from = $request->date_from ? $request->date_from : '';
            $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';

            $distributor = $request->distributor_id ? $request->distributor_id : '';
            $ase = $request->ase_id ? $request->ase_id : '';
            $asm = $request->asm_id ? $request->asm_id : '';
            $stateDetails = $request->state_id ? $request->state_id : '';
            $area = $request->area_id ? $request->area_id : '';
            $keyword = $request->keyword ? $request->keyword : '';
			$statusData = $request->status_id ;
		
			$zsm_approval = $request->zsm_approval_id ;
            $query = Store::selectRaw('stores.*')->with('states','areas','users')->join('teams', 'teams.store_id', 'stores.id');
            if ($from && $to) {
            $query->whereBetween('stores.created_at', [$from, $to]);
        }

        $query->when($request->distributor_id, function ($query) use ($request) {
            $query->whereRaw("find_in_set(?, teams.distributor_id)", [$request->distributor_id]);
        });

        $query->when($request->ase_id, function ($query) use ($request) {
            $query->whereRaw("find_in_set(?, stores.user_id)", [$request->ase_id]);
        });

        $query->when($request->asm_id, function ($query) use ($request) {
            $query->whereRaw("find_in_set(?, stores.user_id)", [$request->asm_id]);
        });

        $query->when($request->state_id, function ($query) use ($request) {
            $query->where('stores.state_id', $request->state_id);
        });

        $query->when($request->area_id, function ($query) use ($request) {
            $query->where('stores.area_id', $request->area_id);
        });

        $query->when($request->keyword, function ($query) use ($request) {
            $query->where('stores.name', 'like', '%' . $request->keyword . '%')
                ->orWhere('stores.business_name', 'like', '%' . $request->keyword . '%')
                ->orWhere('stores.owner_fname', 'like', '%' . $request->keyword . '%')
                ->orWhere('stores.contact', '=', $request->keyword)
                ->orWhere('stores.pin', '=', $request->keyword);
        });

        if ($request->filled('status_id')) {
            $query->where('stores.status', $request->status_id === 'active' ? 1 : 0);
        }

        if ($request->filled('zsm_approval_id')) {
            $query->where('stores.zsm_approval', $request->zsm_approval_id === 'active' ? 1 : 0);
        }

        $data = $query->where('stores.user_id', '!=', '')->latest('stores.id')->cursor();
            $users = $data->all();
           //dd($users);
        }
        else{
            $data = Store::selectRaw('stores.*')->join('teams', 'teams.store_id', 'stores.id')->where('stores.user_id','!=','')
            ->with('states','areas','users')->latest('id')->cursor();
            $users = $data->all();
            //dd($data);
        }
        
        
        $filename = "Lux-store-list-".$from.' to '.$to.".csv";
            $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];


    return Response::stream(function () use ($users, $headers) {
        $file = fopen('php://output', 'w');
        fputcsv($file, ['SR', 'UNIQUE CODE','STORE', 'FIRM', 'ADDRESS','TOWN/CITY', 'AREA','DISTRICT','PINCODE','STATE','OWNER NAME','MOBILE', 'WHATSAPP', 'CONTACT PERSON', 'CONTACT PERSON PHONE', 'OWNER DATE OF BIRTH', 'OWNER DATE OF ANNIVERSARY','EMAIL', 'GST NUMBER','PAN NO','VIDEO LINK','DISTRIBUTOR','DISTRIBUTOR CODE','DISTRIBUTOR CITY','DISTRIBUTOR STATE','CREATED BY','EMP CODE', 'ASE','ASM', 'SM','RSM', 'ZSM', 'NSM','STATUS', 'DATE','TIME']);
         $count = 1;
        foreach ($users as $row) {
            $distributorValue=[];
            $distributorName=[];
            $distributorCode=[];
            $distributorCity=[];
            $distributorState=[];
            $distributorCodeValue=[];
            $distributorCityValue=[];
            $distributorStateValue=[];
              $date = date('j F, Y', strtotime($row['created_at']));
              $time = date('h:i A', strtotime($row['created_at']));
              $displayASEName = '';
              // foreach(explode(',',$row->user_id) as $aseKey => $aseVal) 
                //{
                    
                    $catDetails = DB::table('users')->where('id', $row['user_id'])->first();
                    $displayASEName = $catDetails->name ?? '';
                    $displayASECode = $catDetails->employee_id ?? '';
              // }
                $store_name = $row->store_name ?? '';
               
                $storename = Team::where('store_id', $row->id)->with('distributors','rsm','zsm','nsm','asm','sm','ase')->first();
                //$cat = explode(",", $storename->distributor_id);
                if (strpos($storename->distributor_id, ',') !== false) {
                    // If a comma is present, split the string into an array
                    $cat = explode(",", $storename->distributor_id);
                } else {
                    // If no comma is present, just wrap the single value into an array
                    $cat = [$storename->distributor_id];
                }
                // foreach($cat as $item1){
                                
                //     $distributor=DB::table('users')->where('id',$item1)->first();
                               
                //     $distributorName[]=  $distributor->name;
                //     $distributorCode[]=$distributor->employee_id;
                //     $distributorCity[]=$distributor->city;
                //     $distributorState[]=$distributor->state;
                //  }
                //   $distributorNameValue=implode(', ', $distributorName) ; 
                //   $distributorCodeValue=implode(', ', $distributorCode) ; 
                //   $distributorCityValue=implode(', ', $distributorCity) ; 
                //   $distributorStateValue=implode(', ', $distributorState) ; 
                   $primaryDistributor=DB::table('users')->where('id',$cat[0])->first();
            for ($i = 0; $i < count($cat); $i++) { 
                
                $distributor = DB::table('users')->where('id', $cat[$i])->first();
                fputcsv($file, [
                     $count++,
                    $row->unique_code ??'',
                    ucwords($row->name),
                    ucwords($row->business_name),
                    ucwords($row->address),
                    $row->city,
                    $row->areas->name ?? '',
                    $row->district ?? '',
                    $row->pin,
                    $row->states->name ?? '',
                    ucwords($row->owner_fname.' '.$row->owner_lname),
                    $row->contact,
                    $row->whatsapp,
                    $row->contact_person_fname.' '.$row->contact_person_lname,
                    $row->contact_person_phone,
                    $row->date_of_birth,
                    $row->date_of_anniversary,
                    $row->email,
                    $row->gst_no,
                    $row->pan_no,
                    $row->video_link ?? '',
                    //$primaryDistributor->name??'',
                    //$primaryDistributor->employee_id??'',
                    //$primaryDistributor->city??'',
                    //$primaryDistributor->state??'',
                    //$distributorNameValue ?? '',
                    //$distributorCodeValue ?? '',
                    //$distributorCityValue ?? '',
                    //$distributorStateValue ?? '',
                    $distributor->name ?? '', // Secondary Distributor Name
                    $distributor->employee_id ?? '', // Secondary Distributor Code
                    $distributor->city ?? '', // Secondary Distributor City
                    $distributor->state ?? '', 
                    $displayASEName ?? '',
                    $displayASECode ?? '',
                    $storename->ase->name ?? '',
                    $storename->asm->name ?? '',
                    $storename->sm->name ?? '',
                    $storename->rsm->name ?? '',
                    $storename->zsm->name ?? '',
                    $storename->nsm->name ?? '',
                    
                    ($row->status == 1) ? 'Active' : 'Inactive',
                    $date,
                    $time]);
            }
        }

        fclose($file);
    }, 200, $headers);
        // if (count($data) > 0) {
        //     $delimiter = ",";
        //     $filename = "Lux-store-list-".$from.' to '.$to.".csv";

        //     // Create a file pointer
        //     $f = fopen('php://memory', 'w');

        //     // Set column headers
            
        //     $fields = array('SR', 'UNIQUE CODE','STORE', 'FIRM', 'ADDRESS','TOWN/CITY', 'AREA','DISTRICT','PINCODE','STATE','OWNER NAME','MOBILE', 'WHATSAPP', 'CONTACT PERSON', 'CONTACT PERSON PHONE', 'OWNER DATE OF BIRTH', 'OWNER DATE OF ANNIVERSARY','EMAIL', 'GST NUMBER','PAN NO','DISTRIBUTOR','DISTRIBUTOR CODE','DISTRIBUTOR CITY','DISTRIBUTOR STATE', 'CREATED BY','EMP CODE', 'ASE','ASM', 'SM','RSM', 'ZSM', 'NSM','STATUS', 'DATE','TIME');
        //     fputcsv($f, $fields, $delimiter);

        //     $count = 1;

        //     foreach($data as $row) {
        //         //dd($row);
        //         $date = date('j F, Y', strtotime($row['created_at']));
        //          $time = date('h:i A', strtotime($row['created_at']));
        //         $displayASEName = '';
        //       // foreach(explode(',',$row->user_id) as $aseKey => $aseVal) 
        //         //{
                    
        //             $catDetails = DB::table('users')->where('id', $row['user_id'])->first();
        //             $displayASEName = $catDetails->name ?? '';
        //             $displayASECode = $catDetails->employee_id ?? '';
        //       // }
        //         $store_name = $row->store_name ?? '';
               
        //         $storename = Team::where('store_id', $row->id)->with('distributors','rsm','zsm','nsm','asm','sm','ase')->first();

        //         $lineData = array(
        //             $count,
        //             $row->unique_code ??'',
        //             ucwords($row->name),
        //             ucwords($row->business_name),
        //             ucwords($row->address),
        //             $row->city,
        //             $row->areas->name ?? '',
        //             $row->district ?? '',
        //             $row->pin,
        //             $row->states->name ?? '',
        //             ucwords($row->owner_fname.' '.$row->owner_lname),
        //             $row->contact,
        //             $row->whatsapp,
        //             $row->contact_person_fname.' '.$row->contact_person_lname,
        //             $row->contact_person_phone,
        //             $row->date_of_birth,
        //             $row->date_of_anniversary,
        //             $row->email,
        //             $row->gst_no,
        //             $row->pan_no,
        //             $storename->distributors->name ?? '',
        //             $storename->distributors->employee_id ?? '',
        //             $storename->distributors->city ?? '',
        //             $storename->distributors->state ?? '',
        //             $displayASEName ?? '',
        //             $displayASECode ?? '',
        //             $storename->ase->name ?? '',
        //             $storename->asm->name ?? '',
        //             $storename->sm->name ?? '',
        //             $storename->rsm->name ?? '',
        //             $storename->zsm->name ?? '',
        //             $storename->nsm->name ?? '',
                    
        //             ($row->status == 1) ? 'Active' : 'Inactive',
        //             $date,
        //             $time
        //         );

        //         fputcsv($f, $lineData, $delimiter);

        //         $count++;
        //     }

        //     // Move back to beginning of file
        //     fseek($f, 0);

        //     // Set headers to download file rather than displayed
        //     header('Content-Type: text/csv');
        //     header('Content-Disposition: attachment; filename="' . $filename . '";');

        //     //output all remaining data on a file pointer
        //     fpassthru($f);
        // }
    }
    
       //user no order reason list
    public function noOrderreason(Request $request)
    {
        if (isset($request->date_from) || isset($request->date_to) || isset($request->ase) ||isset($request->zsm) || isset($request->rsm) ||isset($request->sm) ||isset($request->asm) ||isset($request->store_id) || isset($request->comment) || isset($request->keyword)) {
			
			$from = $request->date_from ? $request->date_from : '';
            $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';

            $user_id = $request->ase ? $request->ase : '';
            $asm=$request->asm ? $request->asm : '';
            $store_id = $request->store_id ? $request->store_id : '';
            $comment = $request->comment ? $request->comment : '';
            $keyword = $request->keyword ? $request->keyword : '';

            $query = UserNoorderreason::query();
            if(!empty($request->ase))
            {
                $query->when($user_id, function($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                });
           }else{
            $query->when($asm, function($query) use ($asm) {
                $query->where('user_id', $asm);
            });
           }
            $query->when($store_id, function($query) use ($store_id) {
                $query->where('store_id', $store_id);
            });
            $query->when($comment, function($query) use ($comment) {
                $query->where('comment', 'like', '%'.$comment.'%');
            });
            $query->when($keyword, function($query) use ($keyword) {
                $query->where('comment', 'like', '%'.$keyword.'%');
            })->whereBetween('created_at', [$from, $to]);

            $data = $query->latest('id')->paginate(25);
           
        } else {
            $data = UserNoOrderReason::latest('id')->with('users')->paginate(25);
        }
        $zsm=User::select('id', 'name')->where('type', 2)->orderBy('name')->get();
        $ases = User::select('id', 'name')->where('type', 6)->orWhere('type', 5)->orderBy('name')->get();
        $stores = Store::select('id', 'name')->where('status',1)->orderBy('name')->get();
        $reasons = NoOrderReason::select('noorderreason')->orderBy('noorderreason')->get();
        $state = State::where('status',1)->groupBy('name')->orderBy('name')->get();
        return view('admin.store.noorder',compact('data', 'ases', 'stores', 'reasons','request','zsm','state'));
    }
    //csv export of no order reason list
    public function noOrderreasonCSV(Request $request)
    {
        $from = $request->date_from ? $request->date_from : '';
        $to = date('Y-m-d', strtotime(request()->input('date_to')))? date('Y-m-d', strtotime(request()->input('date_to'))) : '';
        if (isset($request->date_from) || isset($request->date_to) || isset($request->ase) ||isset($request->zsm) || isset($request->rsm) ||isset($request->sm) ||isset($request->asm) ||isset($request->store_id) || isset($request->comment) || isset($request->keyword)) {
			
			$from = $request->date_from ? $request->date_from : '';
            $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';

            $user_id = $request->ase ? $request->ase : '';
            $asm=$request->asm ? $request->asm : '';
            $store_id = $request->store_id ? $request->store_id : '';
            $comment = $request->comment ? $request->comment : '';
            $keyword = $request->keyword ? $request->keyword : '';

            $query = UserNoorderreason::query();
            if(!empty($request->ase))
            {
                $query->when($user_id, function($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                });
           }else{
            $query->when($asm, function($query) use ($asm) {
                $query->where('user_id', $asm);
            });
           }
            $query->when($store_id, function($query) use ($store_id) {
                $query->where('store_id', $store_id);
            });
            $query->when($comment, function($query) use ($comment) {
                $query->where('comment', 'like', '%'.$comment.'%');
            });
            $query->when($keyword, function($query) use ($keyword) {
                $query->where('comment', 'like', '%'.$keyword.'%');
            })->whereBetween('created_at', [$from, $to]);

            $data = $query->latest('id')->cursor();
            $users = $data->all();
           
        } else {
            $data = UserNoOrderReason::latest('id')->with('users')->cursor();
            $users = $data->all();
        }
        
        
        $filename = "lux-no-order-reason-".$from.' to '.$to.".csv";
            $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];


    return Response::stream(function () use ($users, $headers) {
        $file = fopen('php://output', 'w');
        fputcsv($file, ['SR', 'NSM', 'ZSM','RSM','SM','ASM','Employee','Employee Id','Employee Status','Employee Designation','Employee HQ','Employee State','Employee Contact No','Store',  'Comment', 'Description', 'Location', 'Date','Time']);
         $count = 1;
        foreach ($users as $row) {
               $date = date('j F, Y', strtotime($row['created_at']));
                $time = date('h:i A', strtotime($row['created_at']));
                $store = Store::select('name')->where('id', $row['store_id'])->first();
                $ase = User::select('name', 'mobile', 'state', 'city', 'pin')->where('id', $row['user_id'])->first();
                $findTeamDetails= findTeamDetails($row->users->id, $row->users->type);
            fputcsv($file, [
                    $count,
                    $findTeamDetails[0]['nsm'] ?? '',
                    $findTeamDetails[0]['zsm']?? '',
                    $findTeamDetails[0]['rsm']?? '',
                    $findTeamDetails[0]['sm']?? '',
                    $findTeamDetails[0]['asm']?? '',
                    $row->users ? $row->users->name : '',
                    $row->users->employee_id ?? '',
                    ($row->users->status == 1)  ? 'Active' : 'Inactive',
                    $row->users->designation?? '',
                   
                    $row->users->headquater?? '',
                    $row->users->state?? '',
                    $row->users->mobile,
                    $store->name ?? '',
                    $row['comment'],
                    $row['description'],
                    $row['location'],
                    $date,
                    $time]);
        }

        fclose($file);
    }, 200, $headers);
    //     if (count($data) > 0) {
    //         $delimiter = ",";
    //         $filename = "lux-no-order-reason-".$from.' to '.$to.".csv";

    //         // Create a file pointer
    //         $f = fopen('php://memory', 'w');

    //         // Set column headers
    //         $fields = array('SR', 'NSM', 'ZSM','RSM','SM','ASM','Employee','Employee Id','Employee Status','Employee Designation','Employee HQ','Employee State','Employee Contact No','Store',  'Comment', 'Description', 'Location', 'Date','Time');
    //         fputcsv($f, $fields, $delimiter);

    //         $count = 1;

    //         foreach($data as $row) {
    //             $date = date('j F, Y', strtotime($row['created_at']));
    //             $time = date('h:i A', strtotime($row['created_at']));
    //             $store = Store::select('name')->where('id', $row['store_id'])->first();
    //             $ase = User::select('name', 'mobile', 'state', 'city', 'pin')->where('id', $row['user_id'])->first();
    //             $findTeamDetails= findTeamDetails($row->users->id, $row->users->type);
    //             $lineData = array(
    //                 $count,
    //                 $findTeamDetails[0]['nsm'] ?? '',
    //                 $findTeamDetails[0]['zsm']?? '',
    //                 $findTeamDetails[0]['rsm']?? '',
    //                 $findTeamDetails[0]['sm']?? '',
    //                 $findTeamDetails[0]['asm']?? '',
    //                 $row->users ? $row->users->name : '',
    //                 $row->users->employee_id ?? '',
    //                 ($row->users->status == 1)  ? 'Active' : 'Inactive',
    //                 $row->users->designation?? '',
                   
    //                 $row->users->headquater?? '',
    //                 $row->users->state?? '',
    //                 $row->users->mobile,
    //                 $store->name ?? '',
    //                 $row['comment'],
    //                 $row['description'],
    //                 $row['location'],
    //                 $date,
    //                 $time
    //             );

    //             fputcsv($f, $lineData, $delimiter);

    //             $count++;
    //         }

    //         // Move back to beginning of file
    //         fseek($f, 0);

    //         // Set headers to download file rather than displayed
    //         header('Content-Type: text/csv');
    //         header('Content-Disposition: attachment; filename="' . $filename . '";');

    //         //output all remaining data on a file pointer
    //         fpassthru($f);
    //     }
     }
     
     
     public function StoreCodeUpdate(Request $request)
    {
// 		$substring='ST';
//         $category = Store::all();
// 		foreach($category as $item){
// 		    if(!str_contains($item->unique_code, $substring)){
//     		    $store=Store::findOrfail($item->id);
//     		    $store->unique_code='ST'.$item->unique_code;
//     		    $store->save();
// 		    }
		    
// 		}
        $state=State::all();
        foreach($state as $item)
        {
            $distributors=User::where('type',7)->where('state',$item->name)->orderBy('id', 'asc') // You can change 'id' to another column for sorting
                                ->get();
                                
                foreach ($distributors as $index => $distributor) {
                    $position = str_pad($index + 1, 2, '0', STR_PAD_LEFT); // 01, 02, 03, etc.
                    
                    $distributor_code = $item->code . $position; // Example: 0201WB, 0202WB
                    
                    $data=User::findOrfail($distributor->id);
                    
                    $data->distributor_position_code = $distributor_code;
                    $data->save();
                   // dd($data);
                }
        }
        
        if ($data) {
            return redirect()->back()->with('success','Status updated successfully');
        } else {
            return redirect()->route('admin.stores.create')->withInput($request->all());
        }
    }
    
    
public function allStatus(Request $request)
{
    // Define an array of state IDs you want to check
    //$stateIds = [23, 14, 17, 25, 19, 9, 7,20,29,24,2,22,39,38,18,5,13,28]; // Example: Pass your desired state IDs here
    $stateIds = [9];
    // Fetch all stores in the given states (multiple state IDs)
    $stores = Store::whereIn('state_id', $stateIds)->get();
    
    // Loop through each store
    foreach ($stores as $store) {
        // Get the distributor related to the store
        $distributor = Team::select('distributor_id')->where('store_id', $store->id)->first();
        
        // Check if distributor data is found
        if (!empty($distributor)) {
            $discat = explode(",", $distributor->distributor_id);
            
            // Array to hold all distributor pincodes
            $allPincodes = [];

            // Loop through each distributor ID to collect all their pin codes
            foreach ($discat as $cat) {
                $user = User::where('id', $cat)->first();
                
                if ($user && !empty($user->postal_code)) {
                    // Get the distributor's postal codes and merge them into $allPincodes
                    $pincodeArray = explode(',', $user->postal_code);
                    $allPincodes = array_merge($allPincodes, $pincodeArray);
                }
            }
            
            // Check if the store's pin is in the combined distributor pin codes
            if (!in_array($store->pin, $allPincodes)) {
                // Update the store's status to 0 if pin is not found in any distributor
                $storeData = Store::findOrFail($store->id);
                $storeData->status = 0;
                 $storeData->password = Hash::make('Welcome@2023');
                $storeData->save();
            }else{
                $storeData = Store::findOrFail($store->id);
                $storeData->status = 1;
                $storeData->password = Hash::make('Welcome@2023');
                $storeData->save();
            }
        }
    }

    // After processing all stores, return a success message
    return redirect()->back()->with('success', 'Status updated successfully');
    }
    
    
    
public function allStorePincodechange(Request $request)
{
    // Define an array of state IDs to check
    //$stateIds = [23, 14, 17, 25, 19, 9, 7,20,29,24,2,22,39,38,18,5,28,13]; // Pass your desired state IDs here
    $stateIds = [9];
    // Fetch all stores in the given states (multiple state IDs)
    $stores = Store::whereIn('state_id', $stateIds)->get();
   
    // Loop through each store
    foreach ($stores as $store) {
        // Get the distributor related to the store
        
        $distributor = Team::select('distributor_id')->where('store_id', $store->id)->first();
        
        if ($distributor) {
            $state=State::where('id',$store->state_id)->first();
            // Fetch matching distributors based on postal codes, excluding the current distributor ID
            $matchingDistributors = User::where('type', 7) // Assuming '7' is the type for distributors
                 ->where('state',$state->name)
                ->whereRaw("FIND_IN_SET(?, postal_code)", [$store->pin]) // Check if the pin exists in postal_code
                //->where('id', '!=', $distributor->distributor_id) // Exclude the current distributor
                
                ->pluck('id') // Get the IDs of the matching distributors
                ->toArray();
            //dd($matchingDistributors);
            // If there are matching distributors, merge them with the current distributor ID(s)
            if (!empty($matchingDistributors)) {
                // Get the current distributor_id(s) and convert them to an array if it's a comma-separated string
                //$currentDistributorIds = explode(',', $distributor->distributor_id);
                //$onlyFirst[]=$currentDistributorIds[0];
                //dd($onlyFirst);
                // Merge current distributor IDs with the new matching IDs, avoiding duplicates
                $distributorIds = array_unique($matchingDistributors);

                // Convert the distributor IDs back to a comma-separated string
                $distributorIdsString = implode(',', $distributorIds);
                //dd($distributorIdsString);
                // Save the updated distributor IDs
                $storeData = Team::where('store_id', $store->id)->first();
                if(!empty($storeData->distributor_id)){
                $storeData->distributor_id = $distributorIdsString; // Save as comma-separated
                $storeData->save(); // Save the store data
                }else{
                    $storeData->distributor_id = $distributorIdsString; // Save as comma-separated
                    $storeData->save();
                }
            }else{
                $area= $store->area_id;
                $state=$store->state_id;
                $primaryDistributor=Team::select('distributor_id')->where('state_id', $state)->where('area_id', $area)->where('ase_id', $store->user_id)->where('store_id',NULL)->first();
                $storeData = Team::where('store_id', $store->id)->first();
                if(!empty($primaryDistributor)){
                $storeData->distributor_id = $primaryDistributor->distributor_id; // Save as comma-separated
                $storeData->save(); // Save the store data
                }else{
                    $storeData->distributor_id = NULL; // Save as comma-separated
                    $storeData->save(); 
                }
            }
        }
    }

    // After processing all stores, return a success message
    return response()->json(['error' => false, 'resp' => 'Distributor IDs updated successfully']);
}



public function processCsvFiles()
{
    // File paths to your distributor and pincode CSV files
    $distributorCsvFile = 'public/admin/csv/AREA ALLOCATION FOR PUNJAB.csv';
    $pincodeCsvFile = 'public/admin/csv/PIN code file with Lux Distributor_new.csv';

    // Parse both CSV files
    $distributorAreas = $this->parseDistributorCsv($distributorCsvFile);
    $pincodeMap = $this->parsePincodeCsv($pincodeCsvFile);

    // Get unique pincodes for each distributor
    $distributorPincodes = $this->getUniquePincodes($distributorAreas, $pincodeMap);
    
    // Save distributor pincodes to the database
    $this->saveDistributorPincodes($distributorPincodes);

    return "Pincode data processed successfully!";
}
/*private function parseDistributorCsv($filePath)
{
     $areas = [];
    if (($handle = fopen($filePath, 'r')) !== false) {
         $headers = fgetcsv($handle);
        while (($row = fgetcsv($handle)) !== false) {
            //dd($row[1]);
            if (isset($row[0], $row[5], $row[1])) {
                // Assuming area list is in the second column (adjust index if necessary)
                $distributorName = trim($row[1]); // Distributor name in the first column
               
                //$areaList = trim($row[7]);// Area list in the second column
                $districtList=strtoupper(trim($row[5]));
                //dd($districtList);
                $stateList=strtoupper($row[0]);
                $areas[$distributorName] = [
                    //'areas' => explode(', ', $areaList),
                    'districts' => explode(',', $districtList),
                    'states' => $stateList
                ];
            } else {
                // Handle cases where columns are missing
                // You can log this, skip, or add default values
                continue; // Skip rows with missing data
            }
             
        }
        fclose($handle);
    }
   //dd($areas);
    return $areas;
}

private function parsePincodeCsv($filePath)
{
    $pincodeMap = [];
    if (($handle = fopen($filePath, 'r')) !== false) {
        $headers = fgetcsv($handle);
        while (($row = fgetcsv($handle)) !== false) {
            // Assuming the area is in the first column and pincode in the second column
            //$area = $row[3];
            $pincode = $row[4];
            $district = $row[6];
            $state = $row[8];
                
            // If the area already exists in the map, append the pincode to the existing list
             // If the area already exists in the map, append the pincode, district, and state
            if (isset($pincodeMap[$district])) {
                $pincodeMap[$district][] = [$pincode, $state]; // Add to existing array
            } else {
                $pincodeMap[$district] = [[$pincode, $state]]; // Initialize with first pincode array
            }
           
        }
        fclose($handle);
    }
      // dd($pincodeMap);
    return $pincodeMap;
}

private function getUniquePincodes($distributorAreas, $pincodeMap)
{
    $result = [];

    foreach ($distributorAreas as $distributorCode => $areaData) {
       // dd($areaData);
        $allPincodes = []; // Array to collect pincodes
        
        //$areas = $areaData['areas'];      // Areas from the distributor
        $districts = $areaData['districts'];  // Districts from the distributor
        
        $state = $areaData['states'];   // State from the distributor, assuming only one state
        
        // Loop through each area
        //foreach ($areas as $area) {
           // $trimmedArea = $area; // Remove any surrounding spaces
            
            // Loop through each district to create a composite key for district and state
            foreach ($districts as $district) {
                //dd($districts);
                $trimmedDistrict = trim($district); // Remove any surrounding spaces
                //dd($pincodeMap[$trimmedDistrict]);
                // Check if the area exists in the pincode map
                if (isset($pincodeMap[$trimmedDistrict])) {
                    // Loop through each pincode entry for the area
                    foreach ($pincodeMap[$trimmedDistrict] as $pincodeData) {
                        
                        // Assuming that pincode, district, and state are at indexes 0, 1, and 2 respectively
                        $pincode = $pincodeData[0];
                        //$pincodeDistrict = trim($pincodeData[1]);
                        $pincodeState = trim($pincodeData[1]);

                        // Check if district and state match
                        if ($pincodeState === $state) {
                            // If district and state match, add the pincode to the array
                            $allPincodes[] = $pincode;
                        }
                    }
                } else {
                    // Log missing district if needed
                    DB::table('logs')->insert([
                        'distributor' => $distributorCode,
                        'area' => $trimmedDistrict,
                        'state'  => $state,
                        'error_message' => 'District not found in pincode map.',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        //}
       
        // Store unique pincodes and join them into a comma-separated string
        if (!empty($allPincodes)) {
            $result[$distributorCode] = implode(',', array_unique($allPincodes)); // Join unique pincodes into a string
        } else {
            $result[$distributorCode] = ''; // Default to an empty string if no pincodes are found
        }
    }
   //dd($result);
    return $result;
}*/
//for office
private function parseDistributorCsv($filePath)
{
    
    $areas = [];
    if (($handle = fopen($filePath, 'r')) !== false) {
         $headers = fgetcsv($handle);
        while (($row = fgetcsv($handle)) !== false) {
            //dd($row[1]);
            if (isset($row[0], $row[2], $row[6], $row[7])) {
                // Assuming area list is in the second column (adjust index if necessary)
                $distributorName = trim($row[2]); // Distributor name in the first column
               
                $areaList = trim($row[7]);// Area list in the second column
                $districtList=strtoupper(trim($row[6]));
                //dd($districtList);
                $stateList=strtoupper($row[0]);
                $areas[$distributorName] = [
                    'areas' => $areaList ? explode(',', $areaList) : [],
                    'districts' => $districtList ? explode(',', $districtList) : [],
                    'states' => $stateList
                ];
            } else {
                // Handle cases where columns are missing
                // You can log this, skip, or add default values
                continue; // Skip rows with missing data
            }
             
        }
        fclose($handle);
    }
   //dd($areas);
    return $areas;
}

private function parsePincodeCsv($filePath)
{
    $pincodeMap = [];
    if (($handle = fopen($filePath, 'r')) !== false) {
        $headers = fgetcsv($handle);
        while (($row = fgetcsv($handle)) !== false) {
            // Assuming the area is in the first column and pincode in the second column
            $area = $row[3];
            $pincode = $row[4];
            $district = $row[6];
            $state = strtoupper($row[7]);
                
            // If the area already exists in the map, append the pincode to the existing list
             // If the area already exists in the map, append the pincode, district, and state
            if (isset($pincodeMap[$area])) {
                $pincodeMap[$area][] = [$pincode, $district, $state]; // Add to existing array
            } else {
                $pincodeMap[$area] = [[$pincode, $district, $state]]; // Initialize with first pincode array
            }
           
        }
        fclose($handle);
    }
      // dd($pincodeMap);
    return $pincodeMap;
}

/*private function getUniquePincodes($distributorAreas, $pincodeMap)
{
    $result = [];

    foreach ($distributorAreas as $distributorCode => $areas) {
        $allPincodes = []; // Array to collect pincodes
        
        foreach ($areas as $area) {
              dd($areas);
            //$trimmedArea = trim($area);
           if (array_key_exists($area, $pincodeMap)) {
               //dd($pincodeMap[$areas[64]]);
                // Merge pincodes for the area into the allPincodes array
                $allPincodes = array_merge($allPincodes, $pincodeMap[$area]);
            }
            // Check if the trimmed area exists in the pincode map
            
        }
        //dd($allPincodes);
        // Store unique pincodes and join them into a comma-separated string
        //$result[$distributorCode] = implode(',', array_unique($allPincodes));
        
       
        // Store unique pincodes and join them into a comma-separated string
        if (!empty($allPincodes)) {
            $result[$distributorCode] = implode(',', array_unique($allPincodes)); // Join unique pincodes into a string
        } else {
            $result[$distributorCode] = ''; // Default to an empty string if no pincodes are found
        }
    }
    dd($result);
    return $result;
}*/
// private function getUniquePincodes($distributorAreas, $pincodeMap)
// {
//     $result = [];

//     foreach ($distributorAreas as $distributorCode => $areaData) {
//         dd($distributorAreas);
//         $allPincodes = []; // Array to collect pincodes
        
//         $areas = $areaData['areas'];      // Areas from the distributor
//         $districts = $areaData['districts'];  // Districts from the distributor
//         $state = $areaData['states'];   // State from the distributor, assuming only one state
        
//         // Loop through each area
//         foreach ($areas as $area) {
            
//             $trimmedArea = $area; // Remove any surrounding spaces
//             $finalArea= explode(',',$trimmedArea);
//             dd($finalArea);
//             // Loop through each district to create a composite key for district and state
//             foreach ($districts as $district) {
//                 $trimmedDistrict = $district; // Remove any surrounding spaces
                
//                 // Check if the area exists in the pincode map
//                 if (isset($pincodeMap[$finalArea])) {
//                     // Loop through each pincode entry for the area
//                     foreach ($pincodeMap[$finalArea] as $pincodeData) {
                        
//                         // Assuming that pincode, district, and state are at indexes 0, 1, and 2 respectively
//                         $pincode = $pincodeData[0];
//                         $pincodeDistrict = trim($pincodeData[1]);
//                         $pincodeState = trim($pincodeData[2]);

//                         // Check if district and state match
//                         if ($pincodeDistrict === $trimmedDistrict && $pincodeState === $state) {
//                             // If district and state match, add the pincode to the array
//                             $allPincodes[] = $pincode;
//                         }
//                     }
//                 } else {
//                     // Log unmatched areas
//                     DB::table('logs')->insert([
//                         'distributor' => $distributorCode,
//                         'area' => $trimmedArea,
//                         'state' => $state,
//                         'error_message' => 'Area not found in pincode map.',
//                         'created_at' => now(),
//                         'updated_at' => now(),
//                     ]);
//                 }
//             }
//         }
       
//         // Store unique pincodes and join them into a comma-separated string
//         if (!empty($allPincodes)) {
//             $result[$distributorCode] = implode(',', array_unique($allPincodes)); // Join unique pincodes into a string
//         } else {
//             $result[$distributorCode] = ''; // Default to an empty string if no pincodes are found
//         }
//     }
//     dd($result);
//     return $result;
// }


private function getUniquePincodes($distributorAreas, $pincodeMap)
{
   // dd($pincodeMap['Peddakotla B.O']);
    $result = [];

    foreach ($distributorAreas as $distributorCode => $areaData) {
       // dd($distributorAreas);
        $allPincodes = []; // Array to collect pincodes

        $areas = $areaData['areas']; // Areas from the distributor
        $districts = $areaData['districts'] 
           ; 
            // Split districts into an array
            //dd($districts);
        $state = $areaData['states']; // State from the distributor

        // Loop through each area group (comma-separated areas)
        foreach ($areas as $areaGroup) {
            
            // Split the area group into individual areas
            $trimmedArea = trim($areaGroup);

            // Process each individual area
            //foreach ($individualAreas as $area) {
               // $trimmedArea = $area; // Remove surrounding spaces

                // Loop through each district to create a composite key for district and state
                foreach ($districts as $district) {
                   
                    $trimmedDistrict = trim($district); // Remove surrounding spaces
                     //dd($pincodeMap[$trimmedArea]);
                    // Check if the area exists in the pincode map
                    
                        if (isset($pincodeMap[$trimmedArea])) {
                            // Loop through each pincode entry for the area
                            foreach ($pincodeMap[$trimmedArea] as $pincodeData) {
                               
                              //  dd($pincodeMap[$trimmedArea]);
                                // Assuming pincode, district, and state are at indexes 0, 1, and 2 respectively
                                $pincode = $pincodeData[0];
                                $pincodeDistrict = trim($pincodeData[1]);
                                $pincodeState = trim($pincodeData[2]);
    
                                    // Check if district and state match
                                    if ($pincodeDistrict === $trimmedDistrict && $pincodeState === $state) {
                                       
                                        // If district and state match, add the pincode to the array
                                        $allPincodes[] = $pincode;
                                    }
                                
                               
                                    
                                    
                            }
                        } else {
                            // Log unmatched areas
                            DB::table('logs')->insert([
                                'distributor' => $distributorCode,
                                'area' => $trimmedArea.'  '.$trimmedDistrict,
                                'state' => $state,
                                'error_message' => 'Area not found in pincode map.',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    
                }
           // }
        }

        // Store unique pincodes and join them into a comma-separated string
        if (!empty($allPincodes)) {
            $result[$distributorCode] = implode(',', array_unique($allPincodes)); // Join unique pincodes into a string
        } else {
            $result[$distributorCode] = ''; // Default to an empty string if no pincodes are found
        }
    }
    dd($result);
    return $result;
}




private function saveDistributorPincodes($distributorPincodeData)
{
    foreach ($distributorPincodeData as $distributor => $pincodeList) {
            $existingPostalCodes = DB::table('users')
            ->where('employee_id', $distributor)
            ->value('postal_code'); // Fetches the current postal_code

            // Step 2: Convert both existing and new postal codes into arrays
            $existingArray = !empty($existingPostalCodes) ? explode(',', $existingPostalCodes) : [];
            $newArray = explode(',', $pincodeList); // Assuming $pincodeList is a comma-separated string of new pincodes
            
            // Step 3: Merge the arrays and filter out duplicates
            $mergedArray = array_unique(array_merge($existingArray, $newArray));
            
            // Step 4: Convert the merged array back to a comma-separated string
            $updatedPostalCodes = implode(',', $mergedArray);
        // Insert or update the data in your database table
        DB::table('users')->where('employee_id', $distributor)
            ->update(['postal_code' => $updatedPostalCodes]);
    }
}

public function storeIdSave(Request $request)
{
   $activity=Activity::where('type','Order Upload')->where('store_id',NULL)->get();
  // dd($activity);
   foreach($activity as $item){
       $order=Order::where('user_id',$item->user_id)->whereDate('created_at', '=', $item->date)->where('order_lat','like','%'.$item->lat.'%')->where('order_lng','like','%'.$item->lng.'%')->where('order_type','store-visit')->first();
       if(!empty($order)){
           $data=Activity::findOrfail($item->id);
           $data->store_id=$order->store_id;
           $data->save();
       }
       
   }
   return response()->json(['error' => false, 'resp' => 'store IDs updated successfully']);
}


public function storeIdSaveForOrderOnCall(Request $request)
{
   $activity=Activity::where('type','Order On Call')->where('store_id',NULL)->orderby('id','desc')->get();
   //dd($activity);
   foreach($activity as $item){
       //dd($item->lng);
       $order=Order::where('order_type','order-on-call')->where('user_id',$item->user_id)->whereDate('created_at', '=', $item->date)->where('order_lat','like','%'.$item->lat.'%')->where('order_lng','like','%'.$item->lng.'%')->first();
       //dd($order);
       if(!empty($order)){
           $data=Activity::findOrfail($item->id);
           $data->store_id=$order->store_id;
           $data->save();
       }
       
   }
   return response()->json(['error' => false, 'resp' => 'store IDs updated successfully']);
}


public function storeIdSaveForNoOrderReason(Request $request)
{
   $activity=Activity::where('type','No Order Placed')->where('store_id',NULL)->get();
   //dd($activity);
   foreach($activity as $item){
       $order=UserNoOrderReason::where('user_id',$item->user_id)->whereDate('created_at', '=', $item->date)->where('lat',$item->lat)->where('lng',$item->lng)->first();
       if(!empty($order)){
           $data=Activity::findOrfail($item->id);
           $data->store_id=$order->store_id;
           $data->save();
       }
       
   }
   return response()->json(['error' => false, 'resp' => 'store IDs updated successfully']);
}


public function storeIdSaveStoreADD(Request $request)
{
   $activity=Activity::where('type','Store Added')->get();
   //dd($activity);
   foreach($activity as $item){
       $order=Store::where('user_id',$item->user_id)->whereDate('created_at', '=', $item->date)->first();
       if(!empty($order)){
           $data=Activity::findOrfail($item->id);
           $data->store_id=$order->id;
           $data->save();
       }
       
   }
   return response()->json(['error' => false, 'resp' => 'store IDs updated successfully']);
}



public function adjustment(Request $request,$id)
{
   
       $order=Store::where('id',$id)->first();
       if(!empty($order)){
            $data=new RetailerUserTxnHistory();
            $data->user_id = $order->id;
            $data->amount = $request->amount;
    		$data->type = 'manual-adjustment' ?? '';
    		$data->title = $request->amount.' points adjusted manually by admin';
    		$data->description = $request->amount.' points adjusted manually by admin';
    		$data->amount_type = 'manual-adjustment';
    		
    		$data->status = $request->status;
    		$data->created_at = date('Y-m-d H:i:s');
    		$data->updated_at = date('Y-m-d H:i:s');
            $data->save();
            
            $userAmount=RetailerWalletTxn::where('user_id',$order->id)->orWhere('user_id',$order->unique_code)->orderby('id','desc')->first();
									$walletTxn=new RetailerWalletTxn();
									$walletTxn->user_id = $order->id;
									
									$walletTxn->amount = $request->amount;
									if($request->status=='increment'){
									$walletTxn->type = 1 ?? '';
									}else{
									    $walletTxn->type = 2 ?? '';
									}
									if (!$userAmount) {
                                        $walletTxn->final_amount = $request->amount;
                                    } else {
                                        $walletTxn->final_amount = $userAmount->final_amount + $request->amount;
                                    }
                                    
									$walletTxn->created_at = date('Y-m-d H:i:s');
									$walletTxn->updated_at = date('Y-m-d H:i:s');
									$walletTxn->save();
       }
       
   
   return redirect()->back()->with('success','saved successfully');
}



  // ---------------- State Save ----------------
    public function stateSave()
    {
        $successCount = 0;
        $failedCount = 0;
        $failedList = [];

        $response = Http::get("https://api.mysalesdrive.in/api/v1/State/list");

        if (!$response->successful()) {
            return response()->json(['error' => "API failed"], 500);
        }

        $outlets = $response->json('data');

        if (empty($outlets)) {
            return response()->json(['message' => 'No state data found']);
        }

        foreach ($outlets as $outlet) {
            try {
                $exists = DB::table('states')->where('api_id', $outlet['_id'])->exists();

                if ($exists) {
                    $failedCount++;
                    $failedList[] = ['api_id' => $outlet['_id'], 'name' => $outlet['name'], 'error' => 'Already exists'];
                    continue;
                }

                DB::table('states')->updateOrInsert(
                    ['api_id' => $outlet['_id'] ?? ''],
                    [
                        'name'       => $outlet['name'] ?? '',
                        'code'       => $outlet['code'] ?? '',
                        'status'     => $outlet['status'] ?? false,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
                $successCount++;
            } catch (\Exception $e) {
                $failedCount++;
                $failedList[] = ['api_id' => $outlet['_id'] ?? '', 'name' => $outlet['name'] ?? '', 'error' => $e->getMessage()];
            }
        }

        session(['failedList' => $failedList]);

        return response()->json([
            'successCount' => $successCount,
            'failedCount'  => $failedCount,
            'failedList'   => $failedList,
        ]);
    }

    // ---------------- Beat Save ----------------
    public function beatSave()
    {
        $page = 1;
        $limit = 10;
        $successCount = 0;
        $failedCount = 0;
        $failedList = [];

        do {
            $response = Http::get('https://api.mysalesdrive.in/api/v1/beat/beat-list-paginated', [
                'page' => $page,
                'limit' => $limit,
            ]);

            if (!$response->successful()) break;

            $outlets = $response->json('data');

            if (empty($outlets)) break;

            foreach ($outlets as $outlet) {
                try {
                    $exists = DB::table('areas')->where('name', $outlet['name'])->exists();
                    if ($exists) {
                        $failedCount++;
                        $failedList[] = ['api_id' => $outlet['_id'], 'name' => $outlet['name'], 'error' => 'Already exists'];
                        continue;
                    }

                    $state = State::where('name', $outlet['regionId']['name'])->first();

                    DB::table('areas')->updateOrInsert(
                        ['api_id' => $outlet['_id'] ?? ''],
                        [
                            'name'       => $outlet['name'] ?? '',
                            'state_id'   => $state->id ?? null,
                            'status'     => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                    $successCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    $failedList[] = ['api_id' => $outlet['_id'] ?? '', 'name' => $outlet['name'] ?? '', 'error' => $e->getMessage()];
                }
            }

            $page++;
        } while (!empty($outlets));

        session(['failedList' => $failedList]);

        return response()->json([
            'successCount' => $successCount,
            'failedCount'  => $failedCount,
            'failedList'   => $failedList,
        ]);
    }

    // ---------------- Employee Save ----------------
    public function employeeSave()
    {
        $page = 1;
        $limit = 10;
        $successCount = 0;
        $failedCount = 0;
        $failedList = [];

        do {
            $response = Http::get("https://api.mysalesdrive.in/api/v1/employee/all-list-paginated", [
                'page' => $page,
                'limit' => $limit
            ]);

            if (!$response->successful()) break;

            $outlets = $response->json('data');
            if (empty($outlets)) break;

            foreach ($outlets as $outlet) {
                try {
                    $exists = DB::table('users')->where('api_id', $outlet['_id'])->exists();
                    if ($exists) {
                        $failedCount++;
                        $failedList[] = ['api_id' => $outlet['_id'], 'name' => $outlet['name'], 'error' => 'Already exists'];
                        continue;
                    }

                    DB::table('users')->updateOrInsert(
                        ['api_id' => $outlet['_id'] ?? ''],
                        [
                            'name'        => $outlet['name'] ?? '',
                            'employee_id' => $outlet['empId'] ?? '',
                            'designation' => $outlet['desgId']['name'] ?? '',
                            'password'    => $outlet['password'] ?? '',
                            'state'       => $outlet['regionId']['name'] ?? '',
                            'status'      => 1,
                            'updated_at'  => now(),
                            'created_at'  => now(),
                        ]
                    );
                    $successCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    $failedList[] = ['api_id' => $outlet['_id'] ?? '', 'name' => $outlet['name'] ?? '', 'error' => $e->getMessage()];
                }
            }

            $page++;
        } while (!empty($outlets));

        session(['failedList' => $failedList]);

        return response()->json([
            'successCount' => $successCount,
            'failedCount'  => $failedCount,
            'failedList'   => $failedList,
        ]);
    }

    // ---------------- Retailer Save ----------------
    public function retailerSave()
    {
        $page = 1;
        $limit = 10;
        $successCount = 0;
        $failedCount = 0;
        $failedList = [];

        do {
            $response = Http::get("https://api.mysalesdrive.in/api/v1/outletApproved/paginated-list", [
                'page' => $page,
                'limit' => $limit
            ]);

            if (!$response->successful()) break;

            $outlets = $response->json('data');
            if (empty($outlets)) break;

            foreach ($outlets as $outlet) {
                try {
                    $exists = DB::table('stores')->where('api_id', $outlet['_id'])->exists();
                    if ($exists) {
                        $failedCount++;
                        $failedList[] = ['api_id' => $outlet['_id'], 'name' => $outlet['outletName'], 'error' => 'Already exists'];
                        continue;
                    }

                    $user  = User::where('api_id', $outlet['employeeId']['_id'])->first();
                    $state = State::where('name', $outlet['stateId']['name'])->first();
                    $beat  = Area::where('name', $outlet['beatId']['name'])->first();

                    DB::table('stores')->updateOrInsert(
                        ['unique_code' => $outlet['outletUID']],
                        [
                            'api_id'     => $outlet['_id'] ?? '',
                            'name'       => $outlet['outletName'] ?? '',
                            'owner_fname'=> $outlet['ownerName'] ?? '',
                            'contact'    => $outlet['mobile1'] ?? '',
                            'address'    => $outlet['address1'] ?? '',
                            'user_id'    => $user->id ?? null,
                            'city'       => $outlet['city'] ?? '',
                            'state_id'   => $state->id ?? null,
                            'area_id'    => $beat->id ?? null,
                            'pin'        => $outlet['pin'] ?? '',
                            'district'   => $outlet['district'] ?? '',
                            'gst_no'     => $outlet['gstin'] ?? '',
                            'pan_no'     => $outlet['panNumber'] ?? '',
                            'aadhar'     => $outlet['aadharNumber'] ?? '',
                            'password'   => Hash::make(($outlet['mobile1'] ?? '0000') . '@2025'),
                            'status'     => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                    $successCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    $failedList[] = ['api_id' => $outlet['_id'] ?? '', 'name' => $outlet['outletName'] ?? '', 'error' => $e->getMessage()];
                }
            }

            $page++;
        } while (!empty($outlets));

        session(['failedList' => $failedList]);

        return response()->json([
            'successCount' => $successCount,
            'failedCount'  => $failedCount,
            'failedList'   => $failedList,
        ]);
    }

    // ---------------- Download Failed ----------------
    public function downloadFailed()
    {
        $failedList = session('failedList', []);
        $filename = "failed_records_" . now()->format('Ymd_His') . ".csv";

        return response()->streamDownload(function () use ($failedList) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['API ID', 'Name', 'Error']);
            foreach ($failedList as $row) {
                fputcsv($handle, [$row['api_id'], $row['name'], $row['error']]);
            }
            fclose($handle);
        }, $filename);
    }



    //bulk upload
    
    public function bulkUpload(Request $request)
     {
		 //dd($request->all());
         if (!empty($request->file)) {
             $file = $request->file('file');
             $filename = $file->getClientOriginalName();
             $extension = $file->getClientOriginalExtension();
             $tempPath = $file->getRealPath();
             $fileSize = $file->getSize();
             $mimeType = $file->getMimeType();
 
             $valid_extension = array("csv");
             $maxFileSize = 50097152;
             if (in_array(strtolower($extension), $valid_extension)) {
                 if ($fileSize <= $maxFileSize) {
                     $location = 'public/uploads/csv';
                     $file->move($location, $filename);
                     // $filepath = public_path($location . "/" . $filename);
                     $filepath = $location . "/" . $filename;
 
                     // dd($filepath);
 
                     $file = fopen($filepath, "r");
                     $importData_arr = array();
                     $i = 0;
                     while (($filedata = fgetcsv($file, 10000, ",")) !== FALSE) {
                         $num = count($filedata);
                         // Skip first row
                         if ($i == 0) {
                             $i++;
                             continue;
                         }
                         for ($c = 0; $c < $num; $c++) {
                             $importData_arr[$i][] = $filedata[$c];
                         }
                         $i++;
                     }
                     fclose($file);
                     $successCount = 0;
                        $userId='';
                    foreach ($importData_arr as $importData) {
                        $count = $total = 0;
                        $stateData = '';
                        $user=Store::where('contact',$importData[0])->first();
                        if(!empty($user)){
                            $userId =$user->id;
                        
                        
						$user=Store::findOrFail($userId);
						$user->wallet += $importData[1];
						
						$user->save();
						
						$userAmount=RetailerWalletTxn::where('user_id',$userId)->orderby('id','desc')->first();
									$walletTxn=new RetailerWalletTxn();
									$walletTxn->user_id = $userId;
									
									$walletTxn->amount = $importData[1];
									$walletTxn->type = 1 ?? '';
									if (!$userAmount) {
                                        $walletTxn->final_amount = $importData[1];
                                    } else {
                                        $walletTxn->final_amount = $userAmount->final_amount + $importData[1];
                                    }
                        
                                    $walletTxn->entry_date = date('Y-m-d H:i:s');
									$walletTxn->created_at = date('Y-m-d H:i:s');
									$walletTxn->updated_at = date('Y-m-d H:i:s');
									$walletTxn->save();
									$userwalletTxn=new RetailerUserTxnHistory();
									$userwalletTxn->user_id = $userId;
									
									$userwalletTxn->amount = $importData[1];
									$userwalletTxn->type = 'Earn' ?? '';
									$userwalletTxn->title = $importData[1].' points earn for opening stock';
									$userwalletTxn->description = $importData[1].' points earn for opening stock';
									$userwalletTxn->amount_type = 'Opening Stock';
									
									$userwalletTxn->status = 'increment';
									$userwalletTxn->entry_date =  date('Y-m-d H:i:s');
									$userwalletTxn->created_at = date('Y-m-d H:i:s');
									$userwalletTxn->updated_at = date('Y-m-d H:i:s');
									$userwalletTxn->save();
                        }						
                         
                    }
                    return redirect()->back()->with('success', 'File uploaded successfully');
                 } else {
                     return redirect()->back()->with('failure', 'File too large. File must be less than 50MB.');
                 }
             } else {
                return redirect()->back()->with('failure', 'Invalid File Extension. supported extensions are ' . implode(', ', $valid_extension));
             }
         } else {
             return redirect()->back()->with('failure', 'No file found.');
         }
 
         return redirect()->back();
     }

    
    
    
}
