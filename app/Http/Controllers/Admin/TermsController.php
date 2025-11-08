<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Models\RewardTerms;
class TermsController extends Controller
{
    public function index(Request $request)
    {
        $data=DB::table('reward_terms')->latest('id')->first();
        return view('admin.terms.index', compact('data'));
    }
	
	
	public function update(Request $request)
    {
        //dd($request->all());
       
        $params = $request->except('_token');
        
        
         // slug generate
       
        $storeData =  RewardTerms::findOrFail($request->id);
        $storeData->terms = $request['terms'];
        $storeData->save();
        
        if ($storeData) {
            return redirect()->back()->with('success', 'terms and condition content updated');
        } else {
            return redirect()->back()->withInput($request->all())->with('failure', 'Something happened');
        }
    }
}
