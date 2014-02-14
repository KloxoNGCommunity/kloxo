#!/bin/sh

#	Kloxo-MR - Hosting Control Panel
#
#	Copyright (C) 2013 - MRatWork
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
# MRatWork - Kloxo-MR dev Installer
#
# Version: 1.0 (2013-01-11 - by Mustafa Ramadhan <mustafa@bigraf.com>)
#

if [ "$(rpm -qa mratwork-release)" == "" ] ; then
	cd /tmp
	wget https://github.com/mustafaramadhan/kloxo/raw/rpms/release/neutral/noarch/mratwork-release-0.0.1-1.noarch.rpm --no-check-certificate
	cd / 
	rpm -ivh mratwork-release-0.0.1-1.noarch.rpm
	yum update mratwork-release -y

	mv -f /etc/yum.repos.d/lxcenter.repo /etc/yum.repos.d/lxcenter.nonrepo
	mv -f /etc/yum.repos.d/kloxo-mr.repo /etc/yum.repos.d/kloxo-mr.nonrepo
else
	yum update mratwork-release -y
fi

cd / 

checktmpfs=$(cat /etc/fstab|grep '/tmp'|grep 'tmpfs')

if [ "${checktmpfs}" != "" ] ; then
	echo "This server have '/tmp' with 'tmpfs' detect."
	echo "Modified '/etc/fstab' where remove 'tmpfs' in '/tmp' line and then reboot."
	echo "Without remove, backup/restore may have a trouble."
	exit
fi

echo
echo "*** Ready to begin $APP_NAME install. ***"
echo
echo "- Note some file downloads may not show a progress bar so please,"
echo "  do not interrupt the process."
echo
echo "- When it's finished, you will be presented with a welcome message and"
echo "  further instructions."
echo
#read -n 1 -p "Press any key to continue ..."
echo

APP_NAME='Kloxo-MR'

if [ -f /usr/local/lxlabs/kloxo/etc/conf/slave-db.db ] ; then
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

clear

# Check if user is root.
if [ "$UID" -ne "0" ] ; then
	echo -en "Installing as \"root\"	" $C_NO
	echo -e "\a\nYou must be \"root\" to install $APP_NAME.\n\nAborting ...\n"
	exit $E_NOTROOT
else
	echo -en "Installing as \"root\"	" $C_OK
fi

# Check if selinuxenabled exists
if [ ! -f $SELINUX_CHECK ] ; then
	echo -en "SELinux not installed	   " $C_MISS
else
	# Check if SElinux is enabled from exit status. 0 = Enabled; 1 = Disabled;
	eval $SELINUX_CHECK
	OUT=$?
	if [ $OUT -eq "0" ] ; then
		echo -en "SELinux disabled	   	 " $C_NO
	    setenforce 0
		echo "SELINUX=disabled" > $SELINUX_CFG
		echo -e "SELinux disabled successfully\n"
	elif [ $OUT -eq "1" ] ; then
		echo -en "SELinux disabled	   	 " $C_OK
	fi
fi

# Check if yum is installed.
if ! [ -f /usr/sbin/yum ] && ! [ -f /usr/bin/yum ] ; then
	echo -en "Yum installed	   	    " $C_NO
	echo -e "\a\nThe installer requires YUM to continue. Please install it and try again.\nAborting ...\n"
	exit $E_NOYUM
else
	echo -en "Yum installed	   	    " $C_OK
fi

echo

# Start install

cd /

rm -rf *.rpm

yum clean all

yum -y install yum-utils yum-priorities vim-minimal subversion curl zip unzip \
	telnet screen rdate perl nano wget rpm-build

yum remove bind* mysql* mariadb* MariaDB* php* httpd* mod_* *-toaster -y

#if [ ! -f /opt/php52s/bin/php ] ; then
#	if [ -f /usr/bin/php ] ; then
#		yum -y remove php*
#	fi

	osverid=$(yum list *yum*|grep -i "@")

	## it's mean centos 6 or equal
	if [ "${osverid}" != "" ]  ; then
		yum -y install mysql mysql-server mysql-libs
	else
		yum -y install mysql55 mysql55-server mysql55-libs
	fi

	# MR -- always disable mysql-aio
	sh /script/disable-mysql-aio

	yum -y install php53u php53u-mysql

	## install after mysql55 and php53u because if mysql not exist will install 'old' mysql
	yum -y install net-snmp php52s
#fi

cd /

export PATH=/usr/bin:/usr/sbin:/sbin:$PATH

if [ -d ./kloxomr/install ] ; then
	cd ./kloxomr/install
else
	mv ./kloxomr-*.tar.gz ./kloxomr-latest.tar.gz >/dev/null 2>&1
	tar -xzf ./kloxomr-latest.tar.gz >/dev/null 2>&1
	mv ./kloxomr-6* ./kloxomr >/dev/null 2>&1
	cd ./kloxomr/install >/dev/null 2>&1
fi

cd /usr/local/lxlabs/kloxo/install

cd /usr/local/lxlabs/kloxo/install

/usr/bin/lxphp.exe installer.php --install-type=$APP_TYPE --install-from=setup $* | tee kloxo-mr_install.log

# Fix issue because sometimes kloxo database not created
for (( a=1; a<=100; a++ )) ; do
#	echo -n "$a "
	sleep 2s

	if [ $APP_TYPE == 'master' ] ; then
		if [ ! -d /var/lib/mysql/kloxo ] ; then
			cd /usr/local/lxlabs/kloxo/install
			/usr/bin/lxphp.exe installer.php --install-type=$APP_TYPE --install-from=setup --install-step=2 $* | tee kloxo-mr_install.log
		else
			break
		fi
	else 
			break
	fi
done

chkconfig httpd off > /dev/null 2>&1
httpd stop > /dev/null 2>&1

echo
echo "Run 'sh /script/php53s-install' for running panel under PHP 5.3 version"
echo
echo "Run 'sh /script/restart-all' to make sure all services running well"
echo
