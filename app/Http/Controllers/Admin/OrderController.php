<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use League\Csv\Writer;
use App\Models\OrderProduct;
use App\Models\Category;
use App\Models\Product;
use App\Models\Color;
use App\Models\Size;
use App\Models\State;
use App\Models\Area;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Response;
use App\Models\Team;
use App\Models\UserLogin;
use App\Models\UserNoOrderReason;
use App\Models\OrderDistributor;
use DB;
use Illuminate\Support\Facades\Storage;
class OrderController extends Controller
{
    //store wise order report
    // public function index(Request $request)
    // {
       
    //     if (isset($request->date_from) || isset($request->date_to) || isset($request->term) || isset($request->user_id) || isset($request->store_id)|| isset($request->state_id)|| isset($request->area_id)|| isset($request->distributor_id)) {
            

    //         $date_from = $request->date_from ? $request->date_from : '';
    //         $date_to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
            
    //         $term = $request->term ? $request->term : '';
    //         $user_id = $request->user_id ? $request->user_id : '';
    //         $store_id = $request->store_id ? $request->store_id : '';
    //         $state_id = $request->state_id ? $request->state_id : '';
    //         $area_id = $request->area_id ? $request->area_id : '';
    //         $distributor_id = $request->distributor_id ? $request->distributor_id : '';
    //         $query = Order::select('orders.order_no','orders.id','orders.user_id','orders.distributor_id AS distributor','orders.store_id','orders.order_type','orders.comment','stores.name','orders.created_at','teams.distributor_id')->join('stores', 'stores.id', 'orders.store_id')->join('teams', 'stores.id', 'teams.store_id');

        
    //         $query->when($user_id, function($query) use ($user_id) {
    //             $query->where('orders.user_id', $user_id);
    //         });
    //         $query->when($store_id, function($query) use ($store_id) {
    //             $query->where('orders.store_id', $store_id);
    //         });
    //         $query->when($state_id, function($query) use ($state_id) {
    //             $query->where('stores.state_id', $state_id);
    //         });
    //         $query->when($area_id, function($query) use ($area_id) {
    //             $query->where('stores.area_id', $area_id);
    //         });
    //         $query->when($distributor_id, function($query) use ($distributor_id) {
    //             $query->where('teams.distributor_id', $distributor_id);
    //         });
    //         $query->when($query, function($query) use ($term) {
    //             $query->where('orders.order_no', 'like', '%'.$term.'%')->orWhere('stores.contact', $term)->orWhere('stores.name', 'like', '%'.$term.'%');
    //         })->whereBetween('orders.created_at', [$date_from, $date_to]);

    //         $data = $query->latest('orders.id')->paginate(25);
    //       // dd($data);
    //     } else {
    //         $data = Order::orderBy('id', 'desc')->latest('id')->paginate(25);
    //     }
    //     $user = User::select('id', 'name')->where('type', 6)->orWhere('type',5)->where('status',1)->orderBy('name')->get();
    //     $stores = Store::select('id', 'name')->where('status',1)->orderBy('name')->get();
    //     $state = State::where('status',1)->groupBy('name')->orderBy('name')->get();
    //     $distributor = User::select('id', 'name')->where('type', 7)->where('status',1)->orderBy('name')->get();
    //     return view('admin.order.store-report', compact('data','request','user','stores','state','distributor'));
    // }
    
    
    public function index(Request $request)
    {
    $date_from = $request->date_from ?? null;
    $date_to = $request->date_to ? date('Y-m-d', strtotime($request->date_to . ' +1 day')) : null;
    $term = $request->term ?? '';
    $user_id = $request->user_id ?? '';
    $store_id = $request->store_id ?? '';
    $state_id = $request->state_id ?? '';
    $area_id = $request->area_id ?? '';
    $distributor_id = $request->distributor_id ?? '';

    $query = Order::select(
        'orders.order_no', 'orders.id', 'orders.user_id', 
        'orders.distributor_id', 'orders.store_id', 
        'orders.order_type', 'orders.comment', 'stores.name', 
        'orders.created_at', 'teams.distributor_id AS distributor'
    )
    ->join('stores', 'stores.id', '=', 'orders.store_id')
    ->join('teams', 'stores.id', '=', 'teams.store_id');

    if ($user_id) {
        $query->where('orders.user_id', $user_id);
    }

    if ($store_id) {
        $query->where('orders.store_id', $store_id);
    }

    if ($state_id) {
        $query->where('stores.state_id', $state_id);
    }

    if ($area_id) {
        $query->where('stores.area_id', $area_id);
    }

    if ($distributor_id) {
        $query->where('teams.distributor_id', $distributor_id);
    }

    if ($term) {
        $query->where(function ($q) use ($term) {
            $q->where('orders.order_no', 'like', '%' . $term . '%')
              ->orWhere('stores.contact', $term)
              ->orWhere('stores.name', 'like', '%' . $term . '%');
        });
    }

    if ($date_from && $date_to) {
        $query->whereBetween('orders.created_at', [$date_from, $date_to]);
    } elseif ($date_from) {
        $query->whereDate('orders.created_at', '>=', $date_from);
    } elseif ($date_to) {
        $query->whereDate('orders.created_at', '<=', $date_to);
    }

    $data = $query->latest('orders.id')->paginate(25);

    $user = User::select('id', 'name')
                ->whereIn('type', [5, 6])
                ->where('status', 1)
                ->orderBy('name')
                ->get();

    $stores = Store::select('id', 'name')
                   ->where('status', 1)
                   ->orderBy('name')
                   ->get();

    $state = State::where('status', 1)
                  ->groupBy('name')
                  ->orderBy('name')
                  ->get();

    $distributor = User::select('id', 'name','employee_id','state')
                       ->where('type', 7)
                       ->where('status', 1)
                       ->orderBy('name')
                       ->get();

    return view('admin.order.store-report', compact('data', 'request', 'user', 'stores', 'state', 'distributor'));
    }

    //pdf download for individual order
    public function pdfExport(Request $request, $id)
    {
        $data = Order::findOrfail($id);
        return view('admin.order.pdf', compact('data'));
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
            $childFields = array('Name of Quality Shape & Unit', '35', '40', '45', '50', '55', '60', '65', '70', '73','75','','Total');

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
						$row['73'] ? $row['73'] : '',
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
            $date_from = $request->date_from ? $request->date_from : '';
            $date_to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
            $mergedObject=array();
       if (isset($request->date_from) || isset($request->date_to) || isset($request->term) || isset($request->user_id) || isset($request->store_id)|| isset($request->state_id)|| isset($request->area_id)|| isset($request->distributor_id)) {
            

            $date_from = $request->date_from ?? null;
            $date_to = $request->date_to ? date('Y-m-d', strtotime($request->date_to . ' +1 day')) : null;
            $term = $request->term ?? '';
            $user_id = $request->user_id ?? '';
            $store_id = $request->store_id ?? '';
            $state_id = $request->state_id ?? '';
            $area_id = $request->area_id ?? '';
            $distributor_id = $request->distributor_id ?? '';
            $query = Order::select('orders.order_no','orders.id','orders.user_id','orders.store_id','orders.distributor_id','orders.order_type','orders.comment','stores.name','orders.created_at','teams.distributor_id AS distributor','order_products.product_id','order_products.color_id','order_products.size_id','order_products.qty')->join('order_products', 'order_products.order_id', 'orders.id')->join('stores', 'stores.id', 'orders.store_id')->join('teams', 'stores.id', 'teams.store_id');

        
             if ($user_id) {
                $query->where('orders.user_id', $user_id);
            }

            if ($store_id) {
                $query->where('orders.store_id', $store_id);
            }
        
            if ($state_id) {
                $query->where('stores.state_id', $state_id);
            }
        
            if ($area_id) {
                $query->where('stores.area_id', $area_id);
            }
        
            if ($distributor_id) {
                $query->where('teams.distributor_id', $distributor_id);
            }
        
            if ($term) {
                $query->where(function ($q) use ($term) {
                    $q->where('orders.order_no', 'like', '%' . $term . '%')
                      ->orWhere('stores.contact', $term)
                      ->orWhere('stores.name', 'like', '%' . $term . '%');
                });
            }

            if ($date_from && $date_to) {
                $query->whereBetween('orders.created_at', [$date_from, $date_to]);
            } elseif ($date_from) {
                $query->whereDate('orders.created_at', '>=', $date_from);
            } elseif ($date_to) {
                $query->whereDate('orders.created_at', '<=', $date_to);
            }

            //$data = $query->latest('orders.id')->cursor();
            //$users = $data->all();
            //dd($data);
            
        } else {
            $query = Order::join('order_products', 'order_products.order_id', 'orders.id')->orderBy('orders.id', 'desc')->latest('orders.id');
            //$users = $data->all();
            
        }
        
         $filename = "lux-store-wise-sales-".$date_from.' to '.$date_to.".csv";
            $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];


    return Response::stream(function () use ($query, $headers) {
        $file = fopen('php://output', 'w');
        fputcsv($file, ['SR', 'ORDER NO','ORDER TYPE','ZSM','RSM','SM','ASM','EMPLOYEE', 'EMPLOYEE CODE','EMPLOYEE DESIGNATION', 'EMPLOYEE HQ', 'EMPLOYEE DATE OF JOINING','DISTRIBUTOR', 'DISTRIBUTOR ERP ID','DISTRIBUTOR CITY','DISTRIBUTOR STATE','STORE','STORE UNIQUE CODE','STORE OWNER NAME','STORE CONTACT','STORE ADDRESS','STORE STATE','STORE AREA','STORE TOWN/CITY','STORE PINCODE','STORE CREATION DATE',  'CATEGORY','PRODUCT RANGE', 'PRODUCT NAME', 'STYLE NO', 'COLOR', 'SIZE', 'QTY', 'IS TELEPHONIC', 'DATE','TIME']);
         $count = 1;
         $query->chunk(100, function ($orders) use ($file, &$count) {
            foreach ($orders as $key =>  $row) {
            $odate = date('j F, Y', strtotime($row['created_at']));
                $time = date('h:i A', strtotime($row['created_at']));
                $date=date('j F, Y h:i A', strtotime($row['stores']['created_at'])); 
                if(!empty($row['product_id'])){
                $productDetails=Product::where('id',$row['product_id'])->with('collection','category')->first();
                }
                if(!empty($row['color_id'])){
                $color=Color::where('id',$row['color_id'])->first();
                }
                if(!empty($row['size_id'])){
                $size=Size::where('id',$row['size_id'])->first();
                }
                $user=Team::where('store_id',$row['store_id'])->first();
                if(!empty($user)){
                $userName=User::where('id',$user['distributor_id'])->first();
                }
                
                if(!empty($row) && isset($row['stores']['owner_fname']) && isset($row['stores']['owner_lname'])){
                   $owner= $row['stores']['owner_fname'].' '.$row['stores']['owner_lname'];
                }else{
                    $owner=null;
                }
                if($row['order_type']=='order-on-call'){
                    $tel='yes';
                    
                }else{
                    $tel='no';
                }
                
                if($row->distributor_id=='' || $row->distributor_id==0){
                $distributorName= $userName->name??'';
                $distributorCode=$userName->employee_id??'';
                $distributorState=$userName->state??'';
                $distributorCity=$userName->city??'';
                
                }else{
                    $distributorName= $row->distributors->name;
                     $distributorCode=$row->distributors->employee_id;
                    $distributorState=$row->distributors->state;
                    $distributorCity=$row->distributors->city;
                }
            fputcsv($file, [
                    $count++,
                    $row['order_no'],
					$row['order_type'],
					$user['zsm']['name'] ??'',
					$user['rsm']['name'] ??'',
					$user['sm']['name'] ??'',
					$user['asm']['name'] ??'',
					
					$row['users']['name'] ?? 'Self Order',
					
					$row['users']['employee_id'] ?? 'Self Order',
					$row['users']['designation'] ?? '',
					$row['users']['headquater'] ?? '',
					$row['users']['date_of_joining'] ?? '',
					$distributorName ?? '',
                    $distributorCode ?? '',
                    $distributorCity ??'' ,
                    $distributorState ??'' ,
                    $row['stores']['name'] ?? '',
                    $row['stores']['unique_code'] ?? '',
                    $owner ??'',
                    $row['stores']['contact'] ?? '',
                    $row['stores']['address'] ?? '',
                    $row['stores']['states']['name'] ?? '',
                    $row['stores']['areas']['name'] ?? '',
                    $row['stores']['city'] ?? '',
                    $row['stores']['pin'] ?? '',
                    $date,
                    
                    $productDetails->category->name ?? '',
                    $productDetails->collection->name ?? '',
                    $productDetails->name ?? '',
                    $productDetails->style_no ?? '',
                    $color->name ??'',
                    $size->name??'',
                    $row['qty']?? '',
                   
                    $tel,
                    $odate,
                    $time
            ]);
    
    
        }
     });
        fclose($file);
    }, 200, $headers);
    //     if (count($data) > 0) {
    //         $delimiter = ",";
    //         $filename = "lux-store-wise-sales-".$date_from.' to '.$date_to.".csv";
    //         $filePath = storage_path('app/public/csv/' . $filename);
    //         // Create a file pointer
    //         $f = fopen($filePath, 'w');

