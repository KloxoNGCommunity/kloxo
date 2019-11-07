target="${1}"

php_yum="yum install -y --skip-broken"

php_include="mod_${target}"

php_exclude="--exclude=*debuginfo --exclude=${target}-xcache --exclude=${target}-eaccelerator \
	--exclude=${target}-pecl-zendopcache --exclude=${target}-pgsql84 --exclude=${target}-opcache \
	--exclude=${target}-fpm-httpd --exclude=${target}-fpm-nginx --exclude=${target}-pecl-sphinx \
	--exclude=${target}-mysql --exclude=${target}-pecl-memcache --exclude=${target}-pecl-memcache"


php_run="${php_yum} ${target}-* ${php_include} ${php_exclude}"


