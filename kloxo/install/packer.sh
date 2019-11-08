#!/bin/sh

#    KloxoNG - Control Panel
#
#    Copyright (C) 2018 - KloxoNGCommunity
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
# KloxoNGCommunity - KloxoNG dev Installer
#
# Version: 1.0 (2013-01-11 - by Mustafa Ramadhan <mustafa@bigraf.com>)
# Version: 1.1 (2018-01-27 - by Dionysis Kladis <dkstiler@gmail.com>)
#

#we define here the array of variables and for packages that we will use on installing with yum 
yum_pack1=(zip unzip)
yum_pack2=(wget)
kloxoflname="kloxong"

if [ "$1" == "--help" ] || [ "$1" == "-h" ] ; then
	echo
	echo " ----------------------------------------------------------------------"
	echo "  format: sh $0 --fork=<> --branch=<>"
	echo " ----------------------------------------------------------------------"
	echo "  --fork - example: kloxong or <yourforkname> (for certain developer)"
	echo "  --branch - example: master or dev"
	echo
	echo "  * Pack main kloxo package from git"
	echo "  * Thirdparty packages download directly for latest version"
	echo "  * Then run kloxo-installer.sh which the same place with local copy"
	echo
	echo " - If detect './kloxo/httpdocs' and then used it as source" 
	exit
fi

echo "Start pack..."

if [ "$1" == "" ] ; then
	kloxo_fork="kloxoNG-CP"
else
	kloxo_fork=${1#--fork\=}
fi

if [ "$2" == "" ] ; then
	kloxo_branch="master"
else
	kloxo_branch=${2#--branch\=}
fi

#Preparing enviroment for packing

if [ "$(rpm -qa|grep unzip)" == "" ] ; then
	yum install $yum_pack1 -y
fi

if [ "$(rpm -qa|grep wget)" == "" ] ; then
	yum install $yum_pack2 -y
fi

if [ ! -d ./kloxo/httpdocs ] ; then
	echo "Download Kloxo git Sources"
	'rm' -rf ${kloxo_branch}* > /dev/null 2>&1
	wget https://github.com/${kloxo_fork}/kloxo/archive/${kloxo_branch}.zip -O kloxo-ng-${kloxo_branch}.zip

	unzip -oq kloxo-ng-${kloxo_branch}.zip > /dev/null 2>&1
	'rm' -rf kloxo-ng-${kloxo_branch}.zip > /dev/null 2>&1
	'mv' -f ./kloxo*-${kloxo_branch}/kloxo ./
	'rm' -rf ./kloxo*-${kloxo_branch}
else
	echo "No download, local copy already exist"
fi

'cp' -rf ./kloxo/install/installer.sh ./

ver=`cat ./kloxo/bin/kloxoversion`

'mv' ./kloxo ./$kloxoflname-$ver

# delete dirs except en-us
find ./$kloxoflname-$ver/httpdocs/lang/* -type d ! -name 'en-us' -exec rm -R {} \;

### 4. zipped process
tar -czf $kloxoflname-$ver.tar.gz "./$kloxoflname-$ver/bin" "./$kloxoflname-$ver/cexe" "./$kloxoflname-$ver/file" \
	"./$kloxoflname-$ver/httpdocs" "./$kloxoflname-$ver/pscript" "./$kloxoflname-$ver/sbin" \
	"./$kloxoflname-$ver/RELEASEINFO" "./$kloxoflname-$ver/etc/process" \
	"./$kloxoflname-$ver/etc/config.ini" \
	"./$kloxoflname-$ver/install" "./$kloxoflname-$ver/init" \
	"./$kloxoflname-$ver/etc/list" \
	--exclude "./$kloxoflname-$ver/httpdocs/commands.php" \
	--exclude "./$kloxoflname-$ver/httpdocs/newpass" \
	--exclude "./$kloxoflname-$ver/httpdocs/.php.err" \
	--exclude "./$kloxoflname-$ver/httpdocs/thirdparty" \
	--exclude "./$kloxoflname-$ver/httpdocs/editor" \
	--exclude "./$kloxoflname-$ver/file/cache" \
	--exclude "./$kloxoflname-$ver/file/*.repo" \
	--exclude "./$kloxoflname-$ver/serverfile" \
	--exclude "./$kloxoflname-$ver/session" \
	--exclude "./$kloxoflname-$ver/etc/.restart" \
	--exclude "./$kloxoflname-$ver/etc/conf/*" \
	--exclude "./$kloxoflname-$ver/etc/flag/*" \
	--exclude "./$kloxoflname-$ver/etc/slavedb/*" \
	--exclude "./$kloxoflname-$ver/etc/last_sisinfoc" \
	--exclude "./$kloxoflname-$ver/etc/program.*" \
	--exclude "./$kloxoflname-$ver/etc/watchdog.conf" \
	--exclude "./$kloxoflname-$ver/install/*.log" \
	--exclude "./$kloxoflname-$ver/log" \
	--exclude "./$kloxoflname-$ver/pid" \
	--exclude "./$kloxoflname-$ver/init/kloxo_php_active" \
	--exclude "./$kloxoflname-$ver/init/*.sock" \
	--exclude "./$kloxoflname-$ver/init/*.pid" \
	--exclude "./$kloxoflname-$ver/init/kloxo-hiawatha" \
	--exclude "./$kloxoflname-$ver/init/kloxo-phpcgi" \
	--exclude "./$kloxoflname-$ver/*.old" \
	--exclude "./$kloxoflname-$ver/*.bck" \
	--exclude "./$kloxoflname-$ver/*.pyo" \
	--exclude "./$kloxoflname-$ver/*.pyc" \
	--exclude "./$kloxoflname-$ver/init/php_active" \
	--exclude "./$kloxoflname-$ver/httpdocs/login/*.php" \
	--exclude "./$kloxoflname-$ver/httpdocs/login/*.html" \
	--exclude "./$kloxoflname-$ver/httpdocs/login/.norandomimage" \
	--exclude "./$kloxoflname-$ver/httpdocs/login/images" \
	--exclude "./$kloxoflname-$ver/*/user-logo.png"


'rm' -rf ./$kloxoflname-$ver > /dev/null 2>&1
'rm' -rf ./kloxo-install > /dev/null 2>&1
'rm' -rf ./install > /dev/null 2>&1

echo
echo "Now you can run 'sh ./installer.sh' for installing"
echo
echo "Run '$0 --help' for helping"
echo
echo "... the end"
