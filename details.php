<?
error_reporting(E_ALL);
ini_set('display_errors', '1');

require 'include/property.php';

$db = new PDO ('mysql:host=localhost;dbname=teamvacant', 'root', 'hello');


function getGoogleAddress($lat, $lng)
{
  $url = "http://maps.googleapis.com/maps/api/geocode/json?ltlng=$lat,$lng&components=administrative_area:GA|country:US|locality:Atlanta&sensor=false";
  $json = file_get_contents($url);
  return $json;
}

?>

<html>
<head>
<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.1.1.min.js"></script>
</head>
<body>

<?php

session_start();
    print_r($_SESSION);

function process_uploaded_file() {

  $allowedExts = array("jpg", "jpeg", "gif", "png");
  $extension = end(explode(".", $_FILES["photo"]["name"]));
  if ((($_FILES["photo"]["type"] == "image/gif")
  || ($_FILES["photo"]["type"] == "image/jpeg")
  || ($_FILES["photo"]["type"] == "image/png")
  || ($_FILES["photo"]["type"] == "image/pjpeg"))
  && ($_FILES["photo"]["size"] < 200000)
  && in_array($extension, $allowedExts)) {
    if ($_FILES["photo"]["error"] > 0)
      {
      echo "Return Code: " . $_FILES["photo"]["error"] . "<br>";
        return null;
      }
    else
      {
        /*
        echo "Upload: " . $_FILES["photo"]["name"] . "<br>";
        echo "Type: " . $_FILES["photo"]["type"] . "<br>";
        echo "Size: " . ($_FILES["photo"]["size"] / 1024) . " kB<br>";
        echo "Temp photo: " . $_FILES["photo"]["tmp_name"] . "<br>";
         */

        $filename = $_FILES["photo"]["name"];
        //$fp      = fopen($_FILES["photo"]["tmp_name"], 'rb');
        //$content = fread($fp, filesize($_FILES["photo"]["tmp_name"]));

        
        move_uploaded_file($_FILES["photo"]["tmp_name"],
      "/var/www/govathon/upload/" . $filename);
        return $filename;

        //return base64_encode($content);
        // $content;

      }
    }
  else
    {
      //echo "Invalid file";
    }

  return null;
}

if (isset($_GET['id'])) {
  $prop = Table::find('properties', $_GET['id']);
} else {
  $prop = new Property('properties', array('id' => null, 'address' => null));
}

print_r($prop->browserAddress());

if (isset($_POST['submit'])) {



  /*
  $missing = null;
  if(!$_POST['']) {
    $missing = true;
  }
   */

  if(!isset($_SESSION['lat'])) {
    $_SESSION['lat'] = $_POST['lat'];
    $_SESSION['long'] = $_POST['long'];
  }

  $attrs = array('address', 'notes');
  foreach($attrs as $attr) {
    $row[$attr] = $_POST[$attr];
  }

  if (true) {
    $prop->set('address', $row['address']);
    $prop->save();

    $photo_content = process_uploaded_file();
    if ($photo_content) {
      $photo = new Table('resources', array('property_id' => $prop->id(),
                                            'meta' => 'photo',
                                            'data' => $photo_content));
      $photo->save();
      die();
    }

    if (isset($_SESSION['lat']) && !$prop->browserAddress()) {
      $addr = json_decode(getGoogleAddress($_SESSION['lat'], $_SESSION['long']));
      $result = $addr->results[0];
      $address = $result->formatted_address;

      $addr = new Table('resources', array('property_id' => $prop->id(),
                                           'meta' => 'browser_address',
                                           'lat' => $_SESSION['lat'],
                                           'lon' => $_SESSION['long'],
                                           'data' => $address));
      $addr->save();
    }
  }

  $id = $prop->id();
  header("location: details.php?id=$id"); die();
}

?>

<form action="" method="post" enctype="multipart/form-data">
  <div>
    <label for="address">Address</label>
    <input id="address" name="address" type="text" value="<?= $prop->get('address') ?>" />
  </div>

  <div id="photo_div">
    <label for="photo">Photo</label>
    <input id="photo" name="photo" type="file" />
  </div>

  <div>
    <label for="notes">Additional Notes</label>
    <textarea id="notes" name="notes" type="file" /><?= $row['notes'] ?></textarea>
  </div>

  <input type="hidden" id="lat" name="lat" value="" />
  <input type="hidden" id="long" name="long" value="" />

  <input type="submit" id="submit" name="submit" value="Submit Site Details" />
</form>

<? if(!isset($_SESSION['lat'])) { ?>
  <script>

    function showPosition(position) {
      console.log('setting');
      $('#lat').val(position.coords.latitude);
      $('#long').val(position.coords.longitude); 
    }

    $(function(){
      console.log('here');
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
      } else {
        console.log("Geolocation is not supported by this browser.");
      }
    });

  </script>
<? } ?>

<? foreach($prop->photos() as $photo) { ?>
  <img width="400" src="upload/<?= $photo->get('data'); ?>" />
<? } ?>

</body>
</html>

