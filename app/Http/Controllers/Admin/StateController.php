<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\State;
class StateController extends Controller
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
            $data=State::where('name',$request->term)->latest('id')->paginate(30);
        }else{
            $data=State::latest('id')->paginate(30);
        }
        return view('admin.state.index', compact('data','request'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.state.create');
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
            "name" => "required|string|max:255",
            "code" => "required|string|max:255",
            
        ]);
        $collection = $request->except('_token');
        $data = new State;
        $data->name = $collection['name'];
        $data->code = $collection['code'];
        $data->save();
        
        if ($data) {
            return redirect()->route('admin.states.index');
        } else {
            return redirect()->route('admin.states.create')->withInput($request->all());
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
        $data=State::where('id',$id)->first();
        return view('admin.state.detail',compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data=State::findOrfail($id);
        return view('admin.state.edit',compact('data'));
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
        $data =  State::findOrfail($id);
        $data->name = $collection['name'];
        $data->code = $collection['code'];
        $data->save();
        
        if ($data) {
            return redirect()->route('admin.states.index');
        } else {
            return redirect()->route('admin.states.create')->withInput($request->all());
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
        // Check if the state ID is referenced in another table
        $isReferenced = DB::table('stores')->where('state_id', $id)->exists();
    
        if ($isReferenced) {
            return redirect()->route('admin.states.index')->with('error', 'State cannot be deleted because it is referenced in another table.');
        }
    
        // If not referenced, proceed with deletion
        $deleted = State::destroy($id);
    
        if ($deleted) {
            return redirect()->route('admin.states.index')->with('success', 'State deleted successfully.');
        } else {
            return redirect()->route('admin.states.index')->with('error', 'Failed to delete state.');
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
        $data = State::findOrFail($id);
        $status = ( $data->status == 1 ) ? 0 : 1;
        $data->status = $status;
        $data->save();
        if ($data) {
            return redirect()->route('admin.states.index');
        } else {
            return redirect()->route('admin.states.create')->withInput($request->all());
        }
    }
}
