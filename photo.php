<?php

session_start();

header('Content-type: image/jpeg');

print base64_decode($_SESSION['file_content']);

?>
