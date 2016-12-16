<?php
session_start();

if($_SESSION['session_id'] && $_SESSION['logged_in']){
    $auth = true;
    $session_id = $_SESSION['session_id'];
}else{
    $auth = false;
    $session_id = null;
}

require('./_header.php');

$page = ($_GET['page'] ? $_GET['page'] : "home");

echo "<div class='container'>";
require("./pages/$page.php");
if($_SESSION['debug']){print_r($_SESSION);}
echo "</div>";
?>

