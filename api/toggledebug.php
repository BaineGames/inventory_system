<?php
session_start();
if(!$_SESSION['debug']){
    $_SESSION['debug'] = true;
}else{
    unset($_SESSION['debug']);
}
?>