<?php
/*
    Note:
    - Copy 'welcome.txt.tpl' (this file) to 'custom.welcome.txt.tpl' and then
	  program will process custom file
	- By default, ready inputs are:
        - name - user name
		- password - user password
		- clientname - (parent username; admin/reseller)
		- default_domain
		- ipaddress
		- masterserver - mostly blank or as 'localhost'
		- quota
	- Example get data from database table:

        // get data from 'ipaddress' table of 'kloxo' database
        $db = new Sqlite($this->__masterserver, 'ipaddress');
		// result IP in array format
        $iplist = $db->getRowsWhere("syncserver = 'localhost'");
*/

    // Code write here

?>
Thank you for choosing our service to meet your web hosting needs.

Your account has been created with the following details:

Username: <?=$name;?>

Password: <?=$password;?>


To log in immediately, follow this link, using your username and password:

Secure:
https://<?=$ipaddress;?>:<?=$sslport;?>/

Standard:
http://<?=$ipaddress;?>:<?=$nonsslport;?>/


Quota Information:
<?=$quota;?>



Once again, thank you for choosing our hosting service
Please dont hesitate to contact us if you have any questions

