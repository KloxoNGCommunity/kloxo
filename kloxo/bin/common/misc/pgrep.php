<?php 
include_once "lib/html/include.php"; 

$list = getRealPidlist($argv[1]);
dprintr($list);

if ($list) {
	exit(0);
} else {
	exit(11);
}
