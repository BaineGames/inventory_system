<?php

require('./db.php');

$db->exec("TRUNCATE TABLE messages");
$db->exec("TRUNCATE TABLE messages");
$db->exec("TRUNCATE TABLE conversations");
$db->exec("TRUNCATE TABLE friends");
$db->exec("TRUNCATE TABLE items");
$db->exec("TRUNCATE TABLE inventory");
$db->exec("TRUNCATE TABLE users");

?>