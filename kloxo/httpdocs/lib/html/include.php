<?php 

$path = __FILE__;
$dir = dirname(dirname(dirname($path)));
include_once "$dir/lib/html/includecore.php";

print_time("include");
include_once "$dir/lib/php/lxclass.php"  ;
include_once "$dir/lib/html/commonfslib.php";
include_once "$dir/lib/html/objectactionlib.php";
include_once "$dir/lib/html/commandlinelib.php";
include_once "$dir/lib/sgbl.php";
include_once "$dir/lib/gbl.php";
include_once "$dir/lib/html/lib.php";
include_once "$dir/lib/php/lxlib.php" ;
include_once "$dir/lib/php/common.inc";
include_once "$dir/lib/html/remotelib.php";
include_once "$dir/lib/php/lxdb.php";
include_once "$dir/lib/define.php";
include_once "$dir/lib/driver_define.php";
include_once "$dir/lib/sgbl.php";
include_once "$dir/lib/common.inc";
//include_once "$dir/lib/html/xmlinclude.php";
// This is the program specific common lib. There is no need dump everything lib/html/lib.php which has become too large.
// include_once "$dir/lib/programlib.php";

// that mean no Localize before 6.2.x
if (file_exists("$dir/l18n/l18n.php")) {
	// New Localize system (Kloxo 6.2.x) Issue #397
	include_once "$dir/l18n/l18n.php";
}


if (lxfile_exists("../etc/classdefine")) {
	$list = lscandir_without_dot("../etc/classdefine");
	foreach($list as $l) {
		if (cse($l, "phps")) {
			include_once "../etc/classdefine/$l";
		}
	}
}

//print_time("include", "include");
