### MR -- read /usr/local/lxlabs/kloxo/file/apache/conf.d/suphp.conf.original for full description ###

<IfModule !mod_suphp.c>
	LoadModule suphp_module modules/mod_suphp.so
</IfModule>

suPHP_Engine On

<?php
		foreach($phpmlist as $k => $v) {
			$w = str_replace('m', '', $v);
?>
suPHP_AddHandler x-httpd-<?=$w;?>

<?php
		}
?>
suPHP_AddHandler x-suphp-cgi

DirectoryIndex index.php

## MR -- read .htaccess for 'secondary php'
