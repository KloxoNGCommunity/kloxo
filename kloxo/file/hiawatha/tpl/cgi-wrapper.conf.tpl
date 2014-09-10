# CGI wrapper configuration

CGIhandler = /usr/bin/perl
#CGIhandler = /usr/bin/php-cgi
CGIhandler = /usr/bin/python
CGIhandler = /usr/bin/ruby
<?php
	if (file_exists("/opt/hiawatha/usr/sbin/hiawatha")) {
?>
CGIhandler = /opt/hiawatha/usr/sbin/ssi-cgi
<?php
	} else {
?>
CGIhandler = /usr/sbin/ssi-cgi
<?php
	}
?>
#CGIextension = cgi

<?php
	foreach($userlist as &$user) {
?>
Wrap = <?=$user;?>_wrapper ; /home/<?=$user;?> ; <?=$user;?>:<?=$user;?>

<?php
	}
?>
