<?php 

include_once "lib/html/include.php"; 
include_once "lib/html/lxguardincludelib.php";

debug_for_backend();

lxfile_rm('/home/kloxo/lxguard/access.info');
lxfile_rm('/home/kloxo/lxguard/hitlist.info');

lxguard_main($clearflag = false, $since = $argv[1]);

