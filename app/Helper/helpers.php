<?php

use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Store;
use App\Models\State;
use App\Models\Team;
use App\Models\User;
use App\Models\Visit;
use App\Models\UserLogin;
use App\Models\Area;
use App\Models\Activity;
use App\Models\OrderDistributor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
function encrptKey() {
    return 'CUSTMPSWENCDRPkey';
}
$datetime = date('Y-m-d H:i:s');

if (!function_exists('in_array_r')) {

    function in_array_r($item , $array){
        return preg_match('/"'.preg_quote($item, '/').'"/i' , json_encode($array));
    }
}

if(!function_exists('sendNotification')) {
    function sendNotification($sender, $receiver, $type, $route, $title, $body='')
    {
        $noti = new Notification();
        $noti->sender_id = $sender;
        $noti->receiver_id = $receiver;
        $noti->type = $type;
        $noti->route = $route;
        $noti->title = $title;
        $noti->body = $body;
        $noti->read_flag = 0;
        $noti->save();
    }
}

if (!function_exists('slugGenerate')) {
    function slugGenerate($title, $table) {
        $slug = Str::slug($title, '-');
        $slugExistCount = DB::table($table)->where('name', $title)->count();
        if ($slugExistCount > 0) $slug = $slug . '-' . ($slugExistCount + 1);
        return $slug;
    }
}

if (!function_exists('imageUpload')) {
    function imageUpload($image, $folder = 'image') {
        $imageName = randomGenerator();
        $imageExtension = $image->getClientOriginalExtension();
        $uploadPath = 'uploads/'.$folder.'/';

        $image->move(public_path($uploadPath), $imageName.'.'.$imageExtension);
        $imagePath = $uploadPath.$imageName.'.'.$imageExtension;
        return $imagePath;
    }
}


if (!function_exists('orderProductsUpdatedMatrix')) {
    function orderProductsUpdatedMatrix($productsArr) {
       //  dd($productsArr);
        if (count($productsArr) > 0) {
            $newProductArr = [];
            $childrenSizes = ['75', '80', '85', '90', '95', '100', '105', '110','115','120'];

            foreach($productsArr as $key => $product) {
                //dd($product);
                if (in_array($product['size']['name'], $childrenSizes)) {
                    $matchString = $product['product']['name'].'-'.$product['color']['name'];

                    if (!in_array_r($matchString, $newProductArr)) {
                        $newProductArr[] = [
                            'match_string' => $matchString,
                            'product_name' => $product['product']['name'],
                            'product_style_no' => $product['product']['style_no'],
                            'color' => $product['color']['name'],
                            '75' => ($product['size']['name'] == "75") ? $product['qty'] : 0,
                            '80' => ($product['size']['name'] == "80") ? $product['qty'] : 0,
                            '85' => ($product['size']['name'] == "85") ? $product['qty'] : 0,
                            '90' => ($product['size']['name'] == "90") ? $product['qty']: 0,
                            '95' => ($product['size']['name'] == "95") ? $product['qty'] : 0,
                            '100' => ($product['size']['name'] == "100") ? $product['qty'] : 0,
                            '105' => ($product['size']['name'] == "105") ? $product['qty'] : 0,
                            '110' => ($product['size']['name'] == "110") ? $product['qty'] : 0,
                            '115' => ($product['size']['name'] == "115") ? $product['qty'] : 0,
                            '120' => ($product['size']['name'] == "120") ? $product['qty'] : 0,
                            'total' => $product['qty'],
                        ];
                    } else {
                        $i = array_search($matchString, array_column($newProductArr, 'match_string'));
    
                        ($product['size']['name'] == "75") ? $newProductArr[$i]['75'] += $product['qty'] : $newProductArr[$i]['75'] += 0;
                        ($product['size']['name'] == "80") ? $newProductArr[$i]['80'] += $product['qty'] : $newProductArr[$i]['80'] += 0;
                        ($product['size']['name'] == "85") ? $newProductArr[$i]['85'] += $product['qty'] : $newProductArr[$i]['85'] += 0;
                        ($product['size']['name'] == "90") ? $newProductArr[$i]['90'] += $product['qty'] : $newProductArr[$i]['90'] += 0;
                        ($product['size']['name'] == "95") ? $newProductArr[$i]['95'] += $product['qty'] : $newProductArr[$i]['95'] += 0;
                        ($product['size']['name'] == "100") ? $newProductArr[$i]['100'] += $product['qty'] : $newProductArr[$i]['100'] += 0;
                        ($product['size']['name'] == "105") ? $newProductArr[$i]['105'] += $product['qty'] : $newProductArr[$i]['105'] += 0;
                        ($product['size']['name'] == "110") ? $newProductArr[$i]['110'] += $product['qty'] : $newProductArr[$i]['110'] += 0;
                        ($product['size']['name'] == "115") ? $newProductArr[$i]['115'] += $product['qty'] : $newProductArr[$i]['115'] += 0;
                        ($product['size']['name'] == "120") ? $newProductArr[$i]['120'] += $product['qty'] : $newProductArr[$i]['120'] += 0;
                        $newProductArr[$i]['total'] += $product['qty'];
                    }
                }
            }
        }
       // dd($newProductArr);
        return $newProductArr;
    }
}

