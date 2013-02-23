<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require 'include/property.php';

$photo = Table::find('resources', $_GET['id']);

#print_r($photo);

header('Content-type: image/jpeg');
$file = '/var/www/govathon/upload/' . $photo->get('data');

$fp = fopen($file, 'rb');
print fread($fp, '/var/www/govathon/upload/', filesize($file));
//print base64_decode($photo->data);

