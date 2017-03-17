<?php 

include_once "lib/html/include.php";

exit_if_secondary_master();

// MR -- disable this process from kloxo start becuase make /home/<webserver>/conf/domains removed!
// lxshell_return("$sgbl->__path_php_path", "../bin/fixIpAddress.php");

$flg = "$sgbl->__path_program_start_vps_flag";

if (lxfile_exists($flg)) { exit; }

dprint("Executing fix IPADDRESS\n");

lxshell_return("$sgbl->__path_php_path", "../bin/update.php");