if (!function_exists('orderProductsUpdatedMatrixChild')) {
    function orderProductsUpdatedMatrixChild($productsArr) {
        // dd($productsArr);
        if (count($productsArr) > 0) {
            $newProductArr = [];
            $childrenSizes = ['35', '40', '45', '50', '55', '60', '65', '70','73'];

            foreach($productsArr as $key => $product) {
                if (in_array($product['size']['name'], $childrenSizes)) {
                    $matchString = $product['product']['name'].'-'.$product['color']['name'];

                    if (!in_array_r($matchString, $newProductArr)) {
                        $newProductArr[] = [
                            'match_string' => $matchString,
                            'product_name' => $product['product']['name'],
                            'product_style_no' => $product['product']['style_no'],
                            'color' => $product['color']['name'],
                            '35' => ($product['size']['name'] == "35") ? $product['qty']  : 0,
                            '40' => ($product['size']['name'] == "40") ? $product['qty']  : 0,
                            '45' => ($product['size']['name'] == "45") ? $product['qty']  : 0,
                            '50' => ($product['size']['name'] == "50") ? $product['qty']  : 0,
                            '55' => ($product['size']['name'] == "55") ? $product['qty']  : 0,
                            '60' => ($product['size']['name'] == "60") ? $product['qty']  : 0,
                            '65' => ($product['size']['name'] == "65") ? $product['qty']  : 0,
                            '70' => ($product['size']['name'] == "70") ? $product['qty']  : 0,
							'73' => ( $product['size']['name'] == "73") ? $product['qty'] : 0,
                            
                            'total' => $product['qty'] ,
                        ];
                    } else {
                        $i = array_search($matchString, array_column($newProductArr, 'match_string'));
    
                        ($product['size']['name'] == "35") ? $newProductArr[$i]['35'] += $product['qty']  : $newProductArr[$i]['35'] += 0;
                        ($product['size']['name'] == "40") ? $newProductArr[$i]['40'] += $product['qty']  : $newProductArr[$i]['40'] += 0;
                        ($product['size']['name'] == "45") ? $newProductArr[$i]['45'] += $product['qty']  : $newProductArr[$i]['45'] += 0;
                        ($product['size']['name'] == "50") ? $newProductArr[$i]['50'] += $product['qty']  : $newProductArr[$i]['50'] += 0;
                        ($product['size']['name'] == "55") ? $newProductArr[$i]['55'] += $product['qty']  : $newProductArr[$i]['55'] += 0;
                        ($product['size']['name'] == "60") ? $newProductArr[$i]['60'] += $product['qty']  : $newProductArr[$i]['60'] += 0;
                        ($product['size']['name'] == "65") ? $newProductArr[$i]['65'] += $product['qty']  : $newProductArr[$i]['65'] += 0;
                        ($product['size']['name'] == "70") ? $newProductArr[$i]['70'] += $product['qty']  : $newProductArr[$i]['70'] += 0;
						($product['size']['name'] == "73") ? $newProductArr[$i]['73'] += $product['qty']: $newProductArr[$i]['73'] += 0;
                      
                      
                        $newProductArr[$i]['total'] += $product['qty'];
                    }
                }
            }
        }

        return $newProductArr;
    }
}


if (!function_exists('orderProductsUpdatedMatrixNew')) {
    function orderProductsUpdatedMatrixNew($productsArr) {
         //dd($productsArr);
        if (count($productsArr) > 0) {
            $newProductArr = [];
            $childrenSizes = ['S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL'];

            foreach($productsArr as $key => $product) {
                //dd($product);
                if (in_array($product['size']['name'], $childrenSizes)) {
                    $matchString = $product['product']['name'].'-'.$product['color']['name'];

                    if (!in_array_r($matchString, $newProductArr)) {
                        $newProductArr[] = [
                            'match_string' => $matchString,
                            'product_name' => $product['product']['name'],
                            'product_style_no' => $product['product']['style_no'],
                            'color' => $product['color']['name'],
                            'S' => ($product['size']['name'] == "S") ? $product['qty'] : 0,
                            'M' => ($product['size']['name'] == "M") ? $product['qty'] : 0,
                            'L' => ($product['size']['name'] == "L") ? $product['qty'] : 0,
                            'XL' => ($product['size']['name'] == "XL") ? $product['qty']: 0,
                            'XXL' => ($product['size']['name'] == "XXL") ? $product['qty'] : 0,
                            '3XL' => ($product['size']['name'] == "3XL") ? $product['qty'] : 0,
                            '4XL' => ($product['size']['name'] == "4XL") ? $product['qty'] : 0,
                           
                            'total' => $product['qty'],
                        ];
                    } else {
                        $i = array_search($matchString, array_column($newProductArr, 'match_string'));
    
                        ($product['size']['name'] == "S") ? $newProductArr[$i]['S'] += $product['qty'] : $newProductArr[$i]['S'] += 0;
                        ($product['size']['name'] == "M") ? $newProductArr[$i]['M'] += $product['qty'] : $newProductArr[$i]['M'] += 0;
                        ($product['size']['name'] == "L") ? $newProductArr[$i]['L'] += $product['qty'] : $newProductArr[$i]['L'] += 0;
                        ($product['size']['name'] == "XL") ? $newProductArr[$i]['XL'] += $product['qty'] : $newProductArr[$i]['XL'] += 0;
                        ($product['size']['name'] == "XXL") ? $newProductArr[$i]['XXL'] += $product['qty'] : $newProductArr[$i]['XXL'] += 0;
                        ($product['size']['name'] == "3XL") ? $newProductArr[$i]['3XL'] += $product['qty'] : $newProductArr[$i]['3XL'] += 0;
                        ($product['size']['name'] == "4XL") ? $newProductArr[$i]['4XL'] += $product['qty'] : $newProductArr[$i]['4XL'] += 0;
                        $newProductArr[$i]['total'] += $product['qty'];
                    }
                }
            }
        }
 
        return $newProductArr;
       
    }
}

