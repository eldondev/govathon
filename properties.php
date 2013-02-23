<? 
$db = new PDO ('mysql:host=localhost;dbname=teamvacant', 'root', 'hello' );
$stmt = $db->prepare("select * from properties");
    $out = array();
    foreach ($db->query("select * from properties") as $row) {
        array_push($out, array("lat" => $row['lat'], "lon" => $row['lon'], "address" => $row['address'], "id" => $row['id']));
    }
    print json_encode($out);
?>
