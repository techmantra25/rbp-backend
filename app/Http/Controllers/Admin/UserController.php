<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserAttendance;
use App\Models\State;
use App\Models\Store;
use App\Models\Area;
use App\Models\UserArea;
use App\Models\Team;
use App\Models\Activity;
use App\Models\Visit;
use App\Models\Notification; 
use App\Models\DistributorRange;
use App\Models\Collection;
use App\Models\HeadQuater;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\RetailerWalletTxn;
use App\Models\RetailerBarcode;
use App\Models\RetailerOrder;
use App\Models\RewardOrderProduct;
use App\Models\StoreFormSubmit;
use DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user_type = $request->user_type ? $request->user_type : '';
        $state = $request->state ? $request->state : '';
        $area = $request->area ? $request->area : '';
        $keyword = $request->keyword ? $request->keyword : '';
    
        $query = User::query();
    
        $query->when($user_type, function($query) use ($user_type) {
            $query->where('type', $user_type);
        });
        $query->when($state, function($query) use ($state) {
            $query->where('state', $state);
        });
        $query->when($area, function($query) use ($area) {
            $query->where('city', $area);
        });
        $query->when($keyword, function($query) use ($keyword) {
            $query->where('name', 'like', '%'.$keyword.'%')
            ->orWhere('fname', 'like', '%'.$keyword.'%')
            ->orWhere('lname', 'like', '%'.$keyword.'%')
            ->orWhere('mobile', 'like', '%'.$keyword.'%')
            ->orWhere('employee_id', 'like', '%'.$keyword.'%')
            ->orWhere('email', 'like', '%'.$keyword.'%')
           
            ;
        });
    
        $data = $query->latest('id')->paginate(25);
        
		$state = State::where('status',1)->groupBy('name')->orderBy('name')->get();
        return view('admin.user.index', compact('data', 'state', 'request'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::all();
        $stateDetails=State::where('status',1)->orderby('name')->groupby('name')->get();
		$hq=HeadQuater::where('status',1)->orderby('name')->groupby('name')->get();
        return view('admin.user.create', compact('users','stateDetails','hq'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request->all());
         $request->validate([
            "name" => "required",
            "fname" => "required|string|max:255",
            "lname" => "required|string|max:255",
            "email" => "nullable|string|max:255",
            "mobile" => "nullable|integer|digits:10",
            "whatsapp_no" => "nullable|integer|digits:10",
            "type" => "required",
			 "designation" =>"required",
            "employee_id" => "nullable|string|min:1|unique:users",
            "address" => "nullable|string",
            "landmark" => "nullable|string",
            "state" => "nullable|string",
            "area" => "nullable|string",
            "headquater" => "nullable|string",
            "password" => "nullable"
            
        ]);
		function generateUniqueAlphaNumeric($length = 10) {
            $random_string = '';
            for ($i = 0; $i < $length; $i++) {
                $number = random_int(0, 36);
                $character = base_convert($number, 10, 36);
                $random_string .= $character;
            }
            return $random_string;
        }
        $collectedData = $request->except('_token');
        $newEntry = new User;
        $newEntry->fname = $collectedData['fname'];
        $newEntry->lname = $collectedData['lname'];
		$newEntry->name = $collectedData['name'];
        $newEntry->email = $collectedData['email'];
        $newEntry->mobile = $collectedData['mobile'];
        $newEntry->whatsapp_no = $collectedData['whatsapp_no'];
        $newEntry->employee_id = $collectedData['employee_id'];
        $newEntry->type = $collectedData['type'];
        $newEntry->state = $collectedData['state'];
        $newEntry->city = $collectedData['area'];
        $newEntry->headquater = $collectedData['headquater'] ?? '';
        $newEntry->date_of_joining = $collectedData['date_of_joining'] ?? '';
        if($collectedData['type'] ==7){
        $newEntry->password = strtoupper(generateUniqueAlphaNumeric(8));
        }else{
             $newEntry->password = Hash::make($collectedData['password']);
        }
        if(!empty($collectedData->image)){
        $upload_path = "uploads/user/";
        $image = $collectedData['image'];
        $imageName = time() . "." . $image->getClientOriginalName();
        $image->move($upload_path, $imageName);
        $uploadedImage = $imageName;
        $newEntry->image = $upload_path . $uploadedImage;
		}
        $newEntry->save();

        if ($newEntry) {
            return redirect()->route('admin.users.index');
        } else {
            return redirect()->route('admin.users.index')->withInput($request->all());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $data = (object) [];
        $data->user = User::findOrFail($id);

        // VP
        if ($data->user->type == 1) {
            $user_type = $request->user_type ? $request->user_type : '';
            $name = $request->name ? $request->name : '';
            $state = $request->state ? $request->state : '';
            $area = $request->area ? $request->area : '';

            $query = Team::where('nsm_id', $data->user->id);

            $query->when($user_type, function($query) use ($user_type, $name) {
                if(!empty($name)) {
                    if ($user_type == 1) $user_type = 'vp';
                    elseif ($user_type == 2) $user_type = 'rsm';
                    elseif ($user_type == 3) $user_type = 'asm';
                    elseif ($user_type == 4) $user_type = 'ase';
                    elseif ($user_type == 5) $user_type = 'distributor_name';
                    else $user_type = 'retailer';

                    $query->where($user_type, $name);
                } else {
                    if ($user_type == 2) {
                        $user_type = 'rsm';
                        $query->where('rsm', '!=', null)->groupBy('rsm');
                    } elseif ($user_type == 3) {
                        $user_type = 'asm';
                        $query->where('asm', '!=', null)->groupBy('asm');
                    } elseif ($user_type == 4) {
                        $user_type = 'ase';
                        $query->where('ase', '!=', null)->groupBy('ase');
                    } elseif ($user_type == 5) {
                        $user_type = 'distributor_name';
                        $query->where('distributor_name', '!=', null)->groupBy('distributor_name');
                    } else {
                        $user_type = 'retailer';
                        $query->where('retailer', '!=', null)->groupBy('retailer');
                    }
                }
            });

            $query->when($state, function($query) use ($state) {
                $query->where('state', $state);
            });
            $query->when($area, function($query) use ($area) {
                $query->where('area', $area);
            });

            $data->team = $query->paginate(25);
            
            return view('admin.user.detail.nsm', compact('data', 'id', 'request'));
        }
        //ZSM
         elseif ($data->user->type == 2) {
            $user_type = $request->user_type ? $request->user_type : '';
            $name = $request->name ? $request->name : '';
            $state = $request->state ? $request->state : '';
            $area = $request->area ? $request->area : '';

            $query = Team::where('zsm_id', $data->user->id);

            $query->when($user_type, function($query) use ($user_type, $name) {
                if(!empty($name)) {
                    if ($user_type == 1) $user_type = 'vp';
                    elseif ($user_type == 2) $user_type = 'rsm';
                    elseif ($user_type == 3) $user_type = 'asm';
                    elseif ($user_type == 4) $user_type = 'ase';
                    elseif ($user_type == 5) $user_type = 'distributor_name';
                    else $user_type = 'retailer';

                    $query->where($user_type, $name);
                } else {
                    if ($user_type == 2) {
                        $user_type = 'rsm';
                        $query->where('rsm', '!=', null)->groupBy('rsm');
                    } elseif ($user_type == 3) {
                        $user_type = 'asm';
                        $query->where('asm', '!=', null)->groupBy('asm');
                    } elseif ($user_type == 4) {
                        $user_type = 'ase';
                        $query->where('ase', '!=', null)->groupBy('ase');
                    } elseif ($user_type == 5) {
                        $user_type = 'distributor_name';
                        $query->where('distributor_name', '!=', null)->groupBy('distributor_name');
                    } else {
                        $user_type = 'retailer';
                        $query->where('retailer', '!=', null)->groupBy('retailer');
                    }
                }
            });

            $query->when($state, function($query) use ($state) {
                $query->where('state', $state);
            });
            $query->when($area, function($query) use ($area) {
                $query->where('area', $area);
            });

            $data->team = $query->paginate(25);
            
            return view('admin.user.detail.zsm', compact('data', 'id', 'request'));
        }
         // SM
         elseif ($data->user->type == 3) {
            $user_type = $request->user_type ? $request->user_type : '';
            $name = $request->name ? $request->name : '';
            $state = $request->state ? $request->state : '';
            $area = $request->area ? $request->area : '';

            $query = Team::where('rsm_id', $data->user->id);

            $query->when($user_type, function($query) use ($user_type, $name) {
                if(!empty($name)) {
                    if ($user_type == 1) $user_type = 'vp';
                    elseif ($user_type == 2) $user_type = 'rsm';
                    elseif ($user_type == 3) $user_type = 'asm';
                    elseif ($user_type == 4) $user_type = 'ase';
                    elseif ($user_type == 5) $user_type = 'distributor_name';
                    else $user_type = 'retailer';

                    $query->where($user_type, $name);
                } else {
                    if ($user_type == 2) {
                        $user_type = 'rsm';
                        $query->where('rsm', '!=', null)->groupBy('rsm');
                    } elseif ($user_type == 3) {
                        $user_type = 'asm';
                        $query->where('asm', '!=', null)->groupBy('asm');
                    } elseif ($user_type == 4) {
                        $user_type = 'ase';
                        $query->where('ase', '!=', null)->groupBy('ase');
                    } elseif ($user_type == 5) {
                        $user_type = 'distributor_name';
                        $query->where('distributor_name', '!=', null)->groupBy('distributor_name');
                    } else {
                        $user_type = 'retailer';
                        $query->where('retailer', '!=', null)->groupBy('retailer');
                    }
                }
            });

            $query->when($state, function($query) use ($state) {
                $query->where('state', $state);
            });
            $query->when($area, function($query) use ($area) {
                $query->where('area', $area);
            });

            $data->team = $query->paginate(25);
            
            return view('admin.user.detail.rsm', compact('data', 'id', 'request'));
        }
        // SM
        elseif ($data->user->type == 4) {
            $user_type = $request->user_type ? $request->user_type : '';
            $name = $request->name ? $request->name : '';
            $state = $request->state ? $request->state : '';
            $area = $request->area ? $request->area : '';

            $query = Team::where('asm_id', $data->user->id);

            $query->when($user_type, function($query) use ($user_type, $name) {
                if(!empty($name)) {
                    if ($user_type == 1) $user_type = 'vp';
                    elseif ($user_type == 2) $user_type = 'rsm';
                    elseif ($user_type == 3) $user_type = 'asm';
                    elseif ($user_type == 4) $user_type = 'ase';
                    elseif ($user_type == 5) $user_type = 'distributor_name';
                    else $user_type = 'retailer';

                    $query->where($user_type, $name);
                } else {
                    if ($user_type == 2) {
                        $user_type = 'rsm';
                        $query->where('rsm', '!=', null)->groupBy('rsm');
                    } elseif ($user_type == 3) {
                        $user_type = 'asm';
                        $query->where('asm', '!=', null)->groupBy('asm');
                    } elseif ($user_type == 4) {
                        $user_type = 'ase';
                        $query->where('ase', '!=', null)->groupBy('ase');
                    } elseif ($user_type == 5) {
                        $user_type = 'distributor_name';
                        $query->where('distributor_name', '!=', null)->groupBy('distributor_name');
                    } else {
                        $user_type = 'retailer';
                        $query->where('retailer', '!=', null)->groupBy('retailer');
                    }
                }
            });

            $query->when($state, function($query) use ($state) {
                $query->where('state', $state);
            });
            $query->when($area, function($query) use ($area) {
                $query->where('area', $area);
            });

            $data->team = $query->paginate(25);
            
            return view('admin.user.detail.sm', compact('data', 'id', 'request'));
        }
        // ASM
        elseif ($data->user->type == 5) {
            $user_type = $request->user_type ? $request->user_type : '';
            $name = $request->name ? $request->name : '';
            $state = $request->state ? $request->state : '';
            $area = $request->area ? $request->area : '';

            $query = Team::where('asm_id', $data->user->id);

            $query->when($user_type, function($query) use ($user_type, $name) {
                if(!empty($name)) {
                    if ($user_type == 1) $user_type = 'vp';
                    elseif ($user_type == 2) $user_type = 'rsm';
                    elseif ($user_type == 3) $user_type = 'asm';
                    elseif ($user_type == 4) $user_type = 'ase';
                    elseif ($user_type == 5) $user_type = 'distributor_name';
                    else $user_type = 'retailer';

                    $query->where($user_type, $name);
                } else {
                    if ($user_type == 2) {
                        $user_type = 'rsm';
                        $query->where('rsm', '!=', null)->groupBy('rsm');
                    } elseif ($user_type == 3) {
                        $user_type = 'asm';
                        $query->where('asm', '!=', null)->groupBy('asm');
                    } elseif ($user_type == 4) {
                        $user_type = 'ase';
                        $query->where('ase_id', '!=', null)->groupBy('ase_id');
                    } elseif ($user_type == 5) {
                        $user_type = 'distributor_id';
                        $query->where('distributor_id', '!=', null)->groupBy('distributor_id');
                    } else {
                        $user_type = 'retailer';
                        $query->where('store_id', '!=', null)->groupBy('store_id');
                    }
                }
            });

            $query->when($state, function($query) use ($state) {
                $query->where('state_id', $state);
            });
            $query->when($area, function($query) use ($area) {
                $query->where('area_id', $area);
            });

            $data->team = $query->paginate(25);
            
            return view('admin.user.detail.asm', compact('data', 'id', 'request'));
        }
        // ASE
        elseif ($data->user->type == 6) {
            $data->retailerListOfOcc = Team::where('ase_id', $data->user->id)->where('store_id', null)->first();
            $data->workAreaList = UserArea::where('user_id', $data->user->id)->groupby('area_id')->get();
            $data->distributorList = Team::where('ase_id', $data->user->id)->where('distributor_id', '!=', null)->where('store_id',NULL)->groupBy('distributor_id')->orderBy('id','desc')->get();
            
			 $data->areaDetail= Area::orderby('name')->get();
            $data->storeList = Store::where('user_id',$data->user->id)->orderBy('name')->get();
			$data->team = Team::where('ase_id', $data->user->id)->first();
            return view('admin.user.detail.ase', compact('data', 'id', 'request'));
        }
        // Distributor
        elseif ($data->user->type == 7) {
            $area=Area::where('name', $data->user->city)->first();
			
            $data->team = Team::where('distributor_id', $data->user->id)->where('store_id','=',NULL)->first();
			
            $data->distributor = User::where('name', $data->user->name)->first();
			$data->storeList = Team::where('distributor_id', $data->user->id)->where('store_id','!=',null)->groupBy('store_id')->with('store')->get();
            return view('admin.user.detail.distributor', compact('data', 'id', 'request'));
        }
        // Retailer
        else {
            $user = User::findOrFail($id);
            $data = Store::where('name', $user->name)->with('user')->first();

            return view('admin.store.detail', compact('data', 'id', 'request'));
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
        $data=User::findOrfail($id);
        $data->stateDetails = State::where('status',1)->orderby('name')->groupby('name')->get();
        $data->allNSM = User::select('name','id')->where('type', '=', 1)->groupBy('name')->orderBy('name')->get();
        $data->allZSM = User::select('name','id')->where('type', '=', 2)->groupBy('name')->orderBy('name')->get();
        $data->allRSM = User::select('name','id')->where('type', '=', 3)->groupBy('name')->orderBy('name')->get();
        $data->allSM = User::select('name','id')->where('type', '=', 4)->groupBy('name')->orderBy('name')->get();
        $data->allASM = User::select('name','id')->where('type', '=', 5)->groupBy('name')->orderBy('name')->get();
        $data->allASE = User::select('name','id')->where('type', '=', 6)->groupBy('name')->orderBy('name')->get();
        $data->allDistributor = User::select('name','id')->where('type', '=', 7)->groupBy('name')->orderBy('name')->get();
        $hq=HeadQuater::where('status',1)->orderby('name')->groupby('name')->get();
        return view('admin.user.edit', compact('data','hq'));
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
       //dd($request->all());
        $request->validate([
            "type" => "required|integer",
            "designation" => "nullable|string|max:255",
            "name" => "required|string|max:255",
            "fname" => "required|string|max:255",
            "lname" => "required|string|max:255",
            "employee_id" => "nullable|string|max:255",
            "mobile" => "nullable|integer|digits:10",
            "email" => "nullable|string|max:255",
            "state" => "required|string|max:255",
            "area" => "nullable|string|max:255"
          
        ]);

        $updateEntry = User::findOrFail($id);
        $updateEntry->type = $request->type;
        $updateEntry->designation = $request->designation;
        $updateEntry->name = $request->name;
        $updateEntry->fname = $request->fname;
        $updateEntry->lname = $request->lname;
        $updateEntry->mobile = $request->mobile;
        $updateEntry->whatsapp_no = $request->whatsapp_no;
        $updateEntry->employee_id = $request->employee_id;
        $updateEntry->email = $request->email;
        $updateEntry->state = $request->state;
         $updateEntry->date_of_joining = $request->date_of_joining ;
        if(!empty($request->area)){
         $updateEntry->city = $request->area;
        }
        $updateEntry->headquater = $request['headquater'];
        // password
        if(!empty($request->password)) $updateEntry->password = Hash::make($request->password);

        $updateEntry->save();
        if ($updateEntry) {
            return redirect()->route('admin.users.edit', $id)->with('success', 'User detail updated successfully');
        } else {
            return redirect()->route('admin.users.edit', $id)->with('failure', 'Something happened')->withInput($request->all());
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
        $user=User::find($id);
        if($user->type==1){
            $isReferenced = DB::table('teams')->where('nsm_id', $id)->exists();
        
            if ($isReferenced) {
                return redirect()->route('admin.users.index')->with('failure', 'User cannot be deleted because it is referenced in another table.');
            }
        }
        else if($user->type==2){
            $isReferenced = DB::table('teams')->where('zsm_id', $id)->exists();
        
            if ($isReferenced) {
                return redirect()->route('admin.users.index')->with('failure', 'User cannot be deleted because it is referenced in another table.');
            }
        }else if($user->type==3){
            $isReferenced = DB::table('teams')->where('rsm_id', $id)->exists();
        
            if ($isReferenced) {
                return redirect()->route('admin.users.index')->with('failure', 'User cannot be deleted because it is referenced in another table.');
            }
        }
        else if($user->type==4){
            $isReferenced = DB::table('teams')->where('sm_id', $id)->exists();
        
            if ($isReferenced) {
                return redirect()->route('admin.users.index')->with('failure', 'User cannot be deleted because it is referenced in another table.');
            }
        } else if($user->type==5){
            $isReferenced = DB::table('teams')->where('asm_id', $id)->exists();
        
            if ($isReferenced) {
                return redirect()->route('admin.users.index')->with('failure', 'User cannot be deleted because it is referenced in another table.');
            }
        }else if($user->type==6){
            $isReferenced = DB::table('stores')->where('user_id', $id)->exists();
        
            if ($isReferenced) {
                return redirect()->route('admin.users.index')->with('failure', 'User cannot be deleted because it is referenced in another table.');
            }
        }else if($user->type==7){
            $isReferenced = DB::table('teams')->where('distributor_id', $id)->exists();
        
            if ($isReferenced) {
                return redirect()->route('admin.users.index')->with('failure', 'User cannot be deleted because it is referenced in another table.');
            }
        }
        $data=User::destroy($id);
        if ($data) {
            return redirect()->route('admin.users.index')->with('success','User Deleted successfully');
        } else {
            return redirect()->route('admin.users.index')->with('failure', 'Failed to delete user.');
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
        $category = User::findOrFail($id);
        $status = ( $category->status == 1 ) ? 0 : 1;
        $category->status = $status;
        $category->save();
        if ($category) {
            return redirect()->route('admin.users.index');
        } else {
            return redirect()->route('admin.users.create')->withInput($request->all());
        }
    }
	
	public function csvExport(Request $request)
    {
        // return Excel::download(new OrderExport, 'Secondary-sales-'.date('Y-m-d').'.csv');

       // $data = User::latest('id')
      //  ->get()
      //  ->toArray();
			$user_type = $request->user_type ? $request->user_type : '';
            $state = $request->state ? $request->state : '';
            $area = $request->area ? $request->area : '';
            $keyword = $request->keyword ? $request->keyword : '';
    
            $query = User::query();
    
            $query->when($user_type, function($query) use ($user_type) {
                $query->where('type', $user_type);
            });
            $query->when($state, function($query) use ($state) {
                $query->where('state', $state);
            });
            $query->when($area, function($query) use ($area) {
                $query->where('city', $area);
            });
            $query->when($keyword, function($query) use ($keyword) {
                $query->where('name', 'like', '%'.$keyword.'%')
                ->orWhere('fname', 'like', '%'.$keyword.'%')
                ->orWhere('lname', 'like', '%'.$keyword.'%')
                ->orWhere('mobile', 'like', '%'.$keyword.'%')
                ->orWhere('employee_id', 'like', '%'.$keyword.'%')
                ->orWhere('email', 'like', '%'.$keyword.'%');
            });
    
            $data = $query->latest('id')->get();
	
        if (count($data) > 0) {
            $delimiter = ",";
            $filename = "lux-all-users-list-".date('Y-m-d').".csv";

            // Create a file pointer
            $f = fopen('php://memory', 'w');

            // Set column headers
            $fields = array('SR', 'NAME', 'DESIGNATION', 'MOBILE', 'STATE', 'CITY','HQ', 'EMPLOYEE ID', 'WORK EMAIL', 'PERSONAL EMAIL','NSM','ZSM','RSM','SM','ASM','ASE', 'DATE','TIME');
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($data as $row) {
				//dd($row);
                $date = date('j F, Y', strtotime($row['created_at']));
                $time = date('h:i A', strtotime($row['created_at']));
				$findTeamDetails= findTeamDetails($row->id, $row->type);
               
                   
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
                    $row['headquater'] ?? '',
                    $row['employee_id'],
                    $row['email'],
                    $row['personal_mail'],
                    $findTeamDetails[0]['nsm'] ?? '',
                    $findTeamDetails[0]['zsm']?? '',
                    $findTeamDetails[0]['rsm']?? '',
                    $findTeamDetails[0]['sm']?? '',
                    $findTeamDetails[0]['asm']?? '',
					$findTeamDetails[0]['ase']?? '',
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
    }

    //password generate 
      public function passwordGenerate(Request $request)
    {
        $userDetail = User::findOrFail($request->userId);
        $explodedName = $userDetail->fname;
        $var1 = strtoupper($explodedName);

        $state = $userDetail->state;
            $var2 = strtoupper($userDetail->employee_id);

            if (!empty($var2)) {
                $newGeneratedPassword = $var1.$var2;

                return response()->json([
                    'status' => 200,
                    'message' => 'Password generated',
                    'data' => $newGeneratedPassword
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Invalid Employee code. Please generate manually'
                ]);
            }

       
    }
    //reset password
    public function passwordReset(Request $request)
    {
        $updateEntry = User::findOrFail($request->id);
        if($updateEntry->type==7){
          $updateEntry->password=$request->password;
        }else{
        if(!empty($request->password)) $updateEntry->password = Hash::make($request->password);
        }
          
        $updateEntry->save();

        if ($updateEntry) {
            return redirect()->back()->with('success', 'Password changed successfully');
        } else {
            return redirect()->back()->with('failure', 'Something happened');
        }
    }
    
     public function passwordCreate(Request $request)
    {
        $userDetail = User::where('type',7)->where('password','')->get();
        function generateUniqueAlphaNumeric($length = 10) {
            $random_string = '';
            for ($i = 0; $i < $length; $i++) {
                $number = random_int(0, 36);
                $character = base_convert($number, 10, 36);
                $random_string .= $character;
            }
            return $random_string;
        }
        foreach($userDetail as $user){
             $userData=User::findOrfail($user->id);
             $userData->password = strtoupper(generateUniqueAlphaNumeric(8));
             $userData->save();
        }
       

                return response()->json([
                    'status' => 200,
                    'message' => 'Password generated'
                   
                ]);
            

       
    }
    //distributor collection tagging
    public function collection(Request $request, $id)
    {
		$data = DistributorRange::where('distributor_id', $id)->with('users')->get();
		$collections = Collection::where('status', 1)->orderBy('position')->get();
        $distributor = User::findOrFail($id);
        $aseList = Team::where('distributor_id',$distributor->id)->orderBy('ase_id')->groupby('ase_id')->with('ase','states','areas')->get();
		
        return view('admin.user.distributor-range', compact('data', 'collections', 'id', 'distributor', 'aseList'));
    }

	public function collectionCreate(Request $request, $id)
    {
		$request->validate([
			"collection_id" => "required|integer|min:1",
			"user_id" => "required|integer|min:1",
            "distributor_id" => "required|integer|min:1",
		]);

		$check = DistributorRange::where('distributor_id', $request->distributor_id)->where('collection_id', $request->collection_id)->first();

		if($check) {
			return redirect()->back()->with('failure', 'This Range already exists to this Distributor');
		} else {
			DB::table('distributor_ranges')->insert([
                'collection_id' => $request->collection_id,
                'user_id' => $request->user_id,
                'distributor_id' => $request->distributor_id
            ]);
		}

		return redirect()->back()->with('success', 'Range Added to this Distributor');
    }

	public function collectionDelete(Request $request, $id)
    {
		$data = DistributorRange::where('id', $id)->delete();
        return redirect()->back()->with('success', 'Range Deleted for this Distributor');
    }
	
	 //areacreate for ASE
    public function areaStore(Request $request)
    {
		$request->validate([
			"area_id" => "required|integer|min:1",
			"user_id" => "required|integer|min:1",
		]);

		$check = UserArea::where('user_id', $request->user_id)->where('area_id', $request->area_id)->first();

		if($check) {
			return redirect()->back()->with('failure', 'This Area already exists to this ASE');
		} else {
			DB::table('user_areas')->insert([
                'area_id' => $request->area_id,
                'user_id' => $request->user_id
            ]);
		}

		return redirect()->back()->with('success', 'Area Added successfully');
    }
	
	//area delete for ASE
     public function areaDelete(Request $request,$id)
     {
        $data=UserArea::destroy($id);
        if ($data) {
            return redirect()->back()->with('success', 'Area Deleted successfully');
        } else {
            return redirect()->back()->with('success', 'Area Deleted successfully')->withInput($request->all());
        }
 
         
     }

 //activity list
    public function activityList(Request $request)
    {

        if (isset($request->date_from) || isset($request->date_to) || isset($request->ase) ||isset($request->zsm) || isset($request->state) || isset($request->rsm) ||isset($request->sm) ||isset($request->asm)) {
           $date_from = $request->date_from ? $request->date_from : date('Y-m-01');
            $date_to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
            $user_id = $request->ase ? $request->ase : '';
            $asm_id=$request->asm ? $request->asm : '';
            $sm_id=$request->sm ? $request->sm : '';
            $rsm_id=$request->rsm ? $request->rsm : '';
            $zsm_id=$request->zsm ? $request->zsm : '';
            $state_id=$request->state ? $request->state : '';
            $aceids=array();
            $asmids=array();
            $rsmids=array();
            $zsmids=array();
            $teams=array();
            $usersId=array();
            $row=array();
            if(!empty($zsm_id)){
                  # Query with zsm_id and get aceids
                  
                  $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->groupby('ase_id')->get()->toArray();
                }
                if(!empty($zsm_id && $state_id)){
                  # Query with zsm_id and get aceids
                  
                  $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('state_id',$state_id)->groupby('ase_id')->get()->toArray();
                }
                    if(!empty($zsm_id && $state_id && $rsm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->groupby('ase_id')->get()->toArray();
                    }
                     if(!empty($zsm_id && $state_id && $rsm_id && $sm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->groupby('ase_id')->get()->toArray();
                    }
                    if(!empty($zsm_id && $state_id && $rsm_id && $sm_id && $asm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->where('asm_id',$asm_id)->groupby('ase_id')->get()->toArray();
                    }
                if(!empty($zsm_id)){
                  # Query with zsm_id and get aceids
                  
                  $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->groupby('asm_id')->get()->toArray();
                }
                
                 if(!empty($zsm_id && $state_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('state_id',$state_id)->groupby('asm_id')->get()->toArray();
                    }
                    if(!empty($zsm_id && $state_id && $rsm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->groupby('asm_id')->get()->toArray();
                    }
                     if(!empty($zsm_id && $state_id && $rsm_id && $sm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->groupby('asm_id')->get()->toArray();
                    }
                    if(!empty($zsm_id && $state_id && $rsm_id && $sm_id && $asm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->where('asm_id',$asm_id)->groupby('asm_id')->get()->toArray();
                    }
                    $teams=array_merge($aceids,$asmids);
                    if(!empty($zsm_id)){
                  # Query with zsm_id and get aceids
                  
                    $rsmids = Team::select('rsm_id')->where('zsm_id',$zsm_id)->groupby('rsm_id')->get()->toArray();
                   }
                   if(!empty($rsm_id)){
                  # Query with zsm_id and get aceids
                  
                   $rsmids =array($rsm_id);
                }
                     if(!empty($zsm_id)){
                         $zsmids=array($zsm_id);
                     }
                     
               $row=array_merge($zsmids,$rsmids);
               $usersId=array_merge($row,$teams);
                    // dd($aceids, $asmids, $teams);
            //dd($aceids);
            $query = Activity::whereIn('user_id',$usersId);

            $query->when($date_from, function($query) use ($date_from) {
                $query->where('date', '>=', $date_from);
            });
            $query->when($date_to, function($query) use ($date_to) {
                $query->where('date', '<=', $date_to);
            });
           
            $query->when($user_id, function($query) use ($user_id) {
                    $query->where('user_id', $user_id);
            });
            
            DB::enableQueryLog();
            if ($request->zsm != 'all') {
                if (empty($request->zsm)) {
                    $date_from = $request->date_from ? date('Y-m-d', strtotime($request->date_from)) : date('Y-m-01');
                    $date_to = date('Y-m-d', strtotime(request()->input('date_to')))? date('Y-m-d', strtotime(request()->input('date_to'))) : date('Y-m-01');
                    // $data = Activity::latest('id')->paginate(25);

                    $query = Activity::query();
                    $query->when($date_from, function($query) use ($date_from) {
                        $query->where('date', '>=', $date_from);
                    });
                    $query->when($date_to, function($query) use ($date_to) {
                        $query->where('date', '<=', $date_to);
                    });
                    $data = $query->latest('id')->paginate(25);
                } else {
                    $data = $query->latest('id')->paginate(25);
                }
                // dd(DB::getQueryLog());
                // $data = $query->latest('id')->paginate(25);
            } else {
                if (!empty($request->date_from && $request->date_to)) {
                    
                    $date_from = $request->date_from ? date('Y-m-d', strtotime($request->date_from)) : date('Y-m-01');
                    $date_to = date('Y-m-d', strtotime(request()->input('date_to')))? date('Y-m-d', strtotime(request()->input('date_to'))) : date('Y-m-01');
                    // $data = Activity::latest('id')->paginate(25);

                    $query = Activity::query();
                    $query->when($date_from, function($query) use ($date_from) {
                        $query->where('date', '>=', $date_from);
                    });
                    $query->when($date_to, function($query) use ($date_to) {
                        $query->where('date', '<=', $date_to);
                    });
                    $data = $query->latest('id')->paginate(25);
                    
                } else {
                    // $data = $query->latest('id')->paginate(25);
                    $data = Activity::latest('id')->paginate(25);
                }



                
            }
            
            //dd($data);
        } else {
            $data = Activity::latest('id')->paginate(25);
        }
        $user = User::select('id','name')->where('type',6)->orWhere('type',5)->where('name', '!=', null)->orderBy('name')->get();
        $zsm=User::select('id', 'name')->where('type', 2)->orderBy('name')->get();
        return view('admin.activity.index', compact('data', 'request','user','zsm'));
    
    }

       //activity csv export
    public function activityCSV(Request $request)
    {

        /*
        if (isset($request->date_from) || isset($request->date_to) || isset($request->ase) ||isset($request->zsm) || isset($request->rsm) ||isset($request->sm) ||isset($request->asm)) {
            $date_from = $request->date_from ? $request->date_from : date('Y-m-01');
            $date_to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
            $user_id = $request->ase ? $request->ase : '';
            $asm_id=$request->asm ? $request->asm : '';
            $sm_id=$request->sm ? $request->sm : '';
            $rsm_id=$request->rsm ? $request->rsm : '';
            $zsm_id=$request->zsm ? $request->zsm : '';
            $aceids=array();
            $asmids=array();
            $teams=array();
            if(!empty($zsm_id)){
                  # Query with zsm_id and get aceids
                  
                  $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->groupby('ase_id')->get()->toArray();
                }
                    if(!empty($zsm_id && $rsm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->groupby('ase_id')->get()->toArray();
                    }
                     if(!empty($zsm_id && $rsm_id && $sm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->groupby('ase_id')->get()->toArray();
                    }
                    if(!empty($zsm_id && $rsm_id && $sm_id && $asm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->where('asm_id',$asm_id)->groupby('ase_id')->get()->toArray();
                    }
                if(!empty($zsm_id)){
                  # Query with zsm_id and get aceids
                  
                  $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->groupby('asm_id')->get()->toArray();
                }
                    if(!empty($zsm_id && $rsm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->groupby('asm_id')->get()->toArray();
                    }
                     if(!empty($zsm_id && $rsm_id && $sm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->groupby('asm_id')->get()->toArray();
                    }
                    if(!empty($zsm_id && $rsm_id && $sm_id && $asm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->where('asm_id',$asm_id)->groupby('asm_id')->get()->toArray();
                    }
                    $teams=array_merge($aceids,$asmids);
            //dd($aceids);
            $query = Activity::whereIn('user_id',$teams);

            $query->when($date_from, function($query) use ($date_from) {
                $query->where('date', '>=', $date_from);
            });
            $query->when($date_to, function($query) use ($date_to) {
                $query->where('date', '<=', $date_to);
            });
           
            $query->when($user_id, function($query) use ($user_id) {
                    $query->where('user_id', $user_id);
            });
            

            $data = $query->latest('id')->get();
            //dd($data);
        } else {
            $data = Activity::latest('id')->get();
        }
        */


       $date_from = $request->date_from ? $request->date_from : date('Y-m-01');
       $date_to = date('Y-m-d', strtotime(request()->input('date_to')))? date('Y-m-d', strtotime(request()->input('date_to'))) : '';
        if (isset($request->date_from) || isset($request->date_to) || isset($request->ase) ||isset($request->zsm) || isset($request->state)|| isset($request->rsm) ||isset($request->sm) ||isset($request->asm)) {
            $date_from = $request->date_from ? $request->date_from : date('Y-m-01');
             $date_to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
             $user_id = $request->ase ? $request->ase : '';
             $asm_id=$request->asm ? $request->asm : '';
             $sm_id=$request->sm ? $request->sm : '';
             $rsm_id=$request->rsm ? $request->rsm : '';
             $zsm_id=$request->zsm ? $request->zsm : '';
             $state_id=$request->state ? $request->state : '';
             $aceids=array();
             $asmids=array();
            $rsmids=array();
            $zsmids=array();
            $teams=array();
            $usersId=array();
            $row=array();
             if(!empty($zsm_id)){
                   # Query with zsm_id and get aceids
                   
                   $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->groupby('ase_id')->get()->toArray();
                 }
                 
                 if(!empty($zsm_id && $state_id)){
                       # Query with zsm_id and rsm_id and get aceids
                       $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('state_id',$state_id)->groupby('ase_id')->get()->toArray();
                     }
                     if(!empty($zsm_id && $state_id && $rsm_id)){
                       # Query with zsm_id and rsm_id and get aceids
                       $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->groupby('ase_id')->get()->toArray();
                     }
                      if(!empty($zsm_id && $state_id && $rsm_id && $sm_id)){
                       # Query with zsm_id and rsm_id and get aceids
                       $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->groupby('ase_id')->get()->toArray();
                     }
                     if(!empty($zsm_id && $state_id && $rsm_id && $sm_id && $asm_id)){
                       # Query with zsm_id and rsm_id and get aceids
                       $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->where('asm_id',$asm_id)->groupby('ase_id')->get()->toArray();
                     }
                 if(!empty($zsm_id)){
                   # Query with zsm_id and get aceids
                   
                   $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->groupby('asm_id')->get()->toArray();
                 }
                 if(!empty($zsm_id && $state_id )){
                       # Query with zsm_id and rsm_id and get aceids
                       $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('state_id',$state_id)->groupby('asm_id')->get()->toArray();
                     }
                     if(!empty($zsm_id && $state_id && $rsm_id)){
                       # Query with zsm_id and rsm_id and get aceids
                       $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->groupby('asm_id')->get()->toArray();
                     }
                      if(!empty($zsm_id && $state_id && $rsm_id && $sm_id)){
                       # Query with zsm_id and rsm_id and get aceids
                       $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->groupby('asm_id')->get()->toArray();
                     }
                     if(!empty($zsm_id && $state_id && $rsm_id && $sm_id && $asm_id)){
                       # Query with zsm_id and rsm_id and get aceids
                       $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->where('asm_id',$asm_id)->groupby('asm_id')->get()->toArray();
                     }
                     $teams=array_merge($aceids,$asmids);
                     if(!empty($zsm_id)){
                  # Query with zsm_id and get aceids
                  
                    $rsmids = Team::select('rsm_id')->where('zsm_id',$zsm_id)->groupby('rsm_id')->get()->toArray();
                   }
                   if(!empty($rsm_id)){
                  # Query with zsm_id and get aceids
                  
                   $rsmids =array($rsm_id);
                }
                     if(!empty($zsm_id)){
                         $zsmids=array($zsm_id);
                     }
                     
               $row=array_merge($zsmids,$rsmids);
               $usersId=array_merge($row,$teams);
                     
             //dd($aceids);
             $query = Activity::whereIn('user_id',$usersId);
 
             $query->when($date_from, function($query) use ($date_from) {
                 $query->where('date', '>=', $date_from);
             });
             $query->when($date_to, function($query) use ($date_to) {
                 $query->where('date', '<=', $date_to);
             });
            
             $query->when($user_id, function($query) use ($user_id) {
                     $query->where('user_id', $user_id);
             });
             
 
             if ($request->zsm != 'all') {
                if (empty($request->zsm)) {
                    $date_from = $request->date_from ? date('Y-m-d', strtotime($request->date_from)) : date('Y-m-01');
                    $date_to = date('Y-m-d', strtotime(request()->input('date_to')))? date('Y-m-d', strtotime(request()->input('date_to'))) : date('Y-m-01');
                    // $data = Activity::latest('id')->paginate(25);

                    $query = Activity::query();
                    $query->when($date_from, function($query) use ($date_from) {
                        $query->where('date', '>=', $date_from);
                    });
                    $query->when($date_to, function($query) use ($date_to) {
                        $query->where('date', '<=', $date_to);
                    });
                    $data = $query->latest('id')->cursor();
                    $users = $data->all();
                } else {
                    $data = $query->latest('id')->cursor();
                    $users = $data->all();
                }
                // dd(DB::getQueryLog());
                // $data = $query->latest('id')->get();
            } else {
                if (!empty($request->date_from && $request->date_to)) {
                    
                    $date_from = $request->date_from ? date('Y-m-d', strtotime($request->date_from)) : date('Y-m-01');
                    $date_to = date('Y-m-d', strtotime(request()->input('date_to')))? date('Y-m-d', strtotime(request()->input('date_to'))) : date('Y-m-01');
                    // $data = Activity::latest('id')->get();

                    $query = Activity::query();
                    $query->when($date_from, function($query) use ($date_from) {
                        $query->where('date', '>=', $date_from);
                    });
                    $query->when($date_to, function($query) use ($date_to) {
                        $query->where('date', '<=', $date_to);
                    });
                    $data = $query->latest('id')->cursor();
                    $users = $data->all();
                    
                } else {
                    // $data = $query->latest('id')->get();
                    $data = Activity::latest('id')->cursor();
                    $users = $data->all();
                }



                
            }
             
             //dd($data);
         } else {
             $data = Activity::latest('id')->cursor();
             $users = $data->all();
         }

        
        
        $filename = "lux-user-activity-".$date_from.' to '.$date_to.".csv";
            $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];


        return Response::stream(function () use ($users, $headers) {
        $file = fopen('php://output', 'w');
        fputcsv($file, ['SR','NSM', 'ZSM','RSM','SM','ASM','Employee','Employee Id','Employee Status','Employee Designation','Employee Date of Joining','Employee HQ','Employee Contact No', 'Type', 'Date','Time','Comment', 'Location', 'Date','Time']);
         $count = 1;
        foreach ($users as $row) {
               if(!empty($row->users)){
                $date = date('j F, Y', strtotime($row['created_at']));
                $time = date('h:i A', strtotime($row['created_at']));
                
                $findTeamDetails= findTeamDetails($row->users->id, $row->users->type);
            fputcsv($file, [
                    $count++,
                    $findTeamDetails[0]['nsm'] ?? '',
                    $findTeamDetails[0]['zsm']?? '',
                    $findTeamDetails[0]['rsm']?? '',
                    $findTeamDetails[0]['sm']?? '',
                    $findTeamDetails[0]['asm']?? '',
                    $row->users ? $row->users->name : '',
                    $row->users->employee_id ?? '',
                    ($row->users->status == 1)  ? 'Active' : 'Inactive' ,
                    $row->users->designation?? '',
                    $row->users->date_of_joining?? '',
                    $row->users->headquater?? '',
                    $row->users->mobile,
                    $row['type'],
                    $row['date'],
                    $row['time'],
                    $row['comment'],
                    $row['location'],
                    $date,
                    $time]);
               }
        }

        fclose($file);
    }, 200, $headers);

        // if (count($data) > 0) {
        //     $delimiter = ",";
        //     $filename = "lux-user-activity-".$date_from.' to '.$date_to.".csv";

        //     // Create a file pointer
        //     $f = fopen('php://memory', 'w');

        //     // Set column headers
        //     $fields = array('SR','NSM', 'ZSM','RSM','SM','ASM','Employee','Employee Id','Employee Status','Employee Designation','Employee Date of Joining','Employee HQ','Employee Contact No', 'Type', 'Date','Time','Comment', 'Location', 'Date','Time');
        //     fputcsv($f, $fields, $delimiter);

        //     $count = 1;

        //     foreach($data as $row) {
        //          if(!empty($row->users)){
        //         $date = date('j F, Y', strtotime($row['created_at']));
        //          $time = date('h:i A', strtotime($row['created_at']));
                
        //         $findTeamDetails= findTeamDetails($row->users->id, $row->users->type);
                
        //         $lineData = array(
        //             $count,
        //             $findTeamDetails[0]['nsm'] ?? '',
        //             $findTeamDetails[0]['zsm']?? '',
        //             $findTeamDetails[0]['rsm']?? '',
        //             $findTeamDetails[0]['sm']?? '',
        //             $findTeamDetails[0]['asm']?? '',
        //             $row->users ? $row->users->name : '',
        //             $row->users->employee_id ?? '',
        //             ($row->users->status == 1)  ? 'Active' : 'Inactive' ,
        //             $row->users->designation?? '',
        //             $row->users->date_of_joining?? '',
        //             $row->users->headquater?? '',
        //             $row->users->mobile,
        //             $row['type'],
        //             $row['date'],
        //             $row['time'],
        //             $row['comment'],
        //             $row['location'],
        //             $date,
        //             $time
        //         );

        //         fputcsv($f, $lineData, $delimiter);

        //         $count++;
        //          }
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
    
    
    public function dailyactivityList(Request $request)
    {
        if (isset($request->date_from) || isset($request->ase)) {
            $date_from = $request->date_from ? $request->date_from : '';
           
            $user_id = $request->ase ? $request->ase : '';
            
            $query = Activity::query();

            $query->when($date_from, function($query) use ($date_from) {
                $query->where('date', '=', $date_from);
            });
           
           
           
            $query->when($user_id, function($query) use ($user_id) {
                    $query->where('user_id', $user_id);
            });
            

            $data = $query->latest('created_at','asc')->get();
          
        } else {
            $data = '';
        }
        $user = User::select('id','name')->whereNOTIN('type',[4,7,8])->where('name', '!=', null)->orderBy('name')->get();
        
        return view('admin.activity.daily-activity', compact('data', 'request','user'));
    
    }

    //notification list
    public function notificationList(Request $request)
    {
        $date_from = $request->from ? $request->from : '';
        $date_to = $request->to ? $request->to : '';
        $keyword = $request->keyword ? $request->keyword : '';

        $query = Notification::query();

        $query->when($date_from, function($query) use ($date_from) {
            $query->where('created_at', '>=', $date_from);
        });
        $query->when($date_to, function($query) use ($date_to) {
            $query->where('created_at', '<=', date('Y-m-d', strtotime($date_to.'+1 day')));
        });
        $query->when($keyword, function($query) use ($keyword) {
            $query->where('title', 'like', '%'.$keyword.'%')
            ->orWhere('body', 'like', '%'.$keyword.'%');
        });

        $data = $query->where('receiver_id','admin')->latest('id')->with('senderDetails')->paginate(25);

        return view('admin.notification.index', compact('data','request'));

    }

     //state wise area
     public function state(Request $request, $state)
     {
         $stateName=State::where('name',$state)->first();
         $region = Area::where('state_id',$stateName->id)->get();
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


    //attendance list
    public function attendanceList(Request $request) {
        if (isset($request->date_from) || isset($request->zsm)|| isset($request->state) || isset($request->rsm) || isset($request->sm) || isset($request->asm)||isset($request->status_id)) {
            // dd($request->all());

            $date_from = $request->date_from ? $request->date_from : date('Y-m-d');
            $sm_id=$request->sm ? $request->sm : '';
            $asm_id=$request->asm ? $request->asm : '';
            $rsm_id=$request->rsm ? $request->rsm : '';
            $zsm_id=$request->zsm ? $request->zsm : '';
            $state_id=$request->state ? $request->state : '';
            $statusData = $request->status_id ;
                           /* $aceids=array();
                            $asmids=array();
                            $teams=array();
                
                            // if (empty($zsm_id && $rsm_id && $sm_id && $asm_id) && !empty($date_from)) {
                            //     $data = User::where('fname','!=','VACCANT')->paginate(50);
                            //     dd($data);
                            // }
                
                            // if (empty($zsm_id && $rsm_id && $sm_id && $asm_id) && !empty($date_from)) {
                            //     dd('here');
                            // }
                
                            if(!empty($zsm_id)){
                                # Query with zsm_id and get aceids
                                $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->groupby('ase_id')->get()->toArray();
                            }
                            if(!empty($zsm_id && $state_id)){
                                # Query with zsm_id and rsm_id and get aceids
                                $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('state_id',$state_id)->groupby('ase_id')->get()->toArray();
                            }
                            if(!empty($zsm_id && $state_id && $rsm_id)){
                                # Query with zsm_id and rsm_id and get aceids
                                $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->groupby('ase_id')->get()->toArray();
                            }
                            if(!empty($zsm_id && $state_id && $rsm_id && $sm_id)){
                                # Query with zsm_id and rsm_id and get aceids
                                $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->groupby('ase_id')->get()->toArray();
                            }
                            if(!empty($zsm_id && $state_id && $rsm_id && $sm_id && $asm_id)){
                                # Query with zsm_id and rsm_id and get aceids
                                $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->where('asm_id',$asm_id)->groupby('ase_id')->get()->toArray();
                            }
                            if(!empty($zsm_id)){
                                # Query with zsm_id and get aceids
                                $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->groupby('asm_id')->get()->toArray();
                            }
                            if(!empty($zsm_id && $state_id)){
                                # Query with zsm_id and rsm_id and get aceids
                                $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('state_id',$state_id)->groupby('asm_id')->get()->toArray();
                            }
                            if(!empty($zsm_id && $state_id && $rsm_id)){
                                # Query with zsm_id and rsm_id and get aceids
                                $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->groupby('asm_id')->get()->toArray();
                            }
                            if(!empty($zsm_id && $state_id && $rsm_id && $sm_id)){
                                # Query with zsm_id and rsm_id and get aceids
                                $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->groupby('asm_id')->get()->toArray();
                            }
                            if(!empty($zsm_id && $state_id && $rsm_id && $sm_id && $asm_id)){
                                # Query with zsm_id and rsm_id and get aceids
                                $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->where('asm_id',$asm_id)->groupby('asm_id')->get()->toArray();
                            }
                            // if (empty($zsm_id && $state_id && $rsm_id && $sm_id && $asm_id) && !empty($date_from)) {
                            //     $data = User::where('fname','!=','VACCANT')->paginate(50); 
                            // }
                            //else {
                              //  $data = User::where('fname','!=','VACCANT')->paginate(50); 
                            //}
                
                            $teams=array_merge($aceids,$asmids);
                
                            if ($request->zsm != "all") {
                                if (empty($request->zsm)) {
                                    $data = User::where('fname','!=','VACCANT')->whereNotIn('type', [1,4,7])->paginate(50); 
                                } else {
                                    $data = User::whereIn('id', $teams)->where('fname','!=','VACCANT')->paginate(50);
                                }
                            } else {
                                $data = User::where('fname','!=','VACCANT')->whereNotIn('type', [1,4,7])->paginate(50); 
                            }
                            if(!empty($statusData)){
                				if($statusData=='active'){
                					$data= User::where('status','=', 1)->whereNotIn('type', [1,4,7])->paginate(50);
                				}
                				else{
                					$data= User::where('status','=', 0)->whereNotIn('type', [1,4,7])->paginate(50);
                				}
                			}
                        } else {
                            // $data ='';
                            $data = User::whereNotIn('type', [1,4,7])->where('fname','!=','VACCANT')->paginate(50); 
                        }*/
        
            $aceids=array();
            $asmids=array();
            $rsmids=array();
            $zsmids=array();
            $teams=array();
            $usersId=array();
            $row=array();
            if(!empty($zsm_id)){
                  # Query with zsm_id and get aceids
                  
                  $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->groupby('ase_id')->get()->toArray();
                   }
                   if(!empty($zsm_id && $state_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('state_id',$state_id)->groupby('ase_id')->get()->toArray();
                    }
                    if(!empty($zsm_id && $state_id && $rsm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->groupby('ase_id')->get()->toArray();
                    }
                     if(!empty($zsm_id && $state_id && $rsm_id && $sm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->groupby('ase_id')->get()->toArray();
                    }
                    if(!empty($zsm_id && $state_id && $rsm_id && $sm_id && $asm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->where('asm_id',$asm_id)->groupby('ase_id')->get()->toArray();
                    }
                   
                if(!empty($zsm_id)){
                  # Query with zsm_id and get aceids
                  
                  $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->groupby('asm_id')->get()->toArray();
                }
               
                if(!empty($zsm_id && $state_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('state_id',$state_id)->groupby('asm_id')->get()->toArray();
                    }
                    if(!empty($zsm_id && $state_id && $rsm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->groupby('asm_id')->get()->toArray();
                    }
                     if(!empty($zsm_id && $state_id && $rsm_id && $sm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->groupby('asm_id')->get()->toArray();
                    }
                    if(!empty($zsm_id && $state_id  && $state_id && $rsm_id && $sm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->where('asm_id',$asm_id)->groupby('asm_id')->get()->toArray();
                    }
                    if(!empty($asm_id)){
                        $asmids =array($asm_id);
                    }
                    $teams=array_merge($aceids,$asmids);
                   
                     if(!empty($zsm_id)){
                  # Query with zsm_id and get aceids
                  
                    $rsmids = Team::select('rsm_id')->where('zsm_id',$zsm_id)->groupby('rsm_id')->get()->toArray();
                   }
                   if(!empty($rsm_id)){
                  # Query with zsm_id and get aceids
                  
                   $rsmids =array($rsm_id);
                }
                     if(!empty($zsm_id)){
                         $zsmids=array($zsm_id);
                     }
                     
               $row=array_merge($zsmids,$rsmids);
               $usersId=array_merge($row,$teams);

              //dd($usersId);
                
            //     if(empty($usersId) && !empty($statusData)){
        				// if($statusData=='active'){
        				// 	$data= User::where('status','=', 1)->whereNotIn('type', [1,4,7])->paginate(50);
        				// }
        				// else{
        				// 	$data= User::where('status','=', 0)->whereNotIn('type', [1,4,7])->paginate(50);
        				// }
            //     }
                // when all zsm selected
                if(!empty($zsm_id)){
                    if ($usersId[0] == "all" ) {
                        if(!empty($statusData)){
                            if($statusData=='active'){
            					 $data = User::select('users.*')->where('users.fname','!=','VACCANT')->whereNotIn('users.type',[1,4,7])->where('users.status','=', 1)->latest('users.id')->paginate(25);
            				}
            				else{
            					 $data = User::select('users.*')->where('users.fname','!=','VACCANT')->whereNotIn('users.type',[1,4,7])->where('users.status','=', 0)->latest('users.id')->paginate(25);
            				}
                        }else{
                            $data = User::select('users.*')->where('users.fname','!=','VACCANT')->whereNotIn('users.type',[1,4,7])->latest('users.id')->paginate(25);
                        }
                       // $data = User::select('users.*')->where('users.fname','!=','VACCANT')->whereNotIn('users.type',[1,4,7])->latest('users.id')->paginate(25);
                    }
                    
                    // when single zsm passed
                    else {
                        if(!empty($statusData)){
                            if($statusData=='active'){
            					 $data = User::select('users.*')->whereIn('users.id',$usersId)->where('users.fname','!=','VACCANT')->whereNotIn('users.type',[1,4,7])->where('users.status','=', 1)->latest('users.id')->paginate(25);
            				}
            				else{
            					 $data = User::select('users.*')->whereIn('users.id',$usersId)->where('users.fname','!=','VACCANT')->whereNotIn('users.type',[1,4,7])->where('users.status','=', 0)->latest('users.id')->paginate(25);
            				}
                        }else{
                            $data = User::select('users.*')->whereIn('users.id',$usersId)->where('users.fname','!=','VACCANT')->whereNotIn('users.type',[1,4,7])->latest('users.id')->paginate(25);
                        }
                       
                    }
                }else{
                    if(!empty($statusData)){
                            if($statusData=='active'){
            					 $data = User::select('users.*')->where('users.fname','!=','VACCANT')->whereNotIn('users.type',[1,4,7])->where('users.status','=', 1)->latest('users.id')->paginate(25);
            				}
            				else{
            					 $data = User::select('users.*')->where('users.fname','!=','VACCANT')->whereNotIn('users.type',[1,4,7])->latest('users.id')->where('users.status','=', 0)->paginate(25);
            				}
                    }else{
                            $data = User::select('users.*')->where('users.fname','!=','VACCANT')->whereNotIn('users.type',[1,4,7])->latest('users.id')->paginate(25);
                    }
                    //$data = User::select('users.*')->where('users.fname','!=','VACCANT')->whereNotIn('users.type',[1,4,7])->latest('users.id')->paginate(25);
                }
               

            //  $data = User::select('users.*')->whereIn('id',$usersId)->where('fname','!=','VACCANT')->latest('users.id')->paginate(15);


                //$data = $query->latest('users.id')->paginate(25);
        }else{
           // $data=User::whereNotIn('users.type', [1,4,7])->latest('users.id')->paginate(25);
            $data = User::select('users.*')->where('users.fname','!=','VACCANT')->whereNotIn('users.type',[1,4,7])->latest('users.id')->paginate(25);
        //    $data='';
        }

        $zsmDetails=User::select('id', 'name')->where('type', 2)->orderBy('name')->get();
        $date_from = !empty($request->date_from)?$request->date_from:date('Y-m-d');
        return view('admin.attendance.daily-summery', compact('data', 'request', 'zsmDetails', 'date_from'));
    }





    //attendance csv export
    public function attendanceListCSV(Request $request)
    {
        /*
        if (isset($request->date_from) || isset($request->date_to) || isset($request->keyword)||isset($request->ase) ||isset($request->zsm) || isset($request->rsm) ||isset($request->sm) ||isset($request->asm)) {
            $date_from = $request->date_from ? $request->date_from : '';
            $date_to = $request->date_to ? $request->date_to : '';
            $keyword = $request->keyword ? $request->keyword : '';
            $user_id = $request->ase ? $request->ase : '';
            $asm=$request->asm ? $request->asm : '';
            $rsm=$request->rsm ? $request->rsm : '';
            $zsm=$request->zsm ? $request->zsm : '';
            $query = UserAttendance::select('user_attendances.id','user_attendances.user_id','user_attendances.entry_date','user_attendances.type','user_attendances.start_time','user_attendances.end_time','user_attendances.other_activities_id')->join('users', 'user_attendances.user_id', 'users.id');

            $query->when($date_from, function($query) use ($date_from) {
                $query->where('user_attendances.entry_date', '>=', $date_from);
            });
            $query->when($date_to, function($query) use ($date_to) {
                $query->where('user_attendances.entry_date', '<=', $date_to);
            });
            
            $query->when($keyword, function($query) use ($keyword) {
                $query->where('users.name', 'like', '%'.$keyword.'%');
            });

            if(!empty($request->ase))
            {
                $query->when($user_id, function($query) use ($user_id) {
                    $query->where('user_attendances.user_id', $user_id);
                });
            }elseif(!empty($request->asm)){
                $query->when($asm, function($query) use ($asm) {
                    $query->where('user_attendances.user_id', $asm);
                });
            }elseif(!empty($request->rsm)){
                $query->when($rsm, function($query) use ($rsm) {
                    $query->where('user_attendances.user_id', $rsm);
                });
            }else{
                $query->when($zsm, function($query) use ($zsm) {
                    $query->where('user_attendances.user_id', $zsm);
                });
            }

            $data = $query->whereNotIn('users.type', [1,4,7])->orderby('user_attendances.entry_date','desc')->groupby('user_attendances.entry_date','user_attendances.user_id')->paginate(25);
            //dd($data);
        } else {
            $data = UserAttendance::orderby('entry_date','desc')->groupby('entry_date','user_id')->paginate(25);
        }
        */
        $date_from = $request->date_from ? $request->date_from : date('Y-m-d');
         if (isset($request->date_from) || isset($request->zsm)|| isset($request->state) || isset($request->rsm) || isset($request->sm) || isset($request->asm)||isset($request->status_id)) {
            // dd($request->all());

            $date_from = $request->date_from ? $request->date_from : date('Y-m-d');
            $sm_id=$request->sm ? $request->sm : '';
            $asm_id=$request->asm ? $request->asm : '';
            $rsm_id=$request->rsm ? $request->rsm : '';
            $zsm_id=$request->zsm ? $request->zsm : '';
            $state_id=$request->state ? $request->state : '';
            $statusData = $request->status_id ;
                           /* $aceids=array();
                            $asmids=array();
                            $teams=array();
                
                            // if (empty($zsm_id && $rsm_id && $sm_id && $asm_id) && !empty($date_from)) {
                            //     $data = User::where('fname','!=','VACCANT')->paginate(50);
                            //     dd($data);
                            // }
                
                            // if (empty($zsm_id && $rsm_id && $sm_id && $asm_id) && !empty($date_from)) {
                            //     dd('here');
                            // }
                
                            if(!empty($zsm_id)){
                                # Query with zsm_id and get aceids
                                $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->groupby('ase_id')->get()->toArray();
                            }
                            if(!empty($zsm_id && $state_id)){
                                # Query with zsm_id and rsm_id and get aceids
                                $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('state_id',$state_id)->groupby('ase_id')->get()->toArray();
                            }
                            if(!empty($zsm_id && $state_id && $rsm_id)){
                                # Query with zsm_id and rsm_id and get aceids
                                $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->groupby('ase_id')->get()->toArray();
                            }
                            if(!empty($zsm_id && $state_id && $rsm_id && $sm_id)){
                                # Query with zsm_id and rsm_id and get aceids
                                $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->groupby('ase_id')->get()->toArray();
                            }
                            if(!empty($zsm_id && $state_id && $rsm_id && $sm_id && $asm_id)){
                                # Query with zsm_id and rsm_id and get aceids
                                $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->where('asm_id',$asm_id)->groupby('ase_id')->get()->toArray();
                            }
                            if(!empty($zsm_id)){
                                # Query with zsm_id and get aceids
                                $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->groupby('asm_id')->get()->toArray();
                            }
                            if(!empty($zsm_id && $state_id)){
                                # Query with zsm_id and rsm_id and get aceids
                                $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('state_id',$state_id)->groupby('asm_id')->get()->toArray();
                            }
                            if(!empty($zsm_id && $state_id && $rsm_id)){
                                # Query with zsm_id and rsm_id and get aceids
                                $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->groupby('asm_id')->get()->toArray();
                            }
                            if(!empty($zsm_id && $state_id && $rsm_id && $sm_id)){
                                # Query with zsm_id and rsm_id and get aceids
                                $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->groupby('asm_id')->get()->toArray();
                            }
                            if(!empty($zsm_id && $state_id && $rsm_id && $sm_id && $asm_id)){
                                # Query with zsm_id and rsm_id and get aceids
                                $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->where('asm_id',$asm_id)->groupby('asm_id')->get()->toArray();
                            }
                            // if (empty($zsm_id && $state_id && $rsm_id && $sm_id && $asm_id) && !empty($date_from)) {
                            //     $data = User::where('fname','!=','VACCANT')->paginate(50); 
                            // }
                            //else {
                              //  $data = User::where('fname','!=','VACCANT')->paginate(50); 
                            //}
                
                            $teams=array_merge($aceids,$asmids);
                
                            if ($request->zsm != "all") {
                                if (empty($request->zsm)) {
                                    $data = User::where('fname','!=','VACCANT')->whereNotIn('type', [1,4,7])->paginate(50); 
                                } else {
                                    $data = User::whereIn('id', $teams)->where('fname','!=','VACCANT')->paginate(50);
                                }
                            } else {
                                $data = User::where('fname','!=','VACCANT')->whereNotIn('type', [1,4,7])->paginate(50); 
                            }
                            if(!empty($statusData)){
                				if($statusData=='active'){
                					$data= User::where('status','=', 1)->whereNotIn('type', [1,4,7])->paginate(50);
                				}
                				else{
                					$data= User::where('status','=', 0)->whereNotIn('type', [1,4,7])->paginate(50);
                				}
                			}
                        } else {
                            // $data ='';
                            $data = User::whereNotIn('type', [1,4,7])->where('fname','!=','VACCANT')->paginate(50); 
                        }*/
        
            $aceids=array();
            $asmids=array();
            $rsmids=array();
            $zsmids=array();
            $teams=array();
            $usersId=array();
            $row=array();
            if(!empty($zsm_id)){
                  # Query with zsm_id and get aceids
                  
                  $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->groupby('ase_id')->get()->toArray();
                   }
                   if(!empty($zsm_id && $state_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('state_id',$state_id)->groupby('ase_id')->get()->toArray();
                    }
                    if(!empty($zsm_id && $state_id && $rsm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->groupby('ase_id')->get()->toArray();
                    }
                     if(!empty($zsm_id && $state_id && $rsm_id && $sm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->groupby('ase_id')->get()->toArray();
                    }
                    if(!empty($zsm_id && $state_id && $rsm_id && $sm_id && $asm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->where('asm_id',$asm_id)->groupby('ase_id')->get()->toArray();
                    }
                   
                if(!empty($zsm_id)){
                  # Query with zsm_id and get aceids
                  
                  $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->groupby('asm_id')->get()->toArray();
                }
               
                if(!empty($zsm_id && $state_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('state_id',$state_id)->groupby('asm_id')->get()->toArray();
                    }
                    if(!empty($zsm_id && $state_id && $rsm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->groupby('asm_id')->get()->toArray();
                    }
                     if(!empty($zsm_id && $state_id && $rsm_id && $sm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->groupby('asm_id')->get()->toArray();
                    }
                    if(!empty($zsm_id && $state_id  && $state_id && $rsm_id && $sm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->where('asm_id',$asm_id)->groupby('asm_id')->get()->toArray();
                    }
                    if(!empty($asm_id)){
                        $asmids =array($asm_id);
                    }
                    $teams=array_merge($aceids,$asmids);
                   
                     if(!empty($zsm_id)){
                  # Query with zsm_id and get aceids
                  
                    $rsmids = Team::select('rsm_id')->where('zsm_id',$zsm_id)->groupby('rsm_id')->get()->toArray();
                   }
                   if(!empty($rsm_id)){
                  # Query with zsm_id and get aceids
                  
                   $rsmids =array($rsm_id);
                }
                     if(!empty($zsm_id)){
                         $zsmids=array($zsm_id);
                     }
                     
               $row=array_merge($zsmids,$rsmids);
               $usersId=array_merge($row,$teams);

              //dd($usersId);

                // when all zsm selected
                if(!empty($zsm_id)){
                    if ($usersId[0] == "all" ) {
                        if(!empty($statusData)){
                            if($statusData=='active'){
            					 $data = User::select('users.*')->where('users.fname','!=','VACCANT')->whereNotIn('users.type',[1,4,7])->where('users.status','=', 1)->latest('users.id')->paginate(25);
            				}
            				else{
            					 $data = User::select('users.*')->where('users.fname','!=','VACCANT')->whereNotIn('users.type',[1,4,7])->where('users.status','=', 0)->latest('users.id')->paginate(25);
            				}
                        }else{
                            $data = User::select('users.*')->where('users.fname','!=','VACCANT')->whereNotIn('users.type',[1,4,7])->latest('users.id')->paginate(25);
                        }
                       // $data = User::select('users.*')->where('users.fname','!=','VACCANT')->whereNotIn('users.type',[1,4,7])->latest('users.id')->paginate(25);
                    }
                    
                    // when single zsm passed
                    else {
                        if(!empty($statusData)){
                            if($statusData=='active'){
            					 $data = User::select('users.*')->whereIn('users.id',$usersId)->where('users.fname','!=','VACCANT')->whereNotIn('users.type',[1,4,7])->where('users.status','=', 1)->latest('users.id')->paginate(25);
            				}
            				else{
            					 $data = User::select('users.*')->whereIn('users.id',$usersId)->where('users.fname','!=','VACCANT')->whereNotIn('users.type',[1,4,7])->where('users.status','=', 0)->latest('users.id')->paginate(25);
            				}
                        }else{
                            $data = User::select('users.*')->whereIn('users.id',$usersId)->where('users.fname','!=','VACCANT')->whereNotIn('users.type',[1,4,7])->latest('users.id')->paginate(25);
                        }
                       
                    }
                }else{
                    if(!empty($statusData)){
                            if($statusData=='active'){
            					 $data = User::select('users.*')->where('users.fname','!=','VACCANT')->whereNotIn('users.type',[1,4,7])->where('users.status','=', 1)->latest('users.id')->paginate(25);
            				}
            				else{
            					 $data = User::select('users.*')->where('users.fname','!=','VACCANT')->whereNotIn('users.type',[1,4,7])->latest('users.id')->where('users.status','=', 0)->paginate(25);
            				}
                    }else{
                            $data = User::select('users.*')->where('users.fname','!=','VACCANT')->whereNotIn('users.type',[1,4,7])->latest('users.id')->paginate(25);
                    }
                    //$data = User::select('users.*')->where('users.fname','!=','VACCANT')->whereNotIn('users.type',[1,4,7])->latest('users.id')->paginate(25);
                }
               

            //  $data = User::select('users.*')->whereIn('id',$usersId)->where('fname','!=','VACCANT')->latest('users.id')->paginate(15);


                //$data = $query->latest('users.id')->paginate(25);
        }else{
           // $data=User::whereNotIn('users.type', [1,4,7])->latest('users.id')->paginate(25);
            $data = User::select('users.*')->where('users.fname','!=','VACCANT')->whereNotIn('users.type',[1,4,7])->latest('users.id')->paginate(25);
        //    $data='';
        }


        if (count($data) > 0) {
            $delimiter = ",";
            $filename = "luxcozi-daily-summary-".$date_from.".csv";

            // Create a file pointer
            $f = fopen('php://memory', 'w');

            // Set column headers
            $fields = array('SR', 
            'FieldUser Name', 
            'FieldUser Designation',
             'FieldUser Status',
            'Type',
            'Login',
            'First Call',
            'Last Active',
            'SC',
            'TC',
            'PC',
            'PC%',
            'TO',
            'Total Ord Qty',
            'Selected Beat',
            );
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($data as $index => $item) {
                $daysCount=productivityCount($item->id,$date_from);
                       $type= DB::table('activities')->where('user_id',$item->id)->whereDate('date', now()->toDateString())->orderby('created_at','asc')->first();
                       
                       
                       $productivityCount_pc_p=0;

                                                                                             if (!empty($daysCount)) { 
                              if($daysCount['pc']!=0 && $daysCount['tc']!=0){
                                 $productivityCount_pc_p= number_format((float)($daysCount['pc']/$daysCount['tc'])*100);
                                           }else{
                                           $productivityCount_pc_p=0;
                                           }}else{
                                           $productivityCount_pc_p=0;
                                           }





                    if(!empty($type)){
                        if($type->type=='distributor-visit' || $type->type=='distributor-visit-start') {
                            $so = 'Distributor Visit';
                        }
                        elseif($type->type=='leave') {
                            $so = 'Leave';
                        }
                        elseif($type->type=='meeting'){$so='Meeting';}
                        elseif($type->type=='Visit Started' || $type->type=='Visit Ended' || $type->type=='Store Added' || $type->type=='No Order Placed' || $type->type=='Order Upload' || $type->type=='distributor-visit-end' || $type->type=='distributor-visit-start' || $type->type=='Order On Call'){$so='Retailing';}
                        elseif($type->type=='joint-work'){$so='Joint Work';}
                        else{$so='';}
                    }else{
                        $so='';
                    }
                    if ($item->orderDetails) {

                    
                        $ddate = date('Y-m-d', strtotime($date_from));
                    $qrdQty = DB::select("SELECT count(op.id) AS total, sum(op.qty) AS qty FROM `orders` AS o inner join order_products AS op on o.id = op.order_id where o.user_id = '$item->id' AND DATE(o.created_at) = '$ddate'");
                    $so2 = number_format($qrdQty[0]->qty);
                }else{$so2 =0;}

                // $csvSC = $daysCount['sc'] ?? '';


                // updated sc count
                $userId = $item->id ? $item->id : 'all';
                $dateFrom = date('Y-m-d', strtotime($date_from));
                $dateTo = date('Y-m-d', strtotime($date_from));
                $csvSC = updatedSCCount($userId, $dateFrom, $dateTo);

                $lineData = array(
                    $count,
                    $item->name ?? '',
                    $item->designation ?? '',
                    ($item->status == 1) ? 'Active' : 'Inactive',
                    $so,
                    $daysCount['login'] ?? '',
                    $daysCount['firstcall'] ?? '',
                    $daysCount['lastactive'] ?? '',
                    $csvSC,
                    $daysCount['tc'] ?? '',
                    $daysCount['pc'] ?? '',
                    $productivityCount_pc_p  ?? '',
                    $daysCount['to'] ?? '',
                    $so2,
                    $daysCount['beat'] ?? '',
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




            /*
            $delimiter = ",";
            $filename = "lux-user-attendance-".date('Y-m-d').".csv";

            // Create a file pointer
            $f = fopen('php://memory', 'w');

            // Set column headers
            $fields = array('SR', 'NSM', 'ZSM','RSM','SM','ASM','Employee','Employee Id','Employee Status','Employee Designation','Employee Date of Joining','Employee HQ','Employee Contact No', 'TYPE','NOTE','TIME-IN', 'TIME-OUT', 'TOTAL HOURS');
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($data as $row) {
                $findTeamDetails= findTeamDetails($row->users->id, $row->users->type);
                $hours=\Carbon\Carbon::parse($row->start_time)->diffInHours($row->end_time);
                if($row['users']['type']==6){
                    $type='ASE';
                }
                if($row->type=='leave'){
                     $leave ='leave';
                }elseif($row->type=='distributor-visit'){
                     $leave ='Distributor Visit'; 
                   
                }elseif($row->type=='meeting'){
                    $leave = 'Meeting';
                    
                    
                }else{
                    $leave ='Present';
                }
                if($row->type!='leave'){
                    $startTime=$row->start_time;
                    $endTime=$row->end_time ;
                } 
                $lineData = array(
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
                    $row->users->date_of_joining?? '',
                    $row->users->headquater?? '',
                    $row->users->mobile,
                    
                    $leave,
                    $row->otheractivity->reason ?? '',
                    $startTime,
                    $endTime,
                    $hours
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
            */
        }
    }
	
	
	//zsm wise rsm list
    public function zsmwiseRsm(Request $request,$id)
    {
        
        if($id=='all'){
            $data=Team::select('rsm_id')->with('rsm:id,name')->groupby('rsm_id')->get();
        }else{
           $data=Team::where('zsm_id',$id)->with('rsm:id,name')->groupby('rsm_id')->get();
        }
       // dd($data);
       if (count($data)==0) {
                return response()->json(['error'=>true, 'resp'=>'No data found']);
       } else {
                return response()->json(['error'=>false, 'resp'=>'Rsm List','data'=>$data]);
       } 
        
    }
    //zsm wise state
    public function zsmwiseState(Request $request,$id)
    {
        
        if($id=='all'){
            $data=Team::select('state_id')->with('states:id,name')->groupby('state_id')->get();
        }else{
           $data=Team::where('zsm_id',$id)->with('states:id,name')->groupby('state_id')->get();
        }
       // dd($data);
       if (count($data)==0) {
                return response()->json(['error'=>true, 'resp'=>'No data found']);
       } else {
                return response()->json(['error'=>false, 'resp'=>'State List','data'=>$data]);
       } 
        
    }
    
     //state wise rsm
    public function statewiseRSM(Request $request,$id)
    {
        
        if($id=='stateall'){
            $data=Team::select('rsm_id')->with('rsm:id,name')->groupby('rsm_id')->get();
        }else{
           $data=Team::where('state_id',$id)->with('rsm:id,name')->groupby('rsm_id')->get();
        }
       // dd($data);
       if (count($data)==0) {
                return response()->json(['error'=>true, 'resp'=>'No data found']);
       } else {
                return response()->json(['error'=>false, 'resp'=>'RSM List','data'=>$data]);
       } 
        
    }
    //rsm wise sm list
    public function rsmwiseSm(Request $request,$id)
    {
       if($id=='rsmall'){
            $data=Team::with('sm:id,name')->groupby('sm_id')->get();
        }else{
       $data=Team::where('rsm_id',$id)->with('sm:id,name')->groupby('sm_id')->get();
        }
       if (count($data)==0) {
                return response()->json(['error'=>true, 'resp'=>'No data found']);
       } else {
                return response()->json(['error'=>false, 'resp'=>'Sm List','data'=>$data]);
       } 
        
    }
    //sm wise asm list
    public function smwiseAsm(Request $request,$id)
    {
        if($id=='small'){
            $data=Team::with('asm:id,name')->groupby('asm_id')->get();
        }else{
       $data=Team::where('sm_id',$id)->with('asm:id,name')->groupby('asm_id')->get();
        }
       if (count($data)==0) {
                return response()->json(['error'=>true, 'resp'=>'No data found']);
       } else {
                return response()->json(['error'=>false, 'resp'=>'Asm List','data'=>$data]);
       } 
        
    }
    //sm wise asm and ase
    public function smwiseAsmAse(Request $request,$id)
    {
        if($id=='asmall'){
            $data=Team::with('asm:id,name','ase:id,name')->groupby('asm_id','ase_id');
        }else{
       $data=Team::where('sm_id',$id)->with('asm:id,name','ase:id,name')->groupby('asm_id','ase_id')->get();
        }
       if (count($data)==0) {
            return response()->json(['error'=>true, 'resp'=>'No data found']);
       } else {
            return response()->json(['error'=>false, 'resp'=>'ASM 7 ASE List','data'=>$data]);
       } 
        
    }
     //asm wise ase list
     public function asmwiseAse(Request $request,$id)
     {
          if($id=='aseall'){
            $data=Team::with('ase:id,name')->groupby('ase_id')->get();
        }else{
        $data=Team::where('asm_id',$id)->with('ase:id,name')->groupby('ase_id')->get();
        }
        if (count($data)==0) {
                 return response()->json(['error'=>true, 'resp'=>'No data found']);
        } else {
                 return response()->json(['error'=>false, 'resp'=>'Ase List','data'=>$data]);
        } 
         
     }

    //attendance report for all
    public function attendanceReport_bkp_2023_02_03(Request $request)
    {
        $zsmDetails=User::select('id', 'name')->where('type', 2)->orderBy('name')->get();

       // $month = !empty($request->month)?$request->month:date('Y-m');
        if (isset($request->month) || isset($request->zsm)|| isset($request->rsm)|| isset($request->sm)|| isset($request->asm)|| isset($request->ase)) {
            
            $month = !empty($request->month)?$request->month:date('Y-m');
            // $date_from = $request->date_from ? $request->date_from : '';
            // $date_to = $request->date_to ? $request->date_to : '';
            $zsm_id = $request->zsm ? $request->zsm : '';
            $rsm_id = $request->rsm ? $request->rsm : '';
            $sm_id = $request->sm ? $request->sm : '';
            $asm_id = $request->asm ? $request->asm : '';
            $ase_id = $request->ase ? $request->ase : '';
            $aceids=array();
            $asmids=array();
            $rsmids=array();
            $zsmids=array();
            $teams=array();
            $usersId=array();
            $row=array();
            if(!empty($zsm_id)){
                  # Query with zsm_id and get aceids
                  
                  $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->groupby('ase_id')->get()->toArray();
                   }
                    if(!empty($zsm_id && $rsm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->groupby('ase_id')->get()->toArray();
                    }
                     if(!empty($zsm_id && $rsm_id && $sm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->groupby('ase_id')->get()->toArray();
                    }
                    if(!empty($zsm_id && $rsm_id && $sm_id && $asm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->where('asm_id',$asm_id)->groupby('ase_id')->get()->toArray();
                    }
                    if(!empty($ase_id)){
                        $aceids =array($ase_id);
                    }
                if(!empty($zsm_id)){
                  # Query with zsm_id and get aceids
                  
                  $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->groupby('asm_id')->get()->toArray();
                }
                    if(!empty($zsm_id && $rsm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->groupby('asm_id')->get()->toArray();
                    }
                     if(!empty($zsm_id && $rsm_id && $sm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->groupby('asm_id')->get()->toArray();
                    }
                    if(!empty($zsm_id && $rsm_id && $sm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->where('asm_id',$asm_id)->groupby('asm_id')->get()->toArray();
                    }
                    if(!empty($asm_id)){
                        $asmids =array($asm_id);
                    }
                    $teams=array_merge($aceids,$asmids);
                     if(!empty($zsm_id)){
                  # Query with zsm_id and get aceids
                  
                    $rsmids = Team::select('rsm_id')->where('zsm_id',$zsm_id)->groupby('rsm_id')->get()->toArray();
                   }
                   if(!empty($rsm_id)){
                  # Query with zsm_id and get aceids
                  
                   $rsmids =array($rsm_id);
                }
                     if(!empty($zsm_id)){
                         $zsmids=array($zsm_id);
                     }
                     
               $row=array_merge($zsmids,$rsmids);
               $usersId=array_merge($row,$teams);
             $data = User::select('users.*')->whereIn('id',$usersId)->where('fname','!=','VACCANT')->latest('users.id')->get();
    
        
                //$data = $query->latest('users.id')->paginate(25);
        }else{
           // $data=User::whereNotIn('users.type', [1,4,7])->latest('users.id')->paginate(25);
            $data = User::select('users.*')->where('fname','!=','VACCANT')->latest('users.id')->get();
        //    $data='';
        }
        $month = !empty($request->month)?$request->month:date('Y-m');
        return view('admin.attendance.report', compact( 'request','zsmDetails','data','month'));
    }

    //attendance report for all
    public function attendanceReport(Request $request)
    {
        $zsmDetails=User::select('id', 'name')->where('type', 2)->orderBy('name')->get();

       // $month = !empty($request->month)?$request->month:date('Y-m');
        if (isset($request->month) || isset($request->zsm)|| isset($request->rsm)|| isset($request->sm)|| isset($request->asm)|| isset($request->ase)|| isset($request->state)) {
            $month = !empty($request->month)?$request->month:date('Y-m');
            // dd($month);
            // $date_from = $request->date_from ? $request->date_from : '';
            // $date_to = $request->date_to ? $request->date_to : '';
            $zsm_id = $request->zsm ? $request->zsm : '';
            $rsm_id = $request->rsm ? $request->rsm : '';
            $sm_id = $request->sm ? $request->sm : '';
            $asm_id = $request->asm ? $request->asm : '';
            $ase_id = $request->ase ? $request->ase : '';
            $state_id = $request->state ? $request->state : '';
            $aceids=array();
            $asmids=array();
            $rsmids=array();
            $zsmids=array();
            $teams=array();
            $usersId=array();
            $row=array();
            if(!empty($zsm_id)){
                  # Query with zsm_id and get aceids
                  
                  $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->groupby('ase_id')->get()->toArray();
                   }
                   if(!empty($zsm_id && $state_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('state_id',$state_id)->groupby('ase_id')->get()->toArray();
                    }
                    if(!empty($zsm_id && $state_id && $rsm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->groupby('ase_id')->get()->toArray();
                    }
                     if(!empty($zsm_id && $state_id && $rsm_id && $sm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->groupby('ase_id')->get()->toArray();
                    }
                    if(!empty($zsm_id && $state_id && $rsm_id && $sm_id && $asm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->where('asm_id',$asm_id)->groupby('ase_id')->get()->toArray();
                    }
                    if(!empty($ase_id)){
                        $aceids =array($ase_id);
                    }
                if(!empty($zsm_id)){
                  # Query with zsm_id and get aceids
                  
                  $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->groupby('asm_id')->get()->toArray();
                }
                if(!empty($zsm_id && $state_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('state_id',$state_id)->groupby('asm_id')->get()->toArray();
                    }
                    if(!empty($zsm_id && $state_id && $rsm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->groupby('asm_id')->get()->toArray();
                    }
                     if(!empty($zsm_id && $state_id && $rsm_id && $sm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->groupby('asm_id')->get()->toArray();
                    }
                    if(!empty($zsm_id && $state_id  && $state_id && $rsm_id && $sm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->where('asm_id',$asm_id)->groupby('asm_id')->get()->toArray();
                    }
                    if(!empty($asm_id)){
                        $asmids =array($asm_id);
                    }
                    $teams=array_merge($aceids,$asmids);
                     if(!empty($zsm_id)){
                  # Query with zsm_id and get aceids
                  
                    $rsmids = Team::select('rsm_id')->where('zsm_id',$zsm_id)->groupby('rsm_id')->get()->toArray();
                   }
                   if(!empty($rsm_id)){
                  # Query with zsm_id and get aceids
                  
                   $rsmids =array($rsm_id);
                }
                     if(!empty($zsm_id)){
                         $zsmids=array($zsm_id);
                     }
                     
               $row=array_merge($zsmids,$rsmids);
               $usersId=array_merge($row,$teams);

            //    dd($usersId[0]);

                // when all zsm selected
                if ($usersId[0] == "all") {
                    $data = User::select('users.*')->where('fname','!=','VACCANT')->whereNotIn('type',[1,4,7])->latest('users.id')->paginate(15);
                }
                // when single zsm passed
                else {
                    $data = User::select('users.*')->whereIn('id',$usersId)->where('fname','!=','VACCANT')->whereNotIn('type',[1,4,7])->latest('users.id')->paginate(15);
                }



            //  $data = User::select('users.*')->whereIn('id',$usersId)->where('fname','!=','VACCANT')->latest('users.id')->paginate(15);


                //$data = $query->latest('users.id')->paginate(25);
        }else{
           // $data=User::whereNotIn('users.type', [1,4,7])->latest('users.id')->paginate(25);
            $data = User::select('users.*')->where('fname','!=','VACCANT')->whereNotIn('type',[1,4,7])->latest('users.id')->paginate(15);
        //    $data='';
        }
        $month = !empty($request->month)?$request->month:date('Y-m');
        return view('admin.attendance.report', compact( 'request','zsmDetails','data','month'));
    }
     
     

       //employee productivity report for all
     public function employeeProductivity(Request $request)
     {
        $zsmDetails=User::select('id', 'name')->where('type', 2)->orderBy('name')->get();
        if (isset($request->date_from)||isset($request->date_to) || isset($request->zsm)|| isset($request->rsm)|| isset($request->sm)|| isset($request->asm)|| isset($request->ase)) {
            $date_from = $request->date_from ? $request->date_from : '';
            $date_to = $request->date_to ? $request->date_to : '';
            $zsm = $request->zsm ? $request->zsm : '';
            $rsm = $request->rsm ? $request->rsm : '';
            $sm = $request->sm ? $request->sm : '';
            $asm = $request->asm ? $request->asm : '';
            $ase = $request->ase ? $request->ase : '';
            $data = User::whereNotIn('type', [1,2,3,4,7])->where('id', $asm)->orWhere('id', $ase)->paginate(50);
            
            

        } else {
            
            $data = User::whereNotIn('type', [1,2,3,4,7])->paginate(50);
            
        }
        $date_from = !empty($request->month)?$request->month:date('Y-m-01');
        $date_to = !empty($request->month)?$request->month:date('Y-m-d');
        return view('admin.employee-productivity.index', compact('zsmDetails', 'request','data','date_from','date_to'));
     }

     //employee productivity csv export
    public function employeeProductivityCSV(Request $request)
    {
        if (isset($request->date_from) || isset($request->date_to) || isset($request->zsm)|| isset($request->rsm)|| isset($request->sm)|| isset($request->asm)|| isset($request->ase)) {
            
            $date_to = $request->date_to ? $request->date_to : '';
            $keyword = $request->keyword ? $request->keyword : '';

            $query = UserAttendance::join('users', 'user_attendances.user_id', 'users.id')->join('teams', 'teams.ase_id', 'users.id');

            $query->when($date_from, function($query) use ($date_from) {
                $query->where('user_attendances.entry_date', '>=', $date_from);
            });
            $query->when($date_to, function($query) use ($date_to) {
                $query->where('user_attendances.entry_date', '<=', $date_to);
            });
           
            $query->when($keyword, function($query) use ($keyword) {
                $query->where('users.name', 'like', '%'.$keyword.'%');
            });

            $data = $query->orderby('entry_date','desc')->groupby('entry_date','user_id')->get();
        } else {
            $data = UserAttendance::orderby('entry_date','desc')->groupby('entry_date','user_id')->get();
        }


        if (count($data) > 0) {
            $delimiter = ",";
            $filename = "lux-employee-productivty-report-".date('Y-m-d').".csv";

            // Create a file pointer
            $f = fopen('php://memory', 'w');

            // Set column headers
            $fields = array('ZSM', 'RSM','SM', 'ASM', 'EMPLOYEE','EMPLOYEE EMP ID','EMPLOYEE STATUS', 'EMPLOYEE DESIGNATION', 'EMPLOYEE AREA','TOTAL DAYS','ACTUAL RETAILING DAY','TOTAL PRESENT','LEAVE/WEEK-OF/HOLIDAY','TOTAL RETAIL COUNT','TOTAL SALES COUNT','TELEPHONIC ORDER COUNT');
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($data as $row) {
                
                $hours=\Carbon\Carbon::parse($row->start_time)->diffInHours($row->end_time);
                if($row['users']['type']==6){
                    $type='ASE';
                }
                if($row->type=='leave'){
                     $leave ='leave';
                }elseif($row->type=='distributor-visit'){
                     $leave ='Distributor Visit'; 
                   
                }elseif($row->type=='meeting'){
                    $leave = 'Meeting';
                    
                    
                }else{
                    $leave ='Present';
                }
                if($row->type!='leave'){
                    $startTime=$row->start_time;
                    $endTime=$row->end_time ;
                } 
                $lineData = array(
                    $count,
                    $row->users->name ?? '',
                    $row->users->employee_id ?? '',
                    $type,
                    $leave,
                    $row->otheractivity->reason ?? '',
                    $startTime,
                    $endTime,
                    $hours
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
    
    
    
    
           //employee productivity report for all
     public function employeeProductivitycall(Request $request)
     {
        $zsmDetails=User::select('id', 'name','designation')->where('type', 5)->orWhere('type', 6)->orderBy('name')->get();
        if (isset($request->month) ||   isset($request->asm)) {
            if($request->asm=='all'){
                $data=User::where('type', 5)->orWhere('type', 6)->get();
            }
            else{
            $month = !empty($request->month)?$request->month:date('Y-m');
            //$date_from = $request->date_from ? $request->date_from : '';
           // $date_to = $request->date_to ? $request->date_to : '';
            $zsm = $request->zsm ? $request->zsm : '';
            $asm = $request->asm ? $request->asm : '';
            $ase = $request->ase ? $request->ase : '';
            $data = User::where('id', $asm)->paginate(50);
            
            }

        } else{
            $data=User::where('type', 5)->orWhere('type', 6)->paginate(50);
        }
        /*else {
            
            $data = User::whereNotIn('type', [1,2,5])->paginate(50);
            
        }*/
        //$date_from = !empty($request->month)?$request->month:date('Y-m-01');
       // $date_to = !empty($request->month)?$request->month:date('Y-m-d');
        $month = !empty($request->month)?$request->month:date('Y-m');
        return view('admin.employee-productivity.productive-call', compact('zsmDetails', 'request','data','month'));
     }



      public function employeeProductivityMonthly(Request $request)
     {
        $zsmDetails=User::select('id', 'name','designation')->where('type', 6)->orWhere('type', 6)->orderBy('name')->get();
        if (isset($request->month)||isset($request->status_id)) {
           
            $zsm = $request->zsm ? $request->zsm : '';
            $asm = $request->asm ? $request->asm : '';
            $ase = $request->ase ? $request->ase : '';
            $statusData = $request->status_id ;
            
            $query = User::where('fname','!=','VACCANT')->whereIN('type', [5,6]);
            if(!empty($statusData)){
				if($statusData=='active'){
					$query->where('status','=', 1);
				}
				else{
					$query->where('status','=', 0);
				}
			}
			$data = $query->get();
            //$data=User::where('fname','!=','VACCANT')->where('type', 5)->orWhere('type', 6)->get();
           // dd($data);
            

        } else{
            $data=User::where('fname','!=','VACCANT')->where('type', 5)->orWhere('type', 6)->get();
        }
        /*else {
            
            $data = User::whereNotIn('type', [1,2,5])->paginate(50);
            
        }*/
        //$date_from = !empty($request->month)?$request->month:date('Y-m-01');
       // $date_to = !empty($request->month)?$request->month:date('Y-m-d');
        $month = !empty($request->month)?$request->month:date('Y-m');
        $date_from = !empty($request->date_from)?$request->date_from:date('Y-m-01');
        $date_to = !empty($request->date_to)?$request->date_to:date('Y-m-d');
        return view('admin.employee-productivity.monthly-productive-call', compact('zsmDetails', 'request','data','month','date_from','date_to'));
     }
	

//team create
    public function userTeamAdd(Request $request)
    {
        //dd($request->all());
		$request->validate([
			"distributor_id" => "required|integer",
			"ase_id" => "required|integer",
            "stateId" => "required",
            "areaId" => "required",
		]);
        $state_id=State::where('name',$request->stateId)->first();
        $area_id=Area::where('name',$request->areaId)->first();
		$newEntry = new Team;
        $newEntry->state_id = $state_id->id;
        $newEntry->area_id = $area_id->id;
		$newEntry->distributor_id = $request['distributor_id'];
        $newEntry->nsm_id = $request['nsm_id'];
        $newEntry->zsm_id = $request['zsm_id'];
        $newEntry->rsm_id = $request['rsm_id'];
        $newEntry->sm_id = $request['sm_id'];
        $newEntry->asm_id = $request['asm_id'];
        $newEntry->ase_id = $request['ase_id'];
		$newEntry->save();
		//dd($newEntry);
        if($newEntry){
		    return redirect()->back()->with('success', 'Team Added to this Distributor');
        }
    }

     //team update
     public function userTeamEdit(Request $request,$id)
     {
         //dd($request->all());
         $request->validate([
             "distributor_id" => "required|integer",
             "ase_id" => "required|integer",
             "stateId" => "required",
             "stateId" => "required",
         ]);
         $state_id=State::where('name',$request->stateId)->first();
         $area_id=Area::where('name',$request->areaId)->first();
         $newEntry = Team::findOrfail($id);
         $newEntry->state_id = $state_id->id ?? '';
		 if(!empty($request->areaId)){
        	 $newEntry->area_id = $area_id->id ?? '';
		 } 
         $newEntry->distributor_id = $request['distributor_id'] ?? '';
         $newEntry->nsm_id = $request['nsm_id'] ?? '';
         $newEntry->zsm_id = $request['zsm_id'] ?? '';
         $newEntry->rsm_id = $request['rsm_id'] ?? '';
         $newEntry->sm_id = $request['sm_id'] ?? '';
         $newEntry->asm_id = $request['asm_id'] ?? '';
         $newEntry->ase_id = $request['ase_id'] ?? '';
         $newEntry->save();
         if($newEntry){
             return redirect()->back()->with('success', 'Team Updated to this Distributor');
         }
     }

    //team delete
    public function userTeamDestroy(Request $request,$id)
    {
		$data = Team::where('id', $id)->delete();
        return redirect()->back()->with('success', 'Team data Deleted for this Distributor');
    }
	
   //logout from other device
	public function logout(Request $request,$id)
    {
            DB::table('user_logins')->insert([
                'user_id' => $id,
                'is_login' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        
		 return redirect()->back()->with('success', 'Logout Successful');
    }

    //activity remove
    public function removeActivity(Request $request,$id)
    {
        $activity=Activity::findOrfail($id);
        
        DB::table('user_logins')->insert([
                'user_id' => $activity->user_id,
                'is_login' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        $activity->delete();
        
		 return redirect()->back()->with('success', 'Activity removed Successful');
    }
    public function attendanceReportCSV(Request $request)
    {
        // $data = [
        //     ['Name', 'Email', 'Description'],
        //     ['John Doe', '<span style="color:blue">john@example.com</span>', 'Web Developer'],
        //     ['Jane Doe', '<span style="color:green">jane@example.com</span>', 'Graphic Designer'],
        //     // Add your data here
        // ];

        // // Set headers for CSV file
        // header('Content-Type: text/csv');
        // header('Content-Disposition: attachment; filename="exported_data.csv"');

        // // Open file for writing
        // $file = fopen('php://output', 'w');

        // // Write data to file
        // foreach ($data as $row) {
        //     // Convert HTML content to plain text (strip_tags)
        //     $row = array_map('strip_tags', $row);
        //     // Write row to file
        //     fputcsv($file, $row);
        // }

        // // Close the file
        // fclose($file);

        // // Stop Laravel's request lifecycle
        // exit;






        $zsmDetails=User::select('id', 'name')->where('type', 2)->orderBy('name')->get();

       // $month = !empty($request->month)?$request->month:date('Y-m');
       // if (isset($request->month) || isset($request->zsm)|| isset($request->rsm)|| isset($request->sm)|| isset($request->asm)|| isset($request->ase)) {
            
            $month = !empty($request->month)?$request->month:date('Y-m');
            // $date_from = $request->date_from ? $request->date_from : '';
            // $date_to = $request->date_to ? $request->date_to : '';
            $zsm = $request->zsm ? $request->zsm : '';
            $rsm = $request->rsm ? $request->rsm : '';
            $sm = $request->sm ? $request->sm : '';
            $asm = $request->asm ? $request->asm : '';
            $ase = $request->ase ? $request->ase : '';
             $query = User::select('users.*')->join('teams', 'teams.zsm_id', 'users.id');
    
                $query->when($zsm, function($query) use ($zsm) {
                    $query->where('teams.zsm_id', $zsm);
                });
                $query->when($rsm, function($query) use ($rsm) {
                    $query->where('teams.rsm_id', $rsm);
                });
                $query->when($sm, function($query) use ($sm) {
                    $query->where('teams.sm_id', $sm);
                });
                 $query->when($asm, function($query) use ($asm) {
                    $query->where('teams.asm_id', $asm);
                });
                 $query->when($ase, function($query) use ($ase) {
                    $query->where('teams.ase_id', $ase);
                });
        
                $data = $query->latest('users.id')->get();
        
        if (count($data) > 0) {
                        $mon=[];
                        $my_month =  explode("-",$month);
                        $year_val = $my_month[0];
                        $month_val = $my_month[1];
                        $dates_month=dates_month($month_val,$year_val);
                        $month_names = $dates_month['month_names'];
                        $date_values = $dates_month['date_values'];
                        $totaldays=count($dates_month['date_values']);
                         foreach ($month_names as $months){
                            $mon=$months;
                         }
            $delimiter = ",";
            $filename = "lux-user-attendance-".$month.".csv";
            
            // Create a file pointer
            $f = fopen('php://memory', 'w');

            // Set column headers
            $fields = array('SR', 'NSM', 'ZSM','RSM','SM','ASM','Employee','Employee Id','Employee Status','Employee Designation','Employee Date of Joining','Employee HQ','Employee Contact No',$mon );
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($data as $row) {
                
                $lineData = array(
                   
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








    public function attendanceReportCSVAjax(Request $request)
    {
        $zsmDetails=User::select('id', 'name')->where('type', 2)->orderBy('name')->get();

        // dd($request->all());

       // $month = !empty($request->month)?$request->month:date('Y-m');
        if (isset($request->month) || isset($request->zsm)|| isset($request->rsm)|| isset($request->sm)|| isset($request->asm)|| isset($request->ase)|| isset($request->state)) {
            $month = !empty($request->month)?$request->month:date('Y-m');
            // dd($month);
            // $date_from = $request->date_from ? $request->date_from : '';
            // $date_to = $request->date_to ? $request->date_to : '';
            $zsm_id = $request->zsm ? $request->zsm : '';
            $rsm_id = $request->rsm ? $request->rsm : '';
            $sm_id = $request->sm ? $request->sm : '';
            $asm_id = $request->asm ? $request->asm : '';
            $ase_id = $request->ase ? $request->ase : '';
            $state_id = $request->state ? $request->state : '';
            $aceids=array();
            $asmids=array();
            $rsmids=array();
            $zsmids=array();
            $teams=array();
            $usersId=array();
            $row=array();
            if(!empty($zsm_id)){
                  # Query with zsm_id and get aceids
                  
                  $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->groupby('ase_id')->get()->toArray();
                   }
                   if(!empty($zsm_id && $state_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('state_id',$state_id)->groupby('ase_id')->get()->toArray();
                    }
                    if(!empty($zsm_id && $state_id && $rsm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->groupby('ase_id')->get()->toArray();
                    }
                     if(!empty($zsm_id && $state_id && $rsm_id && $sm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->groupby('ase_id')->get()->toArray();
                    }
                    if(!empty($zsm_id && $state_id && $rsm_id && $sm_id && $asm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->where('asm_id',$asm_id)->groupby('ase_id')->get()->toArray();
                    }
                    if(!empty($ase_id)){
                        $aceids =array($ase_id);
                    }
                if(!empty($zsm_id)){
                  # Query with zsm_id and get aceids
                  
                  $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->groupby('asm_id')->get()->toArray();
                }
                if(!empty($zsm_id && $state_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('state_id',$state_id)->groupby('asm_id')->get()->toArray();
                    }
                    if(!empty($zsm_id && $state_id && $rsm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->groupby('asm_id')->get()->toArray();
                    }
                     if(!empty($zsm_id && $state_id && $rsm_id && $sm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->groupby('asm_id')->get()->toArray();
                    }
                    if(!empty($zsm_id && $state_id && $rsm_id && $sm_id)){
                      # Query with zsm_id and rsm_id and get aceids
                      $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->where('asm_id',$asm_id)->groupby('asm_id')->get()->toArray();
                    }
                    if(!empty($asm_id)){
                        $asmids =array($asm_id);
                    }
                    $teams=array_merge($aceids,$asmids);
                     if(!empty($zsm_id)){
                  # Query with zsm_id and get aceids
                  
                    $rsmids = Team::select('rsm_id')->where('zsm_id',$zsm_id)->groupby('rsm_id')->get()->toArray();
                   }
                   if(!empty($rsm_id)){
                  # Query with zsm_id and get aceids
                  
                   $rsmids =array($rsm_id);
                }
                     if(!empty($zsm_id)){
                         $zsmids=array($zsm_id);
                     }
                     
               $row=array_merge($zsmids,$rsmids);
               $usersId=array_merge($row,$teams);

            //    dd($usersId[0]);

                // when all zsm selected
                if ($usersId[0] == "all") {
                    $data = User::select('users.*')->where('fname','!=','VACCANT')->whereNotIn('type', [1,4,7])->latest('id')->get();
                }
                // when single zsm passed
                else {
                    $data = User::select('users.*')->whereIn('id',$usersId)->whereNotIn('type', [1,4,7])->where('fname','!=','VACCANT')->latest('id')->get();
                }



            //  $data = User::select('users.*')->whereIn('id',$usersId)->where('fname','!=','VACCANT')->latest('users.id')->get();


                //$data = $query->latest('users.id')->paginate(25);
        }else{
           // $data=User::whereNotIn('users.type', [1,4,7])->latest('users.id')->paginate(25);
            $data = User::select('users.*')->where('fname','!=','VACCANT')->whereNotIn('type', [1,4,7])->latest('users.id')->get();
        //    $data='';
        }
        $month = !empty($request->month)?$request->month:date('Y-m');







        if (count($data) > 0) {
            // initializing vars
            $my_month =  explode("-",$month);
            $year_val = $my_month[0];
            $month_val = $my_month[1];
            $dates_month=dates_month($month_val,$year_val);
            $month_names = $dates_month['month_names'];
            $date_values = $dates_month['date_values'];
            $totaldays=count($dates_month['date_values']);

            // generating table head content
            $tableHead = ['NSM', 'ZSM', 'RSM', 'SM', 'ASM', 'EMPLOYEE', 'EMPLOYEE ID', 'EMPLOYEE STATUS', 'EMPLOYEE DESIGNATION', 'EMPLOYEE DOJ', 'EMPLOYEE HQ', 'EMPLOYEE CONTACT'];
            foreach($month_names as $months) {
                array_push($tableHead, $months);
            }

            // dd($tableHead);

            // generating table body
            $tableBody = [];
            foreach($data as $index => $item) {
                $findTeamDetails = findTeamDetails($item->id, $item->type);

                // dd($findTeamDetails[0]['nsm']);

                $monthlyDates = [];
                foreach($date_values as $date) {
                    $dates_attendance=dates_attendance($item->id, $date);

                    if($dates_attendance[0][0]['date_wise_attendance'][0]['is_present']=='A') {
                        $htmlRow = '<td class="redColor" style="background-color: red;color: #fff;padding: 15px;text-align: center;border: 1px solid #fff; vertical-align: middle;">'.
                            $dates_attendance[0][0]['date_wise_attendance'][0]['is_present']
                        .'</td>';
                    }
                    elseif($dates_attendance[0][0]['date_wise_attendance'][0]['is_present']=='P') {
                        $htmlRow = '<td class="redColor" style="background-color: rgb(1, 134, 52); color:#fff;padding: 15px;text-align: center;border: 1px solid #fff; vertical-align: middle;">'.
                            $dates_attendance[0][0]['date_wise_attendance'][0]['is_present']
                        .'</td>';
                    }
                    elseif($dates_attendance[0][0]['date_wise_attendance'][0]['is_present']=='W') {
                        $htmlRow = '<td class="redColor"  style="background-color: rgb(241, 225, 0); color:#fff; padding: 15px;text-align: center;border: 1px solid #fff; vertical-align: middle;">'.
                            $dates_attendance[0][0]['date_wise_attendance'][0]['is_present']
                        .'</td>';
                    }
                    elseif($dates_attendance[0][0]['date_wise_attendance'][0]['is_present']=='L') {
                        $htmlRow = '<td class="redColor"  style="background-color: #FFA500; color:#fff; padding: 15px;text-align: center;border: 1px solid #fff; vertical-align: middle;">'.
                            $dates_attendance[0][0]['date_wise_attendance'][0]['is_present']
                        .'</td>';
                    }
                    else {
                        $htmlRow = '<td class="redColor"  style="background-color: #294fa1da; color:#fff; padding: 15px;text-align: center;border: 1px solid #fff; vertical-align: middle;">'.
                            $dates_attendance[0][0]['date_wise_attendance'][0]['is_present']
                        .'</td>';
                    }

                    array_push($monthlyDates, $htmlRow);
                }

                if ($item->status == 1) {
                    $empStatClass = 'success';
                    $empStatType = 'Active';
                } else {
                    $empStatClass = 'danger';
                    $empStatType = 'Inactive';
                }
                
                $empStatus = '<span class="badge bg-'.$empStatClass.'">'.$empStatType.'</span>';

                $tableBody[] = [
                    $findTeamDetails[0]['nsm'] ?? '',
                    $findTeamDetails[0]['zsm'] ?? '',
                    $findTeamDetails[0]['rsm'] ?? '',
                    $findTeamDetails[0]['sm'] ?? '',
                    $findTeamDetails[0]['asm'] ?? '',
                    $item->name ?? '',
                    $item->employee_id ?? '',
                    $empStatus,
                    $item->designation ?? '',
                    $item->date_of_joining ?? '',
                    $item->headquater ?? '',
                    $item->mobile ?? '',
                    $monthlyDates
                ];
            }

            $finalHtml = '';

            $finalHtml .= '
            <table class="table">
                <thead>
                    <tr>';
                    foreach($tableHead as $head) {
                        $finalHtml .= '<th>'.$head.'</th>';
                    }
            $finalHtml .= '</tr>
                </thead>
                <tbody>';

                    foreach($tableBody as $bodyIndex => $body) {
                        $finalHtml .=
                        '<tr>
                            <td>'.$body[0].'</td>
                            <td>'.$body[1].'</td>
                            <td>'.$body[2].'</td>
                            <td>'.$body[3].'</td>
                            <td>'.$body[4].'</td>
                            <td>'.$body[5].'</td>
                            <td>'.$body[6].'</td>
                            <td>'.$body[7].'</td>
                            <td>'.$body[8].'</td>
                            <td>'.$body[9].'</td>
                            <td>'.$body[10].'</td>
                            <td>'.$body[11].'</td>';

                            // monthly dates attendance
                            foreach($body[12] as $attendance) {
                                $finalHtml .= $attendance;
                            }

                        $finalHtml .= '</tr>';
                    }
            $finalHtml .= '
                </tbody>
            </table>
            ';

            // dd($finalHtml);

            return response()->json([
                'status' => 200,
                'message' => 'Data found',
                'data' => $finalHtml
            ]);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'No data found'
            ]);
        }
    }
    
    
    public function hiererchy(Request $request)
     {
        
        if (isset($request->term)) {
            
            $term = $request->term ? $request->term : '';
            $data = User::whereNotIn('type', [1,2,3,4,5,7])->where('name','LIKE' ,'%'.$term.'%')->paginate(50);
            
            

        } else {
            
            $data = User::whereNotIn('type', [1,2,3,4,5,7])->paginate(50);
            
        }
        
        return view('admin.user.hierarchy', compact( 'request','data'));
     }
     
     
     
     
      public function hiererchyExport(Request $request)
    {

       if (isset($request->term)) {
            
            $term = $request->term ? $request->term : '';
            $data = User::whereNotIn('type', [1,2,3,4,5,7])->where('name','LIKE' ,'%'.$term.'%')->get();
            
            

        } else {
            
            $data = User::whereNotIn('type', [1,2,3,4,5,7])->get();
            
        }



        if (count($data) > 0) {
            $delimiter = ",";
            $filename = "lux-employee-hiererchy-".date('Y-m-d').".csv";

            // Create a file pointer
            $f = fopen('php://memory', 'w');

            // Set column headers
            $fields = array('SR','STATE','AREA','NSM', 'ZSM','RSM','SM','ASM','Employee');
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($data as $row) {
               
                $findTeamDetails= findTeamDetails($row->id, $row->type);
                $lineData = array(
                    $count,
                    $findTeamDetails[0]['state']?? '',
                    $findTeamDetails[0]['area']?? '',
                    $findTeamDetails[0]['nsm'] ?? '',
                    
                    $findTeamDetails[0]['zsm']?? '',
                    $findTeamDetails[0]['rsm']?? '',
                    $findTeamDetails[0]['sm']?? '',
                    $findTeamDetails[0]['asm']?? '',
                    $row ? $row->name : ''
                   
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
    
    
    
    
    
    public function distributorhiererchy(Request $request)
     {
        
        if (isset($request->term)) {
            
            $term = $request->term ? $request->term : '';
            $data = User::whereNotIn('type', [1,2,3,4,5,6])->where('name','LIKE' ,'%'.$term.'%')->paginate(50);
            
            

        } else {
            
            $data = User::whereNotIn('type', [1,2,3,4,5,6])->paginate(50);
            
        }
        
        return view('admin.user.distributor-hierarchy', compact( 'request','data'));
     }
     
     
     
     
      public function distributorhiererchyExport(Request $request)
    {

       if (isset($request->term)) {
            
            $term = $request->term ? $request->term : '';
            $data = User::whereNotIn('type', [1,2,3,4,5,6])->where('name','LIKE' ,'%'.$term.'%')->get();
            
            

        } else {
            
            $data = User::whereNotIn('type', [1,2,3,4,5,6])->get();
            
        }



        if (count($data) > 0) {
            $delimiter = ",";
            $filename = "lux-distributor-hiererchy-".date('Y-m-d').".csv";

            // Create a file pointer
            $f = fopen('php://memory', 'w');

            // Set column headers
            $fields = array('SR','DISTRIBUTOR','STATE','AREA','NSM', 'ZSM','RSM','SM','ASM','ASE');
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($data as $row) {
               
                $findTeamDetails= findTeamDetails($row->id, $row->type);
                $lineData = array(
                    $count,
                    $row ? $row->name : '',
                    $findTeamDetails[0]['state']?? '',
                    $findTeamDetails[0]['area']?? '',
                    $findTeamDetails[0]['nsm'] ?? '',
                    
                    $findTeamDetails[0]['zsm']?? '',
                    $findTeamDetails[0]['rsm']?? '',
                    $findTeamDetails[0]['sm']?? '',
                    $findTeamDetails[0]['asm']?? '',
                    $findTeamDetails[0]['ase']?? ''
                    
                   
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
    
    //coupon summary
    public function couponSummary(Request $request)
    {
        
        $stateDetails=State::where('status',1)->orderby('name')->groupby('name')->get();
        return view('admin.coupon.summary', compact('stateDetails','request'));
    }
    
    //coupon summary export
     public function couponSummaryCSV(Request $request)
   {
        $stateId = $request->get('state_id');
        $data = []; // Initialize the data array
    
        if (!empty($stateId)) {
            $state = State::find($stateId);
    
            if ($state) {
                         // Define state categories
                $state1 = ['2', '22', '13', '14']; // Example state IDs
                $state2 = ['17', '7', '25'];
                $state3 = ['1', '3', '4', '5', '6', '9', '10', '11', '12', '15', '16', '18', '19', '20', '21', '23', '24', '28', '29', '37', '38', '39'];
    
                // Assign points based on state ID
                if (in_array($stateId, $state1)) {
                    $points = 400;
                } elseif (in_array($stateId, $state2)) {
                    $points = 500;
                } else {
                    $points = 100;
                }
    
                // Fetch other required data
                $totalEnrolledStore = Store::join('teams', 'stores.id', '=', 'teams.store_id')->where('stores.state_id', $stateId)->count();
                $totalActiveStore = Store::join('teams', 'stores.id', '=', 'teams.store_id')->where('stores.state_id', $stateId)->where('stores.status', 1)->count();
                $workingDistributor = User::where('type', 7)
                    ->where('state', $state->name)
                    ->where('status', 1)
                    ->count();
                $secondaryOrder = Order::whereIn('store_id', Store::where('state_id', $stateId)->pluck('id'))->count();
                $totalSecondaryBox = OrderProduct::join('orders', 'orders.id', '=', 'order_products.order_id')
                    ->whereIn('orders.store_id', Store::where('state_id', $stateId)->pluck('id'))
                    ->sum('order_products.qty');
                // Less Than 10 Box Counters
                $lessThan10BoxCounters = OrderProduct::where('qty', '<', 10)
                ->whereIn('order_id', Order::whereIn('store_id', Store::where('state_id', $stateId)->pluck('id'))->pluck('id'))
                ->groupBy('order_id')
                ->count();
                $loginCountWiseReport = \DB::table('stores')
                    ->selectRaw('COUNT(secret_pin) AS count')
                    ->where('state_id', $stateId)
                    ->groupBy('state_id')
                    ->first();
    
                $scanCountStoreWiseReport = RetailerWalletTxn::whereIn('user_id', Store::where('state_id', $stateId)->pluck('id'))
                    ->where('type', 1)
                    ->count();
    
                $issueCountWiseReport = RetailerBarcode::where('state_id', $stateId)->count();
                $scanCountWiseReport = RetailerBarcode::where('state_id', $stateId)->where('no_of_usage', 1)->count();
                $scanLeftCountWiseReport = RetailerBarcode::where('state_id', $stateId)->where('no_of_usage', 0)->count();
                $giftOrderCountStoreWiseReport = RetailerOrder::whereIn('user_id', Store::where('state_id', $stateId)->pluck('id'))->count();
                $giftOrderDeliveredCountStoreWiseReport = RewardOrderProduct::join('retailer_orders', 'retailer_orders.id', '=', 'reward_order_products.order_id')
                    ->whereIn('reward_order_products.order_id', RetailerOrder::whereIn('user_id', Store::where('state_id', $stateId)->pluck('id'))->pluck('id'))
                    ->where('reward_order_products.status', 5)
                    ->count();
                $retailerEngagement = StoreFormSubmit::whereIn('retailer_id', Store::where('state_id', $stateId)->pluck('id'))->count();
    
                $data[] = [
                    'startingDate' => '02-01-2024',
                    'state' => $state->name,
                    'coupon_value' => $points,
                    'total_enrolled_store' => $totalEnrolledStore,
                    'active_store' => $totalActiveStore,
                    'active_distributor' => $workingDistributor,
                    'secondary_order' => $secondaryOrder,
                    'total_secondary_in_box' => $totalSecondaryBox,
                    'less_than_10_box_counters' => $lessThan10BoxCounters,
                    'retailers_app_download' => $loginCountWiseReport->count ?? 0,
                    'no_of_stores_scan_coupon' => $scanCountStoreWiseReport,
                    'no_of_coupons_scanned' => $scanCountWiseReport,
                    'coupons_issued' => $issueCountWiseReport,
                    'coupons_balance' => $scanLeftCountWiseReport,
                    'gift_order_requested' => $giftOrderCountStoreWiseReport,
                    'delivered_status' => $giftOrderDeliveredCountStoreWiseReport,
                    'retailer_engagement_form_fillup' => $retailerEngagement,
                ];
                
                    // Initialize grand totals
                    $grandTotals = [
                        'total_enrolled_store' => 0,
                        'active_store' => 0,
                        'active_distributor' => 0,
                        'secondary_order' => 0,
                        'total_secondary_in_box' => 0,
                        'less_than_10_box_counters' => 0,
                        'retailers_app_download' => 0,
                        'no_of_stores_scan_coupon' => 0,
                        'no_of_coupons_scanned' => 0,
                        'coupons_issued' => 0,
                        'coupons_balance' => 0,
                        'gift_order_requested' => 0,
                        'delivered_status' => 0,
                        'retailer_engagement_form_fillup' => 0,
                    ];
                    
                    // Calculate grand totals
                        foreach ($data as $row) {
                            $grandTotals['total_enrolled_store'] += $row['total_enrolled_store'];
                            $grandTotals['active_store'] += $row['active_store'];
                            $grandTotals['active_distributor'] += $row['active_distributor'];
                            $grandTotals['secondary_order'] += $row['secondary_order'];
                            $grandTotals['total_secondary_in_box'] += $row['total_secondary_in_box'];
                            $grandTotals['less_than_10_box_counters'] += $row['less_than_10_box_counters'];
                            $grandTotals['retailers_app_download'] += $row['retailers_app_download'];
                            $grandTotals['no_of_stores_scan_coupon'] += $row['no_of_stores_scan_coupon'];
                            $grandTotals['no_of_coupons_scanned'] += $row['no_of_coupons_scanned'];
                            $grandTotals['coupons_issued'] += $row['coupons_issued'];
                            $grandTotals['coupons_balance'] += $row['coupons_balance'];
                            $grandTotals['gift_order_requested'] += $row['gift_order_requested'];
                            $grandTotals['delivered_status'] += $row['delivered_status'];
                            $grandTotals['retailer_engagement_form_fillup'] += $row['retailer_engagement_form_fillup'];
                        }

                            // Prepare grand totals row
                            $grandTotalsRow = [
                                'startingDate' => 'GRAND TOTAL',
                                'state' => '',
                                'coupon_value' => '', // No totals for coupon value
                                'total_enrolled_store' => $grandTotals['total_enrolled_store'],
                                'active_store' => $grandTotals['active_store'],
                                'active_distributor' => $grandTotals['active_distributor'],
                                'secondary_order' => $grandTotals['secondary_order'],
                                'total_secondary_in_box' => $grandTotals['total_secondary_in_box'],
                                'less_than_10_box_counters' => $grandTotals['less_than_10_box_counters'],
                                'retailers_app_download' => $grandTotals['retailers_app_download'],
                                'no_of_stores_scan_coupon' => $grandTotals['no_of_stores_scan_coupon'],
                                'no_of_coupons_scanned' => $grandTotals['no_of_coupons_scanned'],
                                'coupons_issued' => $grandTotals['coupons_issued'],
                                'coupons_balance' => $grandTotals['coupons_balance'],
                                'gift_order_requested' => $grandTotals['gift_order_requested'],
                                'delivered_status' => $grandTotals['delivered_status'],
                                'retailer_engagement_form_fillup' => $grandTotals['retailer_engagement_form_fillup'],
                            ];
               
            }
        } else {
            $states = State::all();
            foreach ($states as $item) {
                // Define state categories
                $state1 = ['2', '22', '13', '14']; // Example state IDs
                $state2 = ['17', '7', '25'];
                $state3 = ['1', '3', '4', '5', '6', '9', '10', '11', '12', '15', '16', '18', '19', '20', '21', '23', '24', '28', '29', '37', '38', '39'];
    
                // Assign points based on state ID
                if (in_array($item->id, $state1)) {
                    $points = 400;
                } elseif (in_array($item->id, $state2)) {
                    $points = 500;
                } else {
                    $points = 100;
                }
    
                // Fetch other required data
                $totalEnrolledStore = Store::join('teams', 'stores.id', '=', 'teams.store_id')->where('stores.state_id', $item->id)->count();
                $totalActiveStore = Store::join('teams', 'stores.id', '=', 'teams.store_id')->where('stores.state_id', $item->id)->where('stores.status', 1)->count();
                $workingDistributor = User::where('type', 7)
                    ->where('state', $item->name)
                    ->where('status', 1)
                    ->count();
                $secondaryOrder = Order::whereIn('store_id', Store::where('state_id', $item->id)->pluck('id'))->count();
                $totalSecondaryBox = OrderProduct::join('orders', 'orders.id', '=', 'order_products.order_id')
                    ->whereIn('orders.store_id', Store::where('state_id', $item->id)->pluck('id'))
                    ->sum('order_products.qty');
                // Less Than 10 Box Counters
                $lessThan10BoxCounters = OrderProduct::where('qty', '<', 10)
                ->whereIn('order_id', Order::whereIn('store_id', Store::where('state_id', $item->id)->pluck('id'))->pluck('id'))
                ->groupBy('order_id')
                ->count();
                $loginCountWiseReport = \DB::table('stores')
                    ->selectRaw('COUNT(secret_pin) AS count')
                    ->where('state_id', $item->id)
                    ->groupBy('state_id')
                    ->first();
    
                $scanCountStoreWiseReport = RetailerWalletTxn::whereIn('user_id', Store::where('state_id', $item->id)->pluck('id'))
                    ->where('type', 1)
                    ->count();
    
                $issueCountWiseReport = RetailerBarcode::where('state_id', $item->id)->count();
                $scanCountWiseReport = RetailerBarcode::where('state_id', $item->id)->where('no_of_usage', 1)->count();
                $scanLeftCountWiseReport = RetailerBarcode::where('state_id', $item->id)->where('no_of_usage', 0)->count();
                $giftOrderCountStoreWiseReport = RetailerOrder::whereIn('user_id', Store::where('state_id', $item->id)->pluck('id'))->count();
                $giftOrderDeliveredCountStoreWiseReport = RewardOrderProduct::join('retailer_orders', 'retailer_orders.id', '=', 'reward_order_products.order_id')
                    ->whereIn('reward_order_products.order_id', RetailerOrder::whereIn('user_id', Store::where('state_id', $item->id)->pluck('id'))->pluck('id'))
                    ->where('reward_order_products.status', 5)
                    ->count();
                $retailerEngagement = StoreFormSubmit::whereIn('retailer_id', Store::where('state_id', $item->id)->pluck('id'))->count();
    
                $data[] = [
                    'startingDate' => '02-01-2024',
                    'state' => $item->name,
                    'coupon_value' => $points,
                    'total_enrolled_store' => $totalEnrolledStore,
                    'active_store' => $totalActiveStore,
                    'active_distributor' => $workingDistributor,
                    'secondary_order' => $secondaryOrder,
                    'total_secondary_in_box' => $totalSecondaryBox,
                    'less_than_10_box_counters' => $lessThan10BoxCounters,
                    'retailers_app_download' => $loginCountWiseReport->count ?? 0,
                    'no_of_stores_scan_coupon' => $scanCountStoreWiseReport,
                    'no_of_coupons_scanned' => $scanCountWiseReport,
                    'coupons_issued' => $issueCountWiseReport,
                    'coupons_balance' => $scanLeftCountWiseReport,
                    'gift_order_requested' => $giftOrderCountStoreWiseReport,
                    'delivered_status' => $giftOrderDeliveredCountStoreWiseReport,
                    'retailer_engagement_form_fillup' => $retailerEngagement,
                ];
                
                
                // Initialize grand totals
                    $grandTotals = [
                        'total_enrolled_store' => 0,
                        'active_store' => 0,
                        'active_distributor' => 0,
                        'secondary_order' => 0,
                        'total_secondary_in_box' => 0,
                        'less_than_10_box_counters' => 0,
                        'retailers_app_download' => 0,
                        'no_of_stores_scan_coupon' => 0,
                        'no_of_coupons_scanned' => 0,
                        'coupons_issued' => 0,
                        'coupons_balance' => 0,
                        'gift_order_requested' => 0,
                        'delivered_status' => 0,
                        'retailer_engagement_form_fillup' => 0,
                    ];
                    
                    // Calculate grand totals
                        foreach ($data as $row) {
                            $grandTotals['total_enrolled_store'] += $row['total_enrolled_store'];
                            $grandTotals['active_store'] += $row['active_store'];
                            $grandTotals['active_distributor'] += $row['active_distributor'];
                            $grandTotals['secondary_order'] += $row['secondary_order'];
                            $grandTotals['total_secondary_in_box'] += $row['total_secondary_in_box'];
                            $grandTotals['less_than_10_box_counters'] += $row['less_than_10_box_counters'];
                            $grandTotals['retailers_app_download'] += $row['retailers_app_download'];
                            $grandTotals['no_of_stores_scan_coupon'] += $row['no_of_stores_scan_coupon'];
                            $grandTotals['no_of_coupons_scanned'] += $row['no_of_coupons_scanned'];
                            $grandTotals['coupons_issued'] += $row['coupons_issued'];
                            $grandTotals['coupons_balance'] += $row['coupons_balance'];
                            $grandTotals['gift_order_requested'] += $row['gift_order_requested'];
                            $grandTotals['delivered_status'] += $row['delivered_status'];
                            $grandTotals['retailer_engagement_form_fillup'] += $row['retailer_engagement_form_fillup'];
                        }

                            // Prepare grand totals row
                            $grandTotalsRow = [
                                'startingDate' => 'GRAND TOTAL',
                                'state' => '',
                                'coupon_value' => '', // No totals for coupon value
                                'total_enrolled_store' => $grandTotals['total_enrolled_store'],
                                'active_store' => $grandTotals['active_store'],
                                'active_distributor' => $grandTotals['active_distributor'],
                                'secondary_order' => $grandTotals['secondary_order'],
                                'total_secondary_in_box' => $grandTotals['total_secondary_in_box'],
                                'less_than_10_box_counters' => $grandTotals['less_than_10_box_counters'],
                                'retailers_app_download' => $grandTotals['retailers_app_download'],
                                'no_of_stores_scan_coupon' => $grandTotals['no_of_stores_scan_coupon'],
                                'no_of_coupons_scanned' => $grandTotals['no_of_coupons_scanned'],
                                'coupons_issued' => $grandTotals['coupons_issued'],
                                'coupons_balance' => $grandTotals['coupons_balance'],
                                'gift_order_requested' => $grandTotals['gift_order_requested'],
                                'delivered_status' => $grandTotals['delivered_status'],
                                'retailer_engagement_form_fillup' => $grandTotals['retailer_engagement_form_fillup'],
                            ];
            }
        }
         $today = date('d M Y'); // Format: 15th Sept 2024

        // Define CSV headers
        $headers = [
            ['SUMMARY REPORT STATE WISE TILL DATE '.$today], // Title
            ['COUPON CURRENCY 100', 'SECONDARY ORDER', 'QR SCAN', 'GIFT ORDER'], // Section headers
            [ // Column headers
                'STARTING DATE', 'STATE', 'COUPON VALUE', 'TOTAL ENROLED STORE', 'ACTIVE STORE',
                'NO OF DISTRIBUTORS WORK IN COZI CLUB', 'SECONDARY ORDER BOOKED FROM NO OF STORE',
                'TOTAL SECONDARY (IN BOX)', 'LESS THEN 10 BOX COUNTERS', 'RETAILERS APP DOWNLOAD',
                'NO OF STORES SCAN COUPON', 'NO OF COUPON SCAN', 'COUPON ISSUE', 'COUPON BALANCE',
                'GIFT ODER REQUESTED', 'DELIVERED STATUS', 'RETAILER ENGAGEMENT FORM FILLUP'
            ]
        ];
    
        // Generate CSV file
        $output = fopen('php://output', 'w');
        ob_start();
        foreach ($headers as $header) {
         fputcsv($output, $header);
       }
    
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        fputcsv($output, $grandTotalsRow);
        fclose($output);
        $csvData = ob_get_clean();
    
        // Return CSV download response
        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="coupon_summary.csv"');
    }
    
    
    
    
    
    
    
    //cozi report statewise
    public function coziReport(Request $request)
    {
        
        $stateDetails=State::where('status',1)->orderby('name')->groupby('name')->get();
        return view('admin.coupon.cozi-report', compact('stateDetails','request'));
    }
    
    //coupon summary export
     public function coziReportCSV(Request $request)
   {
        $from = $request->date_from ?: '2024-10-21';
        $to = $request->date_to ? date('Y-m-d', strtotime($request->date_to)) : date('Y-m-d');
        $stateId = $request->get('state_id');
        $data = []; // Initialize the data array
    
        if (!empty($stateId)) {
            $state = State::find($stateId);
    
            if ($state) {
                         // Define state categories
                $state1 = ['2', '22', '13', '14']; // Example state IDs
                $state2 = ['17', '7', '25'];
                $state3 = ['1', '3', '4', '5', '6', '9', '10', '11', '12', '15', '16', '18', '19', '20', '21', '23', '24', '28', '29', '37', '38', '39'];
    
                // Assign points based on state ID
                if (in_array($stateId, $state1)) {
                    $points = 400;
                } elseif (in_array($stateId, $state2)) {
                    $points = 500;
                } else {
                    $points = 100;
                }
    
                // Fetch other required data
                $totalEnrolledStore = Store::join('teams', 'stores.id', '=', 'teams.store_id')->where('stores.state_id', $stateId)->count();
                $totalActiveStore = Store::join('teams', 'stores.id', '=', 'teams.store_id')->where('stores.state_id', $stateId)->where('stores.status', 1)->count();
                $workingDistributor = User::where('type', 7)
                    ->where('state', $state->name)
                    ->where('status', 1)
                    ->count();
                $secondaryOrder = Order::whereIn('store_id', Store::where('state_id', $stateId)->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->pluck('id'))->count();
                $totalSecondaryBox = OrderProduct::join('orders', 'orders.id', '=', 'order_products.order_id')
                    ->whereIn('orders.store_id', Store::where('state_id', $stateId)->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->pluck('id'))
                    ->sum('order_products.qty');
                // Less Than 10 Box Counters
                //$lessThan10BoxCounters = OrderProduct::where('qty', '<', 10)->whereIn('order_id', Order::whereIn('store_id', Store::where('state_id', $stateId)->pluck('id'))->pluck('id'))->groupBy('order_id')->count();
                //$loginCountWiseReport = \DB::table('stores')->selectRaw('COUNT(secret_pin) AS count')->where('state_id', $stateId)->groupBy('state_id')->first();
    
                $scanCountStoreWiseReport = RetailerWalletTxn::whereIn('user_id', Store::where('state_id', $stateId)->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->pluck('id'))->where('type', 1)->count();
    
                $issueCountWiseReport = User::selectRaw('SUM(given_coupon) AS count')->where('state', $state->name)->get();
                $scanCountWiseReport = RetailerWalletTxn::whereIn('user_id', Store::where('state_id', $stateId)->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->pluck('id'))->where('type', 1)->count();
                //$scanLeftCountWiseReport = RetailerBarcode::where('state_id', $stateId)->where('no_of_usage', 0)->whereBetween('created_at', [$from, $to])->count();
               // $giftOrderCountStoreWiseReport = RetailerOrder::whereIn('user_id', Store::where('state_id', $stateId)->pluck('id'))->count();
                //$giftOrderDeliveredCountStoreWiseReport = RewardOrderProduct::join('retailer_orders', 'retailer_orders.id', '=', 'reward_order_products.order_id')->whereIn('reward_order_products.order_id', RetailerOrder::whereIn('user_id', Store::where('state_id', $stateId)->pluck('id'))->pluck('id'))->where('reward_order_products.status', 5)->count();
                //$retailerEngagement = StoreFormSubmit::whereIn('retailer_id', Store::where('state_id', $stateId)->pluck('id'))->count();
                  $scanLeftCountWiseReport =  $issueCountWiseReport[0]->count-$scanCountWiseReport;
                $data[] = [
                    
                    'state' => $state->name,
                    'total_enrolled_store' => $totalEnrolledStore,
                    'active_store' => $totalActiveStore,
                    'active_distributor' => $workingDistributor,
                    'secondary_order' => $secondaryOrder,
                    'total_secondary_in_box' => $totalSecondaryBox,
                    'no_of_stores_scan_coupon' => $scanCountStoreWiseReport,
                    'no_of_coupons_scanned' => $scanCountWiseReport,
                    'coupons_issued' => $issueCountWiseReport[0]->count,
                    'coupons_balance' => $scanLeftCountWiseReport,
                    
                ];
                
                    // Initialize grand totals
                    $grandTotals = [
                        'total_enrolled_store' => 0,
                        'active_store' => 0,
                        'active_distributor' => 0,
                        'secondary_order' => 0,
                        'total_secondary_in_box' => 0,
                        'no_of_stores_scan_coupon' => 0,
                        'no_of_coupons_scanned' => 0,
                        'coupons_issued' => 0,
                        'coupons_balance' => 0,
                    ];
                    
                    // Calculate grand totals
                        foreach ($data as $row) {
                            $grandTotals['total_enrolled_store'] += $row['total_enrolled_store'];
                            $grandTotals['active_store'] += $row['active_store'];
                            $grandTotals['active_distributor'] += $row['active_distributor'];
                            $grandTotals['secondary_order'] += $row['secondary_order'];
                            $grandTotals['total_secondary_in_box'] += $row['total_secondary_in_box'];
                            $grandTotals['no_of_stores_scan_coupon'] += $row['no_of_stores_scan_coupon'];
                            $grandTotals['no_of_coupons_scanned'] += $row['no_of_coupons_scanned'];
                            $grandTotals['coupons_issued'] += $row['coupons_issued'];
                            $grandTotals['coupons_balance'] += $row['coupons_balance'];
                        }

                            // Prepare grand totals row
                            $grandTotalsRow = [
                                'state' => '',
                                'total_enrolled_store' => $grandTotals['total_enrolled_store'],
                                'active_store' => $grandTotals['active_store'],
                                'active_distributor' => $grandTotals['active_distributor'],
                                'secondary_order' => $grandTotals['secondary_order'],
                                'total_secondary_in_box' => $grandTotals['total_secondary_in_box'],
                                'no_of_stores_scan_coupon' => $grandTotals['no_of_stores_scan_coupon'],
                                'no_of_coupons_scanned' => $grandTotals['no_of_coupons_scanned'],
                                'coupons_issued' => $grandTotals['coupons_issued'],
                                'coupons_balance' => $grandTotals['coupons_balance'],
                            ];
               
            }
        } else {
            $states = State::where('status',1)->get();
            foreach ($states as $item) {
                // Define state categories
                $state1 = ['2', '22', '13', '14']; // Example state IDs
                $state2 = ['17', '7', '25'];
                $state3 = ['1', '3', '4', '5', '6', '9', '10', '11', '12', '15', '16', '18', '19', '20', '21', '23', '24', '28', '29', '37', '38', '39'];
    
                // Assign points based on state ID
                if (in_array($item->id, $state1)) {
                    $points = 400;
                } elseif (in_array($item->id, $state2)) {
                    $points = 500;
                } else {
                    $points = 100;
                }
    
                // Fetch other required data
                $totalEnrolledStore = Store::join('teams', 'stores.id', '=', 'teams.store_id')->where('stores.state_id', $item->id)->count();
                $totalActiveStore = Store::join('teams', 'stores.id', '=', 'teams.store_id')->where('stores.state_id', $item->id)->where('stores.status', 1)->count();
                $workingDistributor = User::where('type', 7)
                    ->where('state', $item->name)
                    ->where('status', 1)
                    ->count();
                $secondaryOrder = Order::whereIn('store_id', Store::where('state_id', $item->id)->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->pluck('id'))->count();
                $totalSecondaryBox = OrderProduct::join('orders', 'orders.id', '=', 'order_products.order_id')
                    ->whereIn('orders.store_id', Store::where('state_id', $item->id)->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->pluck('id'))
                    ->sum('order_products.qty');
                // Less Than 10 Box Counters
                //$lessThan10BoxCounters = OrderProduct::where('qty', '<', 10)->whereIn('order_id', Order::whereIn('store_id', Store::where('state_id', $item->id)->pluck('id'))->pluck('id'))->groupBy('order_id')->count();
                // $loginCountWiseReport = \DB::table('stores')
                //     ->selectRaw('COUNT(secret_pin) AS count')
                //     ->where('state_id', $item->id)
                //     ->groupBy('state_id')
                //     ->first();
    
                $scanCountStoreWiseReport = RetailerWalletTxn::whereIn('user_id', Store::where('state_id', $item->id)->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->pluck('id'))
                    ->where('type', 1)
                    ->count();
    
                //$issueCountWiseReport = RetailerBarcode::where('state_id', $item->id)->whereBetween('created_at', [$from, $to])->count();
                $issueCountWiseReport = User::selectRaw('SUM(given_coupon) AS count')->where('state', $item->name)->get();
                $scanCountWiseReport = RetailerWalletTxn::whereIn('user_id', Store::where('state_id', $item->id)->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->pluck('id'))
                    ->where('type', 1)
                    ->count();
                //$scanLeftCountWiseReport = RetailerBarcode::where('state_id', $item->id)->where('no_of_usage', 0)->whereBetween('created_at', [$from, $to])->count();
                //$giftOrderCountStoreWiseReport = RetailerOrder::whereIn('user_id', Store::where('state_id', $item->id)->pluck('id'))->count();
                // $giftOrderDeliveredCountStoreWiseReport = RewardOrderProduct::join('retailer_orders', 'retailer_orders.id', '=', 'reward_order_products.order_id')
                //     ->whereIn('reward_order_products.order_id', RetailerOrder::whereIn('user_id', Store::where('state_id', $item->id)->pluck('id'))->pluck('id'))
                //     ->where('reward_order_products.status', 5)
                //     ->count();
                // $retailerEngagement = StoreFormSubmit::whereIn('retailer_id', Store::where('state_id', $item->id)->pluck('id'))->count();
                $scanLeftCountWiseReport = $issueCountWiseReport[0]->count-$scanCountWiseReport;
                $data[] = [
                    'state' => $item->name,
                    'total_enrolled_store' => $totalEnrolledStore,
                    'active_store' => $totalActiveStore,
                    'active_distributor' => $workingDistributor,
                    'secondary_order' => $secondaryOrder,
                    'total_secondary_in_box' => $totalSecondaryBox,
                    'no_of_stores_scan_coupon' => $scanCountStoreWiseReport,
                    'no_of_coupons_scanned' => $scanCountWiseReport,
                    'coupons_issued' => $issueCountWiseReport[0]->count,
                    'coupons_balance' => $scanLeftCountWiseReport,
                ];
                
                
                // Initialize grand totals
                    $grandTotals = [
                        'total_enrolled_store' => 0,
                        'active_store' => 0,
                        'active_distributor' => 0,
                        'secondary_order' => 0,
                        'total_secondary_in_box' => 0,
                        'no_of_stores_scan_coupon' => 0,
                        'no_of_coupons_scanned' => 0,
                        'coupons_issued' => 0,
                        'coupons_balance' => 0,
                    ];
                    
                    // Calculate grand totals
                        foreach ($data as $row) {
                            $grandTotals['total_enrolled_store'] += $row['total_enrolled_store'];
                            $grandTotals['active_store'] += $row['active_store'];
                            $grandTotals['active_distributor'] += $row['active_distributor'];
                            $grandTotals['secondary_order'] += $row['secondary_order'];
                            $grandTotals['total_secondary_in_box'] += $row['total_secondary_in_box'];
                            $grandTotals['no_of_stores_scan_coupon'] += $row['no_of_stores_scan_coupon'];
                            $grandTotals['no_of_coupons_scanned'] += $row['no_of_coupons_scanned'];
                            $grandTotals['coupons_issued'] += $row['coupons_issued'];
                            $grandTotals['coupons_balance'] += $row['coupons_balance'];
                        }

                            // Prepare grand totals row
                            $grandTotalsRow = [
                                'state' => '',
                                'total_enrolled_store' => $grandTotals['total_enrolled_store'],
                                'active_store' => $grandTotals['active_store'],
                                'active_distributor' => $grandTotals['active_distributor'],
                                'secondary_order' => $grandTotals['secondary_order'],
                                'total_secondary_in_box' => $grandTotals['total_secondary_in_box'],
                                'no_of_stores_scan_coupon' => $grandTotals['no_of_stores_scan_coupon'],
                                'no_of_coupons_scanned' => $grandTotals['no_of_coupons_scanned'],
                                'coupons_issued' => $grandTotals['coupons_issued'],
                                'coupons_balance' => $grandTotals['coupons_balance'],
                            ];
            }
        }
         $today = date('d M Y'); // Format: 15th Sept 2024

        // Define CSV headers
        $headers = [
            ['COZI CLUB ALL INDIA REPORT FROM '.date('d-m-Y',strtotime($from)).'- '.date('d-m-Y',strtotime($to))], // Title
            
            [ // Column headers
                'STATE', 'TOTAL ENROLED STORE', 'TOTAL ACTIVE STORE', 'NO OF DISTRIBUTORS WORK IN COZI CLUB', 'SECONDARY ORDER BOOKED FROM NO OF STORE FROM '.date('d-m-Y',strtotime($from)).' TO '.date('d-m-Y',strtotime($to)),
                'TOTAL SECONDARY (IN BOX) '.date('d-m-Y',strtotime($from)).' TO '.date('d-m-Y',strtotime($to)), 'NO OF STORES SCAN COUPON FROM '.date('d-m-Y',strtotime($from)).' TO '. date('d-m-Y',strtotime($to)),
                'NO OF COUPON SCAN FROM '.date('d-m-Y',strtotime($from)).' TO '.date('d-m-Y',strtotime($to)), 'COUPON ISSUE FROM '.date('d-m-Y',strtotime($from)).' TO '.date('d-m-Y',strtotime($to)), 'COUPON BALANCE'
                
            ]
        ];
    
        // Generate CSV file
        $output = fopen('php://output', 'w');
        ob_start();
        foreach ($headers as $header) {
         fputcsv($output, $header);
       }
    
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        fputcsv($output, $grandTotalsRow);
        fclose($output);
        $csvData = ob_get_clean();
    
        // Return CSV download response
        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="cozi club report.csv"');
    }
    
    
    
    
    //scan consumption report statewise
    public function scanConsumptionReport(Request $request)
    {
        
        $distributorDetails=User::where('type',7)->where('status',1)->orderby('name')->get();
        return view('admin.coupon.scanconsumption-report', compact('distributorDetails','request'));
    }
    
    //scan consumption export
     public function scanConsumptionReportCSV(Request $request)
   {
        $from = $request->date_from ?: '2024-10-21';
        $to = $request->date_to ? date('Y-m-d', strtotime($request->date_to)) : date('Y-m-d');
        $distributor = $request->get('distributor');
        $data = []; // Initialize the data array
        $issueCountWiseReport=0;
        $scanCountWiseReport=0;
        if (!empty($distributor)) {
            $row = User::find($distributor);
    
            if ($row) {
                         // Define state categories
                
    
                // Fetch other required data
                
                
    
                $issueCountWiseReport = RetailerBarcode::where('distributor_id', $distributor)->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->count();
                $scanCountWiseReport = RetailerWalletTxn::whereIn('barcode_id', function($query) use ($distributor) {
                    $query->select('id')
                          ->from('retailer_barcodes')
                          ->where('distributor_id', $distributor)
                          ->where('no_of_usage', 1);
                })
                ->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
                ->count();
                $scanConsumption = $issueCountWiseReport > 0 ? (($scanCountWiseReport / $issueCountWiseReport) * 100) : 0;
              
    
                 $data[] = [
                    'distributor' => $row->name,
                    'state' => $row->state,
                    'city' => $row->city,
                    'db_code' => $row->employee_id,
                   'coupons_issued' => $row->given_coupon,
                    'no_of_coupons_scanned' => $scanCountWiseReport,
                    
                    'consumption' => $scanConsumption.'%',
                ];
                
                    // Initialize grand totals
                    $grandTotals = [
                        'coupons_issued' => 0,
                        'no_of_coupons_scanned' => 0,
                        
                    ];
                    
                    // Calculate grand totals
                        foreach ($data as $row) {
                             $grandTotals['coupons_issued'] += $row['coupons_issued'];
                            $grandTotals['no_of_coupons_scanned'] += $row['no_of_coupons_scanned'];
                        }

                            // Prepare grand totals row
                           $grandTotalsRow = [
                                'distributor' => '',
                                'state' => '',
                                'city' => '',
                                'db_code' => '',
                                'coupons_issued' => $grandTotals['coupons_issued'],
                                'no_of_coupons_scanned' => $grandTotals['no_of_coupons_scanned'],
                                'consumption' => '',
                                
                            ];
               
            }
        } else {
            $distributor = User::where('type',7)->where('status',1)->get();
            foreach ($distributor as $item) {
                // Define state categories
                
    
                
                
    
               $distributorId=$item->id;
    
                $issueCountWiseReport = RetailerBarcode::where('distributor_id', $item->id)->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->count();
                //$scanCountWiseReport = RetailerBarcode::where('distributor_id', $item->id)->where('no_of_usage', 1)->whereBetween('created_at', [$from, $to])->count();
                $scanCountWiseReport = RetailerWalletTxn::whereIn('barcode_id', function($query) use ($distributorId) {
                    $query->select('id')
                          ->from('retailer_barcodes')
                          ->where('distributor_id', $distributorId)
                          ->where('no_of_usage', 1);
                })
                ->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
                ->count();
                $scanConsumption = $issueCountWiseReport > 0 ? (($scanCountWiseReport / $issueCountWiseReport) * 100) : 0;
    
                $data[] = [
                    'distributor' => $item->name,
                    'state' => $item->state,
                    'city' => $item->city,
                    'db_code' => $item->employee_id,
                   'coupons_issued' => $item->given_coupon,
                    'no_of_coupons_scanned' => $scanCountWiseReport,
                    
                    'consumption' => $scanConsumption.'%',
                ];
                
                
                // Initialize grand totals
                    $grandTotals = [
                        'coupons_issued' => 0,
                        'no_of_coupons_scanned' => 0,
                        
                    ];
                    
                    // Calculate grand totals
                        foreach ($data as $row) {
                             $grandTotals['coupons_issued'] += $row['coupons_issued'];
                            $grandTotals['no_of_coupons_scanned'] += $row['no_of_coupons_scanned'];
                        }

                            // Prepare grand totals row
                            $grandTotalsRow = [
                                'distributor' => '',
                                'state' => '',
                                'city' => '',
                                'db_code' => '',
                                'coupons_issued' => $grandTotals['coupons_issued'],
                                'no_of_coupons_scanned' => $grandTotals['no_of_coupons_scanned'],
                                'consumption' => '',
                                
                            ];
            }
        }
         $today = date('d M Y'); // Format: 15th Sept 2024

        // Define CSV headers
        $headers = [
            ['COUPON SCAN DETAIL TILL '.date('d-m-Y',strtotime($to))], // Title
            
            [ // Column headers
                'DISTRIBUTOR NAME', 'STATE', 'CITY', 'DB CODE', 'COUPON ISSUED TILL '.date('d-m-Y',strtotime($from)).' TO '.date('d-m-Y',strtotime($to)),
                'COUPON SCAN TILL '.date('d-m-Y',strtotime($from)).' TO '.date('d-m-Y',strtotime($to)), 'CONSUMPTION'
                
                
            ]
        ];
    
        // Generate CSV file
        $output = fopen('php://output', 'w');
        ob_start();
        foreach ($headers as $header) {
         fputcsv($output, $header);
       }
    
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        fputcsv($output, $grandTotalsRow);
        fclose($output);
        $csvData = ob_get_clean();
    
        // Return CSV download response
        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="Coupon Issue vs Scan Consumption Report.csv"');
    }
    
    
    
    
        public function manwiseproductivityReport(Request $request)
    {
        
        $aseDetails=User::where('type',6)->orWhere('type',5)->where('status',1)->orderby('name')->get();
        $month = !empty($request->month)?$request->month:date('Y-m');
        return view('admin.coupon.productivity-report', compact('aseDetails','request','month'));
    }
    
    //scan consumption export
   /*  public function manwiseproductivityReportCSV(Request $request)
   {
        $from = $request->date_from ?: '2025-01-01';
        $to = $request->date_to ? date('Y-m-d', strtotime($request->date_to)) : date('Y-m-d');
        // Get the month name from the given date range
        $fromMonth = strtoupper(date('F', strtotime($from)));
        $toMonth = strtoupper(date('F', strtotime($to)));

        // If the months are the same, use a single month name; otherwise, show both
        $monthTitle = ($fromMonth === $toMonth) ? $fromMonth : "$fromMonth - $toMonth";
       
        
        $dates = [];
        $startDate = strtotime($from);
        $endDate = strtotime($to);
    
        while ($startDate <= $endDate) {
            $dates[] = date('Y-m-d', $startDate);
            $startDate = strtotime("+1 day", $startDate);
        }
       
        $dateHeaders = [];
            foreach ($dates as $date) {
                $dateHeaders[] = '1ST CALL';   // 1st Call for each date
                $dateHeaders[] = 'LAST CALL';  // Last Call for each date
                $dateHeaders[] = 'TOTAL HRS';  // Total Hours for each date
                $dateHeaders[] = 'TC';         // TC for each date
                $dateHeaders[] = 'PC';         // PC for each date
                $dateHeaders[] = 'QTY';        // Quantity for each date
            }
            
            // Define headers
            //$monthTitle = 'JANUARY'; // Set the month title, can be dynamic if needed
            $headers = [
                ["$monthTitle DAILY Sales Man Wise Productivity Details"], // Title row
                array_merge(['', '', '', ''], $dates), // Date row - Ensure $dates is a simple array of date strings
                array_merge(
                    ['STATE', 'EMPLOYEE CODE', 'EMPLOYEE', 'EMPLOYEE HQ'], 
                    $dateHeaders, // Expanded date headers (individual columns for each date)
                    [
                        'TOTAL TC', 'TOTAL PC', 'TOTAL SECONDARY', 'TOTAL WORKING HRS', 
                        'AVG WORKING HRS', 'RETAILING DAYS', 'AVG TC', 'AVG PC', 
                        'Productivity %', 'AVG ORDER'
                    ]
                )
            ];

       
        $data = [];
        $ase= $request->get('user');
        
        
        if (!empty($ase)) {
            $userDetails = User::find($ase);
                $row = [
                        $userDetails->state,
                        $userDetails->employee_id,
                        $userDetails->name,
                        $userDetails->headquater,
                    ];
                    $id=$userDetails->id;
                    $totalTC = $totalPC = $totalQty = $totalWorkingHrs = 0;
                    $retailingDays = 0;
    
                    foreach ($dates as $date) {
                       
                        $firstCall = DB::table('activities')
                                    ->where('user_id', $id)
                                   ->whereDate('date', $date) // Filters by date range
                                    ->whereIn('type', [
                                        'Order Upload',
                                        'Order On Call',
                                        'No Order Placed',
                                        'distributor-visit-start',
                                        'Store Added',
                                        'meeting',
                                        'joint-work'
                                    ])
                                    ->orderBy('id', 'asc')
                                    ->value('time');
                        
            
                        $lastCall = DB::table('activities')
                                    ->where('user_id', $id)
                                    ->whereDate('date', $date)
                                    ->whereIn('type', [
                                        'Order Upload',
                                        'Order On Call',
                                        'No Order Placed',
                                        'distributor-visit-start',
                                        'Store Added',
                                        'meeting',
                                        'joint-work'
                                       
                                    ])
                                    ->orderBy('created_at', 'desc')
                                    ->value('time');
                        //$firstCallTime = \DateTime::createFromFormat('h:i A', $firstCall);
                        //$lastCallTime= date('H:i', strtotime($lastCall));
                      
                        // Calculate the difference between the two times
                        if ($firstCall === false || $lastCall === false) {
                           
                            $totalHrs =0;
                            
                        }else{
                            
                            
                                   // Handle cases where lastCall is past midnight (e.g., firstCall = 11:40 PM, lastCall = 1:30 AM)
                                    
                                    // Calculate the difference
                                    //$interval = $lastCallTime->diff($firstCallTime);
                                    
                                    // Format as HH:MM
                                    //$totalHrs = $firstCall && $lastCallTime ? round((strtotime($lastCallTime) - strtotime($firstCall)) /3600, 2) : 0;
                                 //$interval = $firstCallTime->diff($lastCallTime);
                                
                                // Get total hours and minutes
                                //$totalHrs =  round($interval->h + ($interval->i / 60)); 
                                
                                    $firstCallTimestamp = strtotime($firstCall);
                                    $lastCallTimestamp = strtotime($lastCall);
                                    
                                    // If lastCallTime is earlier than firstCallTime, add 24 hours to lastCallTime to handle the midnight case
                                    if ($lastCallTimestamp < $firstCallTimestamp) {
                                        $lastCallTimestamp += 24 * 60 * 60; // Add 24 hours
                                    }
                                    
                                    // Calculate the difference in hours
                                    $totalHrsInSeconds = $lastCallTimestamp - $firstCallTimestamp; // Difference in seconds
                                    $totalHrs = round($totalHrsInSeconds / 3600, 2); 
                            
                        }
                        
                        //$totalHrs = $firstCall && $lastCall ? round((strtotime($lastCall) - strtotime($firstCall)) / 3600, 2) : 0;
                        $tc = DB::table('activities')->where('user_id', $id)->whereDate('created_at', $date)->whereIn('type', [
                                        'Order Upload',
                                        'Order On Call',
                                        'No Order Placed',
                                        'Store Added'
                                        
                                    ])->count();
                        
                        $pc = DB::table('orders')->where('user_id', $id)->whereDate('created_at', $date)->count();
                        
                        $qrdQty = DB::select("
                                SELECT COUNT(op.id) AS total, SUM(op.qty) AS qty 
                                FROM orders AS o 
                                INNER JOIN order_products AS op ON o.id = op.order_id
                                WHERE o.user_id = '$userDetails->id' 
                                AND (DATE_FORMAT(o.created_at, '%Y-%m-%d')) = '$date'
                            ");
                                                
                                            
                                       
                        $qty = $qrdQty[0]->qty; // Assuming quantity is the number of scanned coupons
              
                        if ($tc > 0) {
                            $retailingDays++;
                        }
    
                        $row = array_merge($row, [
                            $firstCall ?: '',
                            $lastCall ?: '',
                            $totalHrs,
                            $tc,
                            $pc,
                            $qty
                        ]);
            
                        $totalTC += $tc;
                        $totalPC += $pc;
                        $totalQty += $qty;
                        $totalWorkingHrs += $totalHrs;
                    }
    
                    $avgWorkingHrs = $retailingDays > 0 ? round($totalWorkingHrs / $retailingDays, 0) : 0;
                    $avgTC = $retailingDays > 0 ? round($totalTC / $retailingDays, 0) : 0;
                    $avgPC = $retailingDays > 0 ? round($totalPC / $retailingDays, 0) : 0;
                    $productivity = $totalTC > 0 ? round(($totalPC / $totalTC) * 100, 0) . '%' : '0%';
                    $avgOrder = $retailingDays > 0 ? round($totalQty / $retailingDays, 0) : 0;
            
                    $row = array_merge($row, [
                        $totalTC,
                        $totalPC,
                        $totalQty,
                        $totalWorkingHrs,
                        $avgWorkingHrs,
                        $retailingDays,
                        $avgTC,
                        $avgPC,
                        $productivity,
                        $avgOrder
                    ]);
            
                    $data[] = $row;
        }else{
             $users = User::where('type', 6)->where('status', 1);
             $users->chunk(20, function ($rows) use (&$dates, &$data) {
                foreach ($rows as $user) {
                    $row = [
                        $user->state,
                        $user->employee_id,
                        $user->name,
                        $user->headquater,
                    ];
                    $id=$user->id;
                    $totalTC = $totalPC = $totalQty = $totalWorkingHrs = 0;
                    $retailingDays = 0;
    
                    foreach ($dates as $date) {
                        $firstCall = DB::table('activities')
                                    ->where('user_id', $id)
                                   ->whereDate('date', $date) // Filters by date range
                                    ->whereIn('type', [
                                        'Order Upload',
                                        'Order On Call',
                                        'No Order Placed',
                                        'distributor-visit-start',
                                        'Store Added',
                                        'meeting',
                                        'joint-work'
                                       
                                    ])
                                    ->orderBy('id', 'asc')
                                    ->value('time');
                        
            
                        $lastCall = DB::table('activities')
                                    ->where('user_id', $id)
                                    ->whereDate('date', $date)
                                     ->whereIn('type', [
                                        'Order Upload',
                                        'Order On Call',
                                        'No Order Placed',
                                        'distributor-visit-start',
                                        'Store Added',
                                        'meeting',
                                        'joint-work'
                                       
                                    ])// Filters by date range
                                    ->orderBy('created_at', 'desc')
                                    ->value('time');
                        $firstCallTime = \DateTime::createFromFormat('h:i A', $firstCall);
                        //$lastCallTime= \DateTime::createFromFormat('h:i A', $lastCall);
                       $lastCallTime= date('H:i', strtotime($lastCall));
                        if ($firstCall === false || $lastCall === false) {
                            $totalHrs=0;
                        }else{
                            
                            $firstCallTimestamp = strtotime($firstCall);
                                    $lastCallTimestamp = strtotime($lastCall);
                                    
                                    // If lastCallTime is earlier than firstCallTime, add 24 hours to lastCallTime to handle the midnight case
                                    if ($lastCallTimestamp < $firstCallTimestamp) {
                                        $lastCallTimestamp += 24 * 60 * 60; // Add 24 hours
                                    }
                                    
                                    // Calculate the difference in hours
                                    $totalHrsInSeconds = $lastCallTimestamp - $firstCallTimestamp; // Difference in seconds
                                    $totalHrs = round($totalHrsInSeconds / 3600, 2);
                        }
                       
                        //$totalHrs = $firstCall && $lastCall ? round((strtotime($lastCall) - strtotime($firstCall)) / 3600, 2) : 0;
                        $tc = DB::table('activities')->where('user_id', $id)->whereDate('date', $date)->whereIn('type', [
                                        'Order Upload',
                                        'Order On Call',
                                        'No Order Placed',
                                        'Store Added'
                                    ])->count();
                        
                        $pc = DB::table('orders')->where('user_id', $id)->whereDate('created_at', $date)->count();
                        
                                                
                        $qrdQty = DB::select("
                                SELECT COUNT(op.id) AS total, SUM(op.qty) AS qty 
                                FROM orders AS o 
                                INNER JOIN order_products AS op ON o.id = op.order_id
                                WHERE o.user_id = '$user->id' 
                                AND (DATE_FORMAT(o.created_at, '%Y-%m-%d')) = '$date'
                            ");
                                                
                                            
                                       
                        $qty = $qrdQty[0]->qty; // Assuming quantity is the number of scanned coupons
              
                        if ($tc > 0) {
                            $retailingDays++;
                        }
    
                        $row = array_merge($row, [
                            $firstCall ?: '',
                            $lastCall ?: '',
                            $totalHrs,
                            $tc,
                            $pc,
                            $qty
                        ]);
            
                        $totalTC += $tc;
                        $totalPC += $pc;
                        $totalQty += $qty;
                        $totalWorkingHrs += $totalHrs;
                    }
    
                    $avgWorkingHrs = $retailingDays > 0 ? round($totalWorkingHrs / $retailingDays, 0) : 0;
                    $avgTC = $retailingDays > 0 ? round($totalTC / $retailingDays, 0) : 0;
                    $avgPC = $retailingDays > 0 ? round($totalPC / $retailingDays, 0) : 0;
                    $productivity = $totalTC > 0 ? round(($totalPC / $totalTC) * 100, 0) . '%' : '0%';
                    $avgOrder = $retailingDays > 0 ? round($totalQty / $retailingDays, 0) : 0;
            
                    $row = array_merge($row, [
                        $totalTC,
                        $totalPC,
                        $totalQty,
                        $totalWorkingHrs,
                        $avgWorkingHrs,
                        $retailingDays,
                        $avgTC,
                        $avgPC,
                        $productivity,
                        $avgOrder
                    ]);
            
                    $data[] = $row;
                }
             });
        }
        // Generate CSV file
        $output = fopen('php://output', 'w');
        ob_start();
        foreach ($headers as $header) {
            fputcsv($output, $header);
        }
    
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
    
        fclose($output);
        $csvData = ob_get_clean();
    
        // Return CSV download response
        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="SalesManWiseProductivityReport.csv"');
        }*/
        
        
    public function manwiseproductivityReportCSV(Request $request)
   {
       $from = $request->date_from ?: '2025-01-01';
        $to = $request->date_to ? date('Y-m-d', strtotime($request->date_to)) : date('Y-m-d');
        // Get the month name from the given date range
        $fromMonth = strtoupper(date('F', strtotime($from)));
        $toMonth = strtoupper(date('F', strtotime($to)));

        // If the months are the same, use a single month name; otherwise, show both
        $monthTitle = ($fromMonth === $toMonth) ? $fromMonth : "$fromMonth - $toMonth";
       
        
        $dates = [];
        $startDate = strtotime($from);
        $endDate = strtotime($to);
    
        while ($startDate <= $endDate) {
            $dates[] = date('Y-m-d', $startDate);
            $startDate = strtotime("+1 day", $startDate);
        }
       
        $dateHeaders = [];
            foreach ($dates as $date) {
                $dateHeaders[] = '1ST CALL';   // 1st Call for each date
                $dateHeaders[] = 'LAST CALL';  // Last Call for each date
                $dateHeaders[] = 'TOTAL HRS';  // Total Hours for each date
                $dateHeaders[] = 'TC';         // TC for each date
                $dateHeaders[] = 'PC';         // PC for each date
                $dateHeaders[] = 'QTY';        // Quantity for each date
            }
            
            // Define headers
            //$monthTitle = 'JANUARY'; // Set the month title, can be dynamic if needed
            // $headers = [
            //     ["$monthTitle DAILY Sales Man Wise Productivity Details"], // Title row
            //     array_merge(['', '', '', ''], $dates), // Date row - Ensure $dates is a simple array of date strings
            //     array_merge(
            //         ['STATE', 'EMPLOYEE CODE', 'EMPLOYEE', 'EMPLOYEE HQ'], 
            //         $dateHeaders, // Expanded date headers (individual columns for each date)
            //         [
            //             'TOTAL TC', 'TOTAL PC', 'TOTAL SECONDARY', 'TOTAL WORKING HRS', 
            //             'AVG WORKING HRS', 'RETAILING DAYS', 'AVG TC', 'AVG PC', 
            //             'Productivity %', 'AVG ORDER'
            //         ]
            //     )
            // ];
            
            //$headers=["$monthTitle DAILY Sales Man Wise Productivity Details"];
            $subHeaders = ['1ST CALL', 'LAST CALL', 'TOTAL HRS', 'TC', 'PC', 'QTY'];
            $staticHeaders = ['STATE', 'EMPLOYEE CODE', 'EMPLOYEE','EMPLOYEE HQ'];
            $dataHeaders=['TOTAL TC', 'TOTAL PC', 'TOTAL SECONDARY', 'TOTAL WORKING HRS', 
                         'AVG WORKING HRS', 'RETAILING DAYS', 'AVG TC', 'AVG PC', 
                        'Productivity %', 'AVG ORDER'];
            $headers = [
                ["$monthTitle DAILY Sales Man Wise Productivity Details"], // Title Row
            ];
            
            // === First Header Row (Date Headers) ===
            $headerRow = ['', '', '', ''];
            foreach ($dates as $date) {
                // Add the date column
                $headerRow[] = $date;
                
                // Add 6 empty columns for spacing after the date
                $headerRow = array_merge($headerRow, array_fill(0, 5, ''));
            }
            //$headerRow = array_merge($headerRow, array_fill(0, 6, ''));

            $headers[] = $headerRow;
            
            // === Add 6 Empty Rows for Spacing ===
            //$headers = array_merge($headers, array_fill(0, 6, []));
            
            // === Second Header Row (Static + Sub-Headers) ===
            $secondHeaderRow = array_merge($staticHeaders, []);
            foreach ($dates as $date) {
                $secondHeaderRow = array_merge($secondHeaderRow, $subHeaders);
    
                // Add 6 empty columns for spacing after each date's sub-headers
                //$secondHeaderRow = array_merge($secondHeaderRow, array_fill(0, 6, ''));
            }
            $secondHeaderRow = array_merge($secondHeaderRow,$dataHeaders, []);
            $headers[] = $secondHeaderRow;
            //dd($headers);
            

       
        $data = [];
        
    $ase = $request->user;
    
   $usersQuery=User::where('status', 1);

    if (!empty($ase)) {
        $usersQuery->where('id', $ase);
    }else{
         $usersQuery = User::where('type', 6)->orWhere('type',5);
    }
    
    $users = $usersQuery->get();
   // dd($users);
    $userIds = $users->pluck('id')->toArray();
  //  dd($to);
    if (empty($userIds)) {
        return response()->json(['error' => 'No users found'], 404);
    }
   $storeCounts = [];
    // Fetch all activities in one query
    /*$activities = DB::table('activities')->selectRaw("activities.*,
        user_id,
        DATE(date) as date,
        CASE 
            WHEN comment LIKE '%no order reason for%' 
                THEN SUBSTRING_INDEX(comment, 'for', -1)
              WHEN comment LIKE '%placed a order for%' AND comment LIKE '%at%'
                THEN CASE 
                   
                    WHEN LENGTH(comment) - LENGTH(REPLACE(comment, ' at', '')) = LENGTH(comment) - LENGTH(REPLACE(comment, ' at ', '')) 
                        THEN SUBSTRING_INDEX(SUBSTRING_INDEX(comment, ' at', 1), ' for ', -1)
                    ELSE 
                        SUBSTRING_INDEX(SUBSTRING_INDEX(comment, ' at', 1), ' for ', -1)
                END
           WHEN comment LIKE '%placed a order on call%' AND comment LIKE '%at%'
                THEN (
                    SUBSTRING_INDEX(
                        SUBSTRING_INDEX(comment, ' at ', 1), ' for ', -1
                    )
                )
            WHEN comment LIKE '%placed a order on call%' 
                THEN (SUBSTRING_INDEX(comment, 'for', -1))
            WHEN comment LIKE '%added a store%'
                THEN (SUBSTRING_INDEX(comment, 'store', -1))
            WHEN comment LIKE '%joint-work%' THEN (comment) 
            
            ELSE NULL
        END AS store_name
    ")
        ->whereIn('user_id', $userIds)
        //->whereBetween('date', [$from, $to])
        ->whereDate('date', '>=', $from)
        ->whereDate('date', '<=', $to)
        ->whereIn('type', [
            'Order Upload',
            'Order On Call',
            'No Order Placed',
           
            'Store Added',
            
            'joint-work'
        ])
        ->groupBy('user_id', 'date', 'store_name')
        ->orderBy('id')
        ->get()
        ->groupBy(['user_id', 'date']);*/
        
        $activities = DB::table(DB::raw("(SELECT 
        activities.*,
       
        CASE 
            WHEN comment LIKE '%no order reason for%' 
                THEN TRIM(SUBSTRING_INDEX(comment, 'for', -1))
            WHEN comment LIKE '%placed a order for%' AND comment LIKE '%at%'
                THEN TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(comment, ' at', 1), ' for ', -1))
            WHEN comment LIKE '%placed a order on call%' AND comment LIKE '%at%'
                THEN TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(comment, ' at ', 1), ' for ', -1))
            WHEN comment LIKE '%placed a order on call%' 
                THEN TRIM(SUBSTRING_INDEX(comment, 'for', -1))
            WHEN comment LIKE '%added a store%' 
                THEN TRIM(SUBSTRING_INDEX(comment, 'store', -1))
            WHEN comment LIKE '%joint-work%' 
                THEN TRIM(comment)
            ELSE NULL
        END AS store_name
    FROM activities) as activity_data"))
     ->leftJoin('stores', DB::raw('TRIM(activity_data.store_name)'), '=', DB::raw('TRIM(stores.name)'))
    ->leftJoin('orders', function ($join) {
        $join->on('orders.user_id', '=', 'activity_data.user_id')
             ->on('orders.store_id', '=', 'stores.id')
             ->whereRaw('DATE(orders.created_at) = DATE(activity_data.date)');
    })
     ->leftJoin('user_no_order_reasons', function ($join) {
        $join->on('user_no_order_reasons.user_id', '=', 'activity_data.user_id')
             ->on('user_no_order_reasons.store_id', '=', 'stores.id')
             ->whereRaw('DATE(user_no_order_reasons.created_at) = DATE(activity_data.date)');
    })
    ->whereIn('activity_data.user_id', $userIds)
    ->whereDate('activity_data.date', '>=', $from)
    ->whereDate('activity_data.date', '<=', $to)
    ->whereIn('activity_data.type', [
        'Order Upload',
        'Order On Call',
        'No Order Placed',
        'Store Added',
        'joint-work'
    ])
    ->select(
        'activity_data.*',
        'stores.id as store_id',
        DB::raw('IF(orders.id IS NOT NULL, 1, 0) as has_order')
    )
    ->groupBy('activity_data.user_id', 'activity_data.date', 'activity_data.store_name', 'stores.id', 'orders.id','user_no_order_reasons.id')
    ->orderBy('activity_data.id')
    ->get()
    ->groupBy(['user_id', 'date']);


        
        
           /* $ordersForReport = DB::table('orders')
    ->join('stores as s', 'orders.store_id', '=', 's.id')
    ->selectRaw("orders.user_id, DATE(orders.created_at) as date, s.name COLLATE utf8mb4_unicode_ci as store_name")
    ->where('orders.order_type', 'store-visit')
    ->whereIn('orders.user_id', $userIds)
    ->whereDate('orders.created_at', '>=', $from)
    ->whereDate('orders.created_at', '<=', $to);

$ordersoncallForReport = DB::table('orders')
    ->join('stores as s', 'orders.store_id', '=', 's.id')
    ->selectRaw("orders.user_id, DATE(orders.created_at) as date, s.name COLLATE utf8mb4_unicode_ci as store_name")
    ->where('orders.order_type', 'order-on-call')
    ->whereIn('orders.user_id', $userIds)
    ->whereDate('orders.created_at', '>=', $from)
    ->whereDate('orders.created_at', '<=', $to);

$noOrdersForReport = DB::table('user_no_order_reasons')
    ->join('stores as s', 'user_no_order_reasons.store_id', '=', 's.id')
    ->selectRaw("user_no_order_reasons.user_id, DATE(user_no_order_reasons.created_at) as date, s.name COLLATE utf8mb4_unicode_ci as store_name")
    ->whereIn('user_no_order_reasons.user_id', $userIds)
    ->whereDate('user_no_order_reasons.created_at', '>=', $from)
    ->whereDate('user_no_order_reasons.created_at', '<=', $to);

$storeAddsForReport = DB::table('stores')
    ->selectRaw("user_id, DATE(created_at) as date, name COLLATE utf8mb4_unicode_ci as store_name")
    ->whereIn('user_id', $userIds)
    ->whereDate('created_at', '>=', $from)
    ->whereDate('created_at', '<=', $to);

$jointworkForReport = DB::table('other_activities')
    ->selectRaw("user_id, DATE(created_at) as date, reason COLLATE utf8mb4_unicode_ci as store_name")
     ->where('reason', '%joint-work%')
    ->whereIn('user_id', $userIds)
    ->whereDate('created_at', '>=', $from)
    ->whereDate('created_at', '<=', $to);

$activityStoresForReport = DB::table('activities')
    ->selectRaw("user_id,
        DATE(date) as date,
         
        CASE 
            WHEN comment LIKE '%no order reason for%' THEN TRIM(SUBSTRING_INDEX(comment, 'for', -1)) COLLATE utf8mb4_unicode_ci
            WHEN comment LIKE '%placed a order for%' AND comment LIKE '%at%' THEN TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(comment, ' at', 1), ' for ', -1)) COLLATE utf8mb4_unicode_ci
            WHEN comment LIKE '%placed a order on call%' AND comment LIKE '%at%' THEN TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(comment, ' at ', 1), ' for ', -1)) COLLATE utf8mb4_unicode_ci
            WHEN comment LIKE '%placed a order on call%' THEN TRIM(SUBSTRING_INDEX(comment, 'for', -1)) COLLATE utf8mb4_unicode_ci
            WHEN comment LIKE '%added a store%' THEN TRIM(SUBSTRING_INDEX(comment, 'store', -1)) COLLATE utf8mb4_unicode_ci
            WHEN comment LIKE '%joint-work%' THEN TRIM(comment) COLLATE utf8mb4_unicode_ci
            ELSE NULL
        END AS store_name")
        
    ->whereIn('user_id', $userIds)
    ->whereDate('date', '>=', $from)
    ->whereDate('date', '<=', $to)
    ->whereIn('type', [
        'Order Upload',
        'Order On Call',
        'No Order Placed',
        'Store Added',
        'joint-work'
    ]);

$combined = $ordersForReport
    ->unionAll($ordersoncallForReport)
    ->unionAll($noOrdersForReport)
    ->unionAll($storeAddsForReport)
    ->unionAll($jointworkForReport)
    ->unionAll($activityStoresForReport);

$activities = DB::table(DB::raw("({$combined->toSql()}) as combined"))
    ->mergeBindings($combined)
    ->select('user_id', 'date', DB::raw('TRIM(store_name) as store_name'))
    ->groupBy('user_id', 'date', 'store_name')
    ->orderBy('user_id')
    
    
    ->get()
    ->groupBy(['user_id', 'date']);*/


       dd($activities);
    // Fetch all orders in one query
    $orders = DB::table('orders')
        ->whereIn('user_id', $userIds)
        //->whereBetween('created_at', [$from, $to])
        ->whereDate('created_at', '>=', $from)
        ->whereDate('created_at', '<=', $to)
        ->selectRaw('user_id, DATE(created_at) as order_date')
        ->groupBy('user_id', 'store_id', 'order_date')
        ->get()
        ->groupBy(['user_id', 'order_date']);
  
    // Fetch all order products in one query
    $orderProducts = DB::table('orders as o')
        ->join('order_products as op', 'o.id', '=', 'op.order_id')
        ->whereIn('o.user_id', $userIds)
        //->whereBetween('o.created_at', [$from, $to])
        ->whereDate('o.created_at', '>=', $from)
        ->whereDate('o.created_at', '<=', $to)
        ->selectRaw('o.user_id, DATE(o.created_at) as order_date, SUM(op.qty) as qty')
        ->groupBy('o.user_id', 'order_date')
        ->get()
        ->groupBy(['user_id', 'order_date']);
    $storeCounts = [];
    
    
    foreach ($users as $user) {
        $row = [
            $user->state,
            $user->employee_id,
            $user->name,
            $user->headquater
        ];
        
        $id =  $user->id;
        $totalTC = $totalPC = $totalQty = $totalWorkingHrs = 0;
        $retailingDays = 0;
        
       // dd($storeCounts);
        foreach ($dates as $date) {
            
                
            
            
             
            $userActivities = $activities[$id][$date] ?? collect();
            //dd($userActivities);
            $firstCall = $userActivities->first()->time ?? '';
            
            $lastCall = $userActivities->last()->time ?? '';

            if ($firstCall && $lastCall) {
                $firstCallTimestamp = strtotime(date('Y-m-d') . ' ' . $firstCall);
                
                $lastCallTimestamp = strtotime(date('Y-m-d') . ' ' . $lastCall);
                //dd($lastCallTimestamp);
                if ($lastCallTimestamp < $firstCallTimestamp) {
                    $lastCallTimestamp += 86400; // Add 24 hours
                }
                $hours = floor(($lastCallTimestamp - $firstCallTimestamp) / 3600); // Get full hours
                $minutes = round((($lastCallTimestamp - $firstCallTimestamp) % 3600) / 60); // Get remaining minutes

                $totalHrs = $hours . '.' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
              
            } else {
                $totalHrs = 0;
            }
            
             $tc = $userActivities->count();
            
            $pc = isset($orders[$id][$date]) ? $orders[$id][$date]->count() : 0;
            
            $qty = isset($orderProducts[$id][$date]) ? $orderProducts[$id][$date]->sum('qty') : 0;

            if ($tc > 0) {
                $retailingDays++;
            }

            $row = array_merge($row, [
                $firstCall ?: '',
                $lastCall ?: '',
                $totalHrs,
                $tc,
                $pc,
                $qty
            ]);

            $totalTC += $tc;
            $totalPC += $pc;
            $totalQty += $qty;
            $totalWorkingHrs += $totalHrs;
        }

        $avgWorkingHrs = $retailingDays > 0 ? round($totalWorkingHrs / $retailingDays, 0) : 0;
        $avgTC = $retailingDays > 0 ? round($totalTC / $retailingDays, 0) : 0;
        $avgPC = $retailingDays > 0 ? round($totalPC / $retailingDays, 0) : 0;
        $productivity = $totalTC > 0 ? round(($totalPC / $totalTC) * 100, 0) . '%' : '0%';
        $avgOrder = $retailingDays > 0 ? round($totalQty / $retailingDays, 0) : 0;

        $row = array_merge($row, [
            $totalTC,
            $totalPC,
            $totalQty,
            $totalWorkingHrs,
            $avgWorkingHrs,
            $retailingDays,
            $avgTC,
            $avgPC,
            $productivity,
            $avgOrder
        ]);

        $data[] = $row;
    }
    
    // Generate CSV file
        $output = fopen('php://output', 'w');
        ob_start();
        foreach ($headers as $header) {
            fputcsv($output, $header);
        }
    
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
    
        fclose($output);
        $csvData = ob_get_clean();
    
        // Return CSV download response
        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="SalesManWiseProductivityReport.csv"');
   }
}
