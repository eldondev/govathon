<?php
require ('Services/Twilio.php');
include ('Auth.php');

$parsedurl = parse_url($_SERVER['REQUEST_URI']);
$query = explode("=",$parsedurl['query']);
$function = $query[0];
$parameter = $query[1];

switch($function)
{
    case "listSMS":
        listSMS();
        break;
        
    case "getGoogle":
        $googleRet = getGoogle($parameter);
        better_print_r($googleRet);
        break;
       
    case "putAddress":
        putAddress($parameter);
        break;
}



if ($_POST)
{
    file_put_contents("log2.txt", file_get_contents("php://input"));
    $post = explode("&", file_get_contents("php://input"));
    $address = explode("=", $post[1]);
    $fromWhole = explode("=", $post[14]);
    $from = substr($fromWhole[1], 4);
    newPutAddress($address[1], $from, false);
    /*$sidSplit = explode("=", $post[1]);
    $sid = $sidSplit[1];
    $ret = putAddress($sid);
    file_put_contents("log.txt", $ret);*/
}
else
{
    $post = explode("&", file_get_contents("log2.txt"));
    $address = explode("=", $post[1]);
    $fromWhole = explode("=", $post[14]);
    $from = substr($fromWhole[1], 4);
    newPutAddress($address[1], $from, true);
}

function listSMS()
{
    $client = new Services_Twilio(SID, TOKEN);
    foreach ($client->account->sms_messages as $message) 
    {
        $message = $client->account->sms_messages->get($message->sid);
        better_print_r($message);
    }
}

function getGoogle($sid)
{
    $client = new Services_Twilio(SID, TOKEN);
    $address = $client->account->sms_messages->get($sid)->body;
    $json = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address)."&components=administrative_area:GA|country:US|locality:Atlanta&sensor=false");
    return $json;
}

function putAddress($sid)
{
    $google = json_decode(getGoogle($sid));
    $address = $google->results[0]->formatted_address;
    $lat = $google->results[0]->geometry->location->lat;
    $lng = $google->results[0]->geometry->location->lng;
    return $lng;
    
   /* $conn = new PDO("mysql:host=".HOST.";dbname=".DB,USER,PASS);
    $title = 'PHP AJAX';
    $sql = "INSERT INTO properties (address,lat,lon) VALUES (:address,:lat,:lon)";
	$q = $conn->prepare($sql);
	$q->execute(array(':address'=>$address, ':lat'=>$lat, ':lon'=>$lng));*/
}

function newPutAddress($address, $from, $test)
{
    $json = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address)."&components=administrative_area:GA|country:US|locality:Atlanta&sensor=false"); 
    $google = json_decode($json);
    $fAddress = str_replace(",", " ", $google->results[0]->formatted_address);
    $lat = $google->results[0]->geometry->location->lat;
    $lng = $google->results[0]->geometry->location->lng;
    
    if (!$test)
    {
        $conn = new PDO("mysql:host=".HOST.";dbname=".DB,USER,PASS);
        $title = 'PHP AJAX';
        $sql = "INSERT INTO properties (address,lat,lon) VALUES (:address,:lat,:lon)";
        $q = $conn->prepare($sql);
        $q->execute(array(':address'=>$fAddress, ':lat'=>$lat, ':lon'=>$lng));
        
        $bodyMessage1 = "Thanks, we've recorded $fAddress.";
        $bodyMessage2 = "To edit your submission, go to http://ec2-23-23-39-12.compute-1.amazonaws.com/govathon/property.php?id=".$conn->lastInsertId();
        $client = new Services_Twilio(SID, TOKEN);
        $client->account->sms_messages->create('4045864114', $from, $bodyMessage1);
        $client->account->sms_messages->create('4045864114', $from, $bodyMessage2);
    }
    else
    {
        echo $fAddress;
    }
     
    file_put_contents("log.txt", $fAddress." - ".$lat." - ".$lng);
    return $fAddress;
}

function better_print_r($input)
{
    echo "<pre>";
    print_r($input);
    echo "</pre>";
}
?>

