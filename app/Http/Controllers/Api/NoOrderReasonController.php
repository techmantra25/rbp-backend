<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NoOrderReason;
use Illuminate\Support\Facades\Validator;
use App\Models\UserNoOrderReason;
class NoOrderReasonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $noorder=NoOrderReason::all();
        if ($noorder) {
            return response()->json(['error'=>false, 'resp'=>'No Order Reason data fetched successfully','data'=>$noorder]);
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
    public function show($id,$userId)
    {
        $noOrder=UserNoOrderReason::where('store_id', $id)->where('user_id',$userId)->with('stores')->orderby('id','desc')->get();
		if ($noOrder) {
        return response()->json(['error'=>false, 'resp'=>'no order list data fetched successfully','data'=>$noOrder]);
		}else{
			  return response()->json(['error' => true, 'message' => 'No data found']);
		}
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
    // public function update(Request $request)
    // {
    //     $validator = Validator::make($request->all(),[
    //         'user_id' => 'required|integer',
    //         'store_id' => 'required|integer',
    //         'no_order_reason_id' => 'required|integer',
    //         'comment' => 'required',
    //         'location' => 'required',
    //         'lat' => 'required',
    //         'lng' => 'required',
    //         'date' => 'required',
    //         'time' => 'required',
    //     ]);

    //     if(!$validator->fails()){
    //         $noorder = new UserNoorderreason();
    //         $noorder->user_id = $request['user_id'];
    //         $noorder->store_id = $request['store_id'];
    //         $noorder->no_order_reason_id = $request['no_order_reason_id'];
    //         $noorder->comment	 = $request['comment'];
    //         $noorder->description	 = $request['description'];
    //         $noorder->location = $request['location'];
    //         $noorder->lat = $request['lat'];
    //         $noorder->lng = $request['lng'];
    //         $noorder->date	 = $request['date'];
    //         $noorder->time	 = $request['time'];
    //         $noorder->save();
    //         return response()->json(['error'=>false, 'resp'=>'No order Reason data created successfully','data'=>$noorder]);
    //     }else {
    //         return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
    //     }
    // }
    public function update(Request $request)
{
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|integer',
        'store_id' => 'required|integer',
        'no_order_reason_id' => 'required|integer',
        'comment' => 'required|string',
        'description' => 'nullable|string',
        'location' => 'required|string',
        'lat' => 'required|numeric',
        'lng' => 'required|numeric',
        'date' => 'required|date',
        'time' => 'required'
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
    }

    // Use mass assignment for cleaner code
    $noorder = UserNoorderreason::create($validator->validated());

    return response()->json([
        'error' => false, 
        'resp' => 'No order reason data created successfully', 
        'data' => $noorder
    ]);
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
}
