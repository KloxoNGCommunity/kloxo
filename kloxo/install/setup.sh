#!/bin/sh

#	KloxoNG - Control Panel
#
#	Copyright (C) 2018 - KloxoNGCommunity
#
#	This program is free software: you can redistribute it and/or modify
#	it under the terms of the GNU Affero General Public License as
#	published by the Free Software Foundation, either version 3 of the
#	License, or (at your option) any later version.
#
#	This program is distributed in the hope that it will be useful,
#	but WITHOUT ANY WARRANTY; without even the implied warranty of
#	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#	GNU Affero General Public License for more details.
#
#	You should have received a copy of the GNU Affero General Public License
#	along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
#
# MRatWork - Kloxo-MR Release Setup
#
# Version: 1.0 (2013-01-11 - by Mustafa Ramadhan <mustafa@bigraf.com>)
# Version: 1.1 (2018-01-27 - by Dionysis Kladis <dkstiler@gmail.com>)
#

#define variables 

mainreponame='kloxong'
main_repo_url="https://github.com/KloxoNGCommunity/kloxong/raw/initial-rpm/"
main_release_rpm="kloxong-release.rpm"
rpm_main_pck='kloxong'
#this is for installing base packages
yum_pack1="wget zip unzip yum-utils yum-priorities yum-plugin-replace \
	vim-minimal subversion curl sudo expect"
#this is for remove packages
yum_pack2=" bind* nsd* pdns* mydns* yadifa* maradns djbdns* mysql-* mariadb-* MariaDB-* php*\
		httpd-* mod_* httpd24u* mod24u_* nginx* lighttpd* varnish* squid* trafficserver* \
		*-toaster postfix* exim* opensmtpd* esmtp* libesmtp* libmhash*"
# database specific pagkages
yum_database_pack="MariaDB MariaDB-shared"

## MR -- prohibit to install to CentOS 5 (EOL since 31 Mar 2017)
if [ "$(yum list|grep ^yum|awk '{print $3}'|grep '@')" == "" ] ; then
	echo "*** No permit to install to CentOS 5 (because EOL since 31 Mar 2017)"
	exit
fi

ppath="/usr/local/lxlabs/kloxo"

if ! [ -d ${ppath}/log ] ; then
	### must create log path because without it possible segfault for php!
	mkdir -p ${ppath}/log
fi

if [ -e /var/run/yum.pid ] ; then
	'rm' -f /var/run/yum.pid
fi

cd /

yum clean all

if rpm -qa|grep 'kloxong-release' >/dev/null 2>&1 ; then
	yum update $mainreponame* -y
else
	cd /tmp
	rpm -Uvh $main_repo_url/$main_release_rpm
	yum update $mainreponame-* -y
	
	'rm' -rf /etc/yum.repos.d/kloxo-mr.repo
	'rm' -rf /etc/yum.repos.d/kloxo-custom.repo
	'rm' -rf /etc/yum.repos.d/lxcenter.repo
	'rm' -rf /etc/yum.repos.d/lxlabs.repo

	'rm' -rf /etc/yum.repos.d/epel*.repo
fi

## trouble with mysql55 for qmail-toaster
sed -i 's/exclude\=mysql51/exclude\=mysql5/g' /etc/yum.repos.d/$mainreponame.repo

cd /

checktmpfs=$(cat /etc/fstab|grep '/tmp'|grep 'tmpfs')

if [ "${checktmpfs}" != "" ] ; then
	echo "This server have '/tmp' with 'tmpfs' detect."
	echo "Modified '/etc/fstab' where remove 'tmpfs' in '/tmp' line and then reboot."
	echo "Without remove, backup/restore may have a trouble."
	exit
fi

echo
echo "*** Ready to begin $APP_NAME setup. ***"
echo
echo "- Note some file downloads may not show a progress bar so please,"
echo "  do not interrupt the process."
echo
echo "- When it's finished, you will be presented with a welcome message and"
echo "  further instructions."
echo
#read -n 1 -p "Press any key to continue ..."
echo

APP_NAME='KloxoNG'

if [ -f ${ppath}/etc/conf/slave-db.db ] ; then
	APP_TYPE='slave'
else
	APP_TYPE='master'
fi

SELINUX_CHECK=/usr/sbin/selinuxenabled
SELINUX_CFG=/etc/selinux/config
ARCH_CHECK=$(eval uname -m)

E_SELINUX=50
E_ARCH=51
E_NOYUM=52
E_NOSUPPORT=53
E_HASDB=54
E_REBOOT=55
E_NOTROOT=85

