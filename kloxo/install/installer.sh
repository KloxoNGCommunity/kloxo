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

if [ "$(rpm -qa mratwork-release)" == "" ] ; then
	cd /tmp
	rpm -ivh https://github.com/mustafaramadhan/rpms/raw/master/mratwork/release/neutral/noarch/mratwork-release-0.0.1-1.noarch.rpm >/dev/null 2>&1
	rpm -ivh mratwork-release-0.0.1-1.noarch.rpm >/dev/null 2>&1
	yum update mratwork-release -y >/dev/null 2>&1

	'mv' -f /etc/yum.repos.d/lxcenter.repo /etc/yum.repos.d/lxcenter.nonrepo >/dev/null 2>&1
	'mv' -f /etc/yum.repos.d/kloxo-mr.repo /etc/yum.repos.d/kloxo-mr.nonrepo >/dev/null 2>&1
else
	yum update mratwork-release -y >/dev/null 2>&1
fi

if [ "$(rpm -qa ^'kloxomr7')" == "" ] ; then
	yum install -y kloxomr7 >/dev/null 2>&1
fi

if [ ! -L /script ] ; then
	if [ -d /script ] ; then
		'rm' -rf /script >/dev/null 2>&1
	fi

	ln -sf /usr/local/lxlabs/kloxo/pscript /script >/dev/null 2>&1
fi

echo ""
echo "*** Update Kloxo-MR 7.0 with github source (packed with packer.sh) ***"

echo ""
'rm' -rf ./kloxomr7*/ >/dev/null 2>&1
echo "- extract tar.gz file and copy to /usr/local/lxlabs/kloxo"
tar -xzf ./kloxomr7-*.tar.gz >/dev/null 2>&1
'cp' -rf ./kloxomr7-*/* /usr/local/lxlabs/kloxo >/dev/null 2>&1
'rm' -rf ./kloxomr7*/ >/dev/null 2>&1

echo ""
echo "* Note:"
echo "  - fresh install: run 'sh /script/upcp'"
echo "  - Update: run 'sh /script/cleanup'"
echo ""
