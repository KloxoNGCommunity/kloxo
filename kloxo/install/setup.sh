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
# MRatWork - Kloxo-MR Release Setup
#
# Version: 1.0 (2013-01-11 - by Mustafa Ramadhan <mustafa@bigraf.com>)
#

echo
echo "*** Ready to begin $APP_NAME setup. ***"
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
#	if [ -d /var/lib/mysql/kloxo ] ; then
		APP_TYPE='master'
#	else
#		echo
#		echo "Select Master/Slave for Kloxo-MR - choose Master for single server"
#		PS3='- Please enter your choice: '
#		options=("Master" "Slave")
#		select opt in "${options[@]}" "Quit"; do 
#			case $opt in
#				"Master")
#					APP_TYPE='master'
#					break
#					;;
#				"Slave")
#					APP_TYPE='slave'
#					break
#					;;
#   				"Quit")
#					exit
#					;;
#					*) echo "  * Invalid option!";;
#			esac
#		done
#	fi
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

# Check if OS is RHEL/CENTOS/FEDORA.
#if [ ! -f /etc/redhat-release ] ; then
#	echo -en "Operating System supported  " $C_NO
#	echo -e "\a\nSorry, only RedHat EL and CentOS are supported by $APP_NAME at this time.\n\nAborting ...\n"
#	exit $E_NOSUPPORT
#else
#	echo -en "Operating System supported  " $C_OK
#fi

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

yum -y install wget zip unzip yum-utils yum-priorities vim-minimal subversion curl

yum remove bind* mysql* -y

if [ ! -f /usr/local/lxlabs/ext/php/php ] ; then
	if [ -f /usr/bin/php ] ; then
		yum -y remove php*
	fi

#	if yum list mysql51 >/dev/null 2>&1 ; then
#		yum -y install mysql51 mysql51-server mysql51-libs
#	else
#		yum -y install mysql55 mysql55-server mysql55-libs
#	fi

	yum -y install mysql mysql-server mysql-libs

	yum -y install php53u php53u-mysql
fi

export PATH=/usr/sbin:/sbin:$PATH

cd /usr/local/lxlabs/kloxo/install

if [ -f /usr/local/lxlabs/ext/php/php ] ; then
	lxphp.exe installer.php --install-type=$APP_TYPE --install-from=setup $* | tee kloxo-mr_install.log
else
	php installer.php --install-type=$APP_TYPE --install-from=setup $* | tee kloxo-mr_install.log
fi

# Fix issue because sometimes kloxo database not created
if [ $APP_TYPE == 'master' ] ; then
	if [ ! -d /var/lib/mysql/kloxo ] ; then
		echo ""
		echo "Wait for final process..."
		echo ""
		cd /usr/local/lxlabs/kloxo/install
		lxphp.exe installer.php --install-type=$APP_TYPE --install-from=setup --install-step=2  $* | tee kloxo-mr_install.log
	fi
fi