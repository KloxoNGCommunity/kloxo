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

kloxo_fork=mustafaramadhan
kloxo_branch=bugfix-6.5.0
kloxo_path=${kloxo_fork}/kloxo/zipball/${kloxo_branch}

## A. Packer portion
### 1. download and unzip phase

cd /tmp
rm -rf ./kloxo/*
rm -rf ${kloxo_branch}* > /dev/null 2>&1
mkdir -p /tmp/kloxo
cd ./kloxo

echo "Download git from "${kloxo_path}
rm -rf ${kloxo_branch}* > /dev/null 2>&1
wget https://github.com/${kloxo_fork}/kloxo/archive/${kloxo_branch}.zip -O kloxo-mr-${kloxo_branch}.zip

unzip -oq kloxo-mr-${kloxo_branch}.zip > /dev/null 2>&1
rm -rf kloxo-mr-${kloxo_branch}.zip > /dev/null 2>&1
mv -f ./kloxo*-${kloxo_branch}/kloxo ./
rm -rf ./kloxo*-${kloxo_branch}

cp -rf ./kloxo/install/installer.sh ./

ver=`cat ./kloxo/bin/kloxoversion`

mv ./kloxo ./kloxomr-$ver

### 4. zipped process
tar -czf kloxomr-$ver.tar.gz "./kloxomr-$ver/bin" "./kloxomr-$ver/cexe" "./kloxomr-$ver/file" \
	"./kloxomr-$ver/httpdocs" "./kloxomr-$ver/pscript" "./kloxomr-$ver/sbin" \
	"./kloxomr-$ver/RELEASEINFO" "./kloxomr-$ver/etc/process" \
	"./kloxomr-$ver/etc/config.ini" \
	"./kloxomr-$ver/install" "./kloxomr-$ver/init" \
	"./kloxomr-$ver/etc/list" \
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
	--exclude "./kloxomr-$ver/httpdocs/panel/fckeditor/editor/_source" \
	--exclude "./kloxomr-$ver/httpdocs/panel/extjs" \
	--exclude "./kloxomr-$ver/httpdocs/panel/yui-dragdrop" \
	--exclude "./kloxomr-$ver/httpdocs/panel/*.old" \
	--exclude "./kloxomr-$ver/httpdocs/panel/*.bck"

rm -rf ./kloxomr-$ver > /dev/null 2>&1
rm -rf ./kloxo-install > /dev/null 2>&1
rm -rf ./install > /dev/null 2>&1

sh ./installer.sh

