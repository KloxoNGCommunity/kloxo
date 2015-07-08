### begin content - please not remove this line

### MR -- using php version different with default php
### 1. Using suphp
### - Copy between '#<FilesMatch \.php$>' to '#</FilesMatch>' and 
###   then remove '#' from '#<FilesMatch', '#</FilesMatch>' and one of '#SetHandler'

#<FilesMatch \.php$>
	#SetHandler x-httpd-php
	#SetHandler x-httpd-php52
	#SetHandler x-httpd-php53
	#SetHandler x-httpd-php54
	#SetHandler x-httpd-php55
	#SetHandler x-httpd-php56
#</FilesMatch>

### OR

### 2. Using fcgid
### - Copy from '#Options' to '#FCGIWrapper' and 
###   then remove '#' one of from '#FCGIWrapper'

#Options +ExecCGI
#<FilesMatch \.php$>
#	SetHandler fcgid-script
#</FilesMatch>
#FCGIWrapper /home/kloxo/client/php.fcgi .php
#FCGIWrapper /home/kloxo/client/php52.fcgi .php
#FCGIWrapper /home/kloxo/client/php53.fcgi .php
#FCGIWrapper /home/kloxo/client/php54.fcgi .php
#FCGIWrapper /home/kloxo/client/php55.fcgi .php
#FCGIWrapper /home/kloxo/client/php56.fcgi .php

### end content - please not remove this line