C_OK=" OK \n"
C_NO=" NO \n"
C_MISS=" UNDETERMINED \n"

# clear

# Check if user is root.
if [ "$UID" -ne "0" ] ; then
	echo -en "Installing as \"root\"   " $C_NO
	echo -e "\a\nYou must be \"root\" to install $APP_NAME.\n\nAborting ...\n"
	exit $E_NOTROOT
else
	echo -en "Installing as \"root\"   " $C_OK
fi

# Check if selinuxenabled exists
if [ ! -f $SELINUX_CHECK ] ; then
	echo -en "SELinux not installed      " $C_MISS
else
	# Check if SElinux is enabled from exit status. 0 = Enabled; 1 = Disabled;
	eval $SELINUX_CHECK
	OUT=$?
	if [ $OUT -eq "0" ] ; then
		echo -en "SELinux disabled       " $C_NO
		setenforce 0
		echo "SELINUX=disabled" > $SELINUX_CFG
		echo -e "SELinux disabled successfully\n"
	elif [ $OUT -eq "1" ] ; then
		echo -en "SELinux disabled       " $C_OK
	fi
fi

# Check if yum is installed.
if ! [ -f /usr/sbin/yum ] && ! [ -f /usr/bin/yum ] ; then
	echo -en "Yum installed          " $C_NO
	echo -e "\a\nThe installer requires YUM to continue. Please install it and try again.\nAborting ...\n"
	exit $E_NOYUM
else
	echo -en "Yum installed          " $C_OK
fi

echo

# Start install

if [ -d /script ] ; then
	'rm' -rf /script
	ln -sf ${ppath}/pscript /script
fi

cd /

'rm' -rf *.rpm

#yum clean all

yum -y install $yum_pack1 --skip-broken

yum remove -y $yum_pack2
rpm -e pure-ftpd --noscripts
userdel postfix
rpm -e vpopmail-toaster --noscripts

if id -u postfix >/dev/null 2>&1 ; then
	userdel postfix
fi

#yum -y install mysql55 mysql55-server mysql55-libs
yum -y install $yum_database_pack
yum -y install mysqlclient* --exclude=*devel* --exclude=*debuginfo*
if ! [ -d /var/lib/mysqltmp ] ; then
	mkdir -p /var/lib/mysqltmp
fi

chown mysql:mysql /var/lib/mysqltmp
	
# MR -- always disable mysql-aio
sh /script/disable-mysql-aio
sh /script/set-mysql-default

# ToDo - probably needs reworking - currently falls back to php56 if php74 isn't available
if [ "$(yum list|grep ^'php74')" != "" ] ; then
	phpused="php74"
#	yum -y install ${phpused}u-cli ${phpused}u-mysqlnd ${phpused}u-fpm
	sh /script/php-branch-installer ${phpused}
else
	phpused="php56"
#	yum -y install ${phpused}-cli ${phpused}-mysqlnd ${phpused}-fpm
	sh /script/php-branch-installer ${phpused}u
fi

chkconfig php-fpm on >/dev/null 2>&1
	
if [ "$(uname -m)" == "x86_64" ] ; then
	ln -sf /usr/lib64/php /usr/lib/php
fi

mkdir -p /opt/${phpused}s/custom
sh /script/phpm-installer ${phpused}s -y
sh /script/fixlxphpexe ${phpused}s

cd /

export PATH=/usr/bin:/usr/sbin:/sbin:$PATH

cd ${ppath}/install

#if [ ! -f ${ppath}/install/step2.inc ] ; then
#	/usr/bin/lxphp.exe installer.php --install-type=$APP_TYPE $*
#else
	installtype=$APP_TYPE
	installstep='1'

	source ${ppath}/install/step2.inc
#fi

## set skin to simplicity
sh /script/skin-set-for-all >/dev/null 2>&1

sh /script/set-hosts >/dev/null 2>&1

echo
echo "... Wait until finished (restart services) ..."

## fix driver - always set default
sh /script/setdriver --server=localhost --class=web --driver=apache >/dev/null 2>&1
chkconfig httpd on >/dev/null 2>&1
sh /script/setdriver --server=localhost --class=webcache --driver=none >/dev/null 2>&1
sh /script/setdriver --server=localhost --class=dns --driver=bind >/dev/null 2>&1
sh /script/setdriver --server=localhost --class=spam --driver=bogofilter >/dev/null 2>&1

## use php-cgi by default
sh /script/set-kloxo-php >/dev/null 2>&1

sh /script/restart-all --force >/dev/null 2>&1

echo

