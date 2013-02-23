<form method="post" action="service.php">
<input type="text" name="input" />
<select name="function">
    <option>listSMS</option>
    <option>getGoogle</option>
    <option>getLatLong</option>
</select>
<input type="Submit" value="Submit" />
</form>


<?php
error_reporting(E_ALL);
require ('Services/Twilio.php');
include ('Auth.php');

if ($_POST)
{
    $function = $_POST['function'];
    $input = $_POST['input'];
    $function($input);
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
    better_print_r($json);
}

function getLatLong($sid)
{
    $client = new Services_Twilio(SID, TOKEN);
    $address = $client->account->sms_messages->get($sid)->body;
    $json = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address)."&components=administrative_area:GA|country:US|locality:Atlanta&sensor=false");
    better_print_r(json_decode($json)->results[0]->geometry->location);
}

function better_print_r($input)
{
    echo "<pre>";
    print_r($input);
    echo "</pre>";
}
?>

