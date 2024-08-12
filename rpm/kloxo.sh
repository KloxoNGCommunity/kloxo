#!/bin/sh

if [ -d /var/lib/mysql/kloxo ] && [ -f /etc/yum.repos.d/kloxo.repo ] ; then
	echo "-------------------------------------------------------------------"
	echo " WARNING:"
	echo ""
	echo " Kloxo has already been installed"
	echo ""
	echo "-------------------------------------------------------------------"
	echo ""
	echo "If you wish to update Kloxo "
	echo " run sh /script/upcp"
	echo
	exit
fi


if [ "$(hostname -f)" == "$(hostname -s)" ] ; then
	echo "-------------------------------------------------------------------"
	echo " WARNING:"
	echo " - Need change hostname with qualify to FQDN"
	echo "   (use 'server1.domain.com' instead 'server1')"
	echo " - May trouble for web and mail without FQDN hostname"
	echo "-------------------------------------------------------------------"
	echo " - For OpenVZ VPS, change hostname from VPS panel"
	echo " - For Others, change with the hostnamectl command "
	echo ""
	echo " eg. hostnamectl set-hostname [your hostname]"
	echo ""
	echo
	exit
fi
checktmpfs=$(cat /etc/fstab|grep '/tmp'|grep 'tmpfs')
if [ "${checktmpfs}" != "" ] ; then
	echo "This server have '/tmp' with 'tmpfs' detect."
	echo "Modify '/etc/fstab' where remove 'tmpfs' in '/tmp' line and then reboot."
	echo "Without remove, backup/restore may have a trouble."
	exit
fi


cd /tmp

	'rm' -rf /etc/yum.repos.d/kloxo-mr.repo
	'rm' -rf /etc/yum.repos.d/kloxo-custom.repo
	'rm' -rf /etc/yum.repos.d/lxcenter.repo
	'rm' -rf /etc/yum.repos.d/lxlabs.repo	
	'rm' -rf /etc/yum.repos.d/kloxong.repo.*
	'rm' -rf /tmp/kloxong*
	'rm' -rf /tmp/kloxo*


cat > kloxo.repo << _EOF_
[kloxo-copr]
name=kloxong master Copr repo 
baseurl=https://download.copr.fedorainfracloud.org/results/kloxong/kloxo/epel-\$releasever-\$basearch/
type=rpm-md
skip_if_unavailable=True
gpgcheck=1
gpgkey=https://download.copr.fedorainfracloud.org/results/kloxong/kloxo/pubkey.gpg
repo_gpgcheck=0
enabled=1
enabled_metadata=1


_EOF_

## Remove alias on cp
unalias cp > /dev/null 2>&1; unalias mv > /dev/null 2>&1; unalias rm > /dev/null 2>&1


cp -rf /tmp/kloxo.repo /etc/yum.repos.d/kloxo.repo

yum clean all

yum install kloxo-release -y

yum clean all

yum install kloxo -y

cd /script

if [ ! -f /script/programname ] ; then
	echo 'kloxo' > /script/programname
fi

. /script/fix-urgent

if [ "$(rpm -qa MariaDB-server)" != "" ] ; then
	echo "Already use MariaDB. No replace"
else
	if [ "$(rpm -qa mysql)" != "" ] ; then
		echo "Replace mysql to MariaDB-server"
		yum swap mysql MariaDB-server -y
	fi
fi

ppath="/usr/local/lxlabs/kloxo"

if ! [ -d ${ppath}/log ] ; then
	### must create log path because without it possible segfault for php!
	mkdir -p ${ppath}/log
fi

sh ${ppath}/install/setup.sh $*  | tee ${ppath}/install/install.log

