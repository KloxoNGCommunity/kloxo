<?php 

include_once "lib/html/include.php"; 

initProgram('admin');

$plist = parse_opt($argv);

if (!isset($argv[1])) {
	print("Format: sh /script/changedriver <class> <driver>\n");
	exit;
} else {
	$class = $argv[1];
}

$driverapp = $gbl->getSyncClass(null, 'localhost', $class);

if (!isset($argv[2])) {
	print("Driver for '{$class}' is '{$driverapp}'\n");
	exit;
} else {
	$pgm = $argv[2];
}


$server = $login->getFromList('pserver', 'localhost');

// $os = $server->ostype;
// include "../file/driver/$os.inc";

include "../file/driver/rhel.inc";

$dr = $server->getObject('driver');

if (!array_search_bool($pgm, $driver[$class])) {
	$str = implode(" ", $driver[$class]);

	print("Current driver: {$driverapp}\n- The driver name isn't correct.\n- Available drivers for '{$class}': {$str}\n");

	exit;
}


$v = "pg_$class";
$dr->driver_b->$v = $pgm;

$dr->setUpdateSubaction();

$dr->write();

if ($class === 'web') {
	slave_save_db('driver', array('web' => $pgm));
} elseif ($class === 'webcache') {
	slave_save_db('driver', array('webcache' => $pgm));
} elseif ($class === 'dns') {
	slave_save_db('driver', array('dns' => $pgm));
} elseif ($class === 'spam') {
	slave_save_db('driver', array('spam' => $pgm));
}

print("Successfully changed Driver for $class to $pgm\n");