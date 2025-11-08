<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Category;
use App\Models\Product;
use App\Models\Color;
use App\Models\Size;
use App\Models\State;
use App\Models\Store;
use App\Models\User;
use App\Models\Team;
use App\Models\StoreOld;
use App\Models\UserLogin;
use App\Models\Activity;
use App\Models\Visit;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use DB;
use Illuminate\Support\Facades\Auth;
class ReportController extends Controller
{
    //store wise order report
    public function index(Request $request)
    {
        $loggedInUserType = Auth::guard('web')->user()->type;
        	$loggedInUserId = Auth::guard('web')->user()->id;
        if ($loggedInUserType == 2) {
        $from =  date('Y-m-01');
        $to =  date('Y-m-d', strtotime('+1 day'));
        if (isset($request->date_from) || isset($request->date_to) || isset($request->term) || isset($request->user_id) || isset($request->store_id)|| isset($request->state_id)|| isset($request->area_id)|| isset($request->distributor_id)) {
            

            $date_from = $request->date_from ? $request->date_from : '';
            $date_to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
            $term = $request->term ? $request->term : '';
            $user_id = $request->user_id ? $request->user_id : '';
            $store_id = $request->store_id ? $request->store_id : '';
            $state_id = $request->state_id ? $request->state_id : '';
            $area_id = $request->area_id ? $request->area_id : '';
            $distributor_id = $request->distributor_id ? $request->distributor_id : '';
            $query = Order::select('orders.order_no','orders.id','orders.user_id','orders.store_id','orders.order_type','orders.comment','stores.name','orders.created_at','teams.distributor_id','users.name')->join('users', 'users.id', 'orders.user_id')->join('stores', 'stores.id', 'orders.store_id')->join('teams', 'stores.id', 'teams.store_id');

        
            $query->when($user_id, function($query) use ($user_id) {
                $query->where('orders.user_id', $user_id);
            });
            $query->when($store_id, function($query) use ($store_id) {
                $query->where('orders.store_id', $store_id);
            });
            $query->when($state_id, function($query) use ($state_id) {
                $query->where('stores.state_id', $state_id);
            });
            $query->when($area_id, function($query) use ($area_id) {
                $query->where('stores.area_id', $area_id);
            });
            $query->when($distributor_id, function($query) use ($distributor_id) {
                $query->join('users', 'users.id', 'teams.distributor_id')->where('users.id', $distributor_id);
            });
            $query->when($query, function($query) use ($term) {
                $query->where('orders.order_no', 'like', '%'.$term.'%');
            })->whereBetween('orders.created_at', [$date_from, $date_to]);

            $data = $query->where('teams.zsm_id',Auth::guard('web')->user()->id)->latest('orders.id')->paginate(25);
            
        } else {
            $data = Order::select('orders.*')->join('stores', 'stores.id', 'orders.store_id')->join('teams', 'stores.id', 'teams.store_id')->where('teams.zsm_id',Auth::guard('web')->user()->id)->orderBy('id', 'desc')->latest('id')->paginate(25);
        }
        $user = User::select('users.id', 'users.name')->join('teams', 'users.id', 'teams.ase_id')->where('teams.zsm_id',Auth::guard('web')->user()->id)->where('users.type', 6)->orWhere('users.type',5)->where('users.status',1)->orderBy('users.name')->groupBy('users.name')->get();
        $stores = Store::select('stores.id', 'stores.name')->join('teams', 'stores.id', 'teams.store_id')->where('teams.zsm_id',Auth::guard('web')->user()->id)->where('stores.status',1)->orderBy('stores.name')->get();
        $state = Team::where('zsm_id',$loggedInUserId)->groupBy('state_id')->with('states')->get();
        $distributor = User::select('users.id', 'users.name')->join('teams', 'users.id', 'teams.distributor_id')->where('teams.zsm_id',Auth::guard('web')->user()->id)->where('users.type', 7)->where('users.status',1)->orderBy('users.name')->groupBy('users.name')->get();
        }else{
            $from =  date('Y-m-01');
            $to =  date('Y-m-d', strtotime('+1 day'));
            if (isset($request->date_from) || isset($request->date_to) || isset($request->term) || isset($request->user_id) || isset($request->store_id)|| isset($request->state_id)|| isset($request->area_id)|| isset($request->distributor_id)) {
                
    
                $date_from = $request->date_from ? $request->date_from : '';
                $date_to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
                $term = $request->term ? $request->term : '';
                $user_id = $request->user_id ? $request->user_id : '';
                $store_id = $request->store_id ? $request->store_id : '';
                $state_id = $request->state_id ? $request->state_id : '';
                $area_id = $request->area_id ? $request->area_id : '';
                $distributor_id = $request->distributor_id ? $request->distributor_id : '';
                $query = Order::select('orders.order_no','orders.id','orders.user_id','orders.store_id','orders.order_type','orders.comment','stores.name','orders.created_at','teams.distributor_id','users.name')->join('users', 'users.id', 'orders.user_id')->join('stores', 'stores.id', 'orders.store_id')->join('teams', 'stores.id', 'teams.store_id');
    
            
                $query->when($user_id, function($query) use ($user_id) {
                    $query->where('orders.user_id', $user_id);
                });
                $query->when($store_id, function($query) use ($store_id) {
                    $query->where('orders.store_id', $store_id);
                });
                $query->when($state_id, function($query) use ($state_id) {
                    $query->where('stores.state_id', $state_id);
                });
                $query->when($area_id, function($query) use ($area_id) {
                    $query->where('stores.area_id', $area_id);
                });
                $query->when($distributor_id, function($query) use ($distributor_id) {
                    $query->join('users', 'users.id', 'teams.distributor_id')->where('users.id', $distributor_id);
                });
                $query->when($query, function($query) use ($term) {
                    $query->where('orders.order_no', 'like', '%'.$term.'%');
                })->whereBetween('orders.created_at', [$date_from, $date_to]);
    
                $data = $query->where('teams.rsm_id',Auth::guard('web')->user()->id)->latest('orders.id')->paginate(25);
                
            } else {
                $data = Order::select('orders.*')->join('stores', 'stores.id', 'orders.store_id')->join('teams', 'stores.id', 'teams.store_id')->where('teams.rsm_id',Auth::guard('web')->user()->id)->orderBy('id', 'desc')->latest('id')->paginate(25);
            }
            $user = User::select('users.id', 'users.name')->join('teams', 'users.id', 'teams.ase_id')->where('teams.rsm_id',Auth::guard('web')->user()->id)->where('users.type', 6)->orWhere('users.type',5)->where('users.status',1)->orderBy('users.name')->groupBy('users.name')->get();
            $stores = Store::select('stores.id', 'stores.name')->join('teams', 'stores.id', 'teams.store_id')->where('teams.rsm_id',Auth::guard('web')->user()->id)->where('stores.status',1)->orderBy('stores.name')->get();
            $state = Team::where('zsm_id',$loggedInUserId)->groupBy('state_id')->with('states')->get();
            $distributor = User::select('users.id', 'users.name')->join('teams', 'users.id', 'teams.distributor_id')->where('teams.rsm_id',Auth::guard('web')->user()->id)->where('users.type', 7)->where('users.status',1)->orderBy('users.name')->groupBy('users.name')->get();
            }
        return view('front.store-report.index', compact('data','request','user','stores','state','distributor'));
    }

