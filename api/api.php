<?php
header('Content-Type: application/json');

session_start();

$json = array();


if($raw = file_get_contents('php://input')){
    
    $lib = $_GET['lib'];
    $fn = $_GET['fn'];
    
    $raw = json_decode($raw, true);
    require($lib.".php");
    $data = $raw['data'];
    $json['result'] = $fn($data);
    
}else{
    $json['result'] = "FAIL";
}

echo json_encode($json,JSON_PRETTY_PRINT);
?>