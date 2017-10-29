<img src="https://kloxong.org/wp-content/uploads/2017/10/cropped-KloxoNG.jpg width="250" >
# KloxoNG

### A Kloxo-MR fork by The Kloxo Next Generation Community


===================

Branch:
- master - original code from lxcenter
- dev - branch for next release
- bugfix-6.5.0 - branch for bugfix for 6.5.0.f
- rpms - branch for mratwork repo rpms

===================

## KloxoNG

This is a new development path of Kloxo based on the work of Kloxo-MR.

The aim of this project is to create a develpment pathway that is not dependent on a single individual, while providing support to existing Kloxo-MR users.

### URL

1. More information about Kloxo NG go to https://kloxong.org/

### Features (based on exsitng Kloxo-MR features - this will change as we develop our road map)

* OS: Redhat/CentOS 5 and 6 (32bit and 64bit) or their variants; also Redhat/Centos 7 *)
* Billing: AWBS, WHMCS, HostBill, TheHostingTool, AccountLab Plus, Blesta and BoxBilling (note: claim by billing's author)
* Web server: Nginx, Nginx-Proxy and Lighttpd-proxy, Hiawatha, Hiawatha-proxy and Httpd 24, beside Httpd and Lighttpd; also Dual and Multiple Web server *)
* Webcache server: Squid, Varnish and ATS *)
* Php: Dual-php with php 5.3/5.4 as primary and php 5.2 as secondary; multiple-php *)
* PHP-type for Apache: php-fpm_worker/_event and fcgid_worker/_event; beside mod_php/_ruid2/_itk and suphp/_worker/_event
* Mail server: qmail-toaster instead special qmail (in progress: change from courier-imap to dovecot as imap/pop3) *)
* Database: MySQL or MariaDB *)
* Database Manager: PHPMyAdmin; Adminer, MyWebSql and SqlBuddy as additional **)
* Webmail: Afterlogic Webmail Lite, Telaen, Squirrelmail, Roundcube and Rainloop; Horde and T-Dah dropped
* FTP server: Pure-ftpd
* DNS Server: Bind and Djbdns; add Powerdns, ~~MaraDNS~~, NSD, myDNS and Yadifa *)
* Addons: ClamAV, Spamassassin/Bogofilter/Spamdyke, RKHunter and MalDetect
* Free SSL: Let's Encrypt (via letsencrypt/certbot-auto and acme.sh) and StartAPI *)
* Fixed many bugs of Kloxo Official (including security issues)
* And many more!

### Contributing

* The door is always open for developers and testers. Pull Requests are very welcome, browse the issues pages if you want to help but don't know where to start

### Licensing - AGPLv3

* Like Kloxo Official, Kloxo-NG adopt AGPLv3 too.

### How to install

* Read https://github.com/KloxoNGCommunity/kloxoNG-CP/blob/dev/how-to-install.txt

### Notes
*) New features in Kloxo-MR 7.0.0 (aka Kloxo-MR 7)

- Version 6.5.1 change to 7.0.0 since 20 Aug 2014 (beta step)
- OS: Redhat/CentOS 7 (64bit) (since 27 Feb 2017) *)
- Web server: Hiawatha (since 28 Sep 2013) and Httpd 2.4 (since 20 Jun 2015); Dual (since 19 Jan 2016) and Multiple Web server (in progress)
- Webcache server: Squid, Varnish and ATS (Apache Traffic Server) (since 3 Oct 2013)
- DNS server: Powerdns, NSD, MyDNS and Yadifa (since 16 Sep 2013)
- Mail server: Dovecot (since 19 Jun 2016)
- Database: using MariaDB 10.x instead MySQL 5.5
- Php: multiple Php versions
  * suphp base (since 27 Jun 2014)
  * fcgid base (since 5 Jul 2015)
  * php-fpm/spawning base (since 24 May 2016)
- Free SSL:
  * Let's Encrypt (since 4 May 2016)
  * StartAPI (since 29 Jun 2016)
- Stats:
  * Change URL from 'domain.com/stats' to 'stats.domain.com' (since 3 Sep 2016)

**) New features in Kloxo-MR 6.5.0 after released
- Panel: Adminer, MyWebSql and SqlBuddy as alternative for Database management
- Core: change from lxphp + lxlighttpd  to use Hiawatha + php52s for running panel

