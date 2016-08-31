<?php 

include_once "lib/html/include.php";

$a = array();

$pass = slave_get_db_pass();
$con = new mysqli("localhost", "root", $pass);
$con->select_db("kloxo");

$result = $con->query("SELECT nname, parent_clname FROM domain");

while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
	$d = $row['nname'];
	$u = str_replace("client-", "", $row['parent_clname']);
	$a[$d]= $u;
}

$v = var_export($a, true);

file_put_contents("/home/kloxo/httpd/cp/phpMyAdmin/domainowner.php",
	'<' . '?php' . "\n" . "\$domainownerlist = " . $v . ';');
