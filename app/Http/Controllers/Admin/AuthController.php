<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\User;
use App\Models\Store; 
use App\Models\Activity; 
use App\Models\OrderProduct; 
use AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{
	protected $redirectTo = '/admin/dashboard';
    /*public function __construct() {
        $this->middleware('guest')->except('logout');
		
    }*/
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //dd('hi');
        return view('admin.auth.login');
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
        
        $request->validate([
            'email' => 'required | string | email | exists:admins',
            'password' => 'required | string'
        ]);
       
        $adminCreds = $request->only('email', 'password');
        
        if ( Auth::guard('admin')->attempt($adminCreds) ) {
          if(Auth::guard('admin')->user()->email=='jyoti.singh@luxcozi.com'){
              return redirect()->route('admin.reward.retailer.order.index');
          }else{
            return redirect()->route('admin.dashboard');
          }
        } else {
            return redirect()->route('admin.login')->withInputs($request->all())->with('failure', 'Invalid credentials. Try again');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        
        $data = (object)[];
        $data->nsm = User::select('name')->where('type',1)->where('mobile','!=','')->get();
        $data->zsm = User::select('name')->where('type',2)->where('mobile','!=','')->get();
        $data->rsm = User::select('name')->where('type',3)->where('mobile','!=','')->get();
        $data->sm = User::select('name')->where('type',4)->where('mobile','!=','')->get();
        $data->asm = User::select('name')->where('type',5)->where('mobile','!=','')->get();
        $data->ase = User::select('name')->where('type',6)->where('mobile','!=','')->get();
        $data->distributor =User::select('name')->where('type',7)->get();
        $data->store = Store::where('status','=', 1)->count();
        $data->secondary = OrderProduct::where('created_at', '>', date('Y-m-d'))->sum('qty');
		$user=User::where('type',6)->get()->pluck('id')->toArray();

		$activeASEreport=Activity::where('type','Visit Started')->where('created_at', '>', date('Y-m-d'))->whereIn('user_id',$user)->pluck('user_id')->toArray();
						//dd($inactiveASEreport);
		$inactiveASE=User::where('type',6)->whereNotIn('id',$activeASEreport)
			->get();
        return view('admin.dashboard.index', compact('data','inactiveASE'));
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
    public function update(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "email" => "nullable|string",
           
        ]);
        $updateRequest = $request->all();
        $id = Auth::guard('admin')->user()->id;
		$admin = Admin::findOrfail($id);
        $collection = collect($request)->except('_token');
        if ($collection->has('name')) {
            $admin->name = $collection['name'];
            $admin->save();
        }
        return redirect()->back()->with('success','Profile updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->flush();
        $request->session()->regenerate();
        return redirect()->guest(route('admin.login'));
    }
	
	//show my profile
     public function profile(Request $request)
    {
		$profile = Admin::findOrFail(Auth::guard('admin')->user()->id);
        return view('admin.profile.index', compact('profile'));
    }

   
	
	 public function changePassword(Request $request) {
        $request->validate([
            "current_password" => "required|string|min:6|",
            "new_password" => "required|string|min:6|",
            "new_confirm_password" => "required|string|min:6|",
        ]);
        $id = Auth::guard('admin')->user()->id;
        $info =  Admin::findOrfail($id);
		$collection = collect($request)->except('_token');
        if ($collection->has('current_password')) {
            $info->update(['password'=> Hash::make($collection['new_password'])]);
        }

        if ($info) {
			Auth::guard('admin')->logout();
			$request->session()->flush();
			$request->session()->regenerate();
        	return redirect()->guest(route('admin.login'))->with('success','Password updated successfully.' );
          // return redirect()->back()->with('success','Password updated successfully.' );
        } else {
			return redirect()->back()->with('failure','failed' );
        }
    }

}
