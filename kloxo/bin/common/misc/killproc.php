<?php 
include_once "lib/html/include.php"; 

$list = getRealPidlist($argv[1]);

foreach((array) $list as $l) {
	lxshell_return("kill", $l);
}
