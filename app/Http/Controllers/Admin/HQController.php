<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\State;
use App\Models\HeadQuater;
use App\Models\User;
use App\Models\Team;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class HQController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!empty($request->term)) 
        {
            $data=HeadQuater::where('name','like','%'.$request->term.'%')->latest('id')->with('states')->paginate(30);
        }else{
            $data=HeadQuater::latest('id')->with('states')->paginate(30);
            
        }
        return view('admin.hq.index', compact('data','request'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $states=State::orderby('name')->get();
        return view('admin.hq.create',compact('states','request'));
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
            "name" => "required|string|max:255"
           
            
        ]);
        $collection = $request->except('_token');
        $data = new HeadQuater;
        $data->name = $collection['name'];
        $data->state_id = $collection['state_id'] ?? '';
        $data->save();
        
        if ($data) {
            return redirect()->route('admin.headquaters.index');
        } else {
            return redirect()->route('admin.headquaters.create')->withInput($request->all());
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
        $data=HeadQuater::where('id',$id)->first();
        return view('admin.hq.detail',compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data=HeadQuater::findOrfail($id);
        $states=State::orderby('name')->get();
        return view('admin.hq.edit',compact('data','states'));
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
        $request->validate([
            "name" => "required|string|max:255",
            
        ]);
        $collection = $request->except('_token');
        $data =  HeadQuater::findOrfail($id);
        $data->name = $collection['name'];
        $data->state_id = $collection['cat_id'] ?? '';
        $data->save();
        
        if ($data) {
            return redirect()->route('admin.headquaters.index');
        } else {
            return redirect()->route('admin.headquaters.create')->withInput($request->all());
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
        $data=HeadQuater::destroy($id);
        if ($data) {
            return redirect()->route('admin.headquaters.index');
        } else {
            return redirect()->route('admin.headquaters.index')->withInput($request->all());
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
        $data = HeadQuater::findOrFail($id);
        $status = ( $data->status == 1 ) ? 0 : 1;
        $data->status = $status;
        $data->save();
        if ($data) {
            return redirect()->route('admin.headquaters.index');
        } else {
            return redirect()->route('admin.headquaters.create')->withInput($request->all());
        }
    }
}
