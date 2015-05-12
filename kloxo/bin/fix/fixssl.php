<?php 

include_once "lib/html/include.php"; 

$kloxo_file_path = $sgbl->__path_program_root . "/file/ssl";
$httpd_ssl_path = "/home/kloxo/httpd/ssl/";
$kloxo_etc_path = $sgbl->__path_program_root . "/etc";

$list = lscandir_without_dot_or_underscore($httpd_ssl_path);

foreach($list as $l) {
	if (cse($l, ".crt")) {
		$newlist[] = basename($l, ".crt");
	} else {
		continue;
	}
}

// MR -- using exec because lxshell_return not work!
exec("cat {$kloxo_file_path}/default.crt {$kloxo_file_path}/default.key > {$kloxo_file_path}/default.pem");

// MR -- remove ssl files first
exec("'rm' -rf {$httpd_ssl_path}/*");

// MR -- not use .ca because trouble with hiawatha and already expired
foreach($newlist as $n) {
	lxfile_cp("{$kloxo_file_path}/default.crt", "{$httpd_ssl_path}/$n.crt");
	lxfile_cp("{$kloxo_file_path}/default.key", "{$httpd_ssl_path}/$n.key");
//	lxfile_cp("{$kloxo_file_path}/default.ca", "{$httpd_ssl_path}/$n.ca");
	lxfile_cp("{$kloxo_file_path}/default.pem", "{$httpd_ssl_path}/$n.pem");
}

lxfile_cp("{$kloxo_file_path}/default.crt", "{$kloxo_etc_path}/program.crt");
lxfile_cp("{$kloxo_file_path}/default.key", "{$kloxo_etc_path}/program.key");
// lxfile_cp("{$kloxo_file_path}/default.ca", "{$kloxo_etc_path}/program.ca");
lxfile_cp("{$kloxo_file_path}/default.pem", "{$kloxo_etc_path}/program.pem");
