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
    file_put_contents("log.txt", file_get_contents("php://input"));
    break;
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
    better_print_r($lng);
    
    $conn = new PDO("mysql:host=."HOST.";dbname=".DB,USER,PASS);
}

function better_print_r($input)
{
    echo "<pre>";
    print_r($input);
    echo "</pre>";
}
?>

