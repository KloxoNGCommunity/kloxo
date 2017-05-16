f="/var/run/yum.pid" ; [[ -f "${f}" ]] && 'rm' -f "${f}"

yum-complete-transaction

sh /script/disable-firewall

lxlabspath="/usr/local/lxlabs"
kloxopath="${lxlabspath}/kloxo"
initdpath="/etc/rc.d/init.d"
systemdpath="/usr/lib/systemd/system"
xinetdpath="/etc/xinetd.d"
currentpath=$(pwd)

if [ -f ${kloxopath}/etc/conf/slave-db.db ] ; then
	kloxostate='slave'
else
	if [ -d /var/lib/mysql/kloxo ] ; then
		kloxostate='master'
	else
		kloxostate='none'
	fi
fi

mypass=$(mkpasswd -l 10)

dbroot='root'
dbpass=''

kloxo_service_init() {
	echo ">>> Copy Kloxo-MR service <<<"

	sh /script/fixlxphpexe
}

## ==== kloxo_all portion ===

install_main() {
	install_web
	install_database
	install_dns
	install_mail
	install_others
}

install_web() {
	echo ">>> Install Web services <<<"

	if [ "$(yum list httpd24u)" != "" ] ; then
		if [ "$(rpm -qa httpd)" != "" ] ; then
			yum -y remove httpd* mod_*
		fi

		yum -y install httpd24u httpd24u-tools \
			httpd24u-filesystem httpd24u-mod_security2 \
			mod24u_ssl mod24u_session \
			mod24u_suphp mod24u_ruid2 mod24u_fcgid mod24u_fastcgi \
			mod24u_evasive
	else
		yum -y install httpd httpd-tools \
			mod_ssl mod_rpaf mod_ruid2 mod_suphp mod_fastcgi mod_fcgid \
			mod_define
	fi
}

install_database() {
	echo ">>> Install Database services <<<"

	m='MariaDB'

	for x in mysql mysql55 mysql56 mariadb MariaDB ; do
		if [ "$(rpm -qa ${x}-server)" != "" ] ; then
			m=${x}
		fi
	done

	yum -y install ${m} ${m}-server ${m}-libs --skip-broken
}

install_dns() {
	echo ">>> Install DNS services <<<"

	yum -y install bind bind-utils --skip-broken

	vlnpath="/var/log/named"

	if [ -d ${vlnpath} ] ; then
		mkdir -p ${vlnpath}
		chown named:root ${vlnpath}
	fi

	if [ -f /etc/rndc.conf ] ; then
		'rm' -f /etc/rndc.conf
	fi
}

install_mail() {
	echo ">>> Remove certain mail packages <<<"

	yum -y remove sendmail* vsftpd* postfix* \
		ssmtp* smail* lxzend* pure-ftpd* exim* opensmtpd* esmtp* libesmtp*

	echo ">>> Remove postfix user <<<"

	## MR -- force remove postfix and their user
	userdel postfix

	## MR -- force remove spamassassin, qmail and vpopmail (because using toaster)
	userdel lxpopuser
	groupdel lxpopgroup

	echo ">>> Install Mail services <<<"

	yum -y install autorespond-toaster courier-authlib-toaster courier-imap-toaster \
		daemontools-toaster ezmlm-toaster libdomainkeys-toaster libsrs2-toaster \
		maildrop-toaster qmail-toaster \
		ucspi-tcp-toaster vpopmail-toaster fetchmail bogofilter --skip-broken

	groupadd -g 89 vchkpw
	useradd -u 89 -g 89 vpopmail -s '/sbin/nologin'
}

install_others() {
	echo ">>> Install OTHER services <<<"

	yum -y install pure-ftpd webalizer cronie cronie-anacron crontabs vixie-cron \
		rpmdevtools yum-utils --skip-broken
}

kloxo_vpopmail() {
	echo ">>> Creating Vpopmail database <<<"

	if [ -d /home/vpopmail/etc ] ; then
		sh /script/fixvpop
	fi

	echo ">>> Fix Vpopmail settings <<<"

	echo 'SPAMDOPTIONS=" -v -d -p 783 -u vpopmail"' > /etc/sysconfig/spamassassin

	## MR -- until Kloxo-MR 7.0.0, still using the same mail path
	mkdir -p /home/lxadmin/mail/domains
	chmod 755 /home/lxadmin
	chmod 755 /home/lxadmin/mail
	chmod 755 /home/lxadmin/mail/domains

	chmod 755 /home/vpopmail
	chmod 755 /home/vpopmail/domains

	for x in "${initdpath}/courier-imap" "${initdpath}/clamav" "${xinetdpath}/smtp_lxa" \
			"${xinetdpath}/kloxo_smtp_lxa" ; do
		if [ -f ${x} ] ; then
			'rm' -f ${x}
		fi
	done
	
	chmod -R 755 /var/log/httpd
	mkdir -p /var/log/kloxo
	mkdir -p /var/log/news
}

