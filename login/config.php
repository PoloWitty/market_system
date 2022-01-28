<?php
define('DB_SERVER','localhost');
define('DB_USERNAME','root');
define('DB_PASSWORD','sql');
define('DB_NAME','qing_zhou');

$link= mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_NAME);

if($link===false){
    die("ERROR: Coule not connect. ".mysqli_connect_error());
}
?>