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

if [ "$#" == 0 ] ; then
	echo
	echo " ----------------------------------------------------------------------"
	echo "  format: sh $0 --fork=<> --branch=<>"
	echo " ----------------------------------------------------------------------"
	echo "  --fork - example: lxcenter or mustafaramadhan (for certain developer)"
	echo "  --branch - example: master or dev"
	echo
	echo "  * Pack main kloxo package from git"
	echo "  * Thirdparty packages download directly for latest version"
	echo "  * Then run kloxo-installer.sh which the same place with local copy"
	echo
	echo " - If packer.sh detect './kloxo/httpdocs' and then used it as source" 
	exit;
fi

echo "Start pack..."

request1=$1
kloxo_fork=${request1#--fork\=}

request2=$2
kloxo_branch=${request2#--branch\=}

kloxo_path=${kloxo_fork}/kloxo/zipball/${kloxo_branch}

yum install zip unzip -y

if [ ! -d ./kloxo/httpdocs ] ; then
	echo "Download git from "${kloxo_path}
	rm -rf ${kloxo_branch}* > /dev/null 2>&1
	wget https://github.com/${kloxo_fork}/kloxo/archive/${kloxo_branch}.zip

	mv -f ${kloxo_branch} kloxo-dev.zip > /dev/null 2>&1
	unzip -oq kloxo-dev.zip > /dev/null 2>&1
	rm -rf kloxo-mr-dev.zip > /dev/null 2>&1
	mv -f ./kloxo-dev/kloxo ./
	rm -rf ./kloxo-dev
else
	echo "No download and use local copy already exist"
fi

cp -rf ./kloxo/install/installer.sh ./

cd ./kloxo/cexe
yum -y install which cpp gcc gcc-c++ glibc* openssl-devel automake autoconf libtool make
make

cd ../../

### 4. zipped process
zip -r9y kloxo-mr-latest.zip "./kloxo/bin" "./kloxo/cexe" "./kloxo/file" \
	"./kloxo/httpdocs" "./kloxo/pscript" "./kloxo/sbin" \
	"./kloxo/RELEASEINFO" "./kloxo/etc/list" "./kloxo/etc/process" \
	"./kloxo/etc/config.ini" \
	"./kloxo/install" \
	-x \
	"./kloxo/httpdocs/commands.php" \
	"./kloxo/httpdocs/newpass" \
	"./kloxo/httpdocs/.php.err" \
	"./kloxo/file/cache/*" \
	"./kloxo/serverfile/*" \
	"./kloxo/session/*" \
	"./kloxo/etc/.restart/*" \
	"./kloxo/etc/conf/*" \
	"./kloxo/etc/flag/*" \
	"./kloxo/etc/last_sisinfoc" \
	"./kloxo/etc/program.*" \
	"./kloxo/etc/slavedb/*" \
	"./kloxo/etc/watchdog.conf" \
	"./kloxo/install/kloxo_install.log"


rm -rf ./kloxo > /dev/null 2>&1
rm -rf ./kloxo-install > /dev/null 2>&1
rm -rf ./install > /dev/null 2>&1

echo
echo "Now you can run 'sh ./installer.sh' for installing"
echo
echo "... the end"
