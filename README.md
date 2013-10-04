![Kloxo-MR logo](http://mratwork.com/images/kloxo-mr.png)

===================

Branch:
- master - original code from lxcenter
- dev - branch for next release
- bugfix-6.5.0 - branch for bugfix for 6.5.0.f
- release - branch for release in zip format
- rpms - branch for rpms

===================

# Kloxo-MR

This is special edition (fork) of Kloxo with many features not existing on Kloxo official release (6.1.12+).

This fork named as Kloxo-MR (meaning 'Kloxo fork by Mustafa Ramadhan').

### URL

1. More information about Kloxo (Official from LxCenter) - http://lxcenter.org/ and http://forum.lxcenter.org/

2. More information about Kloxo-MR - http://mratwork.com/ and http://forum.mratwork.com/

### Kloxo-MR Features

* OS: Redhat/CentOS 5 and 6 (32bit and 64bit) or their variants
* Billing: AWBS, WHMCS, HostBill, TheHostingTool, AccountLab Plus and Blesta (note: claim by billing's author)
* Web server: Nginx, Nginx-Proxy and Lighttpd-proxy; beside Httpd and Lighttpd (in progress: Varnish, Hiawatha, ATS and Httpd 2.4) *)
* Php: Dual-php with php 5.3/5.4 as primary and php 5.2 as secondary (in progress: multiple-php) *)
* PHP-type for Apache: php-fpm_worker/_event and fcgid_worker/_event; beside mod_php/_ruid2/_itk and suphp/_worker/_event
* Mail server: qmail-toaster instead special qmail (in progress: change from courier-imap to dovecot as imap/pop3) *)
* Database: MySQL or MariaDB
* Database Manager: PHPMyAdmin; Adminer, MyWebSql and SqlBuddy as additional **)
* Webmail: Afterlogic Webmail Lite, Telaen, Squirrelmail and Roundcube; Horde and T-Dah dropped
* FTP server: Pure-ftpd
* DNS Server: Bind and Djbdns; ready testing for Powerdns, MaraDNS and NSD *)
* Addons: ClamAV, Spamassassin/Bogofilter/Spamdyke and RKHunter
* Fixed many bugs of Kloxo Official (including security issues)
* And many more!

### Contributing

* Always invite for devs and testers. Go to http://mratwork.com/ and http://forum.mratwork.com/

### Licensing - AGPLv3

* Like Kloxo Official, Kloxo-MR adopt AGPLv3 too.

### How to install

* Read https://github.com/mustafaramadhan/kloxo/blob/release/how-to-install.txt


### Notes
*) New features in Kloxo-MR 6.5.1 (Final version Dec 2013 - Jan 2014)

- Web: Hiawatha (ready for testing since 28 Sep 2013) and Httpd 2.4
- Web cache: Varnish and ATS (Apache Traffic Server) (ready for testing since 3 Oct 2013)
- DNS: Powerdns, MaraDNS and NSD (ready for testing since 16 Sep 2013)
- Mail: Dovecot
- Php: multiple Php versions running together (php-fpm/spawning base)

**) New features in Kloxo-MR 6.5.0 after released
- Panel: Adminer, MyWebSql and SqlBuddy as alternative for Database management
- Core: change to use Hiawatha + php52s from lxphp + lxlighttpd for handling

