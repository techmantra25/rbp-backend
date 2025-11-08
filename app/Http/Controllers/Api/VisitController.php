<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\UserArea;
use App\Models\User;
use App\Models\UserAttendance;
use App\Models\Visit;
use App\Models\DistributorVisit;
use DB;
use Illuminate\Support\Facades\Validator;
class VisitController extends Controller
{
    // store visit start
	public function visitStart(Request $request)
	{
		$validator = Validator::make($request->all(), [
            "user_id" => "required",
            "distributor_id" => "required",
            "area_id" => "required",
            "start_date" => "required",
            "start_time" => "required",
            "start_location" => "nullable",
            "start_lat" => "nullable",
            "start_lon" => "nullable",
        ]);

        if (!$validator->fails()) {
            $data = [
                "user_id" => $request->user_id,
                "distributor_id" => $request->distributor_id,
                "area_id" => $request->area_id,
                "start_date" => $request->start_date,
                "start_time" => $request->start_time,
                "start_location" => $request->start_location,
                "start_lat" => $request->start_lat,
                "start_lon" => $request->start_lon,
                "created_at" => date('Y-m-d H:i:s'),
            ];

            $resp = DB::table('visits')->insertGetId($data);
            $record=UserAttendance::where('user_id',$request->user_id)->where('entry_date',$request->start_date)->first();
            if(empty($record))
            {
                $attendance=new UserAttendance();
                $attendance->user_id=$request->user_id;
                $attendance->entry_date=$request->start_date;
                $attendance->start_time=$request->start_time;
                $attendance->type='store-visit';
                $attendance->save();
            }
            return response()->json(['error' => false, 'resp' => 'Visit started', 'visit_id' => $resp]);
        } else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }
	}

    // store visit end
	public function visitEnd(Request $request)
	{
		$validator = Validator::make($request->all(), [
            "visit_id" => "required",
            "end_date" => "required",
            "end_time" => "required",
            "end_location" => "nullable",
            "end_lat" => "nullable",
            "end_lon" => "nullable",
        ]);

        if (!$validator->fails()) {
            $data = [
                "visit_id" => $request->visit_id,
                "end_date" => $request->end_date,
                "end_time" => $request->end_time,
                "end_location" => $request->end_location,
                "end_lat" => $request->end_lat,
                "end_lon" => $request->end_lon,
                "updated_at" => date('Y-m-d H:i:s'),
            ];

            DB::table('visits')->where('id', $request->visit_id)->update($data);
            $record=UserAttendance::where('user_id',$request->user_id)->where('entry_date',$request->end_date)->first();
            if(!empty($record))
            {
                $attendance=UserAttendance::findOrfail($record->id);
                $attendance->end_time=$request->end_time;
                $attendance->save();
            }
            return response()->json(['error' => false, 'resp' => 'Visit ended', 'data' => $data]);
        } else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }
	}


    //check visit started or not

    public function checkVisit(Request $request, $id)
    {
        $today = now()->toDateString();
    
        // Fetch visit record with related area details in a single query
        $visit = Visit::where('user_id', $id)
            ->whereDate('start_date', $today)
            ->whereNull('visit_id')
            ->with('areas:id,name') // Load only required area details
            ->latest('id')
            ->first();
    
        // Fetch only necessary user details
        $user = User::select('id', 'name')->find($id);
    
        if (!$visit) {
            return response()->json(['error' => true, 'resp' => 'Start Your Visit']);
        }
    
        return response()->json([
            'error' => false,
            'resp' => 'Visit already started',
            'area' => $visit->areas->id ?? null,
            'area_name' => $visit->areas->name ?? null,
            'visit_id' => $visit->id,
            'user' => $user
        ]);
    }


    //activity list
    public function activityList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|integer",
            "date" => "required|date",
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }
    
        $user_id = $request->input('user_id');
        $date = $request->input('date');
    
        // Fetch activities directly without unnecessary object casting
        $activities = Activity::where('user_id', $user_id)
            ->whereDate('date', $date)
            ->latest('id')
            ->get();
    
        if ($activities->isEmpty()) {
            return response()->json(['error' => true, 'resp' => 'No data found']);
        }
    
        return response()->json(['error' => false, 'resp' => 'Activity List', 'data' => $activities]);
    }


    //activity create

    public function activityStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required",
            "date" => "required",
            "time" => "required",
            "type" => "required",
            "comment" => "nullable",
            "location" => "nullable",
            "lat" => "nullable",
        ]);

        if (!$validator->fails()) {
            $data = [
                "user_id" => $request->user_id,
                "store_id" => $request->store_id ?? NULL,
                "date" => $request->date,
                "time" => $request->time,
                "type" => $request->type,
                "comment" => $request->comment,
                "location" => $request->location,
                "lat" => $request->lat,
                "lng" => $request->lng,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ];

            $resp = DB::table('activities')->insertGetId($data);
            if( $resp){
                return response()->json(['error' => false, 'resp' => 'Activity stored successfully', 'data' => $resp]);
            }else{
                return response()->json(['error'=>true, 'resp'=>'Something happend']);
            }
           
        } else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }
    }

     //area list
     public function areaList(Request $request, $id)
    {
        $data = UserArea::where('user_id', $id)
            ->with('areas:id,name')
            ->select('area_id') // Select only necessary columns
            ->groupBy('area_id')
            ->get();
    
        if ($data->isEmpty()) {
            return response()->json(['error' => true, 'resp' => 'No data found']);
        }
    
        return response()->json(['error' => false, 'resp' => 'Area List', 'data' => $data]);
    }

     
     
     //other activity
     public function otheractivityStore(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            "user_id" => "required",
            "date" => "required",
            "time" => "required",
            "type" => "required",
            "reason" => "required"
        ]);

        if (!$validator->fails()) {
            $data = [
                "user_id" => $request->user_id,
                "date" => $request->date,
                "time" => $request->time,
                "type" => $request->type,
                "reason" => $request->reason,
				"lat" => $request->lat,
				"lng" => $request->lng,
				"location" => $request->location,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ];

            $resp = DB::table('other_activities')->insertGetId($data);
            $record=UserAttendance::where('user_id',$request->user_id)->where('entry_date',$request->date)->first();
            if(empty($record))
            {
                $attendance=new UserAttendance();
                $attendance->user_id=$request->user_id;
                $attendance->entry_date=$request->date;
                $attendance->start_time=$request->time;
                $attendance->type=$request->type;
                $attendance->other_activities_id=$resp;
                $attendance->save();
            }
            if( $resp){
                return response()->json(['error' => false, 'resp' => 'Activity stored successfully', 'data' => $resp]);
            }else{
                return response()->json(['error'=>true, 'resp'=>'Something happend']);
            }
           
        } else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }
    }
	
	
	//distributor visit
	 // store visit start
	public function distributorvisitStart(Request $request)
	{
		$validator = Validator::make($request->all(), [
            "user_id" => "required",
			"distributor_id" => "required",
            "area_id" => "required",
            "start_date" => "required",
            "start_time" => "required",
            "start_location" => "nullable",
            "start_lat" => "nullable",
            "start_lon" => "nullable",
        ]);

        if (!$validator->fails()) {
            $data = [
                "user_id" => $request->user_id,
				"distributor_id" => $request->distributor_id,
                "area_id" => $request->area_id,
                "start_date" => $request->start_date,
                "start_time" => $request->start_time,
                "start_location" => $request->start_location,
                "start_lat" => $request->start_lat,
                "start_lon" => $request->start_lon,
                "created_at" => date('Y-m-d H:i:s'),
            ];

            $resp = DB::table('distributor_visits')->insertGetId($data);
            $record=UserAttendance::where('user_id',$request->user_id)->where('entry_date',$request->start_date)->first();
            if(empty($record))
            {
                $attendance=new UserAttendance();
                $attendance->user_id=$request->user_id;
                $attendance->entry_date=$request->start_date;
                $attendance->start_time=$request->start_time;
                $attendance->type='distributor-visit';
                $attendance->save();
            }
            return response()->json(['error' => false, 'resp' => 'Visit started', 'visit_id' => $resp]);
        } else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }
	}

    // store visit end
	public function distributorvisitEnd(Request $request)
	{
		$validator = Validator::make($request->all(), [
            "visit_id" => "required",
            "end_date" => "required",
            "end_time" => "required",
            "end_location" => "nullable",
            "end_lat" => "nullable",
            "end_lon" => "nullable",
        ]);

        if (!$validator->fails()) {
            $data = [
                "visit_id" => $request->visit_id,
                "end_date" => $request->end_date,
                "end_time" => $request->end_time,
                "end_location" => $request->end_location,
                "end_lat" => $request->end_lat,
                "end_lon" => $request->end_lon,
                "updated_at" => date('Y-m-d H:i:s'),
            ];

            DB::table('distributor_visits')->where('id', $request->visit_id)->update($data);
            $record=UserAttendance::where('user_id',$request->user_id)->where('entry_date',$request->end_date)->first();
            if(!empty($record))
            {
                $attendance=UserAttendance::findOrfail($record->id);
                $attendance->end_time=$request->end_time;
                $attendance->save();
            }
            return response()->json(['error' => false, 'resp' => 'Visit ended', 'data' => $data]);
        } else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }
	}


    //check visit started or not