if (!function_exists('generateOrderNumber')) {
    function generateOrderNumber(string $type, int $id) {
        if ($type == "secondary") {
            $shortOrderCode = "SC";
            $orderData = Order::select('sequence_no')->latest('id')->first();
             
            if (!empty($orderData)) {
                if (!empty($orderData->sequence_no)) {
                    $new_sequence_no = (int) $orderData->sequence_no + 1;
                } else {
                    $new_sequence_no = 1;
                }

                $ordNo = sprintf("%'.07d", $new_sequence_no);

                $store_id = $id;
                $storeData = Store::where('id', $store_id)->with('states:id,name','areas:id,name')->first();
               
                if (!empty($storeData)) {
                    $state = $storeData->states->name;
                    
                    if ($state != "UP CENTRAL" || $state != "UP East" || $state != "UP WEST") {
                        $stateCodeData = State::where('name', $state)->first();
                        $stateCode = $stateCodeData->code;
                    } else {
                        if ($state == "UP CENTRAL") $stateCode = "UPC";
                        elseif ($state == "UP East") $stateCode = "UPE";
                        elseif ($state == "UP WEST") $stateCode = "UPW";
                    }

                    $order_no = "Lux-".date('Y').'-'.$shortOrderCode.'-'.$stateCode.'-'.$ordNo;
                   
                    return [$order_no, $new_sequence_no];
                } else {
                    return false;
                }
            }
        } else {
            $shortOrderCode = "PR";
            
        }
    }
}

if (!function_exists('findManagerDetails')) {
    function findManagerDetails($userName, $userType ) {
        switch ($userType) {
            case 1:
                $namagerDetails = "";
                break;
            case 2:
                $query=Team::select('nsm_id')->where('zsm_id',$userName)->where('store_id',NULL)->groupby('zsm_id')->with('nsm')->first();
               
                if ($query) {
                    $namagerDetails = "<span class='text-dark'>NSM:</span> ".$query->nsm->name?? '';
                } else {
                    $namagerDetails = "";
                }
                break;
            case 3:
                $query=Team::select('nsm_id','zsm_id')->where('rsm_id',$userName)->where('store_id',NULL)->groupby('rsm_id')->with('nsm','zsm')->first();
                
                if ($query) {
                    $namagerDetails = "<span class='text-dark'>NSM:</span> ".$query->nsm->name." 
                    <br> 
                    <span class='text-dark'>ZSM:</span> ".$query->zsm->name;
                } else {
                    $namagerDetails = "";
                }
                break;
            case 4:
                $query=Team::select('nsm_id','zsm_id','rsm_id')->where('sm_id',$userName)->where('store_id',NULL)->orderby('id','desc')->with('nsm','zsm','rsm')->first();
                 //dd($query);
                if ($query) {
                    $namagerDetails = "<span class='text-dark'>NSM:</span> ".$query->nsm->name." 
                    <br> 
                    <span class='text-dark'>ZSM:</span> ".$query->zsm->name." 
                    <br> 
                    <span class='text-dark'>RSM:</span> ".$query->rsm->name;
                } else {
                    $namagerDetails = "";
                }
                break;
                case 5:
                   $query=Team::select('nsm_id','zsm_id','rsm_id','sm_id')->where('asm_id',$userName)->where('store_id',NULL)->orderby('id','desc')->with('nsm','zsm','rsm','sm')->first();
                    //dd($userName);
                    if ($query) {
                        $namagerDetails = "<span class='text-dark'>NSM:</span> ".$query->nsm->name." 
                        <br> 
                        <span class='text-dark'>ZSM:</span> ".$query->zsm->name." 
                        <br> 
                       
                        <span class='text-dark'>SM:</span> ".$query->sm->name;
                    } else {
                        $namagerDetails = "";
                    }
                    break;
                case 6:
                        $query=Team::select('nsm_id','zsm_id','rsm_id','sm_id','asm_id')->where('ase_id',$userName)->where('store_id',NULL)->orderby('id','desc')->with('nsm','zsm','rsm','sm','asm')->first();
                        
                        if ($query && $query->rsm) {
                            $namagerDetails = "<span class='text-dark'>NSM:</span> ".$query->nsm->name." 
                            <br> 
                            <span class='text-dark'>ZSM:</span> ".$query->zsm->name ." 
                            <br> 
                            <span class='text-dark'>RSM:</span> ".$query->rsm->name."
                            <br> 
                            <span class='text-dark'>SM:</span> ".$query->sm->name."
                            <br> 
                            <span class='text-dark'>ASM:</span> ".$query->asm->name ?? '';
                        } else {
                            $namagerDetails = "";
                        }
                        break;
				
            default: 
                $namagerDetails = "";
                break;
        }

        return $namagerDetails;
    }
}


