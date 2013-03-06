<?php 

include_once "htmllib/lib/include.php"; 

//if (file_exists("/home/lxadmin/mail/domains")) {
	system("chown root:root /home/lxadmin");
	system("chmod 775 /home/lxadmin");

	system("chown root:root /home/lxadmin/mail");
	system("chmod 775 /home/lxadmin/mail");

	system("chown root:root /home/lxadmin/mail/domains");
	system("chmod 775 /home/lxadmin/mail/domains");
//}

$pass = slave_get_db_pass();
$salt = sha1(rand());

if(isRpmInstalled('qmail-toaster')) {
	print("Using qmail-toaster - fix '/home/vpopmail/etc/vpopmail.mysql'\n");
	system("sh ../bin/misc/vpop.sh 'root' '$pass' vpopmail " . $salt);
} else {
	print("Using qmail-lxcenter - fix '/home/lxadmin/mail/etc/vpopmail.mysql'\n");
	system("sh ../bin/misc/lxpop.sh 'root' '$pass' vpopmail " . $salt);
}
