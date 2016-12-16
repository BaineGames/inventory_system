<?php

session_start();
//DB Connection Information

$host   = 'localhost';
$user   = 'root';
$pass   = '';
$db_name = 'inventory_system';

global $db;

function dbc($host,$user,$pass,$db_name){

    try {
        $db = new PDO("mysql:host=$host;dbname=$db_name", "$user", "$pass");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
        print $e->getMessage() . "<br/>";
        die();
    }
    return $db;
}

function errorHandle($msg){
    //echo "<h2>ERROR:</h2><h3>$msg</h3>";
}

function dumpSession(){
    var_dump($_SESSION);
}
if(!$db){
    $db = dbc($host,$user,$pass,$db_name);
}

?>