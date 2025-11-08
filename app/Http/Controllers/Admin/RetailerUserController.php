<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RetailerUser;
use App\Models\Store;
use App\Models\Distributor;
use App\Models\User;
use App\Models\State;
use App\Models\StoreFormSubmit;
use App\Models\Team;
use App\Models\Area;
use App\Models\Branding;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Auth;
class RetailerUserController extends Controller
{
    /**
     * This method is for show user list
     *
     */
    /**
     * This method is for show store list
     *
     */
     public function index(Request $request)
    {
        
            if(isset($request->date_from) || isset($request->date_to) || isset($request->distributor)||isset($request->ase)||isset($request->state)||isset($request->keyword)||isset($request->area)) 
            {
                $from = $request->date_from ? $request->date_from : '';
                $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';

                $distributor = $request->distributor ? $request->distributor : '';
                $ase = $request->ase ? $request->ase : '';
                $state = $request->state ? $request->state : '';
                $area = $request->area ? $request->area : '';
                $keyword = $request->keyword ? $request->keyword : '';

                // DB::enableQueryLog();

                $query = Store::select('stores.id as id','stores.unique_code as unique_code','stores.created_at as created_at','stores.store_name as store_name','stores.user_id as user_id','stores.state_id as state_id','stores.area_id as area_id','stores.city as city','stores.pin as pin','stores.address as address','stores.email as email','stores.contact as contact','stores.bussiness_name as bussiness_name','stores.status as status');
                $query->when($distributor, function($query) use ($distributor) {
                    $query->join('team', 'team.store_id', 'stores.id')->whereRaw("find_in_set('".$distributor."',team.distributor_id)");
                });
               
                $query->when($state, function($query) use ($state) {
                    $query->where('stores.state_id', $state);
                });
                $query->when($area, function($query) use ($area) {
                    $query->where('stores.area_id', $area);
                });
                $query->when($keyword, function($query) use ($keyword) {
                    $query->where('stores.name', $keyword)
                    ->orWhere('stores.bussiness_name', $keyword)
                    ->orWhere('stores.owner_name', $keyword)
                    ->orWhere('stores.contact', $keyword)
                    ->orWhere('stores.email', $keyword)
                    ->orWhere('stores.whatsapp', $keyword)
                    ->orWhere('stores.address', $keyword)
                    ->orWhere('stores.area', $keyword)
                    ->orWhere('stores.state', $keyword)
                    ->orWhere('stores.pin', $keyword)
                    ->orWhere('stores.contact_person', $keyword)
                    ->orWhere('stores.contact_person_phone', $keyword)
                    ->orWhere('stores.contact_person_whatsapp', $keyword)
					->orWhere('stores.unique_code', $keyword)
                    ->orWhere('stores.gst_no', $keyword);
                })->whereBetween('stores.created_at', [$from, $to]);

                $data = $query->where('stores.user_id','=',NULL)->latest('stores.id')->paginate(25);

                // dd($data);
            }
            else{
                $data = Store::selectRaw('stores.*')->where('stores.user_id','=',NULL)
                ->latest('id')->paginate(25);
                //dd($data);
            }
            $allASEs = User::select('id','name')->where('type',6)->where('name', '!=', null)->groupBy('name')->orderBy('name')->get();
            $allDistributors = User::select('id','name','employee_id','state')->where('type',7)->where('name', '!=', null)->groupBy('name')->orderBy('name')->get();
            $state = State::groupBy('id','name')->orderBy('name')->get();
            $inactiveStore=Store::where('status',0)->groupby('name')->get();
        
        return view('admin.reward.user.index', compact('data', 'allASEs', 'allDistributors', 'state', 'request','inactiveStore'));
    }
    /**
     * This method is for show user details
     * @param  $id
     *
     */
    public function show(Request $request, $id)
    {
        $data = RetailerUser::findOrFail($id);
        return view('admin.reward.user.detail', compact('data'));
    }
     /**
     * This method is for update user status
     * @param  $id
     *
     */
    public function status(Request $request, $id)
    {
        $storeData = RetailerUser::findOrFail($id);

        $status = ($storeData->status == 1) ? 0 : 1;
        $storeData->status = $status;
        $storeData->save();

        if ($storeData) {
            return redirect()->back()->with('success','Status Updated');
            // return redirect()->route('admin.user.list');
        } else {
            return redirect()->route('admin.reward.retailer.user.index')->withInput($request->all());
        }
    }
	
	
	public function exportCSV(Request $request)
    {
       

            if(isset($request->date_from) || isset($request->date_to) || isset($request->distributor)||isset($request->ase)||isset($request->state)||isset($request->keyword)||isset($request->area)) 
            {
                $from = $request->date_from ? $request->date_from : '';
                $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';

                $distributor = $request->distributor ? $request->distributor : '';
                $ase = $request->ase ? $request->ase : '';
                $state = $request->state ? $request->state : '';
                $area = $request->area ? $request->area : '';
                $keyword = $request->keyword ? $request->keyword : '';

                // DB::enableQueryLog();

                $query = Store::select('stores.id as id','stores.created_at as created_at','stores.store_name as store_name','stores.user_id as user_id','stores.state as state','stores.area as area','stores.city as city','stores.pin as pin','stores.address as address','stores.email as email','stores.contact as contact','stores.whatsapp as whatsapp','stores.owner_name as owner_name','stores.owner_lname as owner_lname','stores.date_of_birth as date_of_birth','stores.date_of_anniversary as date_of_anniversary','stores.contact_person as contact_person','stores.contact_person_lname as contact_person_lname','stores.contact_person_phone as contact_person_phone','stores.contact_person_date_of_birth as contact_person_date_of_birth','stores.contact_person_whatsapp as contact_person_whatsapp','stores.contact_person_date_of_anniversary as contact_person_date_of_anniversary','stores.gst_no as gst_no','stores.bussiness_name as bussiness_name','stores.status as status');
               $query->when($distributor, function($query) use ($distributor) {
                    $query->join('retailer_list_of_occ', 'retailer_list_of_occ.store_id', 'stores.id')->whereRaw("find_in_set('".$distributor."',retailer_list_of_occ.distributor_name)");
                });
                $query->when($ase, function($query) use ($ase) {
                    $query->whereRaw("find_in_set('".$ase."',stores.user_id)");
                });
                $query->when($state, function($query) use ($state) {
                    $query->where('stores.state', $state);
                });
                $query->when($area, function($query) use ($area) {
                    $query->where('stores.area', $area);
                });
                $query->when($keyword, function($query) use ($keyword) {
                    $query->where('stores.store_name', $keyword)
                    ->orWhere('stores.bussiness_name', $keyword)
                    ->orWhere('stores.owner_name', $keyword)
                    ->orWhere('stores.contact', $keyword)
                    ->orWhere('stores.email', $keyword)
                    ->orWhere('stores.whatsapp', $keyword)
                    ->orWhere('stores.address', $keyword)
                    ->orWhere('stores.area', $keyword)
                    ->orWhere('stores.state', $keyword)
                    ->orWhere('stores.pin', $keyword)
                    ->orWhere('stores.contact_person', $keyword)
                    ->orWhere('stores.contact_person_phone', $keyword)
                    ->orWhere('stores.contact_person_whatsapp', $keyword)
                    ->orWhere('stores.gst_no', $keyword);
                })->whereBetween('stores.created_at', [$from, $to]);

                $data = $query->where('stores.user_id','=',NULL)->latest('stores.id')->cursor();
                $users = $data->all();
                // dd(DB::getQueryLog());
            }
            else{
                $data = Store::selectRaw('stores.*')->where('stores.user_id','=',NULL)
                ->latest('id')->cursor();
                $users = $data->all();
            }
        
        if (count($users) > 0) {
            $delimiter = ",";
            $filename = "onn-store-list-".date('Y-m-d').".csv";

            // Create a file pointer
            $f = fopen('php://memory', 'w');

            // Set column headers
            // $fields = array('SR', 'STORE', 'FIRM', 'MOBILE', 'EMAIL', 'WHATSAPP', 'DISTRIBUTOR', 'ASE', 'ASM', 'RSM', 'VP', 'ADDRESS', 'AREA', 'STATE', 'CITY', 'PINCODE', 'OWNER', 'OWNER DATE OF BIRTH', 'OWNER DATE OF ANNIVERSARY', 'CONTACT PERSON', 'CONTACT PERSON PHONE', 'CONTACT PERSON WHATSAPP', 'CONTACT PERSON DATE OF BIRTH', 'CONTACT PERSON DATE OF ANNIVERSARY', 'GST NUMBER', 'STATUS', 'DATETIME');
            $fields = array('SR', 'STORE', 'ADDRESS', 'AREA','PINCODE','STATE','OWNER NAME','MOBILE', 'WHATSAPP', 'EMAIL', 'GST NUMBER','DISTRIBUTOR', 'ASE', 'ASM', 'RSM', 'VP', 'STATUS', 'DATETIME');
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($users as $row) {
				//dd($row);
                $datetime = date('j F, Y', strtotime($row['created_at']));
                //$ase = $row->user_id;
               // $username = User::select('name')->where('id', $ase)->first();
				$displayASEName = '';
               
				$store_name = $row->store_name ?? '';
                //$storename = RetailerListOfOcc::select('distributor_name','vp','rsm','asm')->where('retailer', $store_name)->where('ase', $username->name)->where('area', $row->area)->first();
				$storename = RetailerListOfOcc::select('distributor_name','vp','rsm','asm','ase')->where('store_id', $row->id)->first();
                // $store = Store::select('store_name')->where('id', $row['store_id'])->first();
                // $ase = User::select('name', 'mobile', 'state', 'city', 'pin')->where('id', $row['user_id'])->first();

                // dd($store->store_name, $ase->name, $ase->mobile);

                $lineData = array(
                    $count,
                    ucwords($row->store_name),
                    
					ucwords($row->address),
                    $row->area,
                    $row->pin,
					$row->state,
					ucwords($row->owner_name.' '.$row->owner_lname),
                    $row->contact,
					$row->whatsapp,
					
                    $row->email,
                    $row->gst_no,
                    $storename->distributor_name ?? '',
                    $storename->ase ?? '',
                    $storename->asm ?? '',
                    $storename->rsm ?? '',
                    $storename->vp ?? '',
                 
                    
                   // $row->city,
                   
                   
                    
                   // $row->contact_person_whatsapp,
                   // $row->contact_person_date_of_birth,
                   // $row->contact_person_date_of_anniversary,
                   
                    ($row->status == 1) ? 'Active' : 'Inactive',
                    $datetime
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
    }
	
	public function loginCount(Request $request)
	{
		if(!empty($request->state_id)){
			$state=$request->state_id;
				
			 $loginCountWiseReport = \DB::select('SELECT s.state_id,COUNT(secret_pin) AS count FROM `stores` AS s
					WHERE s.state_id = "'.$state.'"
					 GROUP BY s.state_id ORDER BY count desc');
				
			}else{
				
				  $loginCountWiseReport = \DB::select('SELECT s.state_id,COUNT(secret_pin) AS count FROM `stores` AS s
					GROUP BY s.state_id ORDER BY count desc');
			
			}
		 $state = State::groupBy('id','name')->orderBy('name')->get();
		return view('admin.reward.user.login-count', compact('loginCountWiseReport',  'state', 'request'));
	}
	
	
	
	
	public function loginCountexportCSV(Request $request)
    {
		 if(!empty($request->state_id)){
			$state=$request->state_id;
				
			 $loginCountWiseReport = \DB::select('SELECT s.state_id,COUNT(secret_pin) AS count FROM `stores` AS s
					WHERE s.state_id = "'.$state.'"
					 GROUP BY s.state_id ORDER BY count desc');
				 
			}else{
				
				  $loginCountWiseReport = \DB::select('SELECT s.state_id,COUNT(secret_pin) AS count FROM `stores` AS s
					GROUP BY s.state_id ORDER BY count desc');
			
			}
        if (count($loginCountWiseReport) > 0) {
            $delimiter = ","; 
            $filename = "store-login-count-state-wise-list-".date('Y-m-d').".csv"; 

            // Create a file pointer 
            $f = fopen('php://memory', 'w'); 

            // Set column headers 
            $fields = array( 'STATE', 'COUNT'); 
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($loginCountWiseReport as $row) {
               $stateData=DB::table('states')->where('id',$row->state_id)->first();
                $lineData = array(
                    $stateData->name?? '',
                    number_format($row->count)
                    
                   
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
    }
    
    
    
    public function loginStoreCount(Request $request,$state)
	{
		$allASEs = User::select('id','name')->where('type',6)->where('name', '!=', null)->where('status',1)->groupBy('name')->orderBy('name')->get();
        $allASMs = User::select('id','name')->where('type',5)->where('name', '!=', null)->where('status',1)->groupBy('name')->orderBy('name')->get();
        $allDistributors = User::select('id','name')->where('type',7)->where('name', '!=', null)->where('status',1)->groupBy('name')->orderBy('name')->get();
        $stateData = State::where('status',1)->groupBy('name')->orderBy('name')->get();
		$areaData=Area::where('state_id',$state)->orderby('name')->get();
		if( isset($request->distributor_id)||isset($request->ase_id)||isset($request->asm_id)||isset($request->state_id)||isset($request->keyword)||isset($request->area_id)) 
        {
           

            $distributor = $request->distributor_id ? $request->distributor_id : '';
            $ase = $request->ase_id ? $request->ase_id : '';
            $asm = $request->asm_id ? $request->asm_id : '';
            $stateDetails = $request->state_id ? $request->state_id : '';
            $area = $request->area_id ? $request->area_id : '';
            $keyword = $request->keyword ? $request->keyword : '';
			
            $query = Store::selectRaw('stores.*')->with('states','areas','users')->join('teams', 'teams.store_id', 'stores.id');
            $query->when($distributor, function($query) use ($distributor) {
                $query->whereRaw("find_in_set('".$distributor."',teams.distributor_id)");
            });
            $query->when($ase, function($query) use ($ase) {
                $query->whereRaw("find_in_set('".$ase."',stores.user_id)");
            });
            $query->when($asm, function($query) use ($asm) {
                $query->whereRaw("find_in_set('".$asm."',stores.user_id)");
            });
            
            $query->when($area, function($query) use ($area) {
                $query->where('stores.area_id', $area);
            });
		
		
		
            $query->when($keyword, function($query) use ($keyword) {
                $query->where('stores.name','=',$keyword)
                ->orWhere('stores.business_name', $keyword)
                ->orWhere('stores.owner_fname', $keyword)
                ->orWhere('stores.contact','=', $keyword);
            });

            $loginCountWiseReport = $query->where('stores.state_id',$state)->where('secret_pin','!=',NULL)->where('stores.user_id','!=','')->latest('stores.id')->paginate(25);
           // dd($data);
        }
        else{		
			// $loginCountWiseReport = \DB::select('SELECT * FROM `stores` AS s
				//	WHERE s.state_id = "'.$state.'" and s.secret_pin IS NOT NULL
					// GROUP BY s.state_id ');
		$loginCountWiseReport=Store::where('state_id',$state)->where('secret_pin','!=',NULL)->orderby('name')->paginate(25);
        }
		return view('admin.reward.user.login-store', compact('loginCountWiseReport',  'request','allDistributors','allASEs','allASMs','stateData','state','areaData'));
	}
	
	
	
	public function loginStoreCountCsv(Request $request,$state)
	{
		$allASEs = User::select('id','name')->where('type',6)->where('name', '!=', null)->where('status',1)->groupBy('name')->orderBy('name')->get();
        $allASMs = User::select('id','name')->where('type',5)->where('name', '!=', null)->where('status',1)->groupBy('name')->orderBy('name')->get();
        $allDistributors = User::select('id','name')->where('type',7)->where('name', '!=', null)->where('status',1)->groupBy('name')->orderBy('name')->get();
        $stateData = State::where('status',1)->groupBy('name')->orderBy('name')->get();
			
		if( isset($request->distributor_id)||isset($request->ase_id)||isset($request->asm_id)||isset($request->state_id)||isset($request->keyword)||isset($request->area_id)) 
        {
           

            $distributor = $request->distributor_id ? $request->distributor_id : '';
            $ase = $request->ase_id ? $request->ase_id : '';
            $asm = $request->asm_id ? $request->asm_id : '';
            $stateDetails = $request->state_id ? $request->state_id : '';
            $area = $request->area_id ? $request->area_id : '';
            $keyword = $request->keyword ? $request->keyword : '';
			
            $query = Store::selectRaw('stores.*')->with('states','areas','users')->join('teams', 'teams.store_id', 'stores.id');
            $query->when($distributor, function($query) use ($distributor) {
                $query->whereRaw("find_in_set('".$distributor."',teams.distributor_id)");
            });
            $query->when($ase, function($query) use ($ase) {
                $query->whereRaw("find_in_set('".$ase."',stores.user_id)");
            });
            $query->when($asm, function($query) use ($asm) {
                $query->whereRaw("find_in_set('".$asm."',stores.user_id)");
            });
            
            $query->when($area, function($query) use ($area) {
                $query->where('stores.area_id', $area);
            });
		
		
		
            $query->when($keyword, function($query) use ($keyword) {
                $query->where('stores.name','=',$keyword)
                ->orWhere('stores.business_name', $keyword)
                ->orWhere('stores.owner_fname', $keyword)
                ->orWhere('stores.contact','=', $keyword);
            });

            $loginCountWiseReport = $query->where('stores.state_id',$state)->where('secret_pin','!=',NULL)->where('stores.user_id','!=','')->latest('stores.id')->cursor();
            $users = $loginCountWiseReport->all();
           // dd($data);
        }
        else{		
			// $loginCountWiseReport = \DB::select('SELECT * FROM `stores` AS s
				//	WHERE s.state_id = "'.$state.'" and s.secret_pin IS NOT NULL
					// GROUP BY s.state_id ');
		$loginCountWiseReport=Store::where('state_id',$state)->where('secret_pin','!=',NULL)->orderby('name')->cursor();
		$users = $loginCountWiseReport->all();
        }
		 if (count($users) > 0) {
            $delimiter = ","; 
            $filename = "state-wise-store-login-list-".date('Y-m-d').".csv"; 

            // Create a file pointer 
            $f = fopen('php://memory', 'w'); 

            // Set column headers 
            $fields = array( 'SR', 'UNIQUE CODE','STORE', 'FIRM', 'ADDRESS','TOWN/CITY', 'AREA','PINCODE','STATE','OWNER NAME','MOBILE', 'WHATSAPP', 'CONTACT PERSON', 'CONTACT PERSON PHONE', 'OWNER DATE OF BIRTH', 'OWNER DATE OF ANNIVERSARY','EMAIL', 'GST NUMBER','DISTRIBUTOR','DISTRIBUTOR CODE','DISTRIBUTOR CITY', 'CREATED BY','EMP CODE', 'ASE','ASM', 'SM','RSM', 'ZSM', 'NSM','STATUS'); 
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($users as $row) {
               $datetime = date('j F, Y', strtotime($row['created_at']));
                $displayASEName = '';
               // foreach(explode(',',$row->user_id) as $aseKey => $aseVal) 
                //{
                    
                    $catDetails = DB::table('users')->where('id', $row['user_id'])->first();
                    $displayASEName = $catDetails->name ?? '';
                    $displayASECode = $catDetails->employee_id ?? '';
               // }
                $store_name = $row->store_name ?? '';
               
                $storename = Team::where('store_id', $row->id)->with('distributors','rsm','zsm','nsm','asm','sm','ase')->first();

                $lineData = array(
                    $count,
                    $row->unique_code ?? '',
                    ucwords($row->name),
                    ucwords($row->business_name),
                    ucwords($row->address),
                    $row->city,
                    $row->areas->name ?? '',
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
                    $storename->distributors->name ?? '',
                    $storename->distributors->employee_id ?? '',
                    $storename->distributors->city ?? '',
                    $displayASEName ?? '',
                    $displayASECode ?? '',
                    $storename->ase->name ?? '',
                    $storename->asm->name ?? '',
                    $storename->sm->name ?? '',
                    $storename->rsm->name ?? '',
                    $storename->zsm->name ?? '',
                    $storename->nsm->name ?? '',
                    
                    ($row->status == 1) ? 'Active' : 'Inactive'
                    
                   
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
	}
	
	
	
	

	
	public function loginStoreCountCsvall(Request $request)
	{
	   
			// $loginCountWiseReport = \DB::select('SELECT * FROM `stores` AS s
				//	WHERE s.state_id = "'.$state.'" and s.secret_pin IS NOT NULL
					// GROUP BY s.state_id ');
		$loginCountWiseReport=Store::where('secret_pin','!=',NULL)->orderby('name')->cursor();
        $users = $loginCountWiseReport->all();
		 if (count($users) > 0) {
            $delimiter = ","; 
            $filename = "state-wise-store-login-list-".date('Y-m-d').".csv"; 

            // Create a file pointer 
            $f = fopen('php://memory', 'w'); 

            // Set column headers 
            $fields = array( 'SR', 'UNIQUE CODE','STORE', 'FIRM', 'ADDRESS','TOWN/CITY', 'AREA','PINCODE','STATE','OWNER NAME','MOBILE', 'WHATSAPP', 'CONTACT PERSON', 'CONTACT PERSON PHONE', 'OWNER DATE OF BIRTH', 'OWNER DATE OF ANNIVERSARY','EMAIL', 'GST NUMBER','DISTRIBUTOR','DISTRIBUTOR CODE','DISTRIBUTOR CITY', 'CREATED BY','EMP CODE', 'ASE','ASM', 'SM','RSM', 'ZSM', 'NSM','STATUS'); 
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($users as $row) {
               $datetime = date('j F, Y', strtotime($row['created_at']));
                $displayASEName = '';
               // foreach(explode(',',$row->user_id) as $aseKey => $aseVal) 
                //{
                    
                    $catDetails = DB::table('users')->where('id', $row['user_id'])->first();
                    $displayASEName = $catDetails->name ?? '';
                    $displayASECode = $catDetails->employee_id ?? '';
               // }
                $store_name = $row->store_name ?? '';
               
                $storename = Team::where('store_id', $row->id)->with('distributors','rsm','zsm','nsm','asm','sm','ase')->first();

                $lineData = array(
                    $count,
                    $row->unique_code ?? '',
                    ucwords($row->name),
                    ucwords($row->business_name),
                    ucwords($row->address),
                    $row->city,
                    $row->areas->name ?? '',
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
                    $storename->distributors->name ?? '',
                    $storename->distributors->employee_id ?? '',
                    $storename->distributors->city ?? '',
                    $displayASEName ?? '',
                    $displayASECode ?? '',
                    $storename->ase->name ?? '',
                    $storename->asm->name ?? '',
                    $storename->sm->name ?? '',
                    $storename->rsm->name ?? '',
                    $storename->zsm->name ?? '',
                    $storename->nsm->name ?? '',
                    
                    ($row->status == 1) ? 'Active' : 'Inactive'
                    
                   
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
	}
	
	
	
	
	
	
	
	
	 public function retailerProgram(Request $request)
	{
		$allASEs = User::select('id','name')->where('type',6)->where('name', '!=', null)->where('status',1)->groupBy('name')->orderBy('name')->get();
        $allASMs = User::select('id','name')->where('type',5)->where('name', '!=', null)->where('status',1)->groupBy('name')->orderBy('name')->get();
        $allDistributors = User::select('id','name','employee_id','state')->where('type',7)->where('name', '!=', null)->where('status',1)->groupBy('name')->orderBy('name')->get();
        $stateData = State::where('status',1)->groupBy('name')->orderBy('name')->get();
	//	$areaData=Area::where('state_id',$state)->orderby('name')->get();
		if( isset($request->distributor_id)||isset($request->ase_id)||isset($request->asm_id)||isset($request->state_id)||isset($request->keyword)||isset($request->date_from)||isset($request->date_to)) 
        {
           
             $from = $request->date_from ? $request->date_from : '';
            $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';

            $distributor = $request->distributor_id ? $request->distributor_id : '';
            $ase = $request->ase_id ? $request->ase_id : '';
            $asm = $request->asm_id ? $request->asm_id : '';
            $stateDetails = $request->state_id ? $request->state_id : '';
            
            $keyword = $request->keyword ? $request->keyword : '';
			
            $query = StoreFormSubmit::selectRaw('store_form_submits.*')->with('users')->join('stores', 'stores.id', 'store_form_submits.retailer_id')->join('teams', 'teams.store_id', 'stores.id');
            $query->when($distributor, function($query) use ($distributor) {
                $query->whereRaw("find_in_set('".$distributor."',teams.distributor_id)");
            });
            $query->when($ase, function($query) use ($ase) {
                $query->whereRaw("find_in_set('".$ase."',stores.user_id)");
            });
            $query->when($asm, function($query) use ($asm) {
                $query->whereRaw("find_in_set('".$asm."',stores.user_id)");
            });
             $query->when($stateDetails, function($query) use ($stateDetails) {
                $query->where('stores.state_id', $stateDetails);
            });
           
		
		
		
            $query->when($keyword, function($query) use ($keyword) {
                $query->where('stores.name','=',$keyword)
                ->orWhere('stores.business_name', $keyword)
                ->orWhere('stores.owner_fname', $keyword)
                ->orWhere('stores.contact','=', $keyword)
                ->orWhere('stores.unique_code','=', $keyword);
            })->whereBetween('store_form_submits.created_at', [$from, $to]);

            $loginCountWiseReport = $query->where('store_form_submits.retailer_id','!=','')->latest('store_form_submits.id')->groupby('store_form_submits.retailer_id')->paginate(25);
           // dd($data);
        }
        else{		
			// $loginCountWiseReport = \DB::select('SELECT * FROM `stores` AS s
				//	WHERE s.state_id = "'.$state.'" and s.secret_pin IS NOT NULL
					// GROUP BY s.state_id ');
		$loginCountWiseReport=StoreFormSubmit::orderby('id','desc')->groupby('store_form_submits.retailer_id')->paginate(25);
        }
		return view('admin.reward.user.store-engage-form', compact('loginCountWiseReport',  'request','allDistributors','allASEs','allASMs','stateData'));
	}
	
	
	
	public function retailerProgramexportCSV(Request $request)
	{
		$allASEs = User::select('id','name')->where('type',6)->where('name', '!=', null)->where('status',1)->groupBy('name')->orderBy('name')->get();
        $allASMs = User::select('id','name')->where('type',5)->where('name', '!=', null)->where('status',1)->groupBy('name')->orderBy('name')->get();
        $allDistributors = User::select('id','name')->where('type',7)->where('name', '!=', null)->where('status',1)->groupBy('name')->orderBy('name')->get();
        $stateData = State::where('status',1)->groupBy('name')->orderBy('name')->get();
			
		if( isset($request->distributor_id)||isset($request->ase_id)||isset($request->asm_id)||isset($request->state_id)||isset($request->keyword)||isset($request->date_from)||isset($request->date_to)) 
        {
           
             $from = $request->date_from ? $request->date_from : '';
            $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';

            $distributor = $request->distributor_id ? $request->distributor_id : '';
            $ase = $request->ase_id ? $request->ase_id : '';
            $asm = $request->asm_id ? $request->asm_id : '';
            $stateDetails = $request->state_id ? $request->state_id : '';
            
            $keyword = $request->keyword ? $request->keyword : '';
			
            $query = StoreFormSubmit::selectRaw('store_form_submits.*')->with('users')->join('stores', 'stores.id', 'store_form_submits.retailer_id')->join('teams', 'teams.store_id', 'stores.id');
            $query->when($distributor, function($query) use ($distributor) {
                $query->whereRaw("find_in_set('".$distributor."',teams.distributor_id)");
            });
            $query->when($ase, function($query) use ($ase) {
                $query->whereRaw("find_in_set('".$ase."',stores.user_id)");
            });
            $query->when($asm, function($query) use ($asm) {
                $query->whereRaw("find_in_set('".$asm."',stores.user_id)");
            });
             $query->when($stateDetails, function($query) use ($stateDetails) {
                $query->where('stores.state_id', $stateDetails);
            });
           
		
		
		
            $query->when($keyword, function($query) use ($keyword) {
                $query->where('stores.name','=',$keyword)
                ->orWhere('stores.business_name', $keyword)
                ->orWhere('stores.owner_fname', $keyword)
                ->orWhere('stores.contact','=', $keyword)
                ->orWhere('stores.unique_code','=', $keyword);
            })->whereBetween('store_form_submits.created_at', [$from, $to]);

            $loginCountWiseReport = $query->where('store_form_submits.retailer_id','!=','')->latest('store_form_submits.id')->groupby('store_form_submits.retailer_id')->get();
           // dd($data);
        }
        else{		
			// $loginCountWiseReport = \DB::select('SELECT * FROM `stores` AS s
				//	WHERE s.state_id = "'.$state.'" and s.secret_pin IS NOT NULL
					// GROUP BY s.state_id ');
		$loginCountWiseReport=StoreFormSubmit::orderby('id','desc')->groupby('store_form_submits.retailer_id')->get();
        }
		 if (count($loginCountWiseReport) > 0) {
            $delimiter = ","; 
            $filename = "retail-engagement-list-".date('Y-m-d').".csv"; 

            // Create a file pointer 
            $f = fopen('php://memory', 'w'); 

            // Set column headers 
            $fields = array( 'SR', 'UNIQUE CODE','Retailer Name', 'Retailer Address','Retailer City','Retailer State', 'Retailer Pincode','Retailer Contact Person1 Name', 'Retailer Contact Person1 Mobile','Retailer Contact Person2 Name','Retailer Contact Person2 Mobile','Email','Dob', 'GST', 'Distributor 1','Distributor Code', 'Distributor 2', 'Target', 'Date','Time','Video Downloaded'); 
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($loginCountWiseReport as $row) {
                 if(!empty($row->users)){
               $date = date('j F, Y', strtotime($row['created_at']));
                $time = date('H:i s', strtotime($row['created_at']));
                $displayASEName = '';
               // foreach(explode(',',$row->user_id) as $aseKey => $aseVal) 
                //{
                    
                    $catDetails = DB::table('users')->where('id', $row['user_id'])->first();
                    $displayASEName = $catDetails->name ?? '';
                    $displayASECode = $catDetails->employee_id ?? '';
               // }
                $store_name = $row->store_name ?? '';
               
                $storename = \App\Models\Team::where('store_id', $row->users->id)->with('distributors','rsm','zsm','nsm','asm','sm','ase')->first();
               
                $lineData = array(
                    $count,
                    $row->users->unique_code ?? '',
                    $row->users->name,
                    ucwords($row->users->address) ??'',
                    $row->users->areas->name ??'',
                    $row->users->states->name ??'',
                    $row->users->pin,
                    $row->users->contact_person_fname.' '.$row->users->contact_person_lname ?? '',
                    $row->users->contact_person_phone,
                    $row->retailer_contact_person2_name ?? '',
                    $row->retailer_contact_person2_mobile?? '',
                    $row->users->email,
                    $row->users->dob,
                    $row->users->gst,
                    $storename->distributors->name ?? '',
                    $storename->distributors->employee_id ?? '',
                    $row->distributor2 ?? '',
                    $row->target,
                    $date,
                    $time,
                    ($row->is_download==1)? 'yes' : 'no'
                    
                   
                );
                

                fputcsv($f, $lineData, $delimiter);

                $count++;
            }
            }

            // Move back to beginning of file
            fseek($f, 0);

            // Set headers to download file rather than displayed
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '";');

            //output all remaining data on a file pointer
            fpassthru($f);
        }
	}
	
	
	
	
	
	
	
	public function retailerBranding(Request $request)
	{
		$allASEs = User::select('id','name')->where('type',6)->where('name', '!=', null)->where('status',1)->groupBy('name')->orderBy('name')->get();
        $allASMs = User::select('id','name')->where('type',5)->where('name', '!=', null)->where('status',1)->groupBy('name')->orderBy('name')->get();
        $allDistributors = User::select('id','name','employee_id','state')->where('type',7)->where('name', '!=', null)->where('status',1)->groupBy('name')->orderBy('name')->get();
        $stateData = State::where('status',1)->groupBy('name')->orderBy('name')->get();
	//	$areaData=Area::where('state_id',$state)->orderby('name')->get();
		if( isset($request->distributor_id)||isset($request->ase_id)||isset($request->asm_id)||isset($request->state_id)||isset($request->keyword)||isset($request->date_from)||isset($request->date_to)) 
        {
           
            $from = $request->date_from ? $request->date_from : '';
            $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';

            $distributor = $request->distributor_id ? $request->distributor_id : '';
            $ase = $request->ase_id ? $request->ase_id : '';
            $asm = $request->asm_id ? $request->asm_id : '';
            $stateDetails = $request->state_id ? $request->state_id : '';
            
            $keyword = $request->keyword ? $request->keyword : '';
			
            $query = Branding::selectRaw('brandings.*')->join('stores', 'stores.id', 'brandings.store_id')->join('teams', 'teams.store_id', 'stores.id');
            $query->when($distributor, function($query) use ($distributor) {
                $query->whereRaw("find_in_set('".$distributor."',teams.distributor_id)");
            });
            $query->when($ase, function($query) use ($ase) {
                $query->whereRaw("find_in_set('".$ase."',stores.user_id)");
            });
            $query->when($asm, function($query) use ($asm) {
                $query->whereRaw("find_in_set('".$asm."',stores.user_id)");
            });
             $query->when($stateDetails, function($query) use ($stateDetails) {
                $query->where('stores.state_id', $stateDetails);
            });
           
		
		
		
            $query->when($keyword, function($query) use ($keyword) {
                $query->where('stores.name','=',$keyword)
                ->orWhere('stores.business_name', $keyword)
                ->orWhere('stores.owner_fname', $keyword)
                ->orWhere('stores.contact','=', $keyword)
                ->orWhere('stores.unique_code','=', $keyword);
            })->whereBetween('brandings.created_at', [$from, $to]);

            $loginCountWiseReport = $query->where('brandings.store_id','!=','')->latest('brandings.id')->groupby('brandings.store_id')->paginate(25);
           // dd($data);
        }
        else{		
			// $loginCountWiseReport = \DB::select('SELECT * FROM `stores` AS s
				//	WHERE s.state_id = "'.$state.'" and s.secret_pin IS NOT NULL
					// GROUP BY s.state_id ');
		$loginCountWiseReport=Branding::orderby('id','desc')->groupby('brandings.store_id')->paginate(25);
		//dd($loginCountWiseReport);
        }
		return view('admin.reward.user.store-branding', compact('loginCountWiseReport',  'request','allDistributors','allASEs','allASMs','stateData'));
	}
	
	
	
	public function retailerBrandingexportCSV(Request $request)
	{
		$allASEs = User::select('id','name')->where('type',6)->where('name', '!=', null)->where('status',1)->groupBy('name')->orderBy('name')->get();
        $allASMs = User::select('id','name')->where('type',5)->where('name', '!=', null)->where('status',1)->groupBy('name')->orderBy('name')->get();
        $allDistributors = User::select('id','name')->where('type',7)->where('name', '!=', null)->where('status',1)->groupBy('name')->orderBy('name')->get();
        $stateData = State::where('status',1)->groupBy('name')->orderBy('name')->get();
			
		if( isset($request->distributor_id)||isset($request->ase_id)||isset($request->asm_id)||isset($request->state_id)||isset($request->keyword)||isset($request->date_from)||isset($request->date_to)) 
        {
           
            $from = $request->date_from ? $request->date_from : '';
            $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';

            $distributor = $request->distributor_id ? $request->distributor_id : '';
            $ase = $request->ase_id ? $request->ase_id : '';
            $asm = $request->asm_id ? $request->asm_id : '';
            $stateDetails = $request->state_id ? $request->state_id : '';
            
            $keyword = $request->keyword ? $request->keyword : '';
			
            $query = Branding::selectRaw('brandings.*')->join('stores', 'stores.id', 'brandings.store_id')->join('teams', 'teams.store_id', 'stores.id');
            $query->when($distributor, function($query) use ($distributor) {
                $query->whereRaw("find_in_set('".$distributor."',teams.distributor_id)");
            });
            $query->when($ase, function($query) use ($ase) {
                $query->whereRaw("find_in_set('".$ase."',stores.user_id)");
            });
            $query->when($asm, function($query) use ($asm) {
                $query->whereRaw("find_in_set('".$asm."',stores.user_id)");
            });
             $query->when($stateDetails, function($query) use ($stateDetails) {
                $query->where('stores.state_id', $stateDetails);
            });
           
		
		
		
            $query->when($keyword, function($query) use ($keyword) {
                $query->where('stores.name','=',$keyword)
                ->orWhere('stores.business_name', $keyword)
                ->orWhere('stores.owner_fname', $keyword)
                ->orWhere('stores.contact','=', $keyword)
                ->orWhere('stores.unique_code','=', $keyword);
            })->whereBetween('brandings.created_at', [$from, $to]);

            $loginCountWiseReport = $query->where('brandings.store_id','!=','')->latest('brandings.id')->groupby('brandings.store_id')->paginate(25);
           // dd($data);
        }
        else{		
			// $loginCountWiseReport = \DB::select('SELECT * FROM `stores` AS s
				//	WHERE s.state_id = "'.$state.'" and s.secret_pin IS NOT NULL
					// GROUP BY s.state_id ');
		$loginCountWiseReport=Branding::orderby('id','desc')->groupby('brandings.store_id')->paginate(25);
		//dd($loginCountWiseReport);
        }
		 if (count($loginCountWiseReport) > 0) {
            $delimiter = ","; 
            $filename = "retail-branding-request-list-".date('Y-m-d').".csv"; 

            // Create a file pointer 
            $f = fopen('php://memory', 'w'); 

            // Set column headers 
            $fields = array( 'Sr', 'Unique Code','Retailer Name', 'Retailer Contact', 'Retailer State', 'Retailer City','ASE/ASM','Distributor', 'Remarks','Date','Time'); 
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($loginCountWiseReport as $row) {
                 if(!empty($row->stores)){
               $date = date('j F, Y', strtotime($row['created_at']));
                $time = date('H:i s', strtotime($row['created_at']));
                $displayASEName = '';
               // foreach(explode(',',$row->user_id) as $aseKey => $aseVal) 
                //{
                    
                    $catDetails = DB::table('users')->where('id', $row['user_id'])->first();
                    $displayASEName = $catDetails->name ?? '';
                    $displayASECode = $catDetails->employee_id ?? '';
               // }
                $store_name = $row->store_name ?? '';
                $storename = \App\Models\Team::where('store_id', $row->store_id)->with('distributors','rsm','zsm','nsm','asm','sm','ase')->first();
               
               
                $lineData = array(
                    $count,
                    $row->stores->unique_code ?? '',
                    ucwords($row->stores->name),
                    $row->stores->contact ??'',
                    $row->stores->states->name ??'',
                    $row->stores->areas->name ??'',
                    $row->stores->users->name ??'',
                    $storename->distributors->name ??'',
                    $row->remarks,
                    $date,
                    $time
                    
                   
                );
                

                fputcsv($f, $lineData, $delimiter);

                $count++;
            }
            }

            // Move back to beginning of file
            fseek($f, 0);

            // Set headers to download file rather than displayed
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '";');

            //output all remaining data on a file pointer
            fpassthru($f);
        }
	}
}
