<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Color;
class ColorController extends Controller
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
            $data=Color::where('name',$request->term)->latest('id')->paginate(30);
        }else{
            $data=Color::latest('id')->paginate(30);
        }
        return view('admin.color.index', compact('data','request'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.color.create');
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
            "code" => "required",
        ]);
        $collection = $request->except('_token');
        $data = new Color;
        $data->name = $collection['name'];
        $data->code = $collection['code'];
        $data->save();
        
        if ($data) {
            return redirect()->route('admin.colors.index');
        } else {
            return redirect()->route('admin.colors.create')->withInput($request->all());
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
        $data=Color::where('id',$id)->first();
        return view('admin.color.detail',compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data=Color::findOrfail($id);
        return view('admin.color.edit',compact('data'));
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
            "code" => "required",
        ]);
        $collection = $request->except('_token');
        $data =  Color::findOrfail($id);
        $data->name = $collection['name'];
        $data->code = $collection['code'];
        $data->save();
        
        if ($data) {
            return redirect()->route('admin.colors.index');
        } else {
            return redirect()->route('admin.colors.create')->withInput($request->all());
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
        
        $isReferenced = DB::table('order_products')->where('color_id', $id)->exists();
    
        if ($isReferenced) {
            return redirect()->route('admin.colors.index')->with('error', 'Color cannot be deleted because it is referenced in another table.');
        }
        $data=Color::destroy($id);
        if ($data) {
            return redirect()->route('admin.colors.index')->with('success', 'Color deleted successfully.');
        } else {
            return redirect()->route('admin.colors.index')->with('error', 'Failed to delete color.');
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
        $data = Color::findOrFail($id);
        $status = ( $data->status == 1 ) ? 0 : 1;
        $data->status = $status;
        $data->save();
        if ($data) {
            return redirect()->route('admin.colors.index');
        } else {
            return redirect()->route('admin.colors.create')->withInput($request->all());
        }
    }
}
