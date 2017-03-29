#!/bin/sh

if [ "${1}" == "" ] ; then
	t='php56u'
else
	t=${1}
fi

yum deplist ${t}* \
	|grep "dependency:"\
	|grep -v "php"\
	|grep -v "httpd"\
	|grep -v "/bin"\
	|grep -v "/sbin"\
	|sed -e 's:\(GNU\_HASH\)::'\
	|sed -e 's:\.so.*::'\
	|sed -e 's:\-[0-9].*::'\
	|awk '{print $2}' \
	|sort -u