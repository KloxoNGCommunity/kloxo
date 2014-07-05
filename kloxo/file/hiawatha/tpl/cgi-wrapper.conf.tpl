# CGI wrapper configuration

CGIhandler = /usr/bin/perl
#CGIhandler = /usr/bin/php-cgi
CGIhandler = /usr/bin/python
CGIhandler = /usr/bin/ruby
CGIhandler = /usr/bin/ssi-cgi
#CGIextension = cgi

<?php
	foreach($userlist as &$user) {
?>
Wrap = <?=$user;?>_wrapper ; /home/<?=$user;?> ; <?=$user;?>:<?=$user;?>

<?php
	}
?>
