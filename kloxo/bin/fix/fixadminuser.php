<?php 

include_once "lib/html/include.php"; 

initProgram('admin');
$list = posix_getpwnam('admin');

if (!$list) {
	os_create_system_user('admin', $login->password, 'admin', '/sbin/nologin', '/home/admin');
	lxfile_unix_chown_rec("/home/admin", "admin");
}
