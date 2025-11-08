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
class DistributorController extends Controller
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
        
        $keyword = $request->keyword ? $request->keyword : '';

        $query = User::query();

// Filter by type and keyword
        $query->where('type', 7)
            ->when($keyword, function($query) use ($keyword) {
                $query->where(function($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%')
                        ->orWhere('fname', 'like', '%'.$keyword.'%')
                        ->orWhere('lname', 'like', '%'.$keyword.'%')
                        ->orWhere('mobile', 'like', '%'.$keyword.'%')
                        ->orWhere('employee_id', 'like', '%'.$keyword.'%')
                        ->orWhere('email', 'like', '%'.$keyword.'%')->orWhere('postal_code', 'like', '%'.$keyword.'%');
                });
            });
        
        $data = $query->latest('id')->paginate(25);
        //dd($data->toSql());
		$state = State::where('status',1)->groupBy('name')->orderBy('name')->get();
        return view('admin.distributor.user.index', compact('data', 'state', 'request'));
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
	
	
	/*public function exportCSV(Request $request)
    {
        
       
             $keyword = $request->keyword ? $request->keyword : '';

        $query = User::query();

        // Filter by type and keyword
        $query->where('type', 7)
            ->when($keyword, function($query) use ($keyword) {
                $query->where(function($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%')
                        ->orWhere('fname', 'like', '%'.$keyword.'%')
                        ->orWhere('lname', 'like', '%'.$keyword.'%')
                        ->orWhere('mobile', 'like', '%'.$keyword.'%')
                        ->orWhere('employee_id', 'like', '%'.$keyword.'%')
                        ->orWhere('email', 'like', '%'.$keyword.'%');
                });
            });
    
        $data = $query->latest('id')->cursor();
	    $dis=$data->all();
	    dd($dis);
        if (count($dis) > 0) {
            $delimiter = ",";
            $filename = "lux-all-distributor-list-".date('Y-m-d').".csv";

            // Create a file pointer
            $f = fopen('php://memory', 'w');

            // Set column headers
            $fields = array('SR', 'NAME', 'DESIGNATION', 'MOBILE', 'STATE', 'CITY', 'CODE', 'NSM','ZSM','RSM','SM','ASM','ASE','SEQUENCE NO STATE WISE','GIVEN COUPON' ,'RETAILER SCAN COUNT','REST COUPON COUNT','STATUS');
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($data as $row) {
				//dd($row);
                $date = date('j F, Y', strtotime($row['created_at']));
                $time = date('h:i A', strtotime($row['created_at']));
				$findTeamDetails= findTeamDetails($row->id, $row->type);
                $store_id=[];
                $store=DB::select("SELECT s.*  FROM `stores` s
                                				INNER JOIN teams t ON s.id = t.store_id
                                                WHERE t.distributor_id=$row->id and s.status=1
                                               
                                                ORDER BY s.name ASC");
                                                
                                                foreach($store as $stores){
                                                     array_push($store_id, $stores->id);
                                                }
                                        //dd($store_id);
                                        $reward=\App\Models\RetailerWalletTxn::whereIN('user_id',$store_id)->where('type',1)->where('barcode_id','!=',NULL)->count();
                                        
                                        $remainingAmount=(($row->given_coupon)-($reward));
                // $store = Store::select('store_name')->where('id', $row['store_id'])->first();
                // $user = User::select('name', 'mobile', 'state', 'city', 'pin')->where('id', $row['user_id'])->first();

                // dd($store->store_name, $user->name, $user->mobile);

                $lineData = array(
                    $count,
                    // $user->name ?? '',
                    $row['name'],
                    $row['designation'],
                    $row['mobile'],
					
                    $row['state'],
                    $row['city'],
                    $row['employee_id'],
                    $findTeamDetails[0]['nsm'] ?? '',
                    $findTeamDetails[0]['zsm']?? '',
                    $findTeamDetails[0]['rsm']?? '',
                    $findTeamDetails[0]['sm']?? '',
                    $findTeamDetails[0]['asm']?? '',
					$findTeamDetails[0]['ase']?? '',
					$row['distributor_position_code'] ?? '',
					$row->given_coupon ??'',
					$reward ??'',
					$remainingAmount ??'',
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
    }*/
    
    
    public function exportCSV(Request $request)
{
    $keyword = $request->keyword ?? '';

    $query = User::query()
        ->where('type', 7)
        ->when($keyword, function ($query) use ($keyword) {
            $query->where(function ($query) use ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('fname', 'like', '%' . $keyword . '%')
                    ->orWhere('lname', 'like', '%' . $keyword . '%')
                    ->orWhere('mobile', 'like', '%' . $keyword . '%')
                    ->orWhere('employee_id', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%');
            });
        });
   
    $filename = "lux-all-distributor-list-" . date('Y-m-d') . ".csv";
    header('Content-Type: text/csv');
    header("Content-Disposition: attachment; filename=$filename");

    $f = fopen('php://output', 'w');
    // Open output stream
    //$handle = fopen('php://output', 'w');
    //ob_start();

    // Set headers
    $headers = ['SR', 'NAME', 'DESIGNATION', 'MOBILE', 'STATE', 'CITY', 'CODE', 'NSM', 'ZSM', 'RSM', 'SM', 'ASM', 'ASE', 'SEQUENCE NO STATE WISE', 'GIVEN COUPON', 'RETAILER SCAN COUNT', 'REST COUPON COUNT', 'STATUS'];
    fputcsv($f, $headers);

    // Process data in chunks to avoid memory issues
    $count = 1;
   $query->chunk(100, function ($rows) use (&$count, $f) {
        foreach ($rows as $row) {
             //dd($users);
            //$reward = \App\Models\RetailerWalletTxn::whereIN('user_id', [$row->id])->where('type', 1)->where('barcode_id', '!=', NULL)->count();
             $reward = \App\Models\RetailerWalletTxn::join('stores', 'stores.id', 'retailer_wallet_txns.user_id')
                                            ->join('teams', 'stores.id', 'teams.store_id')
                                            ->join('retailer_barcodes', 'retailer_barcodes.id', 'retailer_wallet_txns.barcode_id')
                                            ->whereRaw("find_in_set('".$row->id."', retailer_barcodes.distributor_id)")
                                            ->where('retailer_wallet_txns.created_at', '>', '2024-10-01') // Filter after 1st Oct 2024
                                            ->latest('retailer_wallet_txns.id')
                                            ->count();
            $remainingAmount = ($row->given_coupon ?? 0) - $reward;
             $findTeamDetails = findTeamDetails($row->id, $row->type);
            $lineData = [
                $count++,
                $row->name,
                $row->designation,
                $row->mobile,
                $row->state,
                $row->city,
                $row->employee_id,
                 $findTeamDetails[0]['nsm'] ?? '',
                 $findTeamDetails[0]['zsm'] ?? '',
                 $findTeamDetails[0]['rsm'] ?? '',
                 $findTeamDetails[0]['sm'] ?? '',
                 $findTeamDetails[0]['asm'] ?? '',
                 $findTeamDetails[0]['ase'] ?? '',
                $row->distributor_position_code ?? '',
                $row->given_coupon ?? '',
                $reward,
                $remainingAmount,
                $row->status == 1 ? 'Active' : 'Inactive'
            ];

             fputcsv($f, $lineData);
        }
    });

   fclose($f);
    exit;
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
		
        $allDistributors = User::select('id','name')->where('type',7)->where('name', '!=', null)->where('status',1)->groupBy('name')->orderBy('name')->get();
       
		if( isset($request->distributor_id)||isset($request->keyword)||isset($request->date_from)||isset($request->date_to)) 
        {
           
             $from = $request->date_from ? $request->date_from : '';
            $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';

            $distributor = $request->distributor_id ? $request->distributor_id : '';
           
            
            $keyword = $request->keyword ? $request->keyword : '';
			
            $query = StoreFormSubmit::selectRaw('store_form_submits.*')->with('distributors');
            $query->when($distributor, function($query) use ($distributor) {
                $query->whereRaw("find_in_set('".$distributor."',store_form_submits.distributor_id)");
            });
            
           
		
		
		
            $query->when($keyword, function($query) use ($keyword) {
                $query->where('users.name','=',$keyword)
                ->orWhere('users.contact','=', $keyword);
            })->whereBetween('store_form_submits.created_at', [$from, $to]);

            $loginCountWiseReport = $query->where('store_form_submits.distributor_id','!=','')->latest('store_form_submits.id')->groupby('store_form_submits.distributor_id')->paginate(25);
           // dd($data);
        }
        else{		
			// $loginCountWiseReport = \DB::select('SELECT * FROM `stores` AS s
				//	WHERE s.state_id = "'.$state.'" and s.secret_pin IS NOT NULL
					// GROUP BY s.state_id ');
		$loginCountWiseReport=StoreFormSubmit::orderby('id','desc')->where('store_form_submits.distributor_id','!=','')->groupby('store_form_submits.distributor_id')->paginate(25);
        }
		return view('admin.distributor.user.store-engage-form', compact('loginCountWiseReport',  'request','allDistributors'));
	}
	
	
	
	public function retailerProgramexportCSV(Request $request)
	{
		$allDistributors = User::select('id','name')->where('type',7)->where('name', '!=', null)->where('status',1)->groupBy('name')->orderBy('name')->get();
       
		if( isset($request->distributor_id)||isset($request->keyword)||isset($request->date_from)||isset($request->date_to)) 
        {
           
             $from = $request->date_from ? $request->date_from : '';
            $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';

            $distributor = $request->distributor_id ? $request->distributor_id : '';
           
            
            $keyword = $request->keyword ? $request->keyword : '';
			
            $query = StoreFormSubmit::selectRaw('store_form_submits.*')->with('distributors');
            $query->when($distributor, function($query) use ($distributor) {
                $query->whereRaw("find_in_set('".$distributor."',store_form_submits.distributor_id)");
            });
            
           
		
		
		
            $query->when($keyword, function($query) use ($keyword) {
                $query->where('users.name','=',$keyword)
                ->orWhere('users.contact','=', $keyword);
            })->whereBetween('store_form_submits.created_at', [$from, $to]);

            $loginCountWiseReport = $query->where('store_form_submits.distributor_id','!=','')->latest('store_form_submits.id')->groupby('store_form_submits.distributor_id')->paginate(25);
           // dd($data);
        }
        else{		
			// $loginCountWiseReport = \DB::select('SELECT * FROM `stores` AS s
				//	WHERE s.state_id = "'.$state.'" and s.secret_pin IS NOT NULL
					// GROUP BY s.state_id ');
		$loginCountWiseReport=StoreFormSubmit::orderby('id','desc')->where('store_form_submits.distributor_id','!=','')->groupby('store_form_submits.distributor_id')->paginate(25);
        }
		 if (count($loginCountWiseReport) > 0) {
            $delimiter = ","; 
            $filename = "distributor-engagement-list-".date('Y-m-d').".csv"; 

            // Create a file pointer 
            $f = fopen('php://memory', 'w'); 

            // Set column headers 
            $fields = array( 'SR', 'Distributor Name','Distributor Code', 'Distributor Contact','Email','City', 'State','Target', 'Date','Time','Video Downloaded'); 
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($loginCountWiseReport as $row) {
                 if(!empty($row->distributors)){
               $date = date('j F, Y', strtotime($row['created_at']));
                $time = date('H:i s', strtotime($row['created_at']));
                $displayASEName = '';
               // foreach(explode(',',$row->user_id) as $aseKey => $aseVal) 
                //{
                    
                    $catDetails = DB::table('users')->where('id', $row['user_id'])->first();
                    $displayASEName = $catDetails->name ?? '';
                    $displayASECode = $catDetails->employee_id ?? '';
               // }
                
               
                $lineData = array(
                    $count,
                    $row->distributors->name ??'',
                    $row->distributors->employee_id ??'',
                    $row->distributors->mobile ??'',
                    $row->distributors->email ??'',
                    $row->distributors->city ??'',
                    $row->distributors->state ??'',
                    
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
        $allDistributors = User::select('id','name')->where('type',7)->where('name', '!=', null)->where('status',1)->groupBy('name')->orderBy('name')->get();
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
                $query->whereRaw("find_in_set('".$distributor."',brandings.distributor_id)");
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

            $loginCountWiseReport = $query->where('brandings.distributor_id','!=','')->latest('brandings.id')->groupby('brandings.distributor_id')->paginate(25);
           // dd($data);
        }
        else{		
		
		$loginCountWiseReport=Branding::orderby('id','desc')->where('brandings.distributor_id','!=','')->groupby('brandings.distributor_id')->paginate(25);
		//dd($loginCountWiseReport);
        }
		return view('admin.distributor.user.store-branding', compact('loginCountWiseReport',  'request','allDistributors','allASEs','allASMs','stateData'));
	}
	
	
	
	public function retailerBrandingexportCSV(Request $request)
	{
		$allASEs = User::select('id','name')->where('type',6)->where('name', '!=', null)->where('status',1)->groupBy('name')->orderBy('name')->get();
        $allASMs = User::select('id','name')->where('type',5)->where('name', '!=', null)->where('status',1)->groupBy('name')->orderBy('name')->get();
        $allDistributors = User::select('id','name')->where('type',7)->where('name', '!=', null)->where('status',1)->groupBy('name')->orderBy('name')->get();
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
                $query->whereRaw("find_in_set('".$distributor."',brandings.distributor_id)");
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

            $loginCountWiseReport = $query->where('brandings.distributor_id','!=','')->latest('brandings.id')->groupby('brandings.distributor_id')->paginate(25);
           // dd($data);
        }
        else{		
		
		$loginCountWiseReport=Branding::orderby('id','desc')->where('brandings.distributor_id','!=','')->groupby('brandings.distributor_id')->paginate(25);
		//dd($loginCountWiseReport);
        }
		 if (count($loginCountWiseReport) > 0) {
            $delimiter = ","; 
            $filename = "retail-branding-request-list-by-distributor-".date('Y-m-d').".csv"; 

            // Create a file pointer 
            $f = fopen('php://memory', 'w'); 

            // Set column headers 
            $fields = array( 'Sr', 'Distributor','Unique Code','Retailer Name', 'Retailer Contact', 'Retailer State', 'Retailer City','ASE/ASM','Remarks', 'Date','Time'); 
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
                    $row->distributors->name ??'',
                    $row->stores->unique_code ?? '',
                    ucwords($row->stores->name),
                    $row->stores->contact ??'',
                    $row->stores->states->name ??'',
                    $row->stores->areas->name ??'',
                    $row->stores->users->name ??'',
                    
                    $row->remarks ??'',
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
	
	
	
	
	public function couponCSVUpload(Request $request)
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
                        //$stateData = '';
                        
                        $user=User::where('employee_id',$importData[0])->first();
                        if(!empty($user)){
                            $userId =$user->id;
                        
						$user=User::findOrFail($userId);
						$user->given_coupon = $importData[1];
						
						$user->save();
                        }						
                              
                    
                   
                        
                     }
                 } else {
                     Session::flash('message', 'File too large. File must be less than 50MB.');
                 }
             } else {
                 Session::flash('message', 'Invalid File Extension. supported extensions are ' . implode(', ', $valid_extension));
             }
         } else {
             Session::flash('message', 'No file found.');
         }
 
         return redirect()->back();
     }
     
     
     
     	public function distributorSequenceCodeCSVUpload(Request $request)
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
                        //$stateData = '';
                        
                        $user=User::where('employee_id',$importData[0])->first();
                        if(!empty($user)){
                            $userId =$user->id;
                        
						$user=User::findOrFail($userId);
						$user->	distributor_position_code = $importData[1];
						
						$user->save();
                        }						
                              
                    
                   
                        
                     }
                 } else {
                     Session::flash('message', 'File too large. File must be less than 50MB.');
                 }
             } else {
                 Session::flash('message', 'Invalid File Extension. supported extensions are ' . implode(', ', $valid_extension));
             }
         } else {
             Session::flash('message', 'No file found.');
         }
 
         return redirect()->back();
     }
     
     
     
     
     public function videoCSVUpload(Request $request)
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
                        //$stateData = '';
                        
                        $user=User::where('employee_id',$importData[0])->first();
                        if(!empty($user)){
                            $userId =$user->id;
                        
						$user=User::findOrFail($userId);
						$user->video_link = $importData[1];
						
						$user->save();
                        }						
                              
                    
                   
                        
                     }
                 } else {
                     Session::flash('message', 'File too large. File must be less than 50MB.');
                 }
             } else {
                 Session::flash('message', 'Invalid File Extension. supported extensions are ' . implode(', ', $valid_extension));
             }
         } else {
             Session::flash('message', 'No file found.');
         }
 
         return redirect()->back();
     }
     
     
     
     
     
     
     public function DistributorVideoUpdate(Request $request)
    { 
        try {
            $StoreOld = User::select('id', 'video_link', 'upload_status')
                ->where('type',7)
                ->where('upload_status', 0)
                ->whereNotNull('video_link')
                ->limit(15)
                ->get();
            $total_count = 0;
            if (count($StoreOld) > 0) {
                foreach ($StoreOld as $k => $item) {
                    try {
                        $videoUrl = $item->video_link;
                        if ($videoUrl) {
                            // Fetch the video content from the URL
                            $response = Http::get($videoUrl);
                            // Check if the request was successful
                            $id = $item->id;
                            if ($response->successful()) {
                                // Get the video content
                                $videoContent = $response->body();
                                // Generate a unique filename for the video
                                
                                $fileName = $id . rand(10000000, 99999999) . '.' . pathinfo($videoUrl, PATHINFO_EXTENSION);
                                // Define the path to save the video
                                $filePath = 'uploads/distributor_videos/' . $fileName;
    
                                // Ensure the directory exists
                                if (!file_exists(public_path('uploads/distributor_videos'))) {
                                    mkdir(public_path('uploads/distributor_videos'), 0777, true);
                                }
    
                                // Save the video content to the file
                                file_put_contents(public_path($filePath), $videoContent);
                                $total_count += 1;
    
                                // Save the file path to the database (example assumes a `video_path` column in your model)
                                $video = User::findOrFail($id);
                                $video->video_link = asset($filePath);
                                $video->upload_status = 1;
                                $video->save();
                            } else {
                               $video = User::findOrFail($id);
                                $video->upload_status = 2;
                                $video->save();
                            }
                        }
                    } catch (\Exception $e) {
                        // Log the error message
                        return response()->json([
                            'success' => true,
                            'message' => $e->getMessage(),
                            'upload_video' => $total_count,
                        ]);
                    }
                }
    
                return response()->json([
                    'success' => true,
                    'message' => 'Video downloaded and saved successfully.',
                    'upload_video' => $total_count,
                ]);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'No videos found for download.',
                'upload_video' => $total_count,
            ]);
    
        } catch (\Exception $e) {
            // Log the error message
            return response()->json([
                'success' => true,
                'message' => $e->getMessage(),
                'upload_video' => $total_count,
            ]);
        }
    }

    public function DistributorVideoUploadReport(){
         $StoreOld = User::select('id')->where('upload_status', 0)->whereNotNull('video_link')->get();
         $StoreNew = User::select('id')->where('upload_status', 1)->get();
        return response()->json([
            'success' => true,
            'pending-data' =>count($StoreOld),
            'update-data' =>count($StoreNew),
        ]);
    }
}
