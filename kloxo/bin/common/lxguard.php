<?php 

$clearflag = false;

if ($argv) {
	$since = $argv[1];
} else {
	$since = false;
}

include_once "lib/html/include.php"; 
include_once "lib/html/lxguardincludelib.php";

debug_for_backend();

lxguard_main($clearflag, $since);

