### begin content - please not remove this line

#<IfModule mod_rewrite.c>
#	## MR -- authentically for letsencrypt for webroot-based
#	RewriteRule /\.|^\.(?!well-known/) - [F]
#</FilesMatch>

### MR -- using php version different with default php
### 1. Using suphp
### - Copy between '#<FilesMatch \.php$>' to '#</FilesMatch>' and 
###   then remove '#' from '#<FilesMatch', '#</FilesMatch>' and one of '#SetHandler'

#<FilesMatch \.php$>
	#SetHandler x-httpd-php
<?php
		foreach($phpmlist as $k => $v) {
			$v = str_replace('m', '', $v);
?>
	#SetHandler x-httpd-<?=$v;?>

<?php
		}
?>
#</FilesMatch>

### OR

### 2. Using fcgid
### - Copy from '#Options' to '#FCGIWrapper' and 
###   then remove '#' for one of '#FCGIWrapper'

#Options +ExecCGI
#<FilesMatch \.php$>
#	SetHandler fcgid-script
#</FilesMatch>
#FCGIWrapper /usr/bin/php-cgi .php
<?php
		foreach($phpmlist as $k => $v) {
?>
#FCGIWrapper /usr/bin/<?=$v;?>-cgi .php
<?php
		}
?>

### end content - please not remove this line
