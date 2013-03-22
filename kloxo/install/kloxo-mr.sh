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
# MRatWork - Kloxo-MR release Installer
#
# Version: 1.0 (2013-01-11 - by Mustafa Ramadhan <mustafa@bigraf.com>)
#

kloxo_fork=mustafaramadhan
kloxo_branch=release
kloxo_path=${kloxo_fork}/kloxo/raw/${kloxo_branch}

if [ ! -f ./kloxomr-latest.tar.gz ] ; then
	echo "Download git from "${kloxo_path}

	#wget https://github.com/${kloxo_fork}/kloxo/raw/${kloxo_branch}/version.lst --no-check-certificate
	#kloxo_ver=$(head -1 ./version.lst)

	wget https://github.com/${kloxo_fork}/kloxo/raw/${kloxo_branch}/kloxomr-latest.tar.gz --no-check-certificate
fi

tar -xzf ./kloxomr-latest.tar.gz > /dev/null 2>&1
mv -f ./kloxomr-6* ./kloxomr > /dev/null 2>&1

cp -rf ./kloxomr/install/installer.sh ./installer.sh > /dev/null 2>&1

sh ./installer.sh --release