kloxo_install_step1() {
	echo ">>> Add System users and groups (nouser, nogroup and lxlabs, lxlabs) <<<"

	groupadd nogroup
	useradd nouser -g nogroup -s '/sbin/nologin'
	groupadd lxlabs
	useradd lxlabs -g lxlabs -s '/sbin/nologin'

	echo ">>> Remove DJBDns components <<<"

	if [ -f ${initdpath}/djbdns ] ; then
		for x in axfrdns dnscache dnslog tinydns ; do
			if [ -f /home/${x} ] ; then
				'rm' -f /home/${x}
			fi
		done
	fi

	## MR -- remove lxphp, lxlighttpd and lxzend
	echo ">>> Remove 'old' lxphp/lxligttpd/lxzend/kloxo* <<<"

	ullepath="/usr/local/lxlabs/ext"

	yum remove -y lxphp lxlighttpd lxzend kloxo-*

	if [ -d ${ullepath} ] ; then
		'rm' -f ${ullepath}
	fi

	echo ">>> Add certain components (like curl/contabs/rkhunter) <<<"

	## MR -- install curl-devel (need by php-common) will be install curl-devel
	## in CentOS 5 and libcurl-devel in CentOS 6
	yum -y install tnef which gcc cpp gcc-c++ zip unzip curl-devel libcurl-devel autoconf \
		 automake make libtool openssl-devel pure-ftpd yum-protectbase \
		 yum-plugin-replace crontabs make glibc-static net-snmp tmpwatch \
		 rkhunter quota xinetd screen telnet ncdu sysstat net-tools \
		 xz xz-libs p7zip p7zip-plugins rar unrar lxjailshell --skip-broken

	rkhunter --update

	echo ">>> Add Standard PHP components and Hiawatha <<<"
	## MR -- xcache, zend, ioncube, suhosin and zts not default install
	#  for accept for php and apache branch rpm

	phpbranch='php'

	for x in php php54 php55u php56u php54w php55w php56w ; do
		if [ "$(rpm -qa ${x})" != "" ] ; then
			phpbranch=${x}
		fi
	done

	## MR -- xcache, zend, suhosin and zts not default install
	yum -y install $phpbranch $phpbranch-mbstring $phpbranch-mysqlnd $phpbranch-pear \
		$phpbranch-pecl-geoip $phpbranch-mcrypt $phpbranch-xml \
		$phpbranch-embedded $phpbranch-imap $phpbranch-intl \
		$phpbranch-ldap $phpbranch-litespeed $phpbranch-process $phpbranch-pspell \
		$phpbranch-recode $phpbranch-snmp $phpbranch-soap $phpbranch-tidy \
		$phpbranch-xmlrpc $phpbranch-gd $phpbranch-ioncube-loader hiawatha --skip-broken

	echo ">>> Install MalDetect <<<"

	sh /script/maldet-installer

	echo ">>> Add Kloxo-MR webmail/thirparty/stats <<<"

	## MR -- it's include packages like kloxomr7-thirdparty
	yum -y install kloxomr7-*.noarch

	## MR -- regular packages (as the same as for Kloxo-MR 6.5.0)
	yum -y install kloxomr-webmail-*.noarch kloxomr-thirdparty-*.noarch kloxomr-stats-*.noarch \
		kloxomr-editor-*.noarch --exclude=kloxomr-thirdparty-phpmyadmin-*.noarch --skip-broken

	echo ">>> Prepare installation directories <<<"

	mkdir -p $kloxopath

	echo ">>> Creating Symlink (in 64bit OS) for certain components <<<"

	if [ "$(uname -m)" == 'x86_64' ] ; then
		if [ -f /usr/lib/php ] ; then
			'mv' -f /usr/lib/php /usr/lib/php.bck
		fi

		for x in php httpd lighttpd nginx mysql perl ; do
			if [ ! -d /usr/lib64/${x} ] ; then
				mkdir -p /usr/lib64/${x}
			fi

			if [ ! -L /usr/lib/${x} ] ; then
				ln -s /usr/lib64/${x} /usr/lib/${x}
			fi
		done
	fi

	chown -R lxlabs:lxlabs ${kloxopath}/cexe
	chmod -R 755 ${kloxopath}/cexe
	chmod -R ug+s ${kloxopath}/cexe

	chown -R lxlabs:lxlabs ${lxlabspath}
}

