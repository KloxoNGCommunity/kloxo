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

hnshort=$(hostname -s)
### MR - don't use 'hostname -f'
hnfull=$(hostname)

if [ "$hnshort" == "$hnfull" ] ; then
	echo "*** Kloxo-MR warning ***"
	echo "* Your server have wrong hostname. Modified '/etc/sysconfig/network' with:"
	echo "  - 'HOSTNAME=subdom.dom.tld' format (qualified as FQDN) for Dedicated Server"
	echo "  - Or, set the same FQDN in VPS control panel for VPS server"
	echo
	echo "* Need reboot"

	exit
fi

### MR - use "" instead ''
val=$(cat /etc/hosts|grep -i "$hnfull")

if [ "$val" == "" ] ; then
	echo "*** Kloxo-MR warning ***"
	echo "* Need add line with content '123.123.123.123 subdom.dom.com subdom'"
	echo "  inside '/etc/hosts' file, where:"
	echo "  - '123.123.123.123' = primary IP (run 'ifconfig' to know this IP)"
	echo "  - 'subdom.dom.com' = taken from 'hostname'"
	echo "  - 'subdom' = taken from 'hostname -s'"

	exit
fi

if [ "$1" != "-y" ]; then
	if [ -f /var/lib/mysql/kloxo ] ; then
			echo "Your server already Kloxo-MR installed as 'master'"
			echo "- Use 'sh /script/upcp -y' to 'reinstall'"

			exit
	elif [ -f /usr/local/lxlabs/kloxo/etc/conf/slave-db.db ] ; then
			echo "Your server already Kloxo-MR installed as 'slave'"
			echo "- Use 'sh /script/upcp -y' to 'reinstall'"

			exit
	fi
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

yum -y install wget zip unzip yum-utils yum-priorities vim-minimal subversion curl

yum remove bind* mysql* -y

if [ ! -f /opt/php52s/bin/php ] ; then
	if [ -f /usr/bin/php ] ; then
		yum -y remove php*
	fi

	yum -y install mysql mysql-server mysql-libs

	yum -y install php53u php53u-mysql
fi

export PATH=/usr/sbin:/sbin:$PATH

cd /usr/local/lxlabs/kloxo/install

if [ -f /opt/php52s/bin/php ] ; then
	lxphp.exe installer.php --install-type=$APP_TYPE --install-from=setup $* | tee kloxo-mr_install.log
else
	php installer.php --install-type=$APP_TYPE --install-from=setup $* | tee kloxo-mr_install.log
fi

for (( a=1; a<=2; a++ ))
do
	# Fix issue because sometimes kloxo database not created
	if [ $APP_TYPE == 'master' ] ; then
		if [ ! -d /var/lib/mysql/kloxo ] ; then
			cd /usr/local/lxlabs/kloxo/install
			lxphp.exe installer.php --install-type=$APP_TYPE --install-from=setup --install-step=2  $* | tee kloxo-mr_install.log
		fi
	fi
done

echo
echo "Run 'sh /script/restart-all' to make sure all services running well"
echo