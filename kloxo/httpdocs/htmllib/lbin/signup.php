<?php 

chdir("../..");
include_once "lib/html/include.php"; 
include_once "lib/php/display.php";

initProgram('admin');
do_addform($login, "client", array('var' => 'cttype', 'val' => "customer"));