//     public function checkdistributorVisit(Request $request,$id,$distributorId)
//     {
//         $data = (object)[];
// 		$data->visit=DistributorVisit::where('user_id',$id)->where('distributor_id',$distributorId)->where('start_date',date('Y-m-d'))->where('visit_id',NULL)->orderby('id','desc')->first();
//         $data->user=User::where('id',$id)->first();
//         if (empty($data->visit)) {
//                 return response()->json(['error'=>true, 'resp'=>'Start Your Visit']);
//             } else {
//                 return response()->json(['error'=>false, 'resp'=>'Visit already started','distributor'=>$data->visit->distributors->name,'area'=>$data->visit->areas->id,'area_name'=>$data->visit->areas->name,'visit_id'=>$data->visit->id,'user'=>$data->user]);
//             } 
		
// 	}



    public function checkdistributorVisit(Request $request, $id, $distributorId)
    {
        $today = now()->toDateString();
    
        // Fetch the visit with necessary relationships
        $visit = DistributorVisit::with(['distributors:id,name', 'areas:id,name'])
            ->where('user_id', $id)
            ->where('distributor_id', $distributorId)
            ->whereDate('start_date', $today)
            ->whereNull('visit_id')
            ->latest('id')
            ->first();
    
        if (!$visit) {
            return response()->json(['error' => true, 'resp' => 'Start Your Visit']);
        }
    
        // Fetch the user only if a visit exists
        $user = User::select('id', 'name')->find($id);
    
        return response()->json([
            'error' => false,
            'resp' => 'Visit already started',
            'distributor' => $visit->distributors->name ?? null,
            'area' => $visit->areas->id ?? null,
            'area_name' => $visit->areas->name ?? null,
            'visit_id' => $visit->id,
            'user' => $user,
        ]);
    }

}
