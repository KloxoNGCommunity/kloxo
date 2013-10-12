<?php 
include_once "lib/html/include.php"; 

$sq = new Sqlite(null, 'general');
$sq->rawQuery("update general set disable_admin = 'off' where nname = 'admin'");