if (!function_exists('userTypeName')) {
    function userTypeName($userType ) {
        switch ($userType) {
            case 1: $userTypeDetail = "NSM";break;
            case 2: $userTypeDetail = "ZSM";break;
            case 3: $userTypeDetail = "RSM";break;
            case 4: $userTypeDetail = "SM";break;
            case 5: $userTypeDetail = "ASM";break;
            case 6: $userTypeDetail = "ASE";break;
            case 7: $userTypeDetail = "Distributor";break;
            case 8: $userTypeDetail = "Retailer";break;
            default: $userTypeDetail = "";break;
        }
        return $userTypeDetail;
    }
}
function dates_month($month, $year) {
    $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $month_names = array();
    $date_values = array();

    for ($i = 1; $i <= $num; $i++) {
        $mktime = mktime(0, 0, 0, $month, $i, $year);
        $date = date("d (D)", $mktime);
        $month_names[$i] = $date;
        $date_values[$i] = date("Y-m-d", $mktime);
    }
    
    return ['month_names'=>$month_names,'date_values'=>$date_values];
}

function getFirstLastDayMonth($yearmonthval){
    // $yearmonthval = "2023-02";
    // First day of the month.
    $firstday = date('Y-m-01', strtotime($yearmonthval));
    // Last day of the month.
    $lastday = date('Y-m-t', strtotime($yearmonthval));
    return array('firstday'=>$firstday,'lastday'=>$lastday);
}

function dates_attendance($id, $date) {
    $day = date('D', strtotime($date));
    
    $date_wise_attendance = array();
    $d=array();
    $users = array();
    $user = User::where('id', $id)->first();

    if($user->type==2 || $user->type==3){
        
           // $res=UserLogin::join('other_activities', 'other_activities.user_id', 'user_logins.user_id')->where('user_logins.user_id',$id)->whereRaw("DATE_FORMAT(user_logins.created_at,'%Y-%m-%d')",$date)->get();
           // $res=DB::select("select * from user_logins where user_id='$id' and is_login=1 and created_at like '$date%'");
          $res= DB::table('activities')->where('user_id',$id)->whereDate('date', $date)->groupby('date')->orderby('id','asc')->first();
            if (!empty($res)) {
                $d['is_present'] = 'P';
            }else if($day=='Sun' && empty($res))
            {
                $d['is_present'] = 'W';
            }else if($date > date('Y-m-d')){
                $d['is_present'] = '-';
            }else if(!empty($res2) && $res->type=='leave') {
                  
                    $d['is_present'] = 'L';
                  
                }
            else{
                $d['is_present'] = 'A';
            }

            array_push($date_wise_attendance, $d);
        
    }else{
        
            //$res2=DB::select("select * from activities where user_id='$id' and date='$date'");
            $res2=DB::table('activities')->where('user_id',$id)->whereDate('date', $date)->groupby('date')->orderby('id','asc')->first();
            
                if (!empty($res2) && ($res2->type=='Visit Started'|| $res2->type=='Visit Ended'||  $res2->type=='Store Added' || $res2->type=='No Order Placed'|| $res2->type=='Order Upload' || $res2->type=='distributor-visit-start' || $res2->type=='distributor-visit-end'  || $res2->type=='Order On Call' || $res2->type=='meeting' ||$res2->type=='joint-work')) {
                    $d['is_present']  = 'P';
                }else if($day=='Sun' && empty($res2))
                {
                    $d['is_present'] = 'W';
                }else if($date > date('Y-m-d')){
                    $d['is_present'] = '-';
                
                }else if(!empty($res2) && $res2->type=='leave') {
                  
                    $d['is_present'] = 'L';
                  
                }
                else{
                    $d['is_present']  = 'A';
                }
            

            array_push($date_wise_attendance, $d);
        
    }

    $data['date_wise_attendance'] = $date_wise_attendance;

    array_push($users, $data);
    
    return [$users];
}

