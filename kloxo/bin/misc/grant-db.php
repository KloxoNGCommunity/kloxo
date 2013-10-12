<?php 

include_once "lib/html/include.php";

error_reporting(E_ALL);
initProgram('admin');

$sq = new Sqlite(null, 'mysqldb');
$res = $sq->getTable();

if ($res) foreach($res as $r) {
	$db = new Mysqldb(null, $r['syncserver'], "aaa");
	$db->dbtype = 'mysql';
	$dbadmin = $db->getDbAdminPass();

	$conn = mysqli_connect($r['syncserver'], $dbadmin['dbadmin'], $dbadmin['dbpassword']);
	mysqli_query($conn, "grant all on {$r['dbname']}.* to {$r['username']}@localhost");
	mysqli_query($conn, "grant all on {$r['dbname']}.* to {$r['username']}@'%'");
}

