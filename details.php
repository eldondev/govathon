<?
error_reporting(E_ALL);
ini_set('display_errors', '1');

require 'include/property.php';

$db = new PDO ('mysql:host=localhost;dbname=teamvacant', 'root', 'hello');

?>


<html>
<head>
<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.1.1.min.js"></script>
</head>
<body>

<?php

session_start();

function process_uploaded_file($id) {

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

        $filename = "$id_" . $_FILES["photo"]["name"];
        $fp      = fopen($_FILES["photo"]["tmp_name"], 'rb');
        $content = fread($fp, filesize($_FILES["photo"]["tmp_name"]));
        $_SESSION['file_content'] = base64_encode($content);

        return $filename;
      }
    }
  else
    {
      //echo "Invalid file";
    }

  return null;
}

if (isset($_GET['id'])) {
  $prop = Property::find($_GET['id']);
} else {
  $prop = new Property(array('id' => null, 'address' => null));
}

/*
$row = array('id' => null, 'address' => 'null');

$prop = new Property::find($_GET['id']);
print_r($prop);

if ($_GET['id']) {
  $stmt = $db->prepare("SELECT * FROM properties WHERE id = ?");
  $stmt->execute(array($_GET['id']));
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $row = $rows[0];
}
*/

if (isset($_POST['submit'])) {

  /*
  $missing = null;
  if(!$_POST['']) {
    $missing = true;
  }
   */

  //$filename = print(process_uploaded_file());

  if(!isset($_SESSION['lat'])) {
    $_SESSION['lat'] = $_POST['lat'];
    $_SESSION['long'] = $_POST['long'];
  }

  $attrs = array('address', 'notes');
  foreach($attrs as $attr) {
    $row[$attr] = $_POST[$attr];
  }

  /*
  if ($_GET['id']) {
    $stmt = $db->prepare("UPDATE properties SET address = ? WHERE id = ?");
    $stmt->execute(array($row['address'], $row['id']));
  } else {
    # if valid, save
    $stmt = $db->prepare("INSERT INTO properties (address) VALUES (?)");
    $stmt->execute(array($row['address']));
    $id = $db->lastInsertId();
    header("location: details.php?id=$id"); die();
  }*/

  $prop->set('address', $row['address']);
  $prop->save();

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

</body>
</html>

