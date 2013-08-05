#!/bin/sh
# To use your own php.ini, comment the next line and uncomment the following one
export PHPRC="/home/apache/etc/suphp"
## don't need declate PHP_INI_SCAN_DIR because ready setup when compile 
#export PHP_INI_SCAN_DIR="/opt/php52s/etc/php.d"
export PHP_FCGI_CHILDREN=5
export PHP_FCGI_MAX_REQUESTS=500
exec /opt/php52s/bin/php-cgi
