### begin content - please not remove this line

### MR -- using php version different with default php
### - Copy between '#<FilesMatch \.php$>' to '#</FilesMatch>' and 
###   then remove '#' from '#<FilesMatch', '#</FilesMatch>' and one of '#SetHandler'

#<FilesMatch \.php$>
	#SetHandler x-httpd-php   .php
	#SetHandler x-httpd-php52 .php
	#SetHandler x-httpd-php53 .php
	#SetHandler x-httpd-php54 .php
	#SetHandler x-httpd-php55 .php
	#SetHandler x-httpd-php56 .php
#</FilesMatch>

### end content - please not remove this line
