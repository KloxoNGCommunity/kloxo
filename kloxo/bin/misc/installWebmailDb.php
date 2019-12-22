<?php 

include_once "lib/html/include.php"; 

initProgram('admin');

//system("sh /script/setup-horde");
//system("sh /script/setup-t-dah");
system("sh /script/setup-roundcube");
system("sh /script/setup-afterlogic");
// system("sh /script/setup-squirrelmail");
// system("sh /script/setup-telaen");
system("sh /script/setup-rainloop");

installChooser();
