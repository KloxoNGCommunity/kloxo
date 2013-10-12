<?php

include_once "lib/html/include.php";

initProgram('admin');

$server = $login->getfromList('pserver', 'localhost');
$server->getandwriteipaddress();