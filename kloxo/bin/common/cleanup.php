<?php 
include_once "htmllib/lib/include.php"; 

$list = parse_opt($argv);

$nolog  = (isset($list['nolog']))  ? $list['nolog'] : null;

if (lxfile_exists("__path_slave_db")) {
	$type = 'slave';
} else {
	$type = 'master';
}

if ($nolog) {
	system("lxphp.exe ../bin/common/tmpupdatecleanup.php --type=$type --nolog");
} else {
	system("lxphp.exe ../bin/common/tmpupdatecleanup.php --type=$type");
}
