### begin content - please not remove this line

### MR -- using php version different with default php
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

### end content - please not remove this line
