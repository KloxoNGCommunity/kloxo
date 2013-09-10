#!/bin/sh

#    Kloxo-MR - Hosting Control Panel
#
#    Copyright (C) 2013 - MRatWork
#
#    This program is free software: you can redistribute it and/or modify
#    it under the terms of the GNU Affero General Public License as
#    published by the Free Software Foundation, either version 3 of the
#    License, or (at your option) any later version.
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU Affero General Public License for more details.
#
#    You should have received a copy of the GNU Affero General Public License
#    along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
#
# MRatWork - Kloxo-MR dev Installer
#
# Version: 1.0 (2013-01-11 - by Mustafa Ramadhan <mustafa@bigraf.com>)
#

hnshort=$(hostname -s)
### MR - don't use 'hostname -f'
hnfull=$(hostname)

### MR - use "" instead ''
val1=$(cat /etc/hosts|grep -i "$hnfull")
val2=$(cat /etc/hosts|grep -i "::1")

if [ "$val1" == "" ] ; then
	inserter="### begin - add by Kloxo-MR\n"
	inserter="${inserter}0.0.0.0 ${hnfull} ${hnfull}\n"

	if [ "$val2" != "" ] ; then
		inserter="${inserter}:: ${hnfull} ${hnshort}\n"
	fi

	inserter="${inserter}### end - add by Kloxo-MR\n"

	echo -e $inserter >> /etc/hosts
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
read -n 1 -p "Press any key to continue ..."
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
	echo -en "Installing as \"root\"        " $C_NO
	echo -e "\a\nYou must be \"root\" to install $APP_NAME.\n\nAborting ...\n"
	exit $E_NOTROOT
else
	echo -en "Installing as \"root\"        " $C_OK
fi

# Check if selinuxenabled exists
if [ ! -f $SELINUX_CHECK ] ; then
	echo -en "SELinux not installed       " $C_MISS
else
	# Check if SElinux is enabled from exit status. 0 = Enabled; 1 = Disabled;
	eval $SELINUX_CHECK
	OUT=$?
	if [ $OUT -eq "0" ] ; then
		echo -en "SELinux disabled            " $C_NO
        setenforce 0
		echo "SELINUX=disabled" > $SELINUX_CFG
		echo -e "SELinux disabled successfully\n"
	elif [ $OUT -eq "1" ] ; then
		echo -en "SELinux disabled            " $C_OK
	fi
fi

# Check if yum is installed.
if ! [ -f /usr/sbin/yum ] && ! [ -f /usr/bin/yum ] ; then
	echo -en "Yum installed               " $C_NO
	echo -e "\a\nThe installer requires YUM to continue. Please install it and try again.\nAborting ...\n"
	exit $E_NOYUM
else
	echo -en "Yum installed               " $C_OK
fi

# Start install

yum clean all

yum -y install wget zip unzip yum-utils yum-priorities vim-minimal subversion curl

yum remove bind* mysql* mariadb* MariaDB* php* -y

#if [ ! -f /opt/php52s/bin/php ] ; then
#	if [ -f /usr/bin/php ] ; then
#		yum -y remove php*
#	fi

	yum -y install mysql55 mysql55-server mysql55-libs

	yum -y install php53u php53u-mysql

	## install after mysql55 and php53u because if mysql not exist will install 'old' mysql
	yum -y install net-snmp php52s
#fi

export PATH=/usr/sbin:/sbin:$PATH

if [ -d ./kloxomr/install ] ; then
	cd ./kloxomr/install
else
	mv ./kloxomr-*.tar.gz ./kloxomr-latest.tar.gz >/dev/null 2>&1
	tar -xzf ./kloxomr-latest.tar.gz >/dev/null 2>&1
	mv ./kloxomr-6* ./kloxomr >/dev/null 2>&1
	cd ./kloxomr/install >/dev/null 2>&1
fi

lxphp.exe installer.php --install-type=$APP_TYPE $* | tee kloxo-mr_install.log

# Fix issue because sometimes kloxo database not created
if [ $APP_TYPE == 'master' ] ; then
	if [ ! -d /var/lib/mysql/kloxo ] ; then
		cd /usr/local/lxlabs/kloxo/install
		lxphp.exe installer.php --install-type=$APP_TYPE --install-from=setup --install-step=2 $* | tee kloxo-mr_install.log
	fi
fi

echo
echo "Run 'sh /script/restart-all' to make sure all services running well"
echo
