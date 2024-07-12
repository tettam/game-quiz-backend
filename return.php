<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json");


echo json_encode($array);
exit;