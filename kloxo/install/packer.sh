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

if ! [ -d ./kloxo/httpdocs ] ; then
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
		echo " - If detect './kloxo/httpdocs' and then used it as source" 
		exit;
	fi
fi

echo "Start pack..."

request1=$1
kloxo_fork=${request1#--fork\=}

request2=$2
kloxo_branch=${request2#--branch\=}

kloxo_path=${kloxo_fork}/kloxomr-$ver/zipball/${kloxo_branch}

yum install zip unzip -y

if [ ! -d ./kloxo/httpdocs ] ; then
	echo "Download git from "${kloxo_path}
	rm -rf ${kloxo_branch}* > /dev/null 2>&1
	wget https://github.com/${kloxo_fork}/kloxo/archive/${kloxo_branch}.zip -O kloxo-mr-${kloxo_branch}.zip

	unzip -oq kloxo-mr-${kloxo_branch}.zip > /dev/null 2>&1
	rm -rf kloxo-mr-${kloxo_branch}.zip > /dev/null 2>&1
	mv -f ./kloxo*-${kloxo_branch}/kloxo ./
	rm -rf ./kloxo*-${kloxo_branch}
else
	echo "No download and use local copy already exist"
fi

cp -rf ./kloxo/install/installer.sh ./

#cd ./kloxo/cexe
#yum -y install which cpp gcc gcc-c++ glibc* openssl-devel automake autoconf libtool make
#make

#cd ../../

ver=`cat ./kloxo/bin/kloxoversion`

mv ./kloxo ./kloxomr-$ver

# delete dirs except en-us
find ./kloxomr-$ver/httpdocs/lang/* -type d ! -name 'en-us' -exec rm -R {} \;

### 4. zipped process
tar -czf kloxomr-$ver.tar.gz "./kloxomr-$ver/bin" "./kloxomr-$ver/cexe" "./kloxomr-$ver/file" \
	"./kloxomr-$ver/httpdocs" "./kloxomr-$ver/pscript" "./kloxomr-$ver/sbin" \
	"./kloxomr-$ver/RELEASEINFO" "./kloxomr-$ver/etc/process" \
	"./kloxomr-$ver/etc/config.ini" \
	"./kloxomr-$ver/install" "./kloxomr-$ver/init" \
	"./kloxomr-$ver/etc/list/set.*.lst" \
	"./kloxomr-$ver/etc/list/reserved.lst" \
	"./kloxomr-$ver/etc/list/webcache.lst" \
	--exclude "./kloxomr-$ver/httpdocs/commands.php" \
	--exclude "./kloxomr-$ver/httpdocs/newpass" \
	--exclude "./kloxomr-$ver/httpdocs/.php.err" \
	--exclude "./kloxomr-$ver/httpdocs/thirdparty" \
	--exclude "./kloxomr-$ver/file/cache" \
	--exclude "./kloxomr-$ver/file/*.repo" \
	--exclude "./kloxomr-$ver/serverfile" \
	--exclude "./kloxomr-$ver/session" \
	--exclude "./kloxomr-$ver/etc/.restart" \
	--exclude "./kloxomr-$ver/etc/conf/*" \
	--exclude "./kloxomr-$ver/etc/flag/*" \
	--exclude "./kloxomr-$ver/etc/slavedb/*" \
	--exclude "./kloxomr-$ver/etc/last_sisinfoc" \
	--exclude "./kloxomr-$ver/etc/program.*" \
	--exclude "./kloxomr-$ver/etc/watchdog.conf" \
	--exclude "./kloxomr-$ver/install/kloxo-mr_install.log" \
	--exclude "./kloxomr-$ver/log" \
	--exclude "./kloxomr-$ver/pid" \
	--exclude "./kloxomr-$ver/init/*.sock" \
	--exclude "./kloxomr-$ver/httpdocs/theme/fckeditor/editor/_source" \
	--exclude "./kloxomr-$ver/httpdocs/theme/fckeditor/_samples" \
	--exclude "./kloxomr-$ver/httpdocs/theme/yui-dragdrop" \
	--exclude "./kloxomr-$ver/*.old" \
	--exclude "./kloxomr-$ver/*.bck" \
	--exclude "./kloxomr-$ver/*.pyo" \
	--exclude "./kloxomr-$ver/*.pyc"

rm -rf ./kloxomr-$ver > /dev/null 2>&1
rm -rf ./kloxo-install > /dev/null 2>&1
rm -rf ./install > /dev/null 2>&1

echo
echo "Now you can run 'sh ./installer.sh' for installing"
echo
echo "... the end"
