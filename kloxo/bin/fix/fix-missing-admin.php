<?php

include_once "lib/html/include.php"; 

print("This script will be fixed an issue:\n");
print("- Run 'sh /script/resetpassword' will appear 'only_admin_can_modify_general'\n");
print("- Fail login as 'admin' with error 'This login has been Disabled'\n");

$pass = slave_get_db_pass();

exec("mysql -u root -p{$pass} kloxo < /usr/local/lxlabs/kloxo/bin/fix/admin.sql 2>&1", $out, $ret);

if ($ret !== 0) {
	print("\n* Already exists 'admin' client. Enough running 'sh /script/resetpassword'\n");
} else {
	print("\n* Process finished. Need running 'sh /script/resetpassword'\n");
}