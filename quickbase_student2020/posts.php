<?php
error_reporting(0);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");



//include('cors.php');
//include('time_limit.php');
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
// temporarly extend time limit
set_time_limit(300);



$mt = microtime(true);
$timer = time();
include("time/now.fn");
$created_time=strip_tags($now);
$dt2=date("Y-m-d H:i:s");

$tt = time();




$geo_address = trim($_POST['geo_address']);
$data_policy = strip_tags($_POST['data_policy']);
$fname = strip_tags($_POST['fname']);
$email = strip_tags($_POST['email']);



$address = urlencode($geo_address);
// geocode geo location address to longitudes and latitudes

$call_url ="https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyChebOzRI0rKADR3YIQke65t33nBZrBw3A&address=$address&sensor=false";
 $res = file_get_contents($call_url);
 $json = json_decode($res, true);
//print_r($json);

        if($json['status']='OK'){

         $lat = $json['results'][0]['geometry']['location']['lat'];
         $lng = $json['results'][0]['geometry']['location']['lng'];
         $formatted_address = $json['results'][0]['formatted_address'];

}else{
echo 22;
exit();
}

         $lat = $json['results'][0]['geometry']['location']['lat'];
         $lng = $json['results'][0]['geometry']['location']['lng'];




// insert into safe table at quickbase


//include quickbase token
include('quickbase_token.php');
include('quickbase_tables.php');



$url2="https://api.quickbase.com/v1/records";
$ch2 = curl_init();
curl_setopt($ch2,CURLOPT_URL, $url2);
$useragent2 ='Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1';


$name_field = 6;
$email_field =7;
$report_status_field = 8;
$geo_address_field = 9;
$lat_field =10;
$lng_field =11;
$timing_field =12;


$post_add='

{
  "to": "'.$table_safe.'",
  "data": [
    {


      "'.$name_field.'": {
        "value": "'.$fname.'"
      },
      "'.$email_field.'": {
        "value": "'.$email.'"
      },
"'.$report_status_field.'": {
        "value": "'.$data_policy.'"
      },
"'.$geo_address_field.'": {
        "value": "'.$geo_address.'"
      },
"'.$lat_field.'": {
        "value": "'.$lat.'"
      },
"'.$lng_field.'": {
        "value": "'.$lng.'"
      },

"'.$timing_field.'": {
        "value": "'.$timer.'"
      }
 



    }
  ],

 "fieldsToReturn": [
3,
    6,
    7,
    8,
    9,
    10,
11,
12


  ]

}

';


curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
"QB-Realm-Hostname: $quickbase_domain",
"User-Agent: $useragent2",
"Authorization: QB-USER-TOKEN $access_token",
'Content-Type:application/json'
));  

//curl_setopt($ch2,CURLOPT_CUSTOMREQUEST,'GET');
curl_setopt($ch2,CURLOPT_CUSTOMREQUEST,'POST');

//curl_setopt($ch2,CURLOPT_CUSTOMREQUEST,'DELETE');
curl_setopt($ch2,CURLOPT_POSTFIELDS, $post_add);
curl_setopt($ch2,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($ch2,CURLOPT_SSL_VERIFYHOST,0);
curl_setopt($ch2,CURLOPT_RETURNTRANSFER, true);
$response2 = curl_exec($ch2);

curl_close($ch2);

//print_r($response2);
$json2 = json_decode($response2, true);
$statement= $json2["metadata"]["totalNumberOfRecordsProcessed"];


// get last inserted Id for safe table
$lastId  = $json2['data'][0]['3']['value'];




if($json2 == ''){
echo 4;
exit();
}

	
if($statement){
echo 1;	

}
else{
//echo "post could not be submitted";
echo 2;
}






?>