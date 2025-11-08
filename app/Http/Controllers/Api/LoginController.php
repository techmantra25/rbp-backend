<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserLogin;
use App\Models\Activity;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|digits:10',
            'password' => 'required'
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }
    
        $mobile = $request->mobile;
        $password = $request->password;
        $today = now()->toDateString();
    
        // Fetch user with only required fields
        $userCheck = User::select('id','name','fname','lname','designation','email','mobile','whatsapp_no','employee_id','state','city','type','gender','is_verified','status','video_link','password')->where('mobile', $mobile)->first();
    
        if (!$userCheck) {
            return response()->json(['error' => true, 'resp' => 'No User Found.']);
        }
    
        // Verify password before proceeding with further queries
        if (!Hash::check($password, $userCheck->password)) {
            return response()->json(['error' => true, 'resp' => 'You have entered wrong login credential. Please try with the correct one.']);
        }
    
        // Check account status
        if ($userCheck->status == 0) {
            return response()->json(['error' => true, 'resp' => 'Your account is temporarily blocked. Contact Admin.']);
        }
    
        // Check leave status using `exists()` instead of `get()` to optimize query
        $is_leave = Activity::where('user_id', $userCheck->id)
            ->where('type', 'leave')
            ->whereDate('date', $today)
            ->exists() ? 1 : 0;
    
        return response()->json([
            'error' => false,
            'resp' => 'Login successful',
            'data' => $userCheck,
            'is_leave' => $is_leave
        ]);
    }
    
    
    
    public function distributorLogin(Request $request)
    {
         $validator = Validator::make($request->all(),[
            'mobile' => 'required|digits:10',
            'password' => 'required'
        ]);

        if(!$validator->fails()){
            $is_leave='';
            $mobile = $request->mobile;
            $password = $request->password;
            $today = now()->toDateString(); 
            $userCheck = User::where('mobile', $mobile)->where('password',$password)->first();
            $leaveCheck=Activity::where('user_id',$userCheck->id)->where('type','leave')->whereDate('date', $today)->get();
            if(count($leaveCheck)>0){
                $is_leave=1;
            }else{
                 $is_leave=0;
            }
            if ($userCheck) {
                //if (Hash::check($password, $userCheck->password)) {
    				$status = $userCheck->status;
    					 if ($status == 0) {
    						return response()->json(['error' => true, 'resp' =>  'Your account is temporary blocked. Contact Admin']);
    					}else{
                         return response()->json(['error' => false, 'resp' => 'Login successful', 'data' => $userCheck,'is_leave'=>$is_leave]);
    					}
               //} else {
                //    return response()->json(['error' => true, 'resp' => 'You have entered wrong login credential. Please try with the correct one.']);
               // }
            } else {
                return response()->json(['error' => true, 'resp' => 'No User Found.']);
            }
        }else {
             return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkLogin($id)
    {
        $today = now()->toDateString();
    
        // Fetch user login, user details, and leave status in a single query where possible
        $login = UserLogin::where('user_id', $id)->latest('id')->first();
        $user = User::select('id', 'name', 'status')->find($id); // Fetch only necessary fields
        $is_leave = Activity::where('user_id', $id)
            ->where('type', 'leave')
            ->whereDate('date', $today)
            ->exists() ? 1 : 0;
    
        // Return optimized response
        return response()->json([
            'error' => false,
            'resp' => $login ? 'Check Login Or Not' : 'No data found',
            'is_login' => $login->is_login ?? 0,
            'user' => $user,
            'is_leave' => $is_leave
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function loginflagStore(Request $request)
    {
        $request->validate([
			"user_id" => "required|integer",
			"is_login" => "required|integer"
		]);

        
            DB::table('user_logins')->insert([
                'user_id' => $request->user_id,
                'is_login' => $request->is_login,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        
		 return response()->json(['error'=>false, 'resp'=>'Login flag updated successfully']);
    }
    
    
    
    public function checkLeave($id)
    {
        $today = now()->toDateString(); 
        $data = (object)[];
		$data->leave=Activity::where('user_id',$id)->where('type','leave')->whereDate('date', $today)->get();
		if(count($data->leave)>0){
                $is_leave=1;
            }else{
                 $is_leave=0;
            }
       
        
            return response()->json(['error'=>false, 'resp'=>'Check Leave Or Not','is_leave'=>$is_leave]);
        
    }

}
