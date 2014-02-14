#!/bin/sh

if [ -f /opt/php52s/usr/bin/php ] ; then
	/opt/php52s/usr/bin/php -c /usr/local/lxlabs/kloxo/init/php52s/php.ini $*
else
	/opt/php52s/bin/php -c /usr/local/lxlabs/kloxo/init/php52s/php.ini $*
fi