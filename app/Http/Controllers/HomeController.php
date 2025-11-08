<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\User;
use Auth;
use App\Models\Activity;
use DB;
use Carbon\Carbon;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
		$loggedInUserId = Auth::guard('web')->user()->id;
        $loggedInUser = Auth::guard('web')->user()->name;
        $loggedInUserType = Auth::guard('web')->user()->type;
        $loggedInUserState = Auth::guard('web')->user()->state;

        switch ($loggedInUserType) {
            case 1: $userTypeDetail = "NSM";break;
            case 2: $userTypeDetail = "ZSM";break;
            case 3: $userTypeDetail = "RSM";break;
            case 4: $userTypeDetail = "SM";break;
            case 5: $userTypeDetail = "ASM";break;
            case 6: $userTypeDetail = "ASE";break;
            default: $userTypeDetail = "";break;
        }

 
        // only for 2: RSM
        if ($loggedInUserType == 2) {
            
            $aseDetails = Team::select('users.id')->join('users', 'teams.ase_id', '=', 'users.id')->where('teams.zsm_id', '=', $loggedInUserId)->groupby('teams.ase_id')->orderby('teams.ase_id')->get()->pluck('id')->toArray();
                   
           $activeASEreport=Activity::where('type','Visit Started')->whereDate('created_at', '=', Carbon::now())->whereIn('user_id',$aseDetails)->pluck('user_id')->toArray();
                   
           $inactiveASE=Team::select(DB::raw("users.id as id"),DB::raw("users.name as name"),DB::raw("users.mobile as mobile"),DB::raw("users.state as state"),DB::raw("users.city as city"))->join('users', 'teams.ase_id', '=', 'users.id')->where('teams.zsm_id', '=', $loggedInUserId)->whereNotIn('users.id',$activeASEreport)->groupby('teams.ase_id')->orderby('teams.ase_id')->get();

            
        }
        else{
            $aseDetails = Team::select('users.id')->join('users', 'teams.ase_id', '=', 'users.id')->where('teams.rsm_id', '=', $loggedInUserId)->groupby('teams.ase_id')->orderby('teams.ase_id')->get()->pluck('id')->toArray();
                   
           $activeASEreport=Activity::where('type','Visit Started')->whereDate('created_at', '=', Carbon::now())->whereIn('user_id',$aseDetails)->pluck('user_id')->toArray();
                   
           $inactiveASE=Team::select(DB::raw("users.id as id"),DB::raw("users.name as name"),DB::raw("users.mobile as mobile"),DB::raw("users.state as state"),DB::raw("users.city as city"))->join('users', 'teams.ase_id', '=', 'users.id')->where('teams.rsm_id', '=', $loggedInUserId)->whereNotIn('users.id',$activeASEreport)->groupby('teams.ase_id')->orderby('teams.ase_id')->get();

           
        }
        return view('home', compact('userTypeDetail', 'inactiveASE',  'loggedInUserState'));

    }
    
    
    public function test(Request $request)
    {
        $data=DB::table('SELECT code
FROM retailer_barcodes
WHERE code NOT IN (SELECT barcode FROM retailer_wallet_txns);');
dd($data);
    }
}
