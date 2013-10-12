<?php 

include_once "lib/html/include.php"; 

while (true) {
	print("$");
	flush();
	$string = fread(STDIN, 8096);
	print($string);
}
