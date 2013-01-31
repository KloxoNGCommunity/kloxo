# Kloxo-MR

This is special edition (fork) of Kloxo with many features not existing on Kloxo official release (6.1.12+).

This fork named as Kloxo-MR (meaning 'Kloxo fork by Mustafa Ramadhan').

# About and URL

1. More information about Kloxo (Official from LxCenter) - http://lxcenter.org/ and http://forum.lxcenter.org/

2. More information about Kloxo-MR - http://mratwork.com/ and http://forum.mratwork.com/

# Kloxo-MR Features

* CentOS 5 and 6 (32bit and 64bit) Support
* Integrates with billing software such as AWBS, WHMCS and HostBill
* Web server: Nginx, Nginx-Proxy and Lighttpd-proxy beside Httpd and Lighttpd (in progress: Varnish, Hiawata, ATS and Httpd 2.4)
* Php: Dual-php with php 5.3/5.4 as primary and php 5.2 as secondary (in progress: multiple-php)
* PHP-type for Apache: php-fpm_worker/_event and fcgid_worker/_event beside mod_php/_ruid2/_itk and suphp/_worker/_event
* Mail server: qmail-toaster instead special qmail (in progress: change from courier-imap to dovecot as imap/pop3)
* FTP server: Pure-ftpd
* DNS Server: Bind, Djbdns and Powerdns (in progress)
* Fixed many bugs of Kloxo Official (including security issues)
* And many more!

# Contributing

* Always invite for every as dev and tester. Go to http://mratwork.com/ and http://forum.mratwork.com/

# Licensing - AGPLv3

* Like Kloxo Official, Kloxo-MR adopt AGPLv3 too.

# How to install

A. For Dev:

A.1. pre-install -- better for fresh install

*    cd /

* # update centos to latest version
* yum update -y
* # install some packages like package-cleanup, etc
* yum install yum-utils yum-priorities vim-minimal subversion curl zip unzip -y
* yum install telnet -y
* 
* setenforce 0
* echo 'SELINUX=disabled' > /etc/selinux/config
* 
* cd /

A.2. Install/reinstall/upgrade -- data not destroyed with this fork - for existing kloxo (6.1.x), run 'sh /script/update' first.

* # delete if exist
* rm -rf /tmp/kloxo
* 
* # create kloxo temp dir
* mkdir /tmp/kloxo ; cd /tmp/kloxo
* 
* # get kloxo packer from github
* wget https://github.com/mustafaramadhan/kloxo/raw/dev/kloxo-install/kloxo-packer.sh --no-check-certificate
* 
* # get kloxo fork from github
* sh kloxo-packer.sh --fork=mustafaramadhan --branch=dev
* 
* # install kloxo
* sh kloxo-installer.sh --type=master
* 
* # better reboot
* reboot
   
B. For Final Release:

* Coming soon...