    //         // Set column headers
    //         $fields = array('SR', 'ORDER NO','ORDER TYPE','ZSM','RSM','SM','ASM','EMPLOYEE', 'EMPLOYEE CODE','EMPLOYEE DESIGNATION', 'EMPLOYEE HQ', 'EMPLOYEE DATE OF JOINING','DISTRIBUTOR', 'DISTRIBUTOR ERP ID','DISTRIBUTOR CITY','DISTRIBUTOR STATE','STORE','STORE UNIQUE CODE','STORE OWNER NAME','STORE CONTACT','STORE ADDRESS','STORE STATE','STORE AREA','STORE TOWN/CITY','STORE PINCODE','STORE CREATION DATE',  'CATEGORY','PRODUCT RANGE', 'PRODUCT NAME', 'STYLE NO', 'COLOR', 'SIZE', 'QTY', 'IS TELEPHONIC', 'DATE','TIME');
    //         fputcsv($f, $fields, $delimiter);

    //         $count = 1;

    //         foreach($data as $row) {
				
    //             $odate = date('j F, Y', strtotime($row['created_at']));
    //             $time = date('h:i A', strtotime($row['created_at']));
    //             $date=date('j F, Y h:i A', strtotime($row['stores']['created_at'])); 
    //             if(!empty($row['product_id'])){
    //             $productDetails=Product::where('id',$row['product_id'])->with('collection','category')->first();
    //             }
    //             if(!empty($row['color_id'])){
    //             $color=Color::where('id',$row['color_id'])->first();
    //             }
    //             if(!empty($row['size_id'])){
    //             $size=Size::where('id',$row['size_id'])->first();
    //             }
    //             $user=Team::where('store_id',$row['store_id'])->first();
    //             if(!empty($user)){
    //             $userName=User::where('id',$user['distributor_id'])->first();
    //             }
    //             if($row['order_type']=='order-on-call'){
    //                 $tel='yes';
                    
    //             }else{
    //                 $tel='no';
    //             }
    //             $lineData = array(
    //                 $count,
    //                 $row['order_no'],
				// 	$row['order_type'],
				// 	$user['zsm']['name'] ??'',
				// 	$user['rsm']['name'] ??'',
				// 	$user['sm']['name'] ??'',
				// 	$user['asm']['name'] ??'',
					
				// 	$row['users']['name'] ?? 'Self Order',
					
				// 	$row['users']['employee_id'] ?? 'Self Order',
				// 	$row['users']['designation'] ?? '',
				// 	$row['users']['headquater'] ?? '',
				// 	$row['users']['date_of_joining'] ?? '',
				// 	$userName->name ?? '',
    //                 $userName->employee_id ?? '',
    //                 $userName['city'] ??'' ,
    //                 $userName['state'] ??'' ,
    //                 $row['stores']['name'] ?? '',
    //                 $row['stores']['unique_code'] ?? '',
    //                 $row['stores']['owner_fname'].' '.$row['stores']['owner_lname'] ?? '',
    //                 $row['stores']['contact'] ?? '',
    //                 $row['stores']['address'] ?? '',
    //                 $row['stores']['states']['name'] ?? '',
    //                 $row['stores']['areas']['name'] ?? '',
    //                 $row['stores']['city'] ?? '',
    //                 $row['stores']['pin'] ?? '',
    //                 $date,
                    
    //                 $productDetails->category->name?? '',
    //                 $productDetails->collection->name?? '',
    //                 $productDetails->name?? '',
    //                 $productDetails->style_no?? '',
    //                 $color->name ??'',
    //                 $size->name??'',
    //                 $row['qty']?? '',
                   
    //                 $tel,
    //                 $odate,
    //                 $time
    //             );

    //             fputcsv($f, $lineData, $delimiter);

    //             $count++;
    //         }

    //         // Move back to beginning of file
    //         fseek($f, 0);

    //         // Set headers to download file rather than displayed
    //       // header('Content-Type: text/csv');
    //         //header('Content-Disposition: attachment; filename="' . $filename . '";');
                
