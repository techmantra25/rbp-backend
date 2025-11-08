<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Scheme;
use App\Models\State;
class OfferController extends Controller
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
            $data=Scheme::where('name',$request->term)->latest('id')->paginate(30);
        }else{
            $data=Scheme::latest('id')->paginate(30);
        }
        return view('admin.scheme.index', compact('data','request'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $states=State::orderby('name')->get();
        return view('admin.scheme.create',compact('states','request'));
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
            "image" => "nullable|mimes:jpg,jpeg,png,svg,gif|max:10000000"
        ]);
        $collection = $request->except('_token');
        $upload_path = "public/uploads/scheme/";
        $data = new Scheme;
        $data->name = $collection['name'];
        $data->state_id = implode(',',$collection['state']);
        $data->start_date = $collection['start_date'];
        $data->end_date = $collection['end_date'];
            //  image
            if(isset($collection['image'])){
                $image = $collection['image'];
                $imageName = time().".".mt_rand().".".$image->getClientOriginalName();
                $image->move($upload_path, $imageName);
                $uploadedImage = $imageName;
                $data->image = $upload_path.$uploadedImage;
            }
            // pdf
            if(isset($collection['pdf'])){
                $image = $collection['pdf'];
                $imageName = time().".".mt_rand().".".$image->getClientOriginalName();
                $image->move($upload_path, $imageName);
                $uploadedImage = $imageName;
                $data->pdf = $upload_path.$uploadedImage;
            }
           
            $data->save();
        
        if ($data) {
            return redirect()->route('admin.schemes.index');
        } else {
            return redirect()->route('admin.schemes.create')->withInput($request->all());
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
        $data=Scheme::where('id',$id)->first();
        return view('admin.scheme.detail',compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data=Scheme::findOrfail($id);
         $states=State::orderby('name')->get();
        return view('admin.scheme.edit',compact('data','states'));
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
            "image" => "nullable|mimes:jpg,jpeg,png,svg,gif|max:10000000"
        ]);
        $collection = $request->except('_token');
        $upload_path = "public/uploads/scheme/";
        $data = Scheme::findOrfail($id);
        $data->name = $collection['name'];
         $data->state_id = implode(',',$collection['state']);
        $data->start_date = $collection['start_date'];
        $data->end_date = $collection['end_date'];
        //  image
        if(isset($collection['image'])){
            $image = $collection['image'];
            $imageName = time().".".mt_rand().".".$image->getClientOriginalName();
            $image->move($upload_path, $imageName);
            $uploadedImage = $imageName;
            $data->image = $upload_path.$uploadedImage;
        }
        // pdf
        if(isset($collection['pdf'])){
            $image = $collection['pdf'];
            $imageName = time().".".mt_rand().".".$image->getClientOriginalName();
            $image->move($upload_path, $imageName);
            $uploadedImage = $imageName;
            $data->pdf = $upload_path.$uploadedImage;
        }
       
        $data->save();
        
        if ($data) {
            return redirect()->route('admin.schemes.index');
        } else {
            return redirect()->route('admin.schemes.create')->withInput($request->all());
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
        $data=Scheme::destroy($id);
        if ($data) {
            return redirect()->route('admin.schemes.index');
        } else {
            return redirect()->route('admin.schemes.index')->withInput($request->all());
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
        $data = Scheme::findOrFail($id);
        $status = ( $data->status == 1 ) ? 0 : 1;
        $data->status = $status;
        $data->save();
        if ($data) {
            return redirect()->route('admin.schemes.index');
        } else {
            return redirect()->route('admin.schemes.create')->withInput($request->all());
        }
    }
}
