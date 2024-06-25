#!/bin/sh

#    Kloxo - Hosting Control Panel
#
#    Copyright (C) 2018 - Kloxo
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
# Kloxo - Kloxo dev Installer
#
# Version: 1.0 (2013-01-11 - by Mustafa Ramadhan <mustafa@bigraf.com>)
# Version: 1.1 (2018-01-27 - by Dionysis Kladis <dkstiler@gmail.com>)
#

$main_repo_url="https://github.com/KloxoNGCommunity/kloxo8/raw/initial-rpm/"
$main_release_rpm="kloxo-release.rpm"
$rpm_main_pck='kloxo'

#if [ "$(rpm -qa kloxo-release)" == "" ] ; then
if [ "$(rpm -q kloxo-release | grep -v 'package .* is not installed')" == "" ] ; then


	cd /tmp
	rpm -ivh $main_repo_url/$main_release_rpm >/dev/null 2>&1
	rpm -ivh $main_release_rpm >/dev/null 2>&1
	yum update kloxo-release -y >/dev/null 2>&1

	'mv' -f /etc/yum.repos.d/lxcenter.repo /etc/yum.repos.d/lxcenter.nonrepo >/dev/null 2>&1
	'mv' -f /etc/yum.repos.d/kloxo-mr.repo /etc/yum.repos.d/kloxo-mr.nonrepo >/dev/null 2>&1
        'mv' -f /etc/yum.repos.d/kloxong.repo /etc/yum.repos.d/kloxong.nonrepo >/dev/null 2>&1
#		    'mv' -f /etc/yum.repos.d/kloxo.repo /etc/yum.repos.d/kloxo.nonrepo >/dev/null 2>&1
else
	yum update kloxo-release -y >/dev/null 2>&1
fi

#if [ "$(rpm -qa ^'$rpm_main_pck')" == "" ] ; then
if [ "$(rpm -q ^'$rpm_main_pck' | grep -v 'package .* is not installed')" == "" ] ; then
	yum install -y $rpm_main_pck >/dev/null 2>&1
fi

if [ ! -L /script ] ; then
	if [ -d /script ] ; then
		'rm' -rf /script >/dev/null 2>&1
	fi

	ln -sf /usr/local/lxlabs/kloxo/pscript /script >/dev/null 2>&1
fi

echo ""
echo "*** Update Kloxo with github source (packed with packer.sh) ***"

echo ""
'rm' -rf ./$rpm_main_pck*/ >/dev/null 2>&1
echo "- extract tar.gz file and copy to /usr/local/lxlabs/kloxo"
tar -xzf ./$rpm_main_pck-*.tar.gz >/dev/null 2>&1
'cp' -rf ./$rpm_main_pck-*/* /usr/local/lxlabs/kloxo >/dev/null 2>&1
'rm' -rf ./$rpm_main_pck*/ >/dev/null 2>&1

echo ""
echo "* Note:"
echo "  - fresh install: run 'sh /script/upcp'"
echo "  - Update: run 'sh /script/cleanup'"
echo ""
