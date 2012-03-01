#!/bin/bash
# Shell Script To Run PHP5 using mod_fastcgi under Apache 2.x
# Tested under Red Hat Enterprise Linux / CentOS 5.x
### Set PATH ###
PHP_CGI=/usr/bin/php-cgi
PHP_FCGI_CHILDREN=5
PHP_FCGI_MAX_REQUESTS=1000
### no editing below ###
export PHP_FCGI_CHILDREN
export PHP_FCGI_MAX_REQUESTS
exec $PHP_CGI