kloxo_install_step2() {
	echo ">>> Process basic Kloxo-MR configures (setting and database) <<<"

	if [ ! -d ${kloxopath}/etc/conf ] ; then
		mkdir -p ${kloxopath}/etc/conf
	fi

	if [ ! -f ${kloxopath}/etc/conf/kloxo.pass ] ; then
		echo ${mypass} > ${kloxopath}/etc/conf/kloxo.pass
	fi

	if [ ! -d ${kloxopath}/etc/slavedb ] ; then
		 mkdir -p ${kloxopath}/etc/slavedb
	fi

	if [ ! -d ${kloxopath}/etc/slavedb/dbadmin ] ; then
		dbadmindata='O:6:"Remote":1:{s:4:"data";a:1:{s:5:"mysql";a:1:{s:10:"dbpassword";s:0:"";}}}'
		echo ${dbadmindata} > ${kloxopath}/etc/slavedb/dbadmin
	fi

	if [ ! -d ${kloxopath}/etc/slavedb/driver ] ; then
		driverdata='O:6:"Remote":1:{s:4:"data";a:3:{s:3:"web";s:6:"apache";'
		driverdata=${driverdata}'s:4:"spam";s:10:"bogofilter";'
		driverdata=${driverdata}'s:3:"dns";s:4:"bind";'
		driverdata=${driverdata}'s:4:"pop3";s:7:"courier";'
		driverdata=${driverdata}'s:4:"smtp";s:5:"qmail";}}'

		echo ${driverdata} > ${kloxopath}/etc/slavedb/driver
	fi

	if [ -f ${initdpath}/mysqld ] || [ -f ${systemdpath}/mysqld.service ] ; then
		if [ "$(pgrep mysqld)" == "" ] ; then
			service mysqld start
		fi
	else
		if [ "$(pgrep mysql)" == "" ] ; then
			service mysql start
		fi
	fi

	sh /script/reset-mysql-root-password ${dbpass}

	if [ -f ${initdpath}/mysqld ] || [ -f ${systemdpath}/mysqld.service ] ; then
		service mysqld restart
	else
		service mysql restart
	fi

	cd ${kloxopath}/httpdocs

	lxphp.exe ${kloxopath}/bin/install/create.php \
		--install-type=${installtype} \
		--db-rootuser=${dbroot} \
		--db-rootpassword=${dbpass}
}

kloxo_install_easyinstaller() {
	echo ">>> Install 'Easy Installer' <<<"

	## First run (gets easyinstallerdata)
	/script/easyinstaller-update

	## Second run (gets applications)
	/script/easyinstaller-update
}

kloxo_prepare_kloxo_httpd_dir() {
	echo ">>> Prepare 'defaults' paths <<<"
	
	hkhpath="/home/kloxo/httpd"

	mkdir -p ${hkhpath}

	if [ -f ${hkhpath}/skeleton-disable.zip ] ; then
		'rm' -f ${hkhpath}/skeleton-disable.zip
	fi
	
	chown -R apache:apache ${hkhpath}
}

kloxo_install_before_bye() {
	echo ">>> Setup default configure for Webserver <<<"

	if [ "$(rpm -qa fetchmail)" == "" ] ; then
		yum install fetchmail -y
	fi
	
	sp="${kloxopath}/file/apache/etc/conf.d"
	tp="/etc/httpd/conf.d"

	## MR -- php-fpm_event as default instead mod_php
	#if [ -f /etc/httpd/conf.d/php.conf ] ; then
		'cp' -rf ${sp}/fastcgi.conf ${tp}/fastcgi.conf
		'cp' -rf ${sp}/ssl.conf ${tp}/ssl.conf
		'cp' -rf ${sp}/__version.conf ${tp}/__version.conf
		'cp' -rf ${sp}/~lxcenter.conf ${tp}/~lxcenter.conf

		for x in php fcgid fcgid ruid2 suphp ; do
			'cp' -rf ${sp}/_inactive_.conf ${tp}/${x}.conf 
		done

		echo 'HTTPD=/usr/sbin/httpd.event' >/etc/sysconfig/httpd
	#fi

	## Set ownership for Kloxo httpdocs dir
	chown -R lxlabs:lxlabs ${kloxopath}/httpdocs
}