    //pdf download for individual order
    public function pdfExport(Request $request, $id)
    {
        $data = Order::findOrfail($id);
        return view('front.store-report.pdf', compact('data'));
    }

    //csv download for individual order
    public function individualcsvExport(Request $request, $id)
    {
        $orderDetails = Order::findOrfail($id);
        $data = orderProductsUpdatedMatrix($orderDetails->orderProducts);
        $childData = orderProductsUpdatedMatrixChild($orderDetails->orderProducts);

        if (count($data) > 0 || count($childData) > 0) {
            $delimiter = ",";
            $filename = "lux-secondary-order-detail-".$orderDetails->order_no."-".date('Y-m-d').".csv";

            // Create a file pointer
            $f = fopen('php://memory', 'w');

            // Set column headers
            $fields = array('Name of Quality Shape & Unit', '75', '80', '85', '90', '95', '100', '105', '110', '115','120', 'Total');
            $childFields = array('Name of Quality Shape & Unit', '35', '40', '45', '50', '55', '60', '65', '70', '75','','Total');

            $count = 1;

            if (count($data) > 0) {
                fputcsv($f, $fields, $delimiter);
                foreach($data as $row) {
					 
                     $row1 = $row['product_name']."\n".$row['product_style_no']."\n".$row['color'];

                    $lineData = array(
                        $row1,
                        $row['75'] ? $row['75'] : '',
                        $row['80'] ? $row['80'] : '',
                        $row['85'] ? $row['85'] : '',
                        $row['90'] ? $row['90'] : '',
                        $row['95'] ? $row['95'] : '',
                        $row['100'] ? $row['100'] : '',
                        $row['105'] ? $row['105'] : '',
                        $row['110'] ? $row['110'] : '',
                        $row['115'] ? $row['115'] : '',
                        $row['120'] ? $row['120'] : '',
                        $row['total']
                    );
                    fputcsv($f, $lineData, $delimiter);
                    $count++;
                }
            }

            if (count($childData) > 0) {
                fputcsv($f, $childFields, $delimiter);
                foreach($childData as $row) {
					 
                    $row2 = $row['product_name']."\n".$row['product_style_no']."\n".$row['color'];

                    $lineData = array(
                        $row2,
                        $row['35'] ? $row['35'] : '',
                        $row['40'] ? $row['40'] : '',
                        $row['45'] ? $row['45'] : '',
                        $row['50'] ? $row['50'] : '',
                        $row['55'] ? $row['55'] : '',
                        $row['60'] ? $row['60'] : '',
                        $row['65'] ? $row['65'] : '',
                        $row['70'] ? $row['70'] : '',
                        $row['75'] ? $row['75'] : '',
						'',
                        $row['total']
                    );

                    fputcsv($f, $lineData, $delimiter);
                    $count++;
                }
            }

            // Move back to beginning of file
            fseek($f, 0);

            // Set headers to download file rather than displayed
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '";');

            //output all remaining data on a file pointer
            fpassthru($f);
        }
    }

    //all order csv export
    public function csvExport(Request $request)
    {
       
        $loggedInUserType = Auth::guard('web')->user()->type;
        if ($loggedInUserType == 2) {
            $from =  date('Y-m-01');
            $to =  date('Y-m-d', strtotime('+1 day'));
            if (isset($request->date_from) || isset($request->date_to) || isset($request->term) || isset($request->user_id) || isset($request->store_id)|| isset($request->state_id)|| isset($request->area_id)|| isset($request->distributor_id)) {
                
    
                $date_from = $request->date_from ? $request->date_from : '';
                $date_to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
                $term = $request->term ? $request->term : '';
                $user_id = $request->user_id ? $request->user_id : '';
                $store_id = $request->store_id ? $request->store_id : '';
                $state_id = $request->state_id ? $request->state_id : '';
                $area_id = $request->area_id ? $request->area_id : '';
                $distributor_id = $request->distributor_id ? $request->distributor_id : '';
                $query = Order::select('orders.order_no','orders.id','orders.user_id','orders.store_id','orders.order_type','orders.comment','stores.name','orders.created_at','teams.distributor_id','users.name','order_products.product_id','order_products.color_id','order_products.size_id','order_products.qty')->join('order_products', 'order_products.order_id', 'orders.id')->join('users', 'users.id', 'orders.user_id')->join('stores', 'stores.id', 'orders.store_id')->join('teams', 'stores.id', 'teams.store_id');
    
            
                $query->when($user_id, function($query) use ($user_id) {
                    $query->where('orders.user_id', $user_id);
                });
                $query->when($store_id, function($query) use ($store_id) {
                    $query->where('orders.store_id', $store_id);
                });
                $query->when($state_id, function($query) use ($state_id) {
                    $query->where('stores.state_id', $state_id);
                });
                $query->when($area_id, function($query) use ($area_id) {
                    $query->where('stores.area_id', $area_id);
                });
                $query->when($distributor_id, function($query) use ($distributor_id) {
                    $query->join('users', 'users.id', 'teams.distributor_id')->where('users.id', $distributor_id);
                });
                $query->when($query, function($query) use ($term) {
                    $query->where('orders.order_no', 'like', '%'.$term.'%');
                })->whereBetween('orders.created_at', [$date_from, $date_to]);
    
                $data = $query->where('teams.zsm_id',Auth::guard('web')->user()->id)->latest('orders.id')->get();
                
            } else {
                $data = Order::select('orders.order_no','orders.id','orders.user_id','orders.store_id','orders.order_type','orders.comment','stores.name','orders.created_at','teams.distributor_id','users.name','order_products.product_id','order_products.color_id','order_products.size_id','order_products.qty')->join('order_products', 'order_products.order_id', 'orders.id')->join('stores', 'stores.id', 'orders.store_id')->join('teams', 'stores.id', 'teams.store_id')->where('teams.zsm_id',Auth::guard('web')->user()->id)->orderBy('id', 'desc')->latest('id')->get();
            }
        
        }else{
            $from =  date('Y-m-01');
            $to =  date('Y-m-d', strtotime('+1 day'));
            if (isset($request->date_from) || isset($request->date_to) || isset($request->term) || isset($request->user_id) || isset($request->store_id)|| isset($request->state_id)|| isset($request->area_id)|| isset($request->distributor_id)) {
                
    
                $date_from = $request->date_from ? $request->date_from : '';
                $date_to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
                $term = $request->term ? $request->term : '';
                $user_id = $request->user_id ? $request->user_id : '';
                $store_id = $request->store_id ? $request->store_id : '';
                $state_id = $request->state_id ? $request->state_id : '';
                $area_id = $request->area_id ? $request->area_id : '';
                $distributor_id = $request->distributor_id ? $request->distributor_id : '';
                $query = Order::select('orders.order_no','orders.id','orders.user_id','orders.store_id','orders.order_type','orders.comment','stores.name','orders.created_at','teams.distributor_id','users.name','order_products.product_id','order_products.color_id','order_products.size_id','order_products.qty')->join('order_products', 'order_products.order_id', 'orders.id')->join('users', 'users.id', 'orders.user_id')->join('stores', 'stores.id', 'orders.store_id')->join('teams', 'stores.id', 'teams.store_id');
    
            
                $query->when($user_id, function($query) use ($user_id) {
                    $query->where('orders.user_id', $user_id);
                });
                $query->when($store_id, function($query) use ($store_id) {
                    $query->where('orders.store_id', $store_id);
                });
                $query->when($state_id, function($query) use ($state_id) {
                    $query->where('stores.state_id', $state_id);
                });
                $query->when($area_id, function($query) use ($area_id) {
                    $query->where('stores.area_id', $area_id);
                });
                $query->when($distributor_id, function($query) use ($distributor_id) {
                    $query->join('users', 'users.id', 'teams.distributor_id')->where('users.id', $distributor_id);
                });
                $query->when($query, function($query) use ($term) {
                    $query->where('orders.order_no', 'like', '%'.$term.'%');
                })->whereBetween('orders.created_at', [$date_from, $date_to]);
    
                $data = $query->where('teams.rsm_id',Auth::guard('web')->user()->id)->latest('orders.id')->get();
                
            } else {
                $data = Order::select('orders.order_no','orders.id','orders.user_id','orders.store_id','orders.order_type','orders.comment','stores.name','orders.created_at','teams.distributor_id','users.name','order_products.product_id','order_products.color_id','order_products.size_id','order_products.qty')->join('order_products', 'order_products.order_id', 'orders.id')->join('stores', 'stores.id', 'orders.store_id')->join('teams', 'stores.id', 'teams.store_id')->where('teams.rsm_id',Auth::guard('web')->user()->id)->orderBy('id', 'desc')->latest('id')->get();
            }
        }
        if (count($data) > 0) {
            $delimiter = ",";
            $filename = "lux-store-wise-sales-for-zsm-".date('Y-m-d').".csv";

            // Create a file pointer
            $f = fopen('php://memory', 'w');

            // Set column headers
            $fields = array('SR', 'ORDER NO','ORDER TYPE', 'STORE','STORE STATE','STORE AREA','PINCODE','DISTRIBUTOR', 'SALES PERSON(ASE/ASM)', 'MOBILE', 'STATE', 'CITY',  'PRODUCT', 'STYLE NO', 'COLOR', 'SIZE', 'QTY', 'DATETIME');
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($data as $row) {
				
                $datetime = date('j F, Y h:i A', strtotime($row['created_at']));
                $productDetails=Product::where('id',$row->product_id)->with('collection','category')->first();
                $color=Color::where('id',$row['color_id'])->first();
                $size=Size::where('id',$row['size_id'])->first();
                $user=Team::where('store_id',$row->store_id)->first();
                $userName=User::where('id',$user->distributor_id)->first();
                $lineData = array(
                    $count,
                    $row['order_no'],
                    $row['order_type'],
                    $row->stores->name ?? '',
                    $row->stores->states->name ?? '',
                    $row->stores->areas->name ?? '',
                    $row->stores->pin ?? '',
                    $userName->name ?? '',
                    $row->users->name ?? 'Self Order',
                    $row->users->mobile ?? '',
                    $row->users->state ?? '',
                    $row->users->city ?? '',
                    
                    $productDetails->name?? '',
                    $productDetails->style_no?? '',
                    $color->name ??'',
                    $size->name??'',
                    $row['qty'],
                    $datetime
                );

                fputcsv($f, $lineData, $delimiter);

                $count++;
            }

            // Move back to beginning of file
            fseek($f, 0);

            // Set headers to download file rather than displayed
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '";');

            //output all remaining data on a file pointer
            fpassthru($f);
        }
    }

      public function logout(Request $request)
    {
        $user=User::whereNotIn('type',[1,4,7])->get();
        foreach($user as $item){
            $login=UserLogin::where('user_id',$item->id)->where('is_login',1)->orderby('id','desc')->first();
            if(!empty($login)){
            $loginData=UserLogin::findOrfail($login->id);
            $loginData->is_login=0;
            $loginData->save();
            }
           
        dd('done');
        
    }
    
    }
    
    
    
    public function endvisit(Request $request)
    {
        $user=User::whereNotIn('type',[1,4,7])->get();
        foreach($user as $item){
            
            
            //$visit=Visit::where('user_id',$item->id)->where('start_date',date('Y-m-d'))->where('visit_id',NULL)->orderby('id','desc')->first();
            $visit=Visit::where('user_id',$item->id)->where('start_date','2024-03-13')->where('visit_id',NULL)->orderby('id','desc')->first();
            //dd($visit);
            if(!empty($visit)){
                $dataSet = [
                    "visit_id" => $visit->id,
                    "end_date" => date('Y-m-d'),
                    "end_time" => date('h:i A'),
                    "end_location" => NULL,
                    "end_lat" => NULL,
                    "end_lon" => NULL,
                    "updated_at" => date('Y-m-d H:i:s'),
            ];

            
             DB::table('visits')->where('id',$visit->id)->update($dataSet);
            }
            $login=Activity::where('user_id',$item->id)->where('type','Visit Ended')->where('date','2024-03-13')->orderby('id','desc')->first();
            if(empty($login)){
                $data = [
                    "user_id" => $item->id,
                    "date" => date('Y-m-d'),
                    "time" => date('h:i A'),
                    "type" => 'Visit Ended',
                    "comment" => $item->name.' has been ended visit',
                    "location" => NULL,
                    "lat" => NULL,
                    "lng" => NULL,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
            ];

                 $resp = DB::table('activities')->insertGetId($data);
                    
            }
            
           
            
        }
                dd('done');
        
    }
     public function StoreVideoUpdate(Request $request)
    { 
        try {
            $StoreOld = Store::select('id', 'video_link', 'upload_status')
                ->where('video_link', 'like', '%gan%')
                ->whereNotNull('video_link')
                ->limit(15)
                ->get();
            $total_count = 0;
            if (count($StoreOld) > 0) {
                foreach ($StoreOld as $k => $item) {
                    try {
                        $videoUrl = $item->video_link;
                        if ($videoUrl) {
                            // Fetch the video content from the URL
                            $response = Http::get($videoUrl);
                            // Check if the request was successful
                            $id = $item->id;
                            if ($response->successful()) {
                                // Get the video content
                                  
                                $videoContent = $response->body();
                                // Generate a unique filename for the video
                                
                                $fileName = $id . rand(10000000, 99999999) . '.' . pathinfo($videoUrl, PATHINFO_EXTENSION);
                                // Define the path to save the video
                                $filePath = 'uploads/retailer_videos/' . $fileName;
    
                                // Ensure the directory exists
                                if (!file_exists(public_path('uploads/retailer_videos'))) {
                                    mkdir(public_path('uploads/retailer_videos'), 0777, true);
                                }
    
                                // Save the video content to the file
                                file_put_contents(public_path($filePath), $videoContent);
                                $total_count += 1;
    
                                // Save the file path to the database (example assumes a `video_path` column in your model)
                                $video = Store::findOrFail($id);
                                $video->video_link = asset($filePath);
                                $video->upload_status = 1;
                                $video->save();
                            } else {
                               $video = Store::findOrFail($id);
                                $video->upload_status = 2;
                                $video->save();
                            }
                        }
                    } catch (\Exception $e) {
                        // Log the error message
                        return response()->json([
                            'success' => true,
                            'message' => $e->getMessage(),
                            'upload_video' => $total_count,
                        ]);
                    }
                }
    
                return response()->json([
                    'success' => true,
                    'message' => 'Video downloaded and saved successfully.',
                    'upload_video' => $total_count,
                ]);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'No videos found for download.',
                'upload_video' => $total_count,
            ]);
    
        } catch (\Exception $e) {
            // Log the error message
            return response()->json([
                'success' => true,
                'message' => $e->getMessage(),
                'upload_video' => $total_count,
            ]);
        }
    }
     public function VideoDownloadInLocal(Request $request)
    { 
        try {
            $StoreOld = Store::select('id', 'video_link', 'download_status', 'unique_code','contact')
                ->where('download_status', 0)
                ->whereNotNull('video_link')
                ->limit(1)
                ->get();
            $total_count = 0;
            if (count($StoreOld) > 0) {
                foreach ($StoreOld as $k => $item) {
                    try {
                        $videoUrl = $item->video_link;
                        if ($videoUrl) {
                            // Fetch the video content from the URL
                            $response = Http::get($videoUrl);
                            // Check if the request was successful
                            $id = $item->id;
                            if ($response->successful()) {
                                 // Generate a filename for the video
                                 $videoContent = $response->body();
                                $fileName = basename($videoUrl);
                                // Generate a unique filename for the video
                                $total_count += 1;
                                
                                // Save the file path to the database (example assumes a `video_path` column in your model)
                                $video = Store::findOrFail($id);
                                $video->download_status = 1;
                                $video->save();
                                $fileName = $item->unique_code.'_'.$item->contact.'.mp4';
                                // Return a response to download the file
                                return response($videoContent)
                                    ->header('Content-Type', 'application/octet-stream')
                                    ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                                    ->header('Content-Length', strlen($videoContent));
                                
                            } else {
                               $video = Store::findOrFail($id);
                                $video->download_status = 2;
                                $video->save();
                            }
                        }
                    } catch (\Exception $e) {
                        // Log the error message
                        return response()->json([
                            'success' => true,
                            'message' => $e->getMessage(),
                            'upload_video' => $total_count,
                        ]);
                    }
                }
                return response()->json([
                    'success' => true,
                    'message' => 'Video downloaded and saved successfully.',
                    'upload_video' => $total_count,
                ]);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'No videos found for download.',
                'upload_video' => $total_count,
            ]);
    
        } catch (\Exception $e) {
            // Log the error message
            return response()->json([
                'success' => true,
                'message' => $e->getMessage(),
                'upload_video' => $total_count,
            ]);
        }
    }
    public function StoreVideoReplace(Request $request){
        try{
              $total_count = 0;
            $update_vd_link = DB::table('update_vd_link')->where('status', 0)->limit(10)->get();
         
           
            if(count($update_vd_link)>0){
                foreach($update_vd_link as $key =>$item){
                   $exist_store = Store::select('id', 'contact', 'video_link')
                    ->where(function($query) use ($item) {
                        $query->where('contact', $item->mobile)
                              ->orWhere('contact_person_phone', $item->mobile)
                              ->orWhere('contact_person_whatsapp', $item->mobile);
                    })
                    ->first();
                        $videoUrl = $item->video_link;
                        $ExistingvideoUrl = $exist_store?$exist_store->video_link:"";
                         if ($ExistingvideoUrl) {
                            // Extract the file path from the URL
                            $filePath = parse_url($ExistingvideoUrl, PHP_URL_PATH);
                            $fullPath = public_path($filePath);
                            // Check if the file exists and delete it
                            if (File::exists($fullPath)) {
                                File::delete($fullPath);
                            }
                        }
                        if ($exist_store && $videoUrl) {
                            // Fetch the video content from the URL
                            $response = Http::get($videoUrl);
                            // Check if the request was successful
                            $id = $exist_store->id;
                            if ($response->successful()) {
                                // Get the video content
                                $videoContent = $response->body();
                                // Generate a unique filename for the video
                                
                                $fileName = $id . rand(10000000, 99999999) . '.' . pathinfo($videoUrl, PATHINFO_EXTENSION);
                                // Define the path to save the video
                                $filePath = 'uploads/retailer_videos/' . $fileName;
    
                                // Ensure the directory exists
                                if (!file_exists(public_path('uploads/retailer_videos'))) {
                                    mkdir(public_path('uploads/retailer_videos'), 0777, true);
                                }
    
                                // Save the video content to the file
                                file_put_contents(public_path($filePath), $videoContent);
                                $total_count += 1;
    
                                // Save the file path to the database (example assumes a `video_path` column in your model)
                                $video = Store::findOrFail($id);
                                $video->video_link = asset($filePath);
                                $video->upload_status = 1;
                                $video->save();
                                $dataSet = [
                                    "status" => 1,
                                    "updated_at" => date('Y-m-d H:i:s'),
                                ];
                                 DB::table('update_vd_link')->where('id',$item->id)->update($dataSet);
                            } else {
                               $video = Store::findOrFail($id);
                                $video->upload_status = 2;
                                $video->save();
                            }
                        }
                }
            }
            return response()->json([
                'success' => true,
                'message' => 'Video downloaded and saved successfully.',
                'upload_video' => $total_count,
            ]);
            
        } catch (\Exception $e){
             return response()->json([
                'success' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function StoreVideoUploadReport(){
         $StoreOld = Store::select('id')->where('upload_status', 0)->whereNotNull('video_link')->get();
         $StoreNew = Store::select('id')->where('upload_status', 1)->get();
        return response()->json([
            'success' => true,
            'pending-data' =>count($StoreOld),
            'update-data' =>count($StoreNew),
        ]);
    }
     
     }