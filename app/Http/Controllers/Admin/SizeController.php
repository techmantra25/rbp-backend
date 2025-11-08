<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Size;
class SizeController extends Controller
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
            $data=Size::where('name',$request->term)->latest('id')->paginate(30);
        }else{
            $data=Size::latest('id')->paginate(30);
        }
        return view('admin.size.index', compact('data','request'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.size.create');
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
            
        ]);
        $collection = $request->except('_token');
        $data = new Size;
        $data->name = $collection['name'];
        $data->save();
        
        if ($data) {
            return redirect()->route('admin.sizes.index');
        } else {
            return redirect()->route('admin.sizes.create')->withInput($request->all());
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
        $data=Size::where('id',$id)->first();
        return view('admin.size.detail',compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data=Size::findOrfail($id);
        return view('admin.size.edit',compact('data'));
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
        $data =  Size::findOrfail($id);
        $data->name = $collection['name'];
        $data->save();
        
        if ($data) {
            return redirect()->route('admin.sizes.index');
        } else {
            return redirect()->route('admin.sizes.create')->withInput($request->all());
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
        
        $isReferenced = DB::table('order_products')->where('size_id', $id)->exists();
    
        if ($isReferenced) {
            return redirect()->route('admin.sizes.index')->with('error', 'Size cannot be deleted because it is referenced in another table.');
        }
        $data=Size::destroy($id);
        if ($data) {
            return redirect()->route('admin.sizes.index')->with('success', 'Size deleted successfully.');
        } else {
            return redirect()->route('admin.sizes.index')->with('error', 'Failed to delete size.');
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
        $data = Size::findOrFail($id);
        $status = ( $data->status == 1 ) ? 0 : 1;
        $data->status = $status;
        $data->save();
        if ($data) {
            return redirect()->route('admin.sizes.index');
        } else {
            return redirect()->route('admin.sizes.create')->withInput($request->all());
        }
    }
}