if (!function_exists('findTeamDetails')) {
    function findTeamDetails($userName, $userType ) {
        $namagerDetails = array();
        $team_wise_attendance =array();
        switch ($userType) {
            case 1:
                $namagerDetails[] = "";
                break;
            case 2:
                $query=Team::select('nsm_id','state_id','area_id')->where('zsm_id',$userName)->groupby('zsm_id')->with('nsm','states','areas')->first();
               
                if ($query) {
                    $namagerDetails['nsm'] = $query->nsm->name?? '';
                    $namagerDetails['state'] = $query->states->name?? '';
					$namagerDetails['area'] = $query->areas->name?? '';
                    $namagerDetails['zsm'] = "";
                    $namagerDetails['rsm'] = "";
                    $namagerDetails['sm'] = "";
                    $namagerDetails['asm'] = "";
                } else {
                    $namagerDetails[] = "";
                }
                break;
            case 3:
                $query=Team::select('nsm_id','zsm_id','state_id','area_id')->where('rsm_id',$userName)->groupby('rsm_id')->with('nsm','zsm','states','areas')->first();
                
                if ($query) {
                    $namagerDetails['nsm'] = $query->nsm->name?? '';
                    $namagerDetails['zsm'] = $query->zsm->name?? '';
                    $namagerDetails['state'] = $query->states->name?? '';
					$namagerDetails['area'] = $query->areas->name?? '';
                    $namagerDetails['rsm'] = "";
                    $namagerDetails['sm'] = "";
                    $namagerDetails['asm'] = "";
                } else {
                    $namagerDetails[] = "";
                }
                break;
            case 4:
                $query=Team::select('nsm_id','zsm_id','rsm_id','state_id','area_id')->where('sm_id',$userName)->orderby('id','desc')->with('nsm','zsm','rsm','states','areas')->first();
                
                if ($query) {
                    $namagerDetails['nsm'] = $query->nsm->name?? '';
                    $namagerDetails['zsm'] = $query->zsm->name?? '';
                    $namagerDetails['rsm'] = $query->rsm->name?? '';
                    $namagerDetails['state'] = $query->states->name?? '';
					$namagerDetails['area'] = $query->areas->name?? '';
                    $namagerDetails['sm'] = "";
                    $namagerDetails['asm'] = "";
                } else {
                    $namagerDetails[] = "";
                }
                break;
                case 5:
                    $query=Team::select('nsm_id','zsm_id','rsm_id','sm_id','state_id','area_id')->where('asm_id',$userName)->orderby('id','desc')->with('nsm','zsm','rsm','sm','states','areas')->first();
                    
                    if ($query) {
                        $namagerDetails['nsm'] = $query->nsm->name?? '';
                        $namagerDetails['zsm'] = $query->zsm->name?? '';
                        $namagerDetails['rsm'] = $query->rsm->name?? '';
                        $namagerDetails['sm'] = $query->sm->name?? '';
                        $namagerDetails['state'] = $query->states->name?? '';
						$namagerDetails['area'] = $query->areas->name?? '';
                        $namagerDetails['asm'] = "";
                    } else {
                        $namagerDetails[]= "";
                    }
                    break;
                case 6:
                        $query=Team::select('nsm_id','zsm_id','rsm_id','sm_id','asm_id','state_id','area_id','distributor_id')->where('ase_id',$userName)->orderby('id','desc')->with('nsm','zsm','rsm','sm','asm','states','areas','distributors')->first();
                        
                        if ($query) {
                            $namagerDetails['nsm'] = $query->nsm->name ?? '';
                            $namagerDetails['zsm'] = $query->zsm->name?? '';
                            $namagerDetails['rsm'] = $query->rsm->name?? '';
                            $namagerDetails['sm'] = $query->sm->name?? '';
                            $namagerDetails['asm'] = $query->asm->name?? '';
                            $namagerDetails['state'] = $query->states->name?? '';
							$namagerDetails['area'] = $query->areas->name?? '';
							$namagerDetails['distributor'] = $query->distributors->name?? '';
                        } else {
                            $namagerDetails[] = "";
                        }
                        break;
				case 7:
                        $query=Team::select('nsm_id','zsm_id','rsm_id','sm_id','asm_id','ase_id','state_id','area_id')->where('distributor_id',$userName)->orderby('id','desc')->with('nsm','zsm','rsm','sm','asm','ase','states','areas')->first();
                        
                        if ($query) {
                            $namagerDetails['nsm'] = $query->nsm->name ?? '';
                            $namagerDetails['zsm'] = $query->zsm->name?? '';
                            $namagerDetails['rsm'] = $query->rsm->name?? '';
                            $namagerDetails['sm'] = $query->sm->name?? '';
                            $namagerDetails['asm'] = $query->asm->name?? '';
							$namagerDetails['ase'] = $query->ase->name?? '';
							$namagerDetails['state'] = $query->states->name?? '';
							$namagerDetails['area'] = $query->areas->name?? '';
                        } else {
                            $namagerDetails[] = "";
                        }
                        break;
                
            default: 
                $namagerDetails[] = "";
                break;
        }
        array_push($team_wise_attendance, $namagerDetails);
      
        return $team_wise_attendance;
    }
}

