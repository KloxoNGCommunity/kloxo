![KloxoNG](https://kloxong.org/wp-content/uploads/2017/10/KloxoNG.jpg )
# Kloxo

### A Kloxo fork by The Kloxo Next Generation Community

Please use master branch

===================

Branch:
- Master

===================

The current release Kloxo 8.0.0-18 - A Kloxo build for EL8 and EL9. 

<a href="https://copr.fedorainfracloud.org/coprs/kloxong/kloxo/package/kloxo/"><img src="https://copr.fedorainfracloud.org/coprs/kloxong/kloxo/package/kloxo/status_image/last_build.png" /></a>

## Kloxo

This is a updated development path of Kloxo based on the work of Kloxo-MR.

The aim of this project is to create a development pathway that is sustainable and dependent on a single individual.

### URL

1. More information about Kloxo Next Generation go to https://kloxong.org/ 

2. To support Kloxo Next Generation Join our Patreon at https://patreon.com/KloxoNextGeneration 

### Features 

Note: struck out items may or may not work. They either haven't been tested yet or are still in the process of being repackaged for el8/9   

* OS: Redhat/EL 8 and 9
* ~~Billing: AWBS, WHMCS, HostBill, TheHostingTool, AccountLab Plus, Blesta and BoxBilling (note: claim by billing's author)~~ To be tested and confirmed
* Web server: Nginx, Nginx-Proxy and Lighttpd-proxy, Hiawatha, Hiawatha-proxy and Httpd 24, beside Httpd and Lighttpd; also Dual and Multiple Web server *)
* Webcache server: ~~Squid,~~ Varnish ~~and ATS~~ *)
* Php: Multiple-php with php 5.6, 7.4, and all php 8 versions *)
* PHP-type for Apache: php-fpm_worker/_event and fcgid_worker/_event; beside mod_php/_ruid2/_itk and suphp/_worker/_event
* Mail server: qmail-toaster instead special qmail (in progress: change from courier-imap to dovecot as imap/pop3) *)
* Database: ~~MySQL or~~ MariaDB *)
* Database Manager: PHPMyAdmin; ~~Adminer, MyWebSql and SqlBuddy as additional~~ **)
* Webmail: Afterlogic Webmail Lite, ~~Telaen, Squirrelmail,~~ Roundcube and Rainloop; 
* FTP server: Pure-ftpd
* DNS Server: Bind ~~and Djbdns; add Powerdns,~~ ~~MaraDNS, NSD, myDNS and Yadifa~~ *)
* Addons: ClamAV, Spamassassin/Bogofilter/Spamdyke, RKHunter and MalDetect
* Free SSL: Let's Encrypt (via letsencrypt/certbot-auto and acme.sh) and StartAPI *)
* Fixed many bugs of Kloxo Official (including security issues)
* And many more!

### Contributing

* The door is always open for developers and testers. Pull Requests are very welcome, browse the issues pages if you want to help but don't know where to start

### Licensing - AGPLv3

* Like Kloxo Official, Kloxo will adopt AGPLv3 as well.

### How to install

* Read https://github.com/KloxoNGCommunity/kloxo/blob/main/how-to-install.txt

### Notes
*) Features inheritited from Kloxo-MR and KloxoNG 7 (Note: these may change as we develop our road map)

- OS: Redhat/CentOS 8 and EL 8/9 clones (64bit) 
- Web server: Httpd 2.4 
- Webcache server: ~~Squid,~~ Varnish ~~and ATS (Apache Traffic Server) (since 3 Oct 2013)~~
- DNS server: Powerdns, NSD, MyDNS and Yadifa (since 16 Sep 2013)
- Mail server: Dovecot (since 19 Jun 2016)
- Database: using MariaDB 10.6 instead of MySQL 
- Php: multiple Php versions
  * suphp base (since 27 Jun 2014)
  * fcgid base (since 5 Jul 2015)
  * php-fpm/spawning base (since 24 May 2016)
- Free SSL:
  * Let's Encrypt (since 4 May 2016)
  * StartAPI (since 29 Jun 2016)
- Stats:
  * Change URL from 'domain.com/stats' to 'stats.domain.com' (since 3 Sep 2016)
  



