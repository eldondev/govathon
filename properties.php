[<? 
$db = new PDO ('mysql:host=localhost;dbname=teamvacant', 'root', 'hello' );
$stmt = $db->prepare("select * from properties");

    foreach ($db->query("select * from properties") as $row) {
        print json_encode(array("lat" => $row['lat'], "lon" => $row['lon'], "address" => $row['address'], "id" => $row['id']));
    }
?>]
