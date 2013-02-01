#!/bin/sh
#    Kloxo, Hosting Control Panel
#
#    Copyright (C) 2000-2009	LxLabs
#    Copyright (C) 2009-2011	LxCenter
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
# LxCenter - Kloxo Packer
#
# Version: 1.0 (2011-08-02 - by mustafa.ramadhan@lxcenter.org)
# Version: 1.1 (2012-12-28 - by mustafa.ramadhan@lxcenter.org)
#

if [ "$#" == 0 ] ; then
	echo
	echo " ----------------------------------------------------------------------"
	echo "  format: sh $0 --fork=<> --branch=<>"
	echo " ----------------------------------------------------------------------"
	echo "  --fork - example: lxcenter or mustafaramadhan (for certain developer)"
	echo "  --branch - example: master or dev"
#	echo "  --part - example: core or all (defaulting to all)"
	echo
	echo "  * Pack main kloxo package from git"
	echo "  * Thirdparty packages download directly for latest version"
	echo "  * Then run kloxo-installer.sh which the same place with local copy"
	echo
	exit;
fi

echo "Start pack..."

request1=$1
kloxo_fork=${request1#--fork\=}

request2=$2
kloxo_branch=${request2#--branch\=}

if [ "$3" == '--part=core' ] ; then
	request3=$3
	kloxo_part=${request3#--part\=}
else
	kloxo_part="all"
fi

kloxo_path=${kloxo_fork}/kloxo/zipball/${kloxo_branch}

yum install zip unzip -y

mkdir -p ./combo

if [ ! -d ./current/kloxo/httpdocs ] ; then
	echo "Download kloxo git from "${kloxo_path}
	yes | rm -rf ${kloxo_branch}* > /dev/null 2>&1
	wget https://github.com/${kloxo_path} --no-check-certificate
	mv -f ${kloxo_branch} kloxo.zip > /dev/null 2>&1
	unzip -oq kloxo.zip > /dev/null 2>&1
	mv -f ./${kloxo_fork}* ./current > /dev/null 2>&1
	yes | rm -rf kloxo.zip > /dev/null 2>&1
else
	echo "No download and use local copy - './current/kloxo/httpdocs already' exist"
fi

cp -rf ./current/* ./combo > /dev/null 2>&1

cp -rf ./patch/* ./combo > /dev/null 2>&1

cd ./combo

zip -r9y kloxo-install.zip ./kloxo-install -x "*/kloxo_install.log"

mv -f kloxo-install.zip ../ > /dev/null 2>&1

cd ./kloxo/src
yum -y install which cpp gcc gcc-c++ openssl-devel automake autoconf libtool make
make
cd ../

# cp -rf ./src/closeallinput ./cexe
# chmod -R 755 ./cexe

zip -r9y kloxo-current.zip ./bin ./cexe/closeallinput ./file ./httpdocs ./pscript ./sbin \
	./RELEASEINFO ./src ./etc/list ./etc/process ./etc/config.ini -x \
	"./httpdocs/commands.php" \
	"./httpdocs/newpass" \
	"./httpdocs/.php.err" \
	"*/CVS/*" \
	"*/.svn/*" \
	"./httpdocs/thirdparty/*" \
	"./httpdocs/htmllib/extjs/*" \
	"./httpdocs/htmllib/fckeditor/*" \
	"./httpdocs/htmllib/yui-dragdrop/*" \
	"./file/cache/*" \
	"./serverfile/*" \
	"./session/*" \
	"./etc/.restart/*" \
	"./etc/conf/*" \
	"./etc/flag/*" \
	"./etc/last_sisinfoc" \
	"./etc/program.*" \
	"./etc/slavedb/*" \
	"./etc/watchdog.conf"

mv -f kloxo-current.zip ../../ > /dev/null 2>&1

cd ../../

cp -rf ./combo/kloxo-install/kloxo-installer.sh ./ > /dev/null 2>&1

# delete temporal directory
rm -rf ./patch > /dev/null 2>&1
rm -rf ./current > /dev/null 2>&1
rm -rf ./combo > /dev/null 2>&1

echo
echo "Now you can run 'sh ./kloxo-installer.sh' for installing"
echo
echo "... the end"
