php_yum="yum install -y --skip-broken"

php_exclude="--exclude=*debuginfo* --exclude=php*-xcache* --exclude=php*-eaccelerator* --exclude=php*-pecl-zendopcache* --exclude=php*-pgsql84* --exclude=php*-opcache* --exclude=php*-fpm-httpd* --exclude=php*-fpm-nginx* --exclude=php*-pecl-sphinx*"

php56_run="${php_yum} php56-php ${php_exclude}"

php74_run="${php_yum} php74-php mod_php74 ${php_exclude}"

php80_run="${php_yum} php80-php ${php_exclude}"

php81_run="${php_yum} php81-php-* ${php_exclude}"

php82_run="${php_yum} php82-php-* ${php_exclude}"

php83_run="${php_yum} php83-php-* ${php_exclude}"

php84_run="${php_yum} php84-php-* ${php_exclude}"