kloxo_install_bye() {
	## MR -- use '-i' instead '-I' because not exists in CentOS 5
	this_ip=$(hostname -i|awk '{print $1}')
	len=${#this_ip}

	https_spc=""
	for (( x=0; x<(28-${len}); x++ )) ; do
		https_spc=$https_spc" "
	done

	http_spc=""
	for (( x=0; x<(43-${len}); x++ )) ; do
		http_spc=$http_spc" "
	done

		echo
		echo "._/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/."
		echo "._/                                                                          _/."
		echo "._/ Congratulations. Kloxo-MR has been installed succesfully as 'MASTER'     _/."
		echo "._/                                                                          _/."

	if [ "${installtype}" == 'master' ] ; then
		echo "._/ You can connect to the server at:                                        _/."
		echo "._/     https://${this_ip}:7777 - secure ssl connection, or${https_spc}_/."
		echo "._/     http://${this_ip}:7778 - normal one.${http_spc}_/."
		echo "._/                                                                          _/."
		echo "._/ The login and password are 'admin' and 'admin' for new install.          _/."
		echo "._/ After Logging in, you will have to change your password to               _/."
		echo "._/ something more secure.                                                   _/."
		echo "._/                                                                          _/."
	else
		echo "._/ You should open the port 7779 on this server, since this is used for     _/."
		echo "._/ the communication between master and slave                               _/."
		echo "._/                                                                          _/."
		echo "._/ To access this slave, to go 'admin->servers->add server',                _/."
		echo "._/ give the ip/machine name of this server. The password is 'admin'.        _/."
		echo "._/                                                                          _/."
		echo "._/ The slave will appear in the list of slaves, and you can access it       _/."
		echo "._/ just like you access localhost                                           _/."
		echo "._/                                                                          _/."
	fi

	if [ "${kloxostate}" != 'none' ] ; then
		echo "._/ - Need running 'sh /script/cleanup' for update                           _/."
	fi

		echo "._/ - Run 'sh /script/mysql-convert --engine=myisam' to minimize MySQLcccc   _/."
		echo "._/   memory usage. Or, go to 'admin->Webserver Configure'                   _/."
		echo "._/ - Run 'sh /script/make-slave' for change to 'SLAVE'                      _/."

	if [ "$(rpm -qa qmail)" != "" ] ; then
		echo "._/ - Run 'sh /script/convert-to-qmailtoaster' to convert qmail-toaster      _/."
	fi

		echo "._/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/."
		echo
}

install_yum_repo() {
	echo ">>> Modified mratwork.repo and remove older repo names <<<"

	sh /script/fixrepo

	yum clean all
}

lxins_main() {

	if [ ! -d ${kloxopath}/log ] ; then
		## MR -- crucial because segfault if not exists
		mkdir -p ${kloxopath}/log
	fi 

	## MR -- to make sure /tmp ready for all; found mysql not able to start if not 1777
	chmod 1777 /tmp

	sh /script/set-limits

	install_yum_repo

	if [ "$(uname -m)" == 'x86_64' ] ; then
		## MR -- remove because make conflict
		yum remove -y *.i386 *.i686 >/dev/null 2>&1
	fi

	if [ "${kloxostate}" != 'none' ] ; then
		## MR -- Create temporary flags for install
		mkdir -p /var/cache/kloxo

		for (( x=0; x<1000; x++ )) ; do
			if [ ! -d ${kloxopath}.old${x} ] ; then
					'cp' -rf ${kloxopath} ${kloxopath}.old${x}
					break
			fi
		done
	fi

	kloxo_install_step1

	install_main

	if [ "${kloxostate}" == 'none' ] ; then
		kloxo_install_step2
	fi

	kloxo_vpopmail

	kloxo_prepare_kloxo_httpd_dir

	kloxo_install_before_bye

	if [ "${installtype}" == 'master' ] ; then
		if [ -d /var/lib/mysql/kloxo ] ; then
			kloxo_service_init
			kloxo_install_bye
		fi
	else
		kloxo_service_init
		kloxo_install_bye
	fi
}

lxins_main