    //         //output all remaining data on a file pointer
    //       // fpassthru($f);
    //       fclose($f);
    //       return redirect()->route('admin.show.files')->with('success', 'Data saved successfully.');
    //     }
    }

   public function orderDumpindex(Request $request)
    {
        $from =  date('Y-m-01');
        $to =  date('Y-m-d', strtotime('+1 day'));
       
         $zsm=User::select('id', 'name')->where('type', 2)->orderBy('name')->get();
        return view('admin.order.order-dump', compact('zsm','request'));
    }
    
    //     //all order csv export
    // public function orderDump(Request $request)
    // {
    //         $date_from = $request->date_from ? $request->date_from : '';
    //         $date_to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
    //         $mergedObject=array();
    //         if (isset($request->date_from) || isset($request->date_to) || isset($request->ase) ||isset($request->zsm) || isset($request->state)|| isset($request->rsm) ||isset($request->sm) ||isset($request->asm)) {
            
            

    //         $date_from = $request->date_from ? $request->date_from : '';
    //         $date_to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
    //         $term = $request->term ? $request->term : '';
    //         $user_id = $request->user_id ? $request->user_id : '';
    //         $store_id = $request->store_id ? $request->store_id : '';
    //         $state_id = $request->state_id ? $request->state_id : '';
    //         $area_id = $request->area_id ? $request->area_id : '';
    //         $distributor_id = $request->distributor_id ? $request->distributor_id : '';
    //          $date_from = $request->date_from ? $request->date_from : date('Y-m-01');
    //          $date_to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
    //          $user_id = $request->ase ? $request->ase : '';
    //          $asm_id=$request->asm ? $request->asm : '';
    //          $sm_id=$request->sm ? $request->sm : '';
    //          $rsm_id=$request->rsm ? $request->rsm : '';
    //          $zsm_id=$request->zsm ? $request->zsm : '';
    //          $state_id=$request->state ? $request->state : '';
    //          $aceids=array();
    //          $asmids=array();
    //          $teams=array();
    //          if(!empty($zsm_id)){
    //               # Query with zsm_id and get aceids
                   
    //               $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->groupby('ase_id')->get()->toArray();
    //              }
                 
    //              if(!empty($zsm_id && $state_id)){
    //                   # Query with zsm_id and rsm_id and get aceids
    //                   $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('state_id',$state_id)->groupby('ase_id')->get()->toArray();
    //                  }
    //                  if(!empty($zsm_id && $state_id && $rsm_id)){
    //                   # Query with zsm_id and rsm_id and get aceids
    //                   $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->groupby('ase_id')->get()->toArray();
    //                  }
    //                   if(!empty($zsm_id && $state_id && $rsm_id && $sm_id)){
    //                   # Query with zsm_id and rsm_id and get aceids
    //                   $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->groupby('ase_id')->get()->toArray();
    //                  }
    //                  if(!empty($zsm_id && $state_id && $rsm_id && $sm_id && $asm_id)){
    //                   # Query with zsm_id and rsm_id and get aceids
    //                   $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->where('asm_id',$asm_id)->groupby('ase_id')->get()->toArray();
    //                  }
    //              if(!empty($zsm_id)){
    //               # Query with zsm_id and get aceids
                   
    //               $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->groupby('asm_id')->get()->toArray();
    //              }
    //              if(!empty($zsm_id && $state_id )){
    //                   # Query with zsm_id and rsm_id and get aceids
    //                   $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('state_id',$state_id)->groupby('asm_id')->get()->toArray();
    //                  }
    //                  if(!empty($zsm_id && $state_id && $rsm_id)){
    //                   # Query with zsm_id and rsm_id and get aceids
    //                   $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->groupby('asm_id')->get()->toArray();
    //                  }
    //                   if(!empty($zsm_id && $state_id && $rsm_id && $sm_id)){
    //                   # Query with zsm_id and rsm_id and get aceids
    //                   $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->groupby('asm_id')->get()->toArray();
    //                  }
    //                  if(!empty($zsm_id && $state_id && $rsm_id && $sm_id && $asm_id)){
    //                   # Query with zsm_id and rsm_id and get aceids
    //                   $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->where('asm_id',$asm_id)->groupby('asm_id')->get()->toArray();
    //                  }
    //                  $teams=array_merge($aceids,$asmids);
    //          //dd($aceids);
    //          $query = Order::select('orders.order_no','orders.id','orders.user_id','orders.store_id','orders.order_type','orders.comment AS comment','orders.created_at','order_products.product_id','order_products.color_id','order_products.size_id','order_products.qty')->join('order_products', 'order_products.order_id', 'orders.id')->whereIn('orders.user_id',$teams);
 
             
            
    //          $query->when($user_id, function($query) use ($user_id) {
    //                  $query->where('orders.user_id', $user_id);
    //          })->whereBetween('orders.created_at', [$date_from, $date_to]);
             
    //          $query2 = UserNoOrderReason::select('user_no_order_reasons.user_id','user_no_order_reasons.store_id','user_no_order_reasons.comment AS noordercomment','user_no_order_reasons.description','user_no_order_reasons.date','user_no_order_reasons.time','user_no_order_reasons.created_at')->whereIn('user_no_order_reasons.user_id',$teams);
 
             
            
    //          $query2->when($user_id, function($query2) use ($user_id) {
    //                  $query2->where('user_no_order_reasons.user_id', $user_id);
    //          })->whereBetween('user_no_order_reasons.created_at', [$date_from, $date_to]);
             
             
    //          DB::enableQueryLog();
    //          if ($request->zsm != 'all') {
    //             if (empty($request->zsm)) {
    //                 $date_from = $request->date_from ? date('Y-m-d', strtotime($request->date_from)) : date('Y-m-01');
    //                 $date_to = date('Y-m-d', strtotime(request()->input('date_to')))? date('Y-m-d', strtotime(request()->input('date_to'))) : date('Y-m-01');
    //                 // $data = Activity::latest('id')->paginate(25);

    //                 $query = Order::select('orders.order_no','orders.id','orders.user_id','orders.store_id','orders.order_type','orders.comment AS comment','orders.created_at','order_products.product_id','order_products.color_id','order_products.size_id','order_products.qty')->join('order_products', 'order_products.order_id', 'orders.id');

                    
    //                 $data = $query->whereBetween('orders.created_at', [$date_from, $date_to])->latest('orders.id')->with('stores','users')->get();
    //                 $orderArray = $data->toArray();
                    
                    
                    
    //                 $query2 = UserNoOrderReason::select('user_no_order_reasons.user_id','user_no_order_reasons.store_id','user_no_order_reasons.comment AS noordercomment','user_no_order_reasons.description','user_no_order_reasons.date','user_no_order_reasons.time','user_no_order_reasons.created_at');

                    
    //                 $useNoorderReason  = $query2->whereBetween('user_no_order_reasons.created_at', [$date_from, $date_to])->latest('user_no_order_reasons.id')->with('stores','users')->get();
    //                 $useNoorderReasonArray  = $useNoorderReason->toArray();
    //                 $mergedObject = array_merge($orderArray,$useNoorderReasonArray);
    //             } else {
    //                 $data = $query->latest('id')->with('stores','users')->get();
    //                 $orderArray = $data->toArray();
                    
    //                 $useNoorderReason  = $query2->latest('id')->with('stores','users')->get();
    //                 $useNoorderReasonArray  = $useNoorderReason ->toArray();
    //                 $mergedObject = array_merge($orderArray,$useNoorderReasonArray);
    //             }
    //             // dd(DB::getQueryLog());
    //             // $data = $query->latest('id')->get();
    //         } else {
    //             if (!empty($request->date_from && $request->date_to)) {
                    
    //                 $date_from = $request->date_from ? date('Y-m-d', strtotime($request->date_from)) : date('Y-m-01');
    //                 $date_to = date('Y-m-d', strtotime(request()->input('date_to')))? date('Y-m-d', strtotime(request()->input('date_to'))) : date('Y-m-01');
    //                 // $data = Activity::latest('id')->get();

    //                 $query = Order::select('orders.order_no','orders.id','orders.user_id','orders.store_id','orders.order_type','orders.comment AS comment','orders.created_at','order_products.product_id','order_products.color_id','order_products.size_id','order_products.qty')->join('order_products', 'order_products.order_id', 'orders.id');

                    
    //                 $data = $query->whereBetween('orders.created_at', [$date_from, $date_to])->latest('orders.id')->with('stores','users')->get();
    //                 $orderArray = $data->toArray();
                    
    //                 $query2 = UserNoOrderReason::select('user_no_order_reasons.user_id','user_no_order_reasons.store_id','user_no_order_reasons.comment AS noordercomment','user_no_order_reasons.description','user_no_order_reasons.date','user_no_order_reasons.time','user_no_order_reasons.created_at');

                    
    //                 $useNoorderReason  = $query2->whereBetween('user_no_order_reasons.created_at', [$date_from, $date_to])->latest('user_no_order_reasons.id')->with('stores','users')->get();
    //                 $useNoorderReasonArray  = $useNoorderReason->toArray();
    //                 $mergedObject = array_merge($orderArray,$useNoorderReasonArray);
    //             } else {
    //                 // $data = $query->latest('id')->get();
    //                 $data = Order::latest('id')->get();
    //                 $orderArray = $data->toArray();
    //                 $useNoorderReason  = $query2->latest('id')->with('stores','users')->get();
    //                 $useNoorderReasonArray  = $useNoorderReason->toArray();
    //                 $mergedObject = array_merge($orderArray,$useNoorderReasonArray);
    //             }



                
    //         }
             
    //          //dd($data);
    //      } 

    //         //dd($mergedObject[810]);
            
           
    //     if (count($mergedObject) > 0) {
    //         $delimiter = ",";
    //         $filename = "lux-store-wise-sales-order-dump-".$date_from.' to '.$date_to.".csv";
           
    //         //$filePath = storage_path('app/public/csv/' . $filename);;
           
             
    //          if (Storage::exists($filename)) {
                 
    //         // Modify the filename to make it unique, e.g., by appending a timestamp
    //             $fileName = time() . '_' . $fileName;
    //             $filePath = storage_path('app/public/csv/' . $filename);
    //         }
    //         else{
    //             $filePath = storage_path('app/public/csv/' . $filename);
    //         }
    //         // Create a file pointer
    //         $f = fopen($filePath, 'w');

    //         // Set column headers
    //         $fields = array('SR', 'ORDER NO','ORDER TYPE','ZSM','RSM','SM','ASM','EMPLOYEE', 'EMPLOYEE CODE','EMPLOYEE DESIGNATION', 'EMPLOYEE HQ', 'EMPLOYEE DATE OF JOINING','DISTRIBUTOR', 'DISTRIBUTOR ERP ID','DISTRIBUTOR CITY','DISTRIBUTOR STATE','STORE','STORE UNIQUE CODE','STORE OWNER NAME','STORE CONTACT','STORE ADDRESS','STORE STATE','STORE AREA','STORE TOWN/CITY','STORE PINCODE','STORE CREATION DATE',  'CATEGORY','PRODUCT RANGE', 'PRODUCT NAME', 'STYLE NO', 'COLOR', 'SIZE', 'QTY','NOTE','NO SALES REASON','NO SALES REASON DESCRIPTION', 'IS TELEPHONIC', 'DATE','TIME');
    //         fputcsv($f, $fields, $delimiter);

    //         $count = 1;

    //         foreach($mergedObject as $row) {
				
    //             $odate = date('j F, Y', strtotime($row['created_at']));
    //             $time = date('h:i A', strtotime($row['created_at']));
    //             $date=date('j F, Y h:i A', strtotime($row['stores']['created_at']));
    //             $state=State::where('id',$row['stores']['state_id'])->first();
    //             $area=Area::where('id',$row['stores']['area_id'])->first();
    //           // if(in_array($row['product_id'],$row)){
    //                 if(!empty($row['product_id'])){
    //                   $productDetails=Product::where('id',$row['product_id'])->with('collection','category')->first();
    //                 }else{
    //                   $productDetails='';
                    
                    
    //                 }
                
    //             //}else{
    //               //  $productDetails='';
    //             //}
    //           // if(in_array($row['color_id'],$row)){
    //               if(!empty($row['color_id'])){
    //                 $color=Color::where('id',$row['color_id'])->first();
    //                 }else{
    //                  $color='';
    //                 }
    //           // }else{
    //           //     $color='';
    //           //  }
    //           //  if(in_array($row['size_id'],$row)){
    //                 if(!empty($row['size_id'])){
    //                 $size=Size::where('id',$row['size_id'])->first();
    //                 }else{
    //                  $size='';
    //                 }
    //             // }else{
    //             //    $size='';
    //           // }
    //             $user=Team::where('store_id',$row['store_id'])->first();
    //             if(!empty($user)){
    //             $userName=User::where('id',$user['distributor_id'])->first();
    //             }
    //             if(!empty($row['order_type'])){
    //                 if($row['order_type']=='order-on-call'){
    //                     $tel='yes';
                        
    //                 }else{
    //                     $tel='no';
    //                 }
    //             }
    //             $lineData = array(
    //                 $count,
    //                 $row['order_no'] ??'',
				// 	$row['order_type'] ??'',
				// 	$user['zsm']['name'] ??'',
				// 	$user['rsm']['name'] ??'',
				// 	$user['sm']['name'] ??'',
				// 	$user['asm']['name'] ??'',
					
				// 	$row['users']['name'] ?? 'Self Order',
				
				// 	$row['users']['employee_id'] ?? 'Self Order',
				// 	$row['users']['designation'] ?? '',
				// 	$row['users']['headquater'] ?? '',
				// 	$row['users']['date_of_joining'] ?? '',
				// 	$userName->name ?? '',
    //                 $userName->employee_id ?? '',
    //                 $user['areas']['name'] ??'' ,
    //                 $user['states']['name'] ??'' ,
    //                 $row['stores']['name'] ?? '',
    //                 $row['stores']['unique_code'] ?? '',
    //                 $row['stores']['owner_fname'].' '.$row['stores']['owner_lname'] ?? '',
    //                 $row['stores']['contact'] ?? '',
    //                 $row['stores']['address'] ?? '',
    //                 $state->name ?? '',
    //                 $area->name ?? '',
    //                 $row['stores']['city'] ?? '',
    //                 $row['stores']['pin'] ?? '',
    //                 $date ??'',
                    
    //                 $productDetails->category->name?? '',
    //                 $productDetails->collection->name?? '',
    //                 $productDetails->name?? '',
    //                 $productDetails->style_no?? '',
    //                 $color->name ??'',
    //                 $size->name??'',
    //                 $row['qty']?? '',
    //                 $row['comment']?? '',
    //                 $row['noordercomment'] ??'',
    //                 $row['description'] ??'',
    //                 $tel ??'',
    //                 $odate ??'',
    //                 $time ??''
    //             );

    //             fputcsv($f, $lineData, $delimiter);

    //             $count++;
    //         }

    //         // Move back to beginning of file
    //         //fseek($f, 0);

    //         // Set headers to download file rather than displayed
    //         //header('Content-Type: text/csv');
    //         //header('Content-Disposition: attachment; filename="' . $filename . '";');

    //         //output all remaining data on a file pointer
    //         //fpassthru($f);
    //         fclose($f);
    //       return redirect()->route('admin.show.files')->with('success', 'Data saved successfully.');
    //     }
    // }
    
    
    
     //all order csv export
    public function orderDump2(Request $request)
    {
        try {
            $date_from = $request->date_from ? $request->date_from : '';
            $date_to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
            $mergedObject=array();
            if (isset($request->date_from) || isset($request->date_to) || isset($request->ase) ||isset($request->zsm) || isset($request->state)|| isset($request->rsm) ||isset($request->sm) ||isset($request->asm)) {
            
            

            $date_from =  $request->date_from ? date('Y-m-d', strtotime($request->date_from)) : '';
            $date_to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
            $term = $request->term ? $request->term : '';
            $user_id = $request->user_id ? $request->user_id : '';
            $store_id = $request->store_id ? $request->store_id : '';
            $state_id = $request->state_id ? $request->state_id : '';
            $area_id = $request->area_id ? $request->area_id : '';
            $distributor_id = $request->distributor_id ? $request->distributor_id : '';
            // $date_from = $request->date_from ? $request->date_from : date('Y-m-01');
             //$date_to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
             $user_id = $request->ase ? $request->ase : '';
             $asm_id=$request->asm ? $request->asm : '';
             $sm_id=$request->sm ? $request->sm : '';
             $rsm_id=$request->rsm ? $request->rsm : '';
             $zsm_id=$request->zsm ? $request->zsm : '';
             $state_id=$request->state ? $request->state : '';
             $aceids=array();
             $asmids=array();
             $teams=array();
             if(!empty($zsm_id)){
                   # Query with zsm_id and get aceids
                   
                   $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->groupby('ase_id')->get()->toArray();
                 }
                 
                 if(!empty($zsm_id && $state_id)){
                       # Query with zsm_id and rsm_id and get aceids
                       $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('state_id',$state_id)->groupby('ase_id')->get()->toArray();
                     }
                     if(!empty($zsm_id && $state_id && $rsm_id)){
                       # Query with zsm_id and rsm_id and get aceids
                       $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->groupby('ase_id')->get()->toArray();
                     }
                      if(!empty($zsm_id && $state_id && $rsm_id && $sm_id)){
                       # Query with zsm_id and rsm_id and get aceids
                       $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->groupby('ase_id')->get()->toArray();
                     }
                     if(!empty($zsm_id && $state_id && $rsm_id && $sm_id && $asm_id)){
                       # Query with zsm_id and rsm_id and get aceids
                       $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->where('asm_id',$asm_id)->groupby('ase_id')->get()->toArray();
                     }
                 if(!empty($zsm_id)){
                   # Query with zsm_id and get aceids
                   
                   $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->groupby('asm_id')->get()->toArray();
                 }
                 if(!empty($zsm_id && $state_id )){
                       # Query with zsm_id and rsm_id and get aceids
                       $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('state_id',$state_id)->groupby('asm_id')->get()->toArray();
                     }
                     if(!empty($zsm_id && $state_id && $rsm_id)){
                       # Query with zsm_id and rsm_id and get aceids
                       $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->groupby('asm_id')->get()->toArray();
                     }
                      if(!empty($zsm_id && $state_id && $rsm_id && $sm_id)){
                       # Query with zsm_id and rsm_id and get aceids
                       $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->groupby('asm_id')->get()->toArray();
                     }
                     if(!empty($zsm_id && $state_id && $rsm_id && $sm_id && $asm_id)){
                       # Query with zsm_id and rsm_id and get aceids
                       $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->where('asm_id',$asm_id)->groupby('asm_id')->get()->toArray();
                     }
                     $teams=array_merge($aceids,$asmids);
             //dd($aceids);
             $query = Order::select('orders.order_no','orders.id','orders.user_id','orders.store_id','orders.distributor_id','orders.order_type','orders.comment AS comment','orders.created_at','order_products.product_id','order_products.color_id','order_products.size_id','order_products.qty')->join('order_products', 'order_products.order_id', 'orders.id')->whereIn('orders.user_id',$teams);
 
             
            
             $query->when($user_id, function($query) use ($user_id) {
                     $query->where('orders.user_id', $user_id);
             })->whereBetween('orders.created_at', [$date_from, $date_to]);
             
             $query2 = UserNoOrderReason::select('user_no_order_reasons.user_id','user_no_order_reasons.store_id','user_no_order_reasons.comment AS noordercomment','user_no_order_reasons.description','user_no_order_reasons.date','user_no_order_reasons.time','user_no_order_reasons.created_at')->whereIn('user_no_order_reasons.user_id',$teams);
 
             
            
             $query2->when($user_id, function($query2) use ($user_id) {
                     $query2->where('user_no_order_reasons.user_id', $user_id);
             })->whereBetween('user_no_order_reasons.created_at', [$date_from, $date_to]);
             
             
             DB::enableQueryLog();
             if ($request->zsm != 'all') {
                if (empty($request->zsm)) {
                    $date_from =  $request->date_from ? date('Y-m-d', strtotime($request->date_from)) : '';
                    
                    $date_to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
                    
                    // $data = Activity::latest('id')->paginate(25);

                    $query = Order::select('orders.order_no','orders.id','orders.user_id','orders.store_id','orders.distributor_id','orders.order_type','orders.comment AS comment','orders.created_at','order_products.product_id','order_products.color_id','order_products.size_id','order_products.qty')->join('order_products', 'order_products.order_id', 'orders.id');

                    
                    $data = $query->whereBetween('orders.created_at', [$date_from, $date_to])->latest('orders.id')->with('stores','users')->cursor();
                    //$orderArray = $data->toArray();
                    
                    
                    
                    $query2 = UserNoOrderReason::select('user_no_order_reasons.user_id','user_no_order_reasons.store_id','user_no_order_reasons.comment AS noordercomment','user_no_order_reasons.description','user_no_order_reasons.date','user_no_order_reasons.time','user_no_order_reasons.created_at');

                    
                    $useNoorderReason  = $query2->whereBetween('user_no_order_reasons.created_at', [$date_from, $date_to])->latest('user_no_order_reasons.id')->with('stores','users')->cursor();
                   // $useNoorderReasonArray  = $useNoorderReason->toArray();
                   // $mergedArray = array_merge($orderArray,$useNoorderReasonArray);
                   // $mergedObject = (object)$mergedArray;
                   $mergedObject = $data->merge($useNoorderReason);
                    $users = $mergedObject->all();
                    
                } else {
                    $data = $query->latest('id')->with('stores','users')->cursor();
                    $orderArray = $data->toArray();
                    
                    $useNoorderReason  = $query2->latest('id')->with('stores','users')->cursor();
                   // $useNoorderReasonArray  = $useNoorderReason ->toArray();
                   // $mergedArray = array_merge($orderArray,$useNoorderReasonArray);
                   // $mergedObject = (object)$mergedArray;
                   $mergedObject = $data->merge($useNoorderReason);
                    $users = $mergedObject->all();
                }
                // dd(DB::getQueryLog());
                // $data = $query->latest('id')->get();
            } else {
                if (!empty($request->date_from && $request->date_to)) {
                    
                    $date_from =  $request->date_from ? date('Y-m-d', strtotime($request->date_from)) : '';
                    
                     $date_to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
                    // $data = Activity::latest('id')->get();

                    $query = Order::select('orders.order_no','orders.id','orders.user_id','orders.store_id','orders.distributor_id','orders.order_type','orders.comment AS comment','orders.created_at','order_products.product_id','order_products.color_id','order_products.size_id','order_products.qty')->join('order_products', 'order_products.order_id', 'orders.id');

                    
                    $data = $query->whereBetween('orders.created_at', [$date_from, $date_to])->latest('orders.id')->with('stores','users')->cursor();
                    $orderArray = $data->toArray();
                    
                    $query2 = UserNoOrderReason::select('user_no_order_reasons.user_id','user_no_order_reasons.store_id','user_no_order_reasons.comment AS noordercomment','user_no_order_reasons.description','user_no_order_reasons.date','user_no_order_reasons.time','user_no_order_reasons.created_at');

                    
                    $useNoorderReason  = $query2->whereBetween('user_no_order_reasons.created_at', [$date_from, $date_to])->latest('user_no_order_reasons.id')->with('stores','users')->cursor();
                    //$useNoorderReasonArray  = $useNoorderReason->toArray();
                    //$mergedArray = array_merge($orderArray,$useNoorderReasonArray);
                    //$mergedObject = (object)$mergedArray;
                    $mergedObject = $data->merge($useNoorderReason);
                     $users = $mergedObject->all();
                } else {
                    // $data = $query->latest('id')->get();
                    $data = Order::latest('id')->with('stores','users')->cursor();
                    $orderArray = $data->toArray();
                    $useNoorderReason  = $query2->latest('id')->with('stores','users')->cursor();
                   // $useNoorderReasonArray  = $useNoorderReason->toArray();
                   // $mergedArray = array_merge($orderArray,$useNoorderReasonArray);
                    //$mergedObject = (object)$mergedArray;
                    $mergedObject = $data->merge($useNoorderReason);
                    $users = $mergedObject->all();
                }



                
            }
             
             //dd($data);
         } 
    } catch (\Exception $e) {
    // Catch and display the error
    dd($e->getMessage(), $e->getTrace());
    }
         //dd(DB::getQueryLog());
// dd($users);
            //dd($mergedObject[810]);
             $filename = "lux-store-wise-sales-order-dump-".$date_from.' to '.$date_to.".csv";
            $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];


    return Response::stream(function () use ($users, $headers) {
        $file = fopen('php://output', 'w');
        fputcsv($file, ['SR', 'ORDER NO','ORDER TYPE','ZSM','RSM','SM','ASM','EMPLOYEE', 'EMPLOYEE CODE','EMPLOYEE DESIGNATION', 'EMPLOYEE HQ', 'EMPLOYEE DATE OF JOINING','DISTRIBUTOR', 'DISTRIBUTOR ERP ID','DISTRIBUTOR CITY','DISTRIBUTOR STATE','STORE','STORE UNIQUE CODE','STORE OWNER NAME','STORE CONTACT','STORE ADDRESS','STORE STATE','STORE AREA','STORE TOWN/CITY','STORE PINCODE','STORE CREATION DATE',  'CATEGORY','PRODUCT RANGE', 'PRODUCT NAME', 'STYLE NO', 'COLOR', 'SIZE', 'QTY','NOTE','NO SALES REASON','NO SALES REASON DESCRIPTION', 'IS TELEPHONIC', 'DATE','TIME']);
         $count = 1;
        foreach ($users as $row) {
            $odate = date('j F, Y', strtotime($row['created_at']));
                $time = date('h:i A', strtotime($row['created_at']));
                if(!empty($row) && isset($row['stores']['created_at'])){
                $date=date('j F, Y h:i A', strtotime($row['stores']['created_at']));
                }else{
                    $date=null;
                }
                if(!empty($row) && isset($row['stores']['state_id'])){
                 $state=State::where('id',$row['stores']['state_id'])->first();
                }else{
                    $state=null;
                }
                if(!empty($row) && isset($row['stores']['area_id'])){
                $area=Area::where('id',$row['stores']['area_id'])->first();
                }else{
                    $area=null;
                }
                if(!empty($row) && isset($row['stores']['owner_fname']) && isset($row['stores']['owner_lname'])){
                   $owner= $row['stores']['owner_fname'].' '.$row['stores']['owner_lname'];
                }else{
                    $owner=null;
                }
               // if(in_array($row['product_id'],$row)){
                    if(!empty($row['product_id'])){
                       $productDetails=Product::where('id',$row['product_id'])->with('collection','category')->first();
                    }else{
                       $productDetails='';
                    
                    
                    }
                
                //}else{
                  //  $productDetails='';
                //}
               // if(in_array($row['color_id'],$row)){
                   if(!empty($row['color_id'])){
                    $color=Color::where('id',$row['color_id'])->first();
                    }else{
                     $color='';
                    }
               // }else{
               //     $color='';
              //  }
               //  if(in_array($row['size_id'],$row)){
                    if(!empty($row['size_id'])){
                    $size=Size::where('id',$row['size_id'])->first();
                    }else{
                     $size='';
                    }
                // }else{
                //    $size='';
               // }
               if(!empty($row) && isset($row['store_id'])){
                $user=Team::where('store_id',$row['store_id'])->first();
               }else{
                   $user=null;
               }
                if(!empty($user)){
                $userName=User::where('id',$user['distributor_id'])->first();
                }
                if(!empty($row['order_type'])){
                    if($row['order_type']=='order-on-call'){
                        $tel='yes';
                        
                    }else{
                        $tel='no';
                    }
                }
            if($row->distributor_id=='' || $row->distributor_id==0){
                $distributorName= $userName->name;
                $distributorCode=$userName->employee_id;
                $distributorState=$userName->state;
                $distributorCity=$userName->city;
                
            }else{
                $distributorName= $row->distributors->name;
                 $distributorCode=$row->distributors->employee_id;
                $distributorState=$row->distributors->state;
                $distributorCity=$row->distributors->city;
            }
            
            fputcsv($file, [
                    $count++,
                    $row['order_no'] ??'',
					$row['order_type'] ??'',
					$user['zsm']['name'] ??'',
					$user['rsm']['name'] ??'',
					$user['sm']['name'] ??'',
					$user['asm']['name'] ??'',
					
					$row['users']['name'] ?? 'Self Order',
				
					$row['users']['employee_id'] ?? 'Self Order',
					$row['users']['designation'] ?? '',
					$row['users']['headquater'] ?? '',
					$row['users']['date_of_joining'] ?? '',
					
					$distributorName ?? '',
                    $distributorCode ?? '',
                    $distributorCity ??'' ,
                    $distributorState ??'' ,
                    $row['stores']['name'] ?? '',
                    $row['stores']['unique_code'] ?? '',
                    $owner ?? '',
                    $row['stores']['contact'] ?? '',
                    $row['stores']['address'] ?? '',
                    $state->name ?? '',
                    $area->name ?? '',
                    $row['stores']['city'] ?? '',
                    $row['stores']['pin'] ?? '',
                    $date ??'',
                    
                    $productDetails->category->name ?? '',
                    $productDetails->collection->name ?? '',
                    $productDetails->name ?? '',
                    $productDetails->style_no?? '',
                    $color->name ??'',
                    $size->name??'',
                    $row['qty']?? '',
                    $row['comment']?? '',
                    $row['noordercomment'] ??'',
                    $row['description'] ??'',
                    $tel ??'',
                    $odate ??'',
                    $time ??'']);
        }

        fclose($file);
    }, 200, $headers);
    
    }
    
    //
    public function orderDump(Request $request)
{
    try {
        $date_from = $request->date_from ? date('Y-m-d', strtotime($request->date_from)) : '';
        $date_to = $request->date_to ? date('Y-m-d', strtotime($request->date_to . ' +1 day')) : '';

        // Validate input dates
        if (empty($date_from) || empty($date_to)) {
            return response()->json(['error' => 'Invalid date range.'], 400);
        }

        $teams = $this->getTeams($request);

        // Initial query for orders
        $query = Order::select('orders.order_no', 'orders.id', 'orders.user_id', 'orders.store_id', 'orders.distributor_id', 'orders.order_type', 'orders.comment AS comment', 'orders.created_at', 'order_products.product_id', 'order_products.color_id', 'order_products.size_id', 'order_products.qty')
            ->join('order_products', 'order_products.order_id', 'orders.id')
            
            ->whereBetween('orders.created_at', [$date_from, $date_to])->latest('orders.id');
         // If teams is null, no need to filter by user_id
        if (!empty($teams)) {
            $query->whereIn('orders.user_id', $teams);
        }
        // Initial query for user no order reasons
        $query2 = UserNoOrderReason::select('user_no_order_reasons.user_id', 'user_no_order_reasons.store_id', 'user_no_order_reasons.comment AS noordercomment', 'user_no_order_reasons.description', 'user_no_order_reasons.date', 'user_no_order_reasons.time', 'user_no_order_reasons.created_at')
            
            ->whereBetween('user_no_order_reasons.created_at', [$date_from, $date_to])->latest('user_no_order_reasons.id');
         // Apply the same logic for no order reasons
        if (!empty($teams)) {
            $query2->whereIn('user_no_order_reasons.user_id', $teams);
        }
        //$mergedData = [];
        $filename = "lux-store-wise-sales-order-dump-{$date_from} to {$date_to}.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return Response::stream(function () use ($query, $query2) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['SR', 'ORDER NO', 'ORDER TYPE', 'ZSM', 'RSM', 'SM', 'ASM', 'EMPLOYEE', 'EMPLOYEE CODE', 'EMPLOYEE DESIGNATION', 'EMPLOYEE HQ', 'EMPLOYEE DATE OF JOINING', 'DISTRIBUTOR', 'DISTRIBUTOR ERP ID', 'DISTRIBUTOR CITY', 'DISTRIBUTOR STATE', 'STORE', 'STORE UNIQUE CODE', 'STORE OWNER NAME', 'STORE CONTACT', 'STORE ADDRESS', 'STORE STATE', 'STORE AREA', 'STORE TOWN/CITY', 'STORE PINCODE', 'STORE CREATION DATE', 'CATEGORY', 'PRODUCT RANGE', 'PRODUCT NAME', 'STYLE NO', 'COLOR', 'SIZE', 'QTY', 'NOTE', 'NO SALES REASON', 'NO SALES REASON DESCRIPTION', 'IS TELEPHONIC', 'DATE', 'TIME']);

            $count = 1;

            // Process orders in chunks
            $query->chunk(1000, function ($orders) use ($file, &$count) {
                foreach ($orders as $row) {
                    //$mergedData[] = $row->toArray();
                    // Fetch related data and prepare for CSV output
                    $this->writeRowToCsv($file, $row, $count);
                    $count++;
                }
            });

            // Process no order reasons in chunks
            $query2->chunk(100, function ($noOrderReasons) use ($file, &$count) {
                foreach ($noOrderReasons as $row) {
                     //$mergedData[] = $row->toArray();
                    // Adjust row data as needed for CSV output
                    $this->writeRowToCsv($file, $row, $count);
                    $count++;
                }
            });

            fclose($file);
        }, 200, $headers);
    } catch (\Exception $e) {
        // Catch and display the error
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

private function writeRowToCsv($file, $row, &$count)
{
    
    // Your logic to fetch related data and prepare the row
                $odate = date('j F, Y', strtotime($row['created_at']));
                $time = date('h:i A', strtotime($row['created_at']));
                if(!empty($row) && isset($row['stores']['created_at'])){
                $date=date('j F, Y h:i A', strtotime($row['stores']['created_at']));
                }else{
                    $date=null;
                }
                if(!empty($row) && isset($row['stores']['state_id'])){
                 $state=State::where('id',$row['stores']['state_id'])->first();
                }else{
                    $state=null;
                }
                if(!empty($row) && isset($row['stores']['area_id'])){
                $area=Area::where('id',$row['stores']['area_id'])->first();
                }else{
                    $area=null;
                }
                if(!empty($row) && isset($row['stores']['owner_fname']) && isset($row['stores']['owner_lname'])){
                   $owner= $row['stores']['owner_fname'].' '.$row['stores']['owner_lname'];
                }else{
                    $owner=null;
                }
               // if(in_array($row['product_id'],$row)){
                    if(!empty($row['product_id'])){
                       $productDetails=Product::where('id',$row['product_id'])->with('collection','category')->first();
                    }else{
                       $productDetails='';
                    
                    
                    }
                
                //}else{
                  //  $productDetails='';
                //}
               // if(in_array($row['color_id'],$row)){
                   if(!empty($row['color_id'])){
                    $color=Color::where('id',$row['color_id'])->first();
                    }else{
                     $color='';
                    }
               // }else{
               //     $color='';
              //  }
               //  if(in_array($row['size_id'],$row)){
                    if(!empty($row['size_id'])){
                    $size=Size::where('id',$row['size_id'])->first();
                    }else{
                     $size='';
                    }
                // }else{
                //    $size='';
               // }
               if(!empty($row) && isset($row['store_id'])){
                $user=Team::where('store_id',$row['store_id'])->first();
               }else{
                   $user=null;
               }
               $userName = null; 
                if(!empty($user)){
                $userName=User::where('id',$user['distributor_id'])->first();
                }
                if(!empty($row['order_type'])){
                    if($row['order_type']=='order-on-call'){
                        $tel='yes';
                        
                    }else{
                        $tel='no';
                    }
                }
            if($row->distributor_id=='' || $row->distributor_id==0){
                $distributorName= $userName->name ?? 'N/A';;
                $distributorCode=$userName->employee_id ?? 'N/A';;
                $distributorState=$userName->state?? 'N/A';;
                $distributorCity=$userName->city?? 'N/A';;
                
            }else{
                $distributorName= $row->distributors->name;
                 $distributorCode=$row->distributors->employee_id;
                $distributorState=$row->distributors->state;
                $distributorCity=$row->distributors->city;
            }
            
            fputcsv($file, [
                    $count++,
                    $row['order_no'] ??'',
					$row['order_type'] ??'',
					$user['zsm']['name'] ??'',
					$user['rsm']['name'] ??'',
					$user['sm']['name'] ??'',
					$user['asm']['name'] ??'',
					
					$row['users']['name'] ?? 'Self Order',
				
					$row['users']['employee_id'] ?? 'Self Order',
					$row['users']['designation'] ?? '',
					$row['users']['headquater'] ?? '',
					$row['users']['date_of_joining'] ?? '',
					
					$distributorName ?? '',
                    $distributorCode ?? '',
                    $distributorCity ??'' ,
                    $distributorState ??'' ,
                    $row['stores']['name'] ?? '',
                    $row['stores']['unique_code'] ?? '',
                    $owner ?? '',
                    $row['stores']['contact'] ?? '',
                    $row['stores']['address'] ?? '',
                    $state->name ?? '',
                    $area->name ?? '',
                    $row['stores']['city'] ?? '',
                    $row['stores']['pin'] ?? '',
                    $date ??'',
                    
                    $productDetails->category->name ?? '',
                    $productDetails->collection->name ?? '',
                    $productDetails->name ?? '',
                    $productDetails->style_no?? '',
                    $color->name ??'',
                    $size->name??'',
                    $row['qty']?? '',
                    $row['comment']?? '',
                    $row['noordercomment'] ??'',
                    $row['description'] ??'',
                    $tel ??'',
                    $odate ??'',
                    $time ??''
    ]);
}

private function writeNoOrderReasonRowToCsv($file, $row, &$count)
{
    // Logic to prepare no order reason data for CSV
    fputcsv($file, [
        $count,
        
        // Add other fields as required
        $row['noordercomment'] ?? '',
        $row['description'],
        // ... more fields
    ]);
}

private function getTeams(Request $request)
{
    // Your existing logic to fetch team members based on request parameters
    // Returns an array of team IDs
            $term = $request->term ? $request->term : '';
            $user_id = $request->user_id ? $request->user_id : '';
            $store_id = $request->store_id ? $request->store_id : '';
            $state_id = $request->state_id ? $request->state_id : '';
            $area_id = $request->area_id ? $request->area_id : '';
            $distributor_id = $request->distributor_id ? $request->distributor_id : '';
            // $date_from = $request->date_from ? $request->date_from : date('Y-m-01');
             //$date_to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
             $user_id = $request->ase ? $request->ase : '';
             $asm_id=$request->asm ? $request->asm : '';
             $sm_id=$request->sm ? $request->sm : '';
             $rsm_id=$request->rsm ? $request->rsm : '';
             $zsm_id=$request->zsm ? $request->zsm : '';
             $state_id=$request->state ? $request->state : '';
             $aceids=array();
             $asmids=array();
             $teams=array();
             if(!empty($zsm_id)){
                   # Query with zsm_id and get aceids
                   
                   $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->groupby('ase_id')->get()->toArray();
                 }
                 
                 if(!empty($zsm_id && $state_id)){
                       # Query with zsm_id and rsm_id and get aceids
                       $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('state_id',$state_id)->groupby('ase_id')->get()->toArray();
                     }
                     if(!empty($zsm_id && $state_id && $rsm_id)){
                       # Query with zsm_id and rsm_id and get aceids
                       $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->groupby('ase_id')->get()->toArray();
                     }
                      if(!empty($zsm_id && $state_id && $rsm_id && $sm_id)){
                       # Query with zsm_id and rsm_id and get aceids
                       $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->groupby('ase_id')->get()->toArray();
                     }
                     if(!empty($zsm_id && $state_id && $rsm_id && $sm_id && $asm_id)){
                       # Query with zsm_id and rsm_id and get aceids
                       $aceids = Team::select('ase_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->where('asm_id',$asm_id)->groupby('ase_id')->get()->toArray();
                     }
                 if(!empty($zsm_id)){
                   # Query with zsm_id and get aceids
                   
                   $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->groupby('asm_id')->get()->toArray();
                 }
                 if(!empty($zsm_id && $state_id )){
                       # Query with zsm_id and rsm_id and get aceids
                       $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('state_id',$state_id)->groupby('asm_id')->get()->toArray();
                     }
                     if(!empty($zsm_id && $state_id && $rsm_id)){
                       # Query with zsm_id and rsm_id and get aceids
                       $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->groupby('asm_id')->get()->toArray();
                     }
                      if(!empty($zsm_id && $state_id && $rsm_id && $sm_id)){
                       # Query with zsm_id and rsm_id and get aceids
                       $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->groupby('asm_id')->get()->toArray();
                     }
                     if(!empty($zsm_id && $state_id && $rsm_id && $sm_id && $asm_id)){
                       # Query with zsm_id and rsm_id and get aceids
                       $asmids = Team::select('asm_id')->where('zsm_id',$zsm_id)->where('rsm_id',$rsm_id)->where('sm_id',$sm_id)->where('asm_id',$asm_id)->groupby('asm_id')->get()->toArray();
                     }
                     $teams=array_merge($aceids,$asmids);
}
    
    public function orderDumpOld(Request $request)
  {
    try {
        $mergedObject=array();
        $date_from = $request->date_from ? date('Y-m-d', strtotime($request->date_from)) : '';
        $date_to = $request->date_to ? date('Y-m-d', strtotime($request->date_to . '+1 day')) : '';

        $zsm_id = $request->zsm ? $request->zsm : '';
        $asm_id = $request->asm ? $request->asm : '';
        $rsm_id = $request->rsm ? $request->rsm : '';
        $sm_id = $request->sm ? $request->sm : '';
        $state_id = $request->state ? $request->state : '';
        $user_id = $request->ase ? $request->ase : '';
        // Get teams based on provided filters
        $teams = Team::query()
            ->when($zsm_id, fn($query) => $query->where('zsm_id', $zsm_id))
            ->when($state_id, fn($query) => $query->where('state_id', $state_id))
            ->when($rsm_id, fn($query) => $query->where('rsm_id', $rsm_id))
            ->when($sm_id, fn($query) => $query->where('sm_id', $sm_id))
            ->when($asm_id, fn($query) => $query->where('asm_id', $asm_id))
            ->when($user_id, fn($query) => $query->where('ase_id', $user_id))
            ->groupBy('ase_id')
            ->pluck('ase_id')
            ->toArray();

        // Query the orders
        $orders = Order::select(
            'orders.order_no',
            'orders.id',
            'orders.user_id',
            'orders.store_id',
            'orders.distributor_id',
            'orders.order_type',
            'orders.comment as comment',
            'orders.created_at',
            'order_products.product_id',
            'order_products.color_id',
            'order_products.size_id',
            'order_products.qty'
        )
        ->join('order_products', 'order_products.order_id', 'orders.id')
        ->whereIn('orders.user_id', $teams)
        ->whereBetween('orders.created_at', [$date_from, $date_to])->latest('orders.id')
        ->cursor();

        // Query the user no-order reasons
        $noOrderReasons = UserNoOrderReason::select(
            'user_no_order_reasons.user_id',
            'user_no_order_reasons.store_id',
            'user_no_order_reasons.comment as noordercomment',
            'user_no_order_reasons.description',
            'user_no_order_reasons.date',
            'user_no_order_reasons.time',
            'user_no_order_reasons.created_at'
        )
        ->whereIn('user_no_order_reasons.user_id', $teams)
        ->whereBetween('user_no_order_reasons.created_at', [$date_from, $date_to])->latest('user_no_order_reasons.id')
        ->cursor();
        $mergedObject = $orders->merge($noOrderReasons);
        $data = $mergedObject->all();
        //dd($data);
        $filename = "lux-store-wise-sales-order-dump-{$date_from}_to_{$date_to}.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->stream(function () use ($data, $headers) {
            $file = fopen('php://output', 'w');
            // CSV header
            fputcsv($file, ['SR', 'ORDER NO', 'ORDER TYPE', 'ZSM', 'RSM', 'SM', 'ASM', 'EMPLOYEE', 'EMPLOYEE CODE', 'EMPLOYEE DESIGNATION', 'EMPLOYEE HQ', 'EMPLOYEE DATE OF JOINING', 'DISTRIBUTOR', 'DISTRIBUTOR ERP ID', 'DISTRIBUTOR CITY', 'DISTRIBUTOR STATE', 'STORE', 'STORE UNIQUE CODE', 'STORE OWNER NAME', 'STORE CONTACT', 'STORE ADDRESS', 'STORE STATE', 'STORE AREA', 'STORE TOWN/CITY', 'STORE PINCODE', 'STORE CREATION DATE', 'CATEGORY', 'PRODUCT RANGE', 'PRODUCT NAME', 'STYLE NO', 'COLOR', 'SIZE', 'QTY', 'NOTE', 'NO SALES REASON', 'NO SALES REASON DESCRIPTION', 'IS TELEPHONIC', 'DATE', 'TIME']);

            // Write order data
            $count = 1;
            foreach ($data as $order) {
                $orderDate = date('j F, Y', strtotime($order->created_at));
                $orderTime = date('h:i A', strtotime($order->created_at));
                if(!empty($order) && isset($order['stores']['created_at'])){
                    $date=date('j F, Y h:i A', strtotime($order['stores']['created_at']));
                }else{
                    $date=null;
                }
                if(!empty($order) && isset($order['stores']['state_id'])){
                 $state=State::where('id',$order['stores']['state_id'])->first();
                }else{
                    $state=null;
                }
                if(!empty($order) && isset($order['stores']['area_id'])){
                $area=Area::where('id',$order['stores']['area_id'])->first();
                }else{
                    $area=null;
                }
                if(!empty($order) && isset($order['stores']['owner_fname']) && isset($order['stores']['owner_lname'])){
                   $owner= $order['stores']['owner_fname'].' '.$order['stores']['owner_lname'];
                }else{
                    $owner=null;
                }
               
                    if(!empty($order['product_id'])){
                       $productDetails=Product::where('id',$order['product_id'])->with('collection','category')->first();
                    }else{
                       $productDetails='';
                    
                    
                    }
                
               
                   if(!empty($order['color_id'])){
                    $color=Color::where('id',$order['color_id'])->first();
                    }else{
                     $color='';
                    }
               
                    if(!empty($order['size_id'])){
                    $size=Size::where('id',$order['size_id'])->first();
                    }else{
                     $size='';
                    }
               
               if(!empty($order) && isset($order['store_id'])){
                $user=Team::where('store_id',$order['store_id'])->first();
               }else{
                   $user=null;
               }
                if(!empty($user)){
                $userName=User::where('id',$user['distributor_id'])->first();
                }
                if(!empty($order['order_type'])){
                    if($order['order_type']=='order-on-call'){
                        $tel='yes';
                        
                    }else{
                        $tel='no';
                    }
                }
            if($order->distributor_id=='' || $order->distributor_id==0){
                $distributorName= $userName->name;
                $distributorCode=$userName->employee_id;
                $distributorState=$userName->state;
                $distributorCity=$userName->city;
                
            }else{
                $distributorName= $order->distributors->name;
                 $distributorCode=$order->distributors->employee_id;
                $distributorState=$order->distributors->state;
                $distributorCity=$order->distributors->city;
            }
                // Add your logic to format the data
                fputcsv($file, [
                    $count++,
                    $order->order_no,
                    $order->order_type,
                    $user['zsm']['name'] ??'',
					$user['rsm']['name'] ??'',
					$user['sm']['name'] ??'',
					$user['asm']['name'] ??'',
					
					$order['users']['name'] ?? 'Self Order',
				
					$order['users']['employee_id'] ?? 'Self Order',
					$order['users']['designation'] ?? '',
					$order['users']['headquater'] ?? '',
					$order['users']['date_of_joining'] ?? '',
					
					$distributorName ?? '',
                    $distributorCode ?? '',
                    $distributorCity ??'' ,
                    $distributorState ??'' ,
                    $order['stores']['name'] ?? '',
                    $order['stores']['unique_code'] ?? '',
                    $owner ?? '',
                    $order['stores']['contact'] ?? '',
                    $order['stores']['address'] ?? '',
                    $state->name ?? '',
                    $area->name ?? '',
                    $order['stores']['city'] ?? '',
                    $order['stores']['pin'] ?? '',
                    $date ??'',
                    
                    $productDetails->category->name ?? '',
                    $productDetails->collection->name ?? '',
                    $productDetails->name ?? '',
                    $productDetails->style_no?? '',
                    $color->name ??'',
                    $size->name??'',
                    $order['qty']?? '',
                    $order['comment']?? '',
                    $order['noordercomment'] ??'',
                    $order['description'] ??'',
                    $tel ??'',
                    $orderDate,
                    $orderTime
                ]);
            }

            // Write no-order reasons
            // foreach ($noOrderReasons as $reason) {
            //     $reasonDate = date('j F, Y', strtotime($reason->created_at));
            //     $reasonTime = date('h:i A', strtotime($reason->created_at));
            //     // Add your logic to format the data
            //     fputcsv($file, [
            //         $count++,
            //         '', // Fill other fields as needed
            //         $reason->noordercomment,
            //         $reason->description,
            //         $reasonDate,
            //         $reasonTime
            //     ]);
            // }

            fclose($file);
        }, 200, $headers);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
    
    //product wise sales report
    public function productwiseOrder(Request $request)
    { 
        $data = (object) [];
        $from =  date('Y-m-01');
        $to =  date('Y-m-d', strtotime('+1 day'));
        if(isset($request->date_from) || isset($request->date_to) || isset($request->orderNo)||isset($request->store_id)||isset($request->user_id)||isset($request->state_id)||isset($request->product_id)||isset($request->area_id)) 
		{
            $from = $request->date_from ? $request->date_from : date('Y-m-01');
            $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
            $orderNo = $request->orderNo ? $request->orderNo : '';
            $product = $request->product_id ?? '';
            $state = $request->state_id ?? '';
            $area = $request->area_id ?? '';
            $ase = $request->user_id ?? '';
 			$store_id = $request->store_id ? $request->store_id : '';
            // all order products
            $query1 = OrderProduct::join('products', 'products.id', 'order_products.product_id')
            ->join('orders', 'orders.id', 'order_products.order_id');
            $query1->when($ase, function($query1) use ($ase) {
                $query1->join('users', 'users.id', 'orders.user_id')->where('users.id', $ase);
            });
            $query1->when($product, function($query1) use ($product) {
                $query1->where('order_products.product_id', $product);
            });
            $query1->when($state, function($query1) use ($state) {
                $query1->join('stores', 'stores.id', 'orders.store_id')->where('stores.state_id', $state);
            });
            $query1->when($area, function($query1) use ($area) {
                $query1->where('stores.area_id', $area);
            });
			$query1->when($store_id, function($query1) use ($store_id) {
                $query1->where('orders.store_id', $store_id);
            });
            $query1->when($orderNo, function($query1) use ($orderNo) {
                $query1->Where('orders.order_no', 'like', '%' . $orderNo . '%');
            })->whereBetween('order_products.created_at', [$from, $to]);

            $data->all_orders = $query1->latest('orders.id')
            ->paginate(50);
           
       }else{
            $data->all_orders = OrderProduct::join('products', 'products.id', 'order_products.product_id')
            ->join('orders', 'orders.id', 'order_products.order_id')->whereBetween('order_products.created_at', [$from, $to])->with('color','size')->latest('orders.id')->paginate(50);
           
       }
        $allASEs = User::select('id','name','employee_id','state')->where('type',5)->orWhere('type',6)->where('name', '!=', null)->orderBy('name')->get();
      	$allStores = Store::select('id', 'name')->where('status',1)->orderBy('name')->get();
        $state = State::where('status',1)->groupBy('name')->orderBy('name')->get();
        $data->products = Product::where('status', 1)->orderBy('name')->get();
        return view('admin.order.product-order', compact('data','allASEs','state','request','allStores'));
    }

    //product wise order report csv download
//     public function productcsvExport(Request $request)
//     {
//         $data = (object) [];
//         $from = $request->date_from ? $request->date_from : date('Y-m-01');
//         $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
//         if(isset($request->date_from) || isset($request->date_to) || isset($request->orderNo)||isset($request->store_id)||isset($request->user_id)||isset($request->state_id)||isset($request->product_id)||isset($request->area_id)) 
// 		{
//             $from = $request->date_from ? $request->date_from : date('Y-m-01');
//             $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
//             $orderNo = $request->orderNo ? $request->orderNo : '';
//             $product = $request->product_id ?? '';
//             $state = $request->state_id ?? '';
//             $area = $request->area_id ?? '';
//             $ase = $request->user_id ?? '';
//  			$store_id = $request->store_id ? $request->store_id : '';
//             // all order products
//             $query1 = OrderProduct::join('products', 'products.id', 'order_products.product_id')
//             ->join('orders', 'orders.id', 'order_products.order_id');
//             $query1->when($ase, function($query1) use ($ase) {
//                 $query1->join('users', 'users.id', 'orders.user_id')->where('users.id', $ase);
//             });
//             $query1->when($product, function($query1) use ($product) {
//                 $query1->where('order_products.product_id', $product);
//             });
//             $query1->when($state, function($query1) use ($state) {
//                 $query1->join('stores', 'stores.id', 'orders.store_id')->where('stores.state_id', $state);
//             });
//             $query1->when($area, function($query1) use ($area) {
//                 $query1->where('stores.area_id', $area);
//             });
// 			 $query1->when($store_id, function($query1) use ($store_id) {
//                 $query1->where('orders.store_id', $store_id);
//             });
//             $query1->when($orderNo, function($query1) use ($orderNo) {
//                 $query1->Where('orders.order_no', 'like', '%' . $orderNo . '%');
//             })->whereBetween('order_products.created_at', [$from, $to]);

//             $data = $query1->latest('orders.id')
//             ->cursor();
//              $users = $data->all();
           
//       }else{
//             $data = OrderProduct::join('products', 'products.id', 'order_products.product_id')
//             ->join('orders', 'orders.id', 'order_products.order_id')->whereBetween('order_products.created_at', [$from, $to])->with('color','size')->latest('orders.id')->cursor();
//           $users = $data->all();
//       }
       
//       $filename = "lux-product-wise-sales-".$from.' to '.$to.".csv";
//             $headers = [
//         'Content-Type' => 'text/csv',
//         'Content-Disposition' => 'attachment; filename="' . $filename . '"',
//         ];


//     return Response::stream(function () use ($users, $headers) {
//         $file = fopen('php://output', 'w');
//         fputcsv($file, ['SR', 'ORDER NUMBER', 'PRODUCT STYLE NO','PRODUCT NAME', 'COLOR', 'SIZE','QUANTITY', 'SALES PERSON(ASE/ASM)', 
//              'STATE', 'AREA', 'STORE','DATE','TIME']);
//          $count = 1;
//         foreach ($users as $row) {
//               $date = date('j M Y', strtotime($row['orders']['created_at']));
// 			  $time =date('g:i A', strtotime($row['orders']['created_at']));
//             fputcsv($file, [
//                      $count++,
//                     $row['order_no'] ?? '',
//                     $row['style_no'] ?? '',
//                     $row['name'] ?? '',
//                     $row['color']['name'] ?? '',
//                     $row['size']['name'] ?? '',
//                     $row['qty'] ?? '',
//                     $row['orders']['users']['name'] ?? '',
//                     $row['orders']['stores']['states']['name'] ?? '',
//                     $row['orders']['stores']['areas']['name'] ?? '',
//                     $row['orders']['stores']['name'] ?? '',
//                     $date,
//                     $time]);
//         }

//         fclose($file);
//     }, 200, $headers);

    
//     }


public function productcsvExport(Request $request)
{
    $from = $request->date_from ? $request->date_from : date('Y-m-01');
    $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) ?: '';
    $orderNo = $request->orderNo ?? '';
    $product = $request->product_id ?? '';
    $state = $request->state_id ?? '';
    $area = $request->area_id ?? '';
    $ase = $request->user_id ?? '';
    $store_id = $request->store_id ?? '';

    // Query to fetch order products
    $query1 = OrderProduct::join('products', 'products.id', 'order_products.product_id')
        ->join('orders', 'orders.id', 'order_products.order_id');

    // Apply filters
    $query1->when($ase, function($query1) use ($ase) {
        $query1->join('users', 'users.id', 'orders.user_id')->where('users.id', $ase);
    });
    $query1->when($product, function($query1) use ($product) {
        $query1->where('order_products.product_id', $product);
    });
    $query1->when($state, function($query1) use ($state) {
        $query1->join('stores', 'stores.id', 'orders.store_id')->where('stores.state_id', $state);
    });
    $query1->when($area, function($query1) use ($area) {
        $query1->where('stores.area_id', $area);
    });
    $query1->when($store_id, function($query1) use ($store_id) {
        $query1->where('orders.store_id', $store_id);
    });
    $query1->when($orderNo, function($query1) use ($orderNo) {
        $query1->where('orders.order_no', 'like', '%' . $orderNo . '%');
    });

    // Apply date range filter
    $query1->whereBetween('order_products.created_at', [$from, $to])->latest('orders.id');

    // Set the file name
    $filename = "lux-product-wise-sales-".$from.' to '.$to.".csv";
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ];

    // Use stream response with chunking
    return Response::stream(function () use ($query1) {
        $file = fopen('php://output', 'w');
        fputcsv($file, ['SR', 'ORDER NUMBER', 'PRODUCT STYLE NO', 'PRODUCT NAME', 'COLOR', 'SIZE', 'QUANTITY', 'SALES PERSON(ASE/ASM)', 'STATE', 'AREA', 'STORE', 'DATE', 'TIME']);
        
        $count = 1;

        // Fetch records in chunks to avoid memory issues
        $query1->chunk(1000, function ($users) use ($file, &$count) {
            foreach ($users as $row) {
                $date = date('j M Y', strtotime($row->orders->created_at));
                $time = date('g:i A', strtotime($row->orders->created_at));
                fputcsv($file, [
                    $count++,
                    $row->orders->order_no ?? '',
                    $row->products->style_no ?? '',
                    $row->products->name ?? '',
                    $row->color->name ?? '',
                    $row->size->name ?? '',
                    $row->qty ?? '',
                    $row->orders->users->name ?? '',
                    $row->orders->stores->states->name ?? '',
                    $row->orders->stores->areas->name ?? '',
                    $row->orders->stores->name ?? '',
                    $date,
                    $time
                ]);
            }
        });

        fclose($file);
    }, 200, $headers);
}
	//area wise sales report
    public function areawiseOrder(Request $request)
    {
        $data = (object) [];
        $from =  date('Y-m-01');
        $to =  date('Y-m-d', strtotime('+1 day'));
        if(isset($request->date_from) || isset($request->date_to) || isset($request->state_id)||isset($request->area_id)) 
		{
            $from = $request->date_from ? $request->date_from : date('Y-m-01');
            $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
            $state = $request->state_id ?? '';
            $area = $request->area_id ?? '';
            // all order products
            $query1 = OrderProduct::select(DB::raw("(SUM(order_products.qty)) as qty"),DB::raw("areas.name as area"),DB::raw("states.name as state"))->join('orders', 'orders.id', 'order_products.order_id')->join('stores', 'stores.id', 'orders.store_id')->join('areas', 'stores.area_id', 'areas.id')->join('states', 'stores.state_id', 'states.id');
            
            $query1->when($state, function($query1) use ($state) {
                $query1->where('stores.state_id', $state);
            });
            $query1->when($area, function($query1) use ($area) {
                $query1->where('stores.area_id', $area);
            })
			->whereBetween('order_products.created_at', [$from, $to]);
            $data->all_orders = $query1->groupby('stores.area_id')
            ->paginate(50);
           
        }else{
            $data->all_orders = OrderProduct::select(DB::raw("(SUM(order_products.qty)) as qty"),DB::raw("areas.name as area"),DB::raw("states.name as state"))->join('orders', 'orders.id', 'order_products.order_id')->join('stores', 'stores.id', 'orders.store_id')->join('states', 'stores.state_id', 'states.id')->join('areas', 'stores.area_id', 'areas.id')->whereBetween('orders.created_at', [$from, $to])->groupby('stores.area_id')->paginate(50);
            
        }
        $state = State::where('status',1)->groupBy('name')->orderBy('name')->get();
        return view('admin.order.area-order', compact('data','state','request'));
    }

        //area wise order report csv download
        public function areacsvExport(Request $request)
        {
            $data = (object) [];
            $from = $request->date_from ? $request->date_from : date('Y-m-01');
            $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
            if(isset($request->date_from) || isset($request->date_to) || isset($request->state_id)||isset($request->area_id)) 
            {
                $from = $request->date_from ? $request->date_from : date('Y-m-01');
                $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
                $state = $request->state_id ?? '';
                $area = $request->area_id ?? '';
                // all order products
                $query1 = OrderProduct::select(DB::raw("(SUM(order_products.qty)) as qty"),DB::raw("areas.name as area"),DB::raw("states.name as state"))->join('orders', 'orders.id', 'order_products.order_id')->join('stores', 'stores.id', 'orders.store_id')->join('areas', 'stores.area_id', 'areas.id')->join('states', 'stores.state_id', 'states.id');
                
                $query1->when($state, function($query1) use ($state) {
                    $query1->where('stores.state_id', $state);
                });
                $query1->when($area, function($query1) use ($area) {
                    $query1->where('stores.area_id', $area);
                })
                ->whereBetween('order_products.created_at', [$from, $to]);
                $data->all_orders = $query1->groupby('stores.area_id')
                ->get();
               
            }else{
                $data->all_orders = OrderProduct::select(DB::raw("(SUM(order_products.qty)) as qty"),DB::raw("areas.name as area"),DB::raw("states.name as state"))->join('orders', 'orders.id', 'order_products.order_id')->join('stores', 'stores.id', 'orders.store_id')->join('states', 'stores.state_id', 'states.id')->join('areas', 'stores.area_id', 'areas.id')->whereBetween('orders.created_at', [$from, $to])->groupby('stores.area_id')->get();
                
            }
    
            if (count($data->all_orders) > 0) {
                $delimiter = ",";
                $filename = "lux-area-wise-sales-report-".$from.' to '.$to.".csv";
    
                // Create a file pointer 
                $f = fopen('php://memory', 'w');
    
                // Set column headers 
                $fields = array('SR', 'AREA', 'STATE','QUANTITY');
                fputcsv($f, $fields, $delimiter); 
    
                $count = 1;
    
                foreach($data->all_orders as $row) {
                   
                   
                    $lineData = array(
                        $count,
                        $row['area'] ?? '',
                        $row['state'] ?? '',
                        $row['qty'] ?? '',
                       
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

        //category wise sales report
        public function categorywiseOrder(Request $request)
        {
            $data = (object) [];
            $from = $request->date_from ? $request->date_from : date('Y-m-01');
            $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
            DB::enableQueryLog();
            if(isset($request->date_from) || isset($request->date_to)) 
            {
                $from = $request->date_from ? $request->date_from : date('Y-m-01');
                $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
                $state = $request->state_id ?? '';
                $area = $request->area_id ?? '';
                // all order products
                $query1 = OrderProduct::select(DB::raw("(SUM(order_products.qty)) as qty"),DB::raw("categories.name as category"),DB::raw("categories.id as id"))->join('orders', 'orders.id', 'order_products.order_id')->join('products', 'products.id', 'order_products.product_id')->join('categories', 'categories.id', 'products.cat_id')
                
                ->whereBetween('order_products.created_at', [$from, $to]);
                $data->all_orders = $query1->groupby('products.cat_id')
                ->paginate(50);
               
            }else{
                $data->all_orders = OrderProduct::select(DB::raw("(SUM(order_products.qty)) as qty"),DB::raw("categories.name as category"),DB::raw("categories.id as id"))->join('orders', 'orders.id', 'order_products.order_id')->join('products', 'products.id', 'order_products.product_id')->join('categories', 'categories.id', 'products.cat_id')
                
                ->whereBetween('order_products.created_at', [$from, $to])->groupby('products.cat_id')->paginate(50);
               
            }

            
            // dd(DB::getQueryLog());


            $category = Category::where('status',1)->groupBy('name')->orderBy('name')->get();
            return view('admin.order.category-order', compact('data','category','request','from','to'));
        }

          //category wise sales report
          public function categorycsvExport(Request $request)
          {
               $data = (object) [];
            $from = $request->date_from ? $request->date_from : date('Y-m-01');
            $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
            if(isset($request->date_from) || isset($request->date_to)) 
            {
                $from = $request->date_from ? $request->date_from : date('Y-m-01');
                $to = date('Y-m-d', strtotime(request()->input('date_to'). '+1 day'))? date('Y-m-d', strtotime(request()->input('date_to'). '+1 day')) : '';
                $state = $request->state_id ?? '';
                $area = $request->area_id ?? '';
                // all order products
                $query1 = Order::select(DB::raw("(SUM(order_products.qty)) as qty"),DB::raw("categories.name as category"),"orders.user_id as user_id")->join('order_products', 'orders.id', 'order_products.order_id')->join('products', 'products.id', 'order_products.product_id')->join('categories', 'categories.id', 'products.cat_id')
                
                ->whereBetween('order_products.created_at', [$from, $to]);
                $data->all_orders = $query1->groupby('products.cat_id','orders.user_id')
                ->get();
               
            }else{
                $data->all_orders = Order::select(DB::raw("(SUM(order_products.qty)) as qty"),DB::raw("categories.name as category"),"orders.user_id as user_id")->join('order_products', 'orders.id', 'order_products.order_id')->join('products', 'products.id', 'order_products.product_id')->join('categories', 'categories.id', 'products.cat_id')
                
                ->whereBetween('order_products.created_at', [$from, $to])->groupby('products.cat_id','orders.user_id')->get();
               
            }
              
            if (count($data->all_orders) > 0) {
                $delimiter = ",";
                $filename = "lux-category-wise-sales-report-".$from.' to '.$to.".csv";
    
                // Create a file pointer 
                $f = fopen('php://memory', 'w');
    
                // Set column headers 
                $fields = array('SR', 'EMPLOYEE', 'CATEGORY','QUANTITY');
                fputcsv($f, $fields, $delimiter); 
    
                $count = 1;
    
                foreach($data->all_orders as $row) {
                   
                   
                    $lineData = array(
                        $count,
                        $row['users']['name'] ?? '',
                        $row['category'] ?? '',
                        $row['qty'] ?? '',
                       
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
	 //login report
    public function loginReport(Request $request)
    {
        if (isset($request->ase) ||isset($request->zsm) || isset($request->rsm) ||isset($request->sm) ||isset($request->asm) ||  isset($request->date_from)|| isset($request->date_to)) {

            $ase = $request->ase ? $request->ase : '';
            $asm=$request->asm ? $request->asm : '';
            $sm=$request->sm ? $request->sm : '';
            $rsm=$request->rsm ? $request->rsm : '';
            $zsm=$request->zsm ? $request->zsm : '';
            $date_from = $request->date_from ? $request->date_from : '';
            $date_to = $request->date_to ? $request->date_to : '';
            $query = UserLogin::query();

            $query->when($ase, function($query) use ($ase) {
                $query->where('user_id', $ase);
            });
            $query->when($asm, function($query) use ($asm) {
                $query->where('user_id', $asm);
            });
            $query->when($rsm, function($query) use ($rsm) {
                $query->where('user_id', $rsm);
            });
            $query->when($sm, function($query) use ($sm) {
                $query->where('user_id', $sm);
            });
            $query->when($zsm, function($query) use ($zsm) {
                $query->where('user_id', $zsm);
            });

            $data = $query->where('is_login',1)->orderby('created_at','desc')->with('users')->paginate(25);
           
        } else {
            $data = UserLogin::where('is_login',1)->orderby('created_at','desc')->with('users')->paginate(25);
           
        }
        $zsm=User::select('id', 'name')->where('type', 2)->orderBy('name')->get();
        $ases = User::select('id', 'name')->where('type', 6)->orWhere('type', 5)->orderBy('name')->get();
        
    
        return view('admin.report.login-report',compact('data', 'ases','request','zsm'));
    }
	
	  //csv download
    public function loginReportcsvExport(Request $request)
    {
        if (isset($request->ase) ||isset($request->zsm) || isset($request->rsm) ||isset($request->sm) ||isset($request->asm) ||  isset($request->date_from)|| isset($request->date_to)) {

            $ase = $request->ase ? $request->ase : '';
            $asm=$request->asm ? $request->asm : '';
            $sm=$request->sm ? $request->sm : '';
            $rsm=$request->rsm ? $request->rsm : '';
            $zsm=$request->zsm ? $request->zsm : '';
            $date_from = $request->date_from ? $request->date_from : '';
            $date_to = $request->date_to ? $request->date_to : '';
            $query = UserLogin::query();

            $query->when($ase, function($query) use ($ase) {
                $query->where('user_id', $ase);
            });
            $query->when($asm, function($query) use ($asm) {
                $query->where('user_id', $asm);
            });
            $query->when($rsm, function($query) use ($rsm) {
                $query->where('user_id', $rsm);
            });
            $query->when($sm, function($query) use ($sm) {
                $query->where('user_id', $sm);
            });
            $query->when($zsm, function($query) use ($zsm) {
                $query->where('user_id', $zsm);
            });

            $data = $query->latest('id')->with('users')->get();
           
        } else {
            $data = UserLogin::latest('id')->with('users')->get();
           
        }
        if (count($data) > 0) {
            $delimiter = ",";
            $filename = "lux-login-report-".date('Y-m-d').".csv";

            // Create a file pointer
            $f = fopen('php://memory', 'w');

            // Set column headers
            $fields = array('SR', 'NSM', 'ZSM','RSM','SM','ASM','Employee','Employee Id','Employee Status','Employee Designation','Employee Date of Joining','Employee HQ','Employee Contact No','Login Status',  'Time');
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($data as $row) {
                $datetime = date('j F, Y h:i A', strtotime($row['created_at']));
                if($row->is_login==''){
                    $is_login='Inactive';
                   
                }else{
                     $is_login= 'Logged In';
                }
                $store = Store::select('name')->where('id', $row['store_id'])->first();
                $ase = User::select('name', 'mobile', 'state', 'city', 'pin')->where('id', $row['user_id'])->first();
                $findTeamDetails= findTeamDetails($row->users->id, $row->users->type);
                $lineData = array(
                    $count,
                    $findTeamDetails[0]['nsm'] ?? '',
                    $findTeamDetails[0]['zsm']?? '',
                    $findTeamDetails[0]['rsm']?? '',
                    $findTeamDetails[0]['sm']?? '',
                    $findTeamDetails[0]['asm']?? '',
                    $row->users ? $row->users->name : '',
                    $row->users->employee_id ?? '',
                    ($row->users->status == 1)  ? 'Active' : 'Inactive',
                    $row->users->designation?? '',
                    $row->users->date_of_joining?? '',
                    $row->users->headquater?? '',
                    $row->users->mobile,
                    $is_login ?? '',
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
    
    
     public function showFiles()
    {
        // Get files from the storage directory
        $files = Storage::files('public/csv'); // Adjust the path as needed

        return view('admin.order.file-download', ['files' => $files]);
    }
    
    
    public function downloadFile(Request $request)
    {
        $filePath = $request->input('file_path'); // Get the file path from the request
        $fileName = basename($filePath); // Extract the file name from the path

        // Check if the file exists in storage
        if (Storage::exists($filePath)) {
            // Generate download response
            return response()->download(storage_path('app/' . $filePath), $fileName);
        } else {
            // File not found
            abort(404);
        }
    }
    
    
    
     public function distributorOrder(Request $request)
    {
    $date_from = $request->date_from ?? null;
    $date_to = $request->date_to ? date('Y-m-d', strtotime($request->date_to . ' +1 day')) : null;
    $term = $request->term ?? '';
    $user_id = $request->user_id ?? '';
    
    $state_id = $request->state_id ?? '';
    $area_id = $request->area_id ?? '';
    $distributor_id = $request->distributor_id ?? '';

    $query = OrderDistributor::select(
        'orders_distributors.order_no', 'orders_distributors.id', 'orders_distributors.user_id', 
        'orders_distributors.distributor_name',
        'orders_distributors.order_type', 'orders_distributors.comment',
        'orders_distributors.created_at','orders_distributors.qty'
    )
    ->join('users', 'users.id', '=', 'orders_distributors.user_id')
    ;

    if ($user_id) {
        $query->where('orders_distributors.user_id', $user_id);
    }

   

    

    if ($term) {
        $query->where(function ($q) use ($term) {
            $q->where('orders_distributors.order_no', 'like', '%' . $term . '%')
              ;
        });
    }

    if ($date_from && $date_to) {
        $query->whereBetween('orders_distributors.created_at', [$date_from, $date_to]);
    } elseif ($date_from) {
        $query->whereDate('orders_distributors.created_at', '>=', $date_from);
    } elseif ($date_to) {
        $query->whereDate('orders_distributors.created_at', '<=', $date_to);
    }

    $data = $query->latest('orders_distributors.id')->paginate(25);

    $user = User::select('id', 'name')
                ->whereIn('type', [5, 6])
                ->where('status', 1)
                ->orderBy('name')
                ->get();

    $distributor = User::select('id', 'name','employee_id','state')
                       ->where('type', 7)
                       ->where('status', 1)
                       ->orderBy('name')
                       ->get();

    return view('admin.order.primary-order', compact('data', 'request', 'user',  'distributor'));

}
}

