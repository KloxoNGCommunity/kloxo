<?php

include_once "lib/html/include.php";

initProgram('admin');

$server = $login->getfromList('pserver', 'localhost');
$server->getandwriteipaddress();

// MR -- fix issue when start/restart kloxo will be delete /opt/configs/<webserver>/conf/domains contents
lxshell_return("sh", "/script/fixweb", "--nolog");