<?php
error_reporting(E_ALL);

define('DB_USERNAME', 'luxcozi');
define('DB_PASSWORD', 'root_luxcozi@2023');
define('DB_HOST', 'localhost');
define('DB_NAME', 'admin_luxcozi');

header('Content-Type:application/json');
header('Access-Control-Allow-Origin: *');

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";

if (function_exists($action)) {
    $action();
}

function conn(){
  $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Check for database connection error
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    // returing connection resource
    return $conn;
}

$y = '2023';
$m = '9';

$d = cal_days_in_month(CAL_GREGORIAN,$m,$y);

echo "days: ".$d;

$start_date = $y.'-'.$m.'-01';
$end_date = $y.'-'.$m.'-'.$d;

$period = new DatePeriod(
     new DateTime($start_date),
     new DateInterval('P1D'),
     new DateTime($end_date)
);

$all_dates = array();
$all_dates2 = array();

foreach ($period as $key => $value) {
  /*$data1 = array(
    'date'=>$value->format('Y-m-d'),
    'day'=>date('D', strtotime($value->format('Y-m-d'))
  )*/
  array_push($all_dates,$value->format('Y-m-d'));     
}

foreach($all_dates as $a){
    $data1 = array(
        'date'=>$a,
        'day'=>date('D', strtotime($a))
    );
        
    array_push($all_dates2,$data1);  
}

$data2 = array(
  'date'=>$end_date,
  'day'=>date('D', strtotime($end_date))
);
array_push($all_dates2, $data2);

$users = array();

$sql = "select * from users where type not in (1,7,4)";
$result = mysqli_query(conn(), $sql);

if (mysqli_num_rows($result)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data['id'] = $row['id'];
        $data['name'] = $row['name'];
        $data['designation'] = $row['designation'];
        $data['type'] = $row['type'];
        $data['type'] = $row['id'];
        //$data['all_dates'] = $all_dates2;

        $type = $row['type'];
        $user_id = $row['id'];
        $date_wise_attendance = array();

        if($type=2 || $type==3){
            foreach($all_dates2 as $d){
                $date = $d['date'];

                $sql1 = "select * from user_logins where user_id='$user_id' and created_at like '$date%'";
                $result1 = mysqli_query(conn(), $sql1);

                if (mysqli_num_rows($result1)) {
                    $d['is_present'] = 'Y';
                }else{
                    $d['is_present'] = 'N';
                }

                array_push($date_wise_attendance, $d);
            }
        }else{
            foreach($all_dates2 as $d){
                $date = $d->date;

                $sql2 = "select * from activities where user_id='$user_id' and date='$date'";
                $result2 = mysqli_query(conn(), $sql2);

                if (mysqli_num_rows($result2)) {
                    $d['is_present']  = 'Y';
                }else{
                    $d['is_present']  = 'N';
                }

                array_push($date_wise_attendance, $d);
            }
        }

        $data['date_wise_attendance'] = $date_wise_attendance;

        array_push($users, $data);
    }
}

echo "<pre>";
print_r($users);
?>