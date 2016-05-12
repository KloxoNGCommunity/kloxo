<?php 

include_once "lib/html/include.php"; 

$kloxo_file_path = $sgbl->__path_program_root . "/file/ssl";
$kloxo_ssl_path = "/home/kloxo/ssl/";
$kloxo_etc_path = $sgbl->__path_program_root . "/etc";

$list = lscandir_without_dot_or_underscore($kloxo_ssl_path);

foreach($list as $l) {
	if (cse($l, ".crt")) {
		$newlist[] = basename($l, ".crt");
	} else {
		continue;
	}
}

// MR -- using exec because lxshell_return not work!
exec("cat {$kloxo_file_path}/default.key {$kloxo_file_path}/default.crt > {$kloxo_file_path}/default.pem");

// MR -- not use .ca because trouble with hiawatha and already expired
foreach($newlist as $n) {
	lxfile_cp("{$kloxo_file_path}/default.crt", "{$kloxo_ssl_path}/$n.crt");
	lxfile_cp("{$kloxo_file_path}/default.key", "{$kloxo_ssl_path}/$n.key");
//	lxfile_cp("{$kloxo_file_path}/default.ca", "{$kloxo_ssl_path}/$n.ca");
	lxfile_cp("{$kloxo_file_path}/default.pem", "{$kloxo_ssl_path}/$n.pem");
}

lxfile_cp("{$kloxo_file_path}/default.crt", "{$kloxo_etc_path}/program.crt");
lxfile_cp("{$kloxo_file_path}/default.key", "{$kloxo_etc_path}/program.key");
// lxfile_cp("{$kloxo_file_path}/default.ca", "{$kloxo_etc_path}/program.ca");
lxfile_cp("{$kloxo_file_path}/default.pem", "{$kloxo_etc_path}/program.pem");
