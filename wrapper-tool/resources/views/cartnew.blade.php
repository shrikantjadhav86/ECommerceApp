<?php
header('Access-Control-Allow-Headers: Content-Type, x-xsrf-token');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
header('Content-Type: application/json');
?>
{{$data}}