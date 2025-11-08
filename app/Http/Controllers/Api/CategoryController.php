<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\BrandingVideo;
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cat=Category::where('status',1)->orderby('position')->get();
        if ($cat) {
            return response()->json(['error'=>false, 'resp'=>'Category List fetched successfully','data'=>$cat]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
    
    //for dsm
     public function categoryFetch()
    {
        $cat=Category::where('status',1)->orderby('position')->get();
        if ($cat) {
            return response()->json($cat);
        } else {
            return response()->json(['resp' => 'Something happened']);
        }
    }
    
    
    public function brandingVideo()
    {
        $cat=BrandingVideo::orderby('id','desc')->get();
        if ($cat) {
            return response()->json(['error'=>false, 'resp'=>'Branding video List fetched successfully','data'=>$cat]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }
    }
    
}
