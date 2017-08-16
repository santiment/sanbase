<?php
error_reporting(E_ALL & ~E_NOTICES); ini_set('display_errors', '1');
$file = file_get_contents("http://46.102.240.191/cashflow/");
file_put_contents("static.html", $file);
?>
