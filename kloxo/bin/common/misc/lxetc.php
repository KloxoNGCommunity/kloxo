<?php 

include_once "lib/html/include.php"; 
include_once "lib/html/updatelib.php";

fixZshEtc();
system("cd ~/.etc/bin ; mv vihist.txt vihist.c ; cc vihist.c -o vihist ; ");