function daysCount($from, $to,$userId) {
    $days=array();
    $d=array();
    $to = \Carbon\Carbon::parse($to);
    $from = \Carbon\Carbon::parse($from);
   
        $years = $to->diffInYears($from);
        $months = $to->diffInMonths($from);
        $weeks = $to->diffInWeeks($from);
        $days = $to->diffInDays($from);
        $hours = $to->diffInHours($from);
        $minutes = $to->diffInMinutes($from);
        $seconds = $to->diffInSeconds($from);
        $d['total_days']  = $days;
        $res2=DB::select("select * from activities where user_id='$userId' and (DATE(date) BETWEEN '".$from."' AND '".$to."') GROUP BY date");
        $d['work_count'] = count($res2);
        $leave=DB::select("select * from other_activities where user_id='$userId' and (DATE(date) BETWEEN '".$from."' AND '".$to."') and type='leave' GROUP BY date");
        $d['leave_count'] = count($leave);
        $sundays = intval($days / 7) + ($from->format('N') + $days % 7 >= 7);
        $d['weekend_count'] = $sundays;
        $storeCount=DB::select("select * from stores where user_id='$userId' and (DATE(created_at) BETWEEN '".$from."' AND '".$to."') GROUP BY created_at");
        $d['store_count'] = count($storeCount);
        $orderCount =DB::select("SELECT  IFNULL(SUM(op.qty), 0) AS product_count FROM `order_products` op
        INNER JOIN orders o ON o.id = op.order_id
        WHERE o.user_id = ".$userId."
        AND (DATE(op.created_at) BETWEEN '".$from."' AND '".$to."')
        GROUP BY o.user_id
         ");
         $d['order_count'] = $orderCount[0]->product_count ?? '';
         $orderoncallCount =DB::select("SELECT  IFNULL(SUM(op.qty), 0) AS product_count FROM `order_products` op
        INNER JOIN orders o ON o.id = op.order_id
        WHERE o.user_id = ".$userId." AND o.order_type='order-on-call'
        AND (DATE(op.created_at) BETWEEN '".$from."' AND '".$to."')
        GROUP BY o.user_id
         ");
         $d['order_on_call_count'] = $orderoncallCount[0]->product_count ?? '';
        //dd($d);
    return $d;
}




function daysProductivityCount($from, $to,$userId) {
   
    $days=array();
    $d=array();
    $to = \Carbon\Carbon::parse($to);
    $from = \Carbon\Carbon::parse($from);
        $total_sc=0;
        $years = $to->diffInYears($from);
        $months = $to->diffInMonths($from);
        $weeks = $to->diffInWeeks($from);
        $days = $to->diffInDays($from);
        $hours = $to->diffInHours($from);
        $minutes = $to->diffInMinutes($from);
        $seconds = $to->diffInSeconds($from);
        $date_from = date('Y-m-d', strtotime($from));
        $date_to = date('Y-m-d', strtotime($to));
        // $visit=DB::select("select * from visits where user_id='$userId' and (DATE_FORMAT(created_at,'%Y-%m-%d') BETWEEN '".$from."' AND '".$to."')  ORDER BY id asc");
        // $visit=DB::select("select * from visits where user_id='$id' and (DATE_FORMAT(created_at,'%Y-%m-%d')) = '$date'  ORDER BY id asc");

        $visit=DB::select("select * from visits where user_id='$userId' 
        AND DATE(created_at) >= '".$date_from."' AND DATE(created_at) <= '".$date_to."'
        ORDER BY id asc");
        
        if(!empty($visit)){
                $res=Store::select('stores.*')->join('teams', 'teams.store_id', 'stores.id')->where('stores.area_id',$visit[0]->area_id)->where('teams.distributor_id',$visit[0]->distributor_id)->get();
                }else{
                    $res=[];
                }
            
           
            $d['total_sc'] = count($res);



         $res2=DB::select("select * from activities where user_id='$userId' and (DATE(created_at) BETWEEN '".$from."' AND '".$to."') and  (type='Order Upload' or type='No Order Placed' or type='Order On Call') GROUP BY created_at");
        $d['total_tc'] = count($res2);
        $res3=DB::select("select * from orders where user_id='$userId' and (DATE(created_at) BETWEEN '".$from."' AND '".$to."') GROUP BY  store_id");
        $d['total_pc'] = count($res3);
       $res4=DB::select("select * from orders where user_id='$userId' and (DATE(created_at) BETWEEN '".$from."' AND '".$to."') GROUP BY  store_id");
           // $res4= Order::where('user_id',$id)->whereMonth(DATE_FORMAT(created_at,'%Y-%m-%d'), 'like',$month.'%')->groupby('store_id')->get();
       $d['mub'] = count($res4);
        //dd($d);
    return $d;
}


function productivityCount($id, $date) {
   
    //$day = date('D', strtotime($date));
    $month = date('m', strtotime($date));
    //dd($month);
   // $date_wise_attendance = array();
    $d=array();
    $users = array();
    $user = User::where('id', $id)->first();
   
   // if($user->type==5 || $user->type==6){
        
           // $res=UserLogin::join('other_activities', 'other_activities.user_id', 'user_logins.user_id')->where('user_logins.user_id',$id)->whereRaw("DATE_FORMAT(user_logins.created_at,'%Y-%m-%d')",$date)->get();
            $visit=DB::select("select * from visits where user_id='$id' and (DATE_FORMAT(created_at,'%Y-%m-%d')) = '$date'  ORDER BY id asc");
        
        if(!empty($visit)){
                $res=Store::select('stores.*')->join('teams', 'teams.store_id', 'stores.id')->where('stores.area_id',$visit[0]->area_id)->where('teams.distributor_id',$visit[0]->distributor_id)->get();
                }else{
                    $res=[];
                }
            
           


            $d['sc'] = count($res);
            // $d['sc'] = count($res).'visit>> '.json_encode($visit);



            $res2=DB::select("select * from activities where user_id='$id' and DATE_FORMAT(created_at,'%Y-%m-%d') = '$date' and  (type='Order Upload' or type='No Order Placed' or type='Order On Call' or type='Store Added') GROUP BY created_at");
           // if (!empty($res2)) {
              $d['tc'] = count($res2);
            //}
        //    $res3=DB::select("select * from orders where user_id='$id' and DATE_FORMAT(created_at,'%Y-%m-%d') = '$date' GROUP BY created_at and store_id");
           $res3=DB::select("select * from orders where user_id='$id' and DATE_FORMAT(created_at,'%Y-%m-%d') = '$date' GROUP BY created_at");
           //if (!empty($res3)) {
              $d['pc'] = count($res3);
           //}
            $res4=DB::select("select * from orders where user_id='$id' and MONTH(created_at) = $month GROUP BY  store_id");
           // $res4= Order::where('user_id',$id)->whereMonth(DATE_FORMAT(created_at,'%Y-%m-%d'), 'like',$month.'%')->groupby('store_id')->get();
             $d['mub'] = count($res4);
             $res5=DB::select("select * from orders where user_id='$id' and DATE_FORMAT(created_at,'%Y-%m-%d') = '$date' and  (order_type='order-on-call') GROUP BY created_at");
              $d['to'] = count($res5);
               $login=DB::select("select * from activities where user_id='$id' and (DATE_FORMAT(date,'%Y-%m-%d')) = '$date' and (type='Visit Started' or type='distributor_visit' or type='meeting' or type='joint-work')");
             
               $d['login'] = $login[0]->time ?? '';
                       $first_call=DB::select("select * from activities where user_id='$id' and (DATE_FORMAT(date,'%Y-%m-%d')) = '$date' and (type='Order Upload' or type='Order On Call' or type='No Order Placed' or type='distributor-visit-start' or type='Store Added' )  ORDER BY id asc");
                       $d['firstcall'] = $first_call[0]->time ?? '';
                       $lastActive=DB::select("select * from activities where user_id='$id' and (DATE_FORMAT(date,'%Y-%m-%d')) = '$date'   ORDER BY created_at desc");
                       $d['lastactive'] = $lastActive[0]->time ?? '';
                       
                       
                       
                    //   $beat=DB::select("select * from visits where user_id='$id' and (DATE_FORMAT(created_at,'%Y-%m-%d')) = '$date'  ORDER BY id asc");
                       $beat=Visit::where('user_id', $id)->whereDate('start_date',  $date)->get();
                       
                       
                       
                    //    $d['beat'] = $beat[0]->areas->name ?? '';
                    if (count($beat) > 0) {
                        $d['beat'] = $beat[0]->areas->name;
                        // $d['beat'] = $beat;
                    } else {
                        $d['beat'] = '';
                    }
                    
           
   // }
    
        
    
    return $d;
}


function getMonthName($dateVal){
    $data = date("d (D)", strtotime($dateVal));
    return $data;
}





//primary order

if (!function_exists('generatePrimaryOrderNumber')) {
    function generatePrimaryOrderNumber(string $type, int $id) {
        if ($type == "primary") {
            $shortOrderCode = "PR";
            $orderData = OrderDistributor::select('sequence_no')->latest('id')->first();

           // if (!empty($orderData)) {
                if (!empty($orderData->sequence_no)) {
                    $new_sequence_no = (int) $orderData->sequence_no + 1;
                } else {
                    $new_sequence_no = 1;
                }

                $ordNo = sprintf("%'.07d", $new_sequence_no);

                $distributor_id = $id;
                $storeData = User::select('state', 'city')->where('type',7)->where('id', $distributor_id)->first();
				//dd($storeData);

                if (!empty($storeData)) {
                    $state = $storeData->state;

                    if ($state != "UP CENTRAL" || $state != "UP East" || $state != "UP WEST") {
                        $stateCodeData = State::where('name', $state)->first();
                        $stateCode = $stateCodeData->code;
                    } else {
                        if ($state == "UP CENTRAL") $stateCode = "UPC";
                        elseif ($state == "UP East") $stateCode = "UPE";
                        elseif ($state == "UP WEST") $stateCode = "UPW";
                    }

                    $order_no = "Lux-".date('Y').'-'.$shortOrderCode.'-'.$stateCode.'-'.$ordNo;

                    return [$order_no, $new_sequence_no];
                } else {
                    return false;
                }
           // }
        } else {
            $shortOrderCode = "SC";
            
        }
    }
}


if (!function_exists('getFirstAndLastDayOfMonth')) {
    function getFirstAndLastDayOfMonth($yearMonth) {
        list($year, $month) = explode('-', $yearMonth);
        $firstDay = date('Y-m-01', strtotime("$year-$month-01"));
        $lastDay = date('Y-m-t', strtotime("$year-$month-01"));

        return [$firstDay, $lastDay];
    }
}


if (!function_exists('updatedSCCount')) {
    function updatedSCCount($userId, $dateFrom, $dateTo) {
        if ($userId != 'all') {
            $areaVisits = DB::select("SELECT v.area_id FROM `visits` AS v where v.user_id = '$userId' and date(v.created_at) >= '$dateFrom' and date(v.created_at) <= '$dateTo' group by area_id");
        } else {
            $areaVisits = DB::select("SELECT v.area_id FROM `visits` AS v where date(v.created_at) >= '$dateFrom' and date(v.created_at) <= '$dateTo' group by area_id");
        }

        $comparisonArr = [];
        foreach ($areaVisits as $key => $singleArea) {
            array_push($comparisonArr, $singleArea->area_id);
        }

        if ($userId != 'all') {
            $returnData = Store::where('user_id', $userId)->whereIn('area_id', $comparisonArr)->count();
        } else {
            $returnData = Store::whereIn('area_id', $comparisonArr)->count();
        }

        return $returnData;
    }
}

if (!function_exists('customEncrypt')) {
    function customEncrypt($plaintext, $ENCKEY) {
        $iv_length = openssl_cipher_iv_length('AES-256-CBC');
        $iv = openssl_random_pseudo_bytes($iv_length);
        $ciphertext = openssl_encrypt($plaintext, 'AES-256-CBC', $ENCKEY, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext, $ENCKEY, true);
        return base64_encode($iv . $hmac . $ciphertext);
    }
}

if (!function_exists('customDecrypt')) {
    function customDecrypt($ciphertext, $ENCKEY) {
        $ciphertext = base64_decode($ciphertext);
        $iv_length = openssl_cipher_iv_length('AES-256-CBC');
        $iv = substr($ciphertext, 0, $iv_length);
        $hmac = substr($ciphertext, $iv_length, 32);
        $ciphertext = substr($ciphertext, $iv_length + 32);

        // Pad IV if its length is not the expected length
        if(strlen($iv) !== $iv_length){
            $iv = str_pad($iv, $iv_length, "\0");
        }

        $original_plaintext = openssl_decrypt($ciphertext, 'AES-256-CBC', $ENCKEY, OPENSSL_RAW_DATA, $iv);
        $calculated_hmac = hash_hmac('sha256', $ciphertext, $ENCKEY, true);
        if (!hash_equals($hmac, $calculated_hmac)) {
            return false;
        }
        return $original_plaintext;
    }
}

 function convertHtmlToPdf($html, $pdfPath)
{
    $pdfFile = fopen($pdfPath, "w"); // Create a new file
    fwrite($pdfFile, $html); // Store raw HTML content
    fclose($pdfFile);
}

function SendMail($emailData, $attachmentPath = null, $ccEmail = null)
{
	if(isset($data['from']) || !empty($data['from'])) {
		$mail_from = $data['from'];
	} else {
		$mail_from = 'info@vanguardit.co';
	}
	// $mail_from = $data['from'] ? $data['from'] : 'support@onninternational.com';



    try {
        Mail::send($emailData['blade_file'], ['emailData' => $emailData], function ($message) use ($emailData, $attachmentPath, $ccEmail) {
            $message->to($emailData['email'])
                    ->subject($emailData['subject']);

            // Add CC if provided
            if ($ccEmail) {
                $message->cc($ccEmail);
            }

            // Attach a file if path exists
            if ($attachmentPath && file_exists($attachmentPath)) {
                $message->attach($attachmentPath);
            }
        });

        return true;
    } catch (\Exception $e) {
        \Log::error('Email sending failed: ' . $e->getMessage());
        return false;
    }
}
 