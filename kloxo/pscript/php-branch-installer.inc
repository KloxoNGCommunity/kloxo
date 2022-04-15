php_yum="yum install -y --skip-broken"

php_exclude="--exclude=*debuginfo* --exclude=php*-xcache* --exclude=php*-eaccelerator* --exclude=php*-pecl-zendopcache* --exclude=php*-pgsql84* --exclude=php*-opcache* --exclude=php*-fpm-httpd* --exclude=php*-fpm-nginx* --exclude=php*-pecl-sphinx*"

if [ "$(yum list php74 |grep 'remi')" != "" ] ; then

php56_run="${php_yum} php56 php56-* ${php_exclude}"

php70_run="${php_yum} php70 php70-* ${php_exclude}"

php71_run="${php_yum} php71  php71-* ${php_exclude}"

php72_run="${php_yum} php72 php72-* ${php_exclude}"

php73_run="${php_yum} php73 php73-* ${php_exclude}"

php74_run="${php_yum} php74 php74-* ${php_exclude}"

php80_run="${php_yum} php80 php80-*${php_exclude}"

php81_run="${php_yum} php80 php80-*${php_exclude}"

else
php54_run="${php_yum} php54-* --exclude=php*-mysql* --exclude=php*-pecl-memcache* ${php_exclude}"

php55u_run="${php_yum} php55u-* ${php_exclude}"
php55w_run="${php_yum} php55w-* ${php_exclude}"

php56u_run="${php_yum} php56u-* ${php_exclude}"
php56w_run="${php_yum} php56w-* ${php_exclude}"

php70u_run="${php_yum} php70u-* ${php_exclude}"
php70w_run="${php_yum} php70w-* --exclude=php*-mysql* ${php_exclude}"

php71u_run="${php_yum} php71u-* mod_php71u ${php_exclude}"
php71w_run="${php_yum} php71w-* mod_php71u --exclude=php*-mysql* ${php_exclude}"

php72u_run="${php_yum} php72u-* mod_php72u ${php_exclude}"
php72w_run="${php_yum} php72w-* mod_php72u --exclude=php*-mysql* ${php_exclude}"

php73_run="${php_yum} php73-* mod_php73 ${php_exclude}"
php73u_run="${php_yum} php73u-* mod_php73u ${php_exclude}"
php73w_run="${php_yum} php73w-* mod_php73u --exclude=php*-mysql* ${php_exclude}"

php74_run="${php_yum} php74-* mod_php74 ${php_exclude}"

php80_run="${php_yum} php80-* ${php_exclude}"

fi