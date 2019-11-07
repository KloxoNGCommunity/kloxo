<?php 
include_once "lib/html/include.php"; 

$list = parse_opt($argv);

if (isset($list['nolog'])) {
	$nologtext = '--nolog';
} else {
	$nologtext = '';
}

if (lxfile_exists($sgbl->__path_slave_db)) {
	$typetext = '--type=slave';
} else {
	$typetext = '--type=master';
}

if (isset($list['without-services'])) {
	$withoutservices = '--without-services';
} else {
	$withoutservices = '';
}

system("lxphp.exe ../bin/common/tmpupdatecleanup.php {$typetext} {$withoutservices} {$nologtext}");

