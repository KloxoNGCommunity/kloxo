<?php 

include_once "lib/html/include.php"; 

initProgram('admin');


//exec("sh /script/setup-horde");
//exec("sh /script/setup-t-dah");
exec("sh /script/setup-roundcube");
exec("sh /script/setup-afterlogic");
exec("sh /script/setup-squirrelmail");
exec("sh /script/setup-telaen");
exec("sh /script/setup-rainloop");

installChooser();
