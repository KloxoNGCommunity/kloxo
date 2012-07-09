%define nginx_user      nginx
%define nginx_group     %{nginx_user}
%define nginx_home      %{_localstatedir}/lib/nginx
%define nginx_home_tmp  %{nginx_home}/tmp
%define nginx_logdir    %{_localstatedir}/log/nginx
%define nginx_confdir   %{_sysconfdir}/nginx
%define nginx_datadir   %{_datadir}/nginx
%define nginx_webroot   %{nginx_datadir}/html

Name:           nginx
Version:        1.3.1
Release:        1%{?dist}
Summary:        Robust, small and high performance http and reverse proxy server
Group:          System Environment/Daemons

License:        BSD
URL:            http://nginx.net/
BuildRoot:      %{_tmppath}/%{name}-%{version}-%{release}-root-%(%{__id_u} -n)

BuildRequires:      pam-devel,pcre-devel,zlib-devel,openssl-devel,perl(ExtUtils::Embed)
BuildRequires:      GeoIP-devel
Requires:           pcre,zlib,openssl
Requires:           GeoIP
Requires:           perl(:MODULE_COMPAT_%(eval "`%{__perl} -V:version`"; echo $version))
Requires(pre):      shadow-utils
Requires(post):     chkconfig
Requires(preun):    chkconfig, initscripts
Requires(postun):   initscripts

Source0:        http://sysoev.ru/nginx/nginx-%{version}.tar.gz
Source1:        %{name}.init
Source2:        %{name}.logrotate
Source3:        virtual.conf
Source4:        ssl.conf
Source5:        nginx-upstream-fair.tar.gz
Source6:        upstream-fair.conf
Source7:        %{name}.sysconfig
Source8:        nginx_upload_module-2.2.0.tar.gz
Source9:        Mod_zip-1.1.6.tar.gz
Source10:       masterzen-nginx-upload-progress-module-v0.8.3-2-g03cbf1f.tar.gz
Source11:       ngx_http_auth_pam_module-1.2.tar.gz
Source20:       ngx_cache_purge-1.5.tar.gz
Source21:       GeoIPCountryCSV.zip
Source100:      index.html
Source103:      50x.html
Source104:      404.html
Source200:      nginx.pam
Patch0:         nginx-auto-cc-gcc.patch
Patch1:         nginx-config.patch
Patch2:         nginx-ngx_upload.patch


%description
Nginx [engine x] is an HTTP(S) server, HTTP(S) reverse proxy and IMAP/POP3
proxy server written by Igor Sysoev.

Following third party modules added:
* nginx-upstream-fair
* mod_zip
* ngx_http_auth_pam_module


%prep
%setup -q
%patch0 -p0
%patch1 -p0
%{__tar} zxvf %{SOURCE5}
%setup -T -D -a 8
%setup -T -D -a 9
%setup -T -D -a 10
%setup -T -D -a 11
%setup -T -D -a 20
%setup -T -D -a 21
%patch2 -p0


%build

# Convert GeoIP
perl contrib/geo2nginx.pl < GeoIPCountryWhois.csv > geo.data

# Rename dir
mv masterzen-nginx-upload-progress-module-03cbf1f nginx-upload-progress-module

# nginx does not utilize a standard configure script.  It has its own
# and the standard configure options cause the nginx configure script
# to error out.  This is is also the reason for the DESTDIR environment
# variable.  The configure script(s) have been patched (Patch1 and
# Patch2) in order to support installing into a build environment.
export DESTDIR=%{buildroot}
./configure \
    --user=%{nginx_user} \
    --group=%{nginx_group} \
    --prefix=%{nginx_datadir} \
    --sbin-path=%{_sbindir}/%{name} \
    --conf-path=%{nginx_confdir}/%{name}.conf \
    --error-log-path=%{nginx_logdir}/error.log \
    --http-log-path=%{nginx_logdir}/access.log \
    --http-client-body-temp-path=%{nginx_home_tmp}/client_body \
    --http-proxy-temp-path=%{nginx_home_tmp}/proxy \
    --http-fastcgi-temp-path=%{nginx_home_tmp}/fastcgi \
    --http-uwsgi-temp-path=%{nginx_home_tmp}/uwsgi \
    --http-scgi-temp-path=%{nginx_home_tmp}/scgi \
    --pid-path=%{_localstatedir}/run/%{name}.pid \
    --lock-path=%{_localstatedir}/lock/subsys/%{name} \
    --with-http_secure_link_module \
    --with-http_random_index_module \
    --with-http_ssl_module \
    --with-http_realip_module \
    --with-http_addition_module \
    --with-http_sub_module \
    --with-http_dav_module \
    --with-http_flv_module \
    --with-http_mp4_module \
    --with-http_gzip_static_module \
    --with-http_degradation_module \
    --with-http_stub_status_module \
    --with-http_perl_module \
    --with-http_geoip_module \
    --with-mail \
    --with-mail_ssl_module \
    --with-cc-opt="%{optflags} $(pcre-config --cflags)" \
    --with-ipv6 \
    --with-file-aio     \
    --add-module=%{_builddir}/nginx-%{version}/nginx-upstream-fair \
    --add-module=%{_builddir}/nginx-%{version}/nginx-upload-progress-module \
    --add-module=%{_builddir}/nginx-%{version}/mod_zip-1.1.6 \
    --add-module=%{_builddir}/nginx-%{version}/nginx_upload_module-2.2.0 \
    --add-module=%{_builddir}/nginx-%{version}/ngx_http_auth_pam_module-1.2\
    --add-module=%{_builddir}/nginx-%{version}/ngx_cache_purge-1.5
make %{?_smp_mflags}

mv nginx-upstream-fair/README nginx-upstream-fair/README.nginx-upstream-fair

mv nginx_upload_module-2.2.0/Changelog nginx_upload_module-2.2.0/Changelog.nginx_upload_module
mv nginx_upload_module-2.2.0/example.php nginx_upload_module-2.2.0/example.php.nginx_upload_module
mv nginx_upload_module-2.2.0/nginx.conf nginx_upload_module-2.2.0/nginx.conf.nginx_upload_module
mv nginx_upload_module-2.2.0/upload.html nginx_upload_module-2.2.0/upload.html.nginx_upload_module

mv mod_zip-1.1.6/CHANGES mod_zip-1.1.6/CHANGES.mod_zip
mv mod_zip-1.1.6/README mod_zip-1.1.6/README.mod_zip
mv mod_zip-1.1.6/t/nginx.conf mod_zip-1.1.6/t/nginx.conf.mod_zip

mv nginx-upload-progress-module/CHANGES nginx-upload-progress-module/CHANGES.nginx_uploadprogress_module
mv nginx-upload-progress-module/README nginx-upload-progress-module/README.nginx_uploadprogress_module

mv ngx_http_auth_pam_module-1.2/ChangeLog ngx_http_auth_pam_module-1.2/ChangeLog.ngx_http_auth_pam_module-1.2
mv ngx_http_auth_pam_module-1.2/README ngx_http_auth_pam_module-1.2/README.ngx_http_auth_pam_module-1.2

mv ngx_cache_purge-1.5/CHANGES ngx_cache_purge-1.5/CHANGES.ngx_cache_purge
mv ngx_cache_purge-1.5/README ngx_cache_purge-1.5/README.ngx_cache_purge


%install
rm -rf %{buildroot}
make install DESTDIR=%{buildroot} INSTALLDIRS=vendor
find %{buildroot} -type f -name .packlist -exec rm -f {} \;
find %{buildroot} -type f -name perllocal.pod -exec rm -f {} \;
find %{buildroot} -type f -empty -exec rm -f {} \;
find %{buildroot} -type f -exec chmod 0644 {} \;
find %{buildroot} -type f -name '*.so' -exec chmod 0755 {} \;
chmod 0755 %{buildroot}%{_sbindir}/nginx
%{__install} -p -D -m 0755 %{SOURCE1} %{buildroot}%{_initrddir}/%{name}
%{__install} -p -D -m 0644 %{SOURCE2} %{buildroot}%{_sysconfdir}/logrotate.d/%{name}
%{__install} -p -D -m 0644 %{SOURCE7} %{buildroot}%{_sysconfdir}/sysconfig/%{name}
%{__install} -p -d -m 0755 %{buildroot}%{nginx_confdir}/conf.d
%{__install} -p -D -m 0644 geo.data %{buildroot}%{nginx_confdir}/conf.d/geo.data
%{__install} -p -m 0644 %{SOURCE3} %{SOURCE4} %{SOURCE6} %{buildroot}%{nginx_confdir}/conf.d
%{__install} -p -d -m 0755 %{buildroot}%{nginx_home_tmp}
%{__install} -p -d -m 0755 %{buildroot}%{nginx_logdir}
%{__install} -p -d -m 0755 %{buildroot}%{nginx_webroot}
%{__install} -p -m 0644 %{SOURCE100} %{SOURCE103} %{SOURCE104} %{buildroot}%{nginx_webroot}
%{__mkdir_p} %{buildroot}%{_sysconfdir}/pam.d/
%{__install} -p -m 0644 %{SOURCE200} %{buildroot}%{_sysconfdir}/pam.d/%{name}


# convert to UTF-8 all files that give warnings.
for textfile in CHANGES
do
    mv $textfile $textfile.old
    iconv --from-code ISO8859-1 --to-code UTF-8 --output $textfile $textfile.old
    rm -f $textfile.old
done

%clean
rm -rf %{buildroot}

%pre
%{_sbindir}/useradd -c "Nginx user" -s /bin/false -r -d %{nginx_home} %{nginx_user} 2>/dev/null || :

%post
if [ $1 = 1 ]; then
/sbin/chkconfig --add %{name}
fi
if [ $1 = 2 ]; then
/sbin/service %{name} upgrade
fi


%preun
if [ $1 = 0 ]; then
    /sbin/service %{name} stop >/dev/null 2>&1
    /sbin/chkconfig --del %{name}
fi


%files
%defattr(-,root,root,-)
%doc LICENSE CHANGES README

%doc nginx-upstream-fair/README.nginx-upstream-fair

%doc nginx_upload_module-2.2.0/Changelog.nginx_upload_module nginx_upload_module-2.2.0/upload.html.nginx_upload_module
%doc nginx_upload_module-2.2.0/example.php.nginx_upload_module nginx_upload_module-2.2.0/nginx.conf.nginx_upload_module

%doc mod_zip-1.1.6/CHANGES.mod_zip mod_zip-1.1.6/README.mod_zip mod_zip-1.1.6/t/nginx.conf.mod_zip

%doc nginx-upload-progress-module/CHANGES.nginx_uploadprogress_module nginx-upload-progress-module/README.nginx_uploadprogress_module

%doc ngx_http_auth_pam_module-1.2/ChangeLog.ngx_http_auth_pam_module-1.2 ngx_http_auth_pam_module-1.2/README.ngx_http_auth_pam_module-1.2

%doc ngx_cache_purge-1.5/CHANGES.ngx_cache_purge ngx_cache_purge-1.5/README.ngx_cache_purge

%{nginx_datadir}/
%{_sbindir}/%{name}
%{_mandir}/man3/%{name}.3pm.gz
%{_initrddir}/%{name}
%dir %{nginx_confdir}
%dir %{nginx_confdir}/conf.d
%config(noreplace) %{nginx_confdir}/conf.d/*
%config(noreplace) %{nginx_confdir}/win-utf
%config(noreplace) %{nginx_confdir}/%{name}.conf.default
%config(noreplace) %{nginx_confdir}/mime.types.default
%config(noreplace) %{nginx_confdir}/fastcgi_params
%config(noreplace) %{nginx_confdir}/fastcgi_params.default
%config(noreplace) %{nginx_confdir}/fastcgi.conf
%config(noreplace) %{nginx_confdir}/fastcgi.conf.default
%config(noreplace) %{nginx_confdir}/uwsgi_params
%config(noreplace) %{nginx_confdir}/uwsgi_params.default
%config(noreplace) %{nginx_confdir}/scgi_params
%config(noreplace) %{nginx_confdir}/scgi_params.default
%config(noreplace) %{nginx_confdir}/koi-win
%config(noreplace) %{nginx_confdir}/koi-utf
%config(noreplace) %{nginx_confdir}/%{name}.conf
%config(noreplace) %{nginx_confdir}/mime.types
%config(noreplace) %{_sysconfdir}/logrotate.d/%{name}
%config(noreplace) %{_sysconfdir}/sysconfig/%{name}
%config(noreplace) %{_sysconfdir}/pam.d/%{name}
%dir %{perl_vendorarch}/auto/%{name}
%{perl_vendorarch}/%{name}.pm
%{perl_vendorarch}/auto/%{name}/%{name}.so
%attr(-,%{nginx_user},%{nginx_group}) %dir %{nginx_home}
%attr(-,%{nginx_user},%{nginx_group}) %dir %{nginx_home_tmp}
%attr(-,%{nginx_user},%{nginx_group}) %dir %{nginx_logdir}


%changelog
* Wed Jun 06 2012 Denis Frolov <d.frolov81 at mail dot ru> - 1.3.1
- update to 1.3.1

* Wed May 16 2012 Denis Frolov <d.frolov81 at mail dot ru> - 1.3.0
- update to 1.3.0

* Thu Apr 24 2012 Denis Frolov <d.frolov81 at mail dot ru> - 1.1.20
- update to 1.1.20

* Fri Apr 13 2012 Denis Frolov <d.frolov81 at mail dot ru> - 1.1.19
- update to 1.1.19

* Wed Mar 29 2012 Denis Frolov <d.frolov81 at mail dot ru> - 1.1.18
- update to 1.1.18

* Wed Mar 16 2012 Denis Frolov <d.frolov81 at mail dot ru> - 1.1.17
- update to 1.1.17

* Wed Feb 28 2012 Denis Frolov <d.frolov81 at mail dot ru> - 1.1.16
- update to 1.1.16

* Tue Feb 22 2012 Denis Frolov <d.frolov81 at mail dot ru> - 1.1.15
- update to 1.1.15

* Tue Jan 31 2012 Denis Frolov <d.frolov81 at mail dot ru> - 1.1.14
- update to 1.1.14

* Tue Jan 17 2012 Denis Frolov <d.frolov81 at mail dot ru> - 1.1.13
- update to 1.1.13

* Tue Dec 27 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.1.12
- update to 1.1.12

* Tue Dec 13 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.1.11
- update to 1.1.11

* Tue Dec 01 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.1.10
- update to 1.1.10

* Wed Nov 30 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.1.9-2
- add epoll patch

* Thu Nov 29 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.1.9-1
- update to 1.1.9

* Wed Nov 16 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.1.8-1
- update to 1.1.8

* Thu Nov 01 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.1.7-1
- update to 1.1.7

* Thu Oct 18 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.1.6-1
- update to 1.1.6

* Tue Oct 06 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.1.5-1
- update to 1.1.5

* Wed Sep 21 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.1.4-1
- update to 1.1.4

* Mon Sep 19 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.1.3-2
- Delete nginx_mod_h264_streaming module

* Wed Sep 14 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.1.3-1
- add --with-http_mp4_module options

* Wed Sep 07 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.1.2-2
- update keep alive patch

* Wed Sep 07 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.1.2-1
- update to 1.1.2

* Mon Aug 22 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.1.1-1
- update to 1.1.1

* Thu Aug 03 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.1.0-2
- delete stripe patch
- add keepalive patch http://mailman.nginx.org/pipermail/nginx-ru/2011-August/042069.html
- add ngx_http_upstream_keepalive-0.4 module

* Thu Aug 03 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.1.0-1
- update to 1.1.0

* Wed Jul 20 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.0.5-1
- update to 1.0.5

* Tue Jun 02 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.0.4-1
- update to 1.0.4

* Tue May 25 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.0.3-1
- update to 1.0.3

* Thu May 11 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.0.2-1
- update to 1.0.2

* Thu May 03 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.0.1-1
- update to 1.0.1

* Fri Apr 29 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.0.0-7
- add patch for mod_strip

* Mon Apr 18 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.0.0-7
- update init script

* Mon Apr 18 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.0.0-6
- update %post section

* Mon Apr 18 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.0.0-5
- update init script
- add module ngx_http_auth_pam_module-1.2
- add module mod_strip
- update http://www.maxmind.com/download/geoip/database/GeoIPCountryCSV.zip

* Wed Apr 13 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.0.0-2
- add ipv6-fix patch

* Thu Apr 12 2011 Denis Frolov <d.frolov81 at mail dot ru> - 1.0.0-1
- update to 1.0.0

* Wed Apr 06 2011 Denis Frolov <d.frolov81 at mail dot ru> - 0.9.7-1
- update to 0.9.7

* Mon Mar 21 2011 Denis Frolov <d.frolov81 at mail dot ru> - 0.9.6-1
- update to 0.9.6

* Mon Feb 21 2011 Denis Frolov <d.frolov81 at mail dot ru> - 0.9.5-1
- update to 0.9.5

* Sun Jan 23 2011 Denis Frolov <d.frolov81 at mail dot ru> - 0.9.4-1
- update to 0.9.4

* Thu Dec 14 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.9.3-1
- update to 0.9.3

* Thu Dec 07 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.9.2-1
- update to 0.9.2

* Thu Nov 30 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.9.1-1
- update to 0.9.1

* Thu Nov 02 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.53-5
- add --with-file-aio
- add 2 aio patch

* Mon Oct 25 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.53-4
- fix geo.conf

* Thu Oct 19 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.53-1
- update to 0.8.53-1
- add --with-http_geoip_module

* Wed Sep 28 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.52-1
- update to 0.8.52-1

* Wed Sep 28 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.51-1
- update to 0.8.51-1
- update nginx-upload-module to 2.2.0
- update mod_zip to 1.1.6

* Wed Sep 03 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.50-1
- update to 0.8.50-1

* Tue Aug 10 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.49-1
- update to 0.8.49-1
- add --with-ipv6

* Thu Aug 03 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.48-1
- update to 0.8.48-1

* Thu Jul 28 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.47-1
- update to 0.8.47-1

* Tue Jul 20 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.46-1
- update to 0.8.46-1

* Thu Jul 15 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.45-1
- update to 0.8.45-1

* Mon Jul 05 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.44-1
- update to 0.8.44-1

* Fri Jul 02 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.43-1
- update to 0.8.43-1

* Wed Jun 23 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.42-1
- update to 0.8.42-1

* Wed Jun 16 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.41-1
- update to 0.8.41-1

* Thu Jun 08 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.40-1
- update to 0.8.40-1

* Sun Jun 06 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.39-1
- update to 0.8.39-1

* Thu May 25 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.38-1
- update to 0.8.38-1
- add nginx_mod_h264_streaming-2.2.7.patch

* Wed May 19 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.37-1
- update to 0.8.37-1

* Fri Apr 23 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.35-1
- update to 0.8.36-1

* Fri Apr 02 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.35-1
- update to 0.8.35-1

* Tue Mar 04 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.34-1
- update to 0.8.34-1

* Wed Mar 03 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.33-4
- update nginx_upload_module to 2.0.12

* Wed Feb 18 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.33-3
- Add nginx_mod_h264_streaming
- Add nginx.xs.patch http://nginx.org/pipermail/nginx-ru/2010-February/032233.html

* Wed Feb 10 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.33-2
- update to 0.8.33-2

* Wed Feb 02 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.33-1
- update to 0.8.33

* Wed Jan 13 2010 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.32-1
- update to 0.8.32

* Wed Dec 23 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.31-1
- update to 0.8.31
- remove nginx-proxy-cache-empty patch

* Mon Dec 15 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.30-1
- update to 0.8.30

* Tue Dec 03 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.29-2
- add nginx-proxy-cache-empty patch

* Thu Dec 01 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.29-1
- update to 0.8.29

* Thu Nov 24 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.28-1
- update to 0.8.28
- update nginx_upload_module to 2.0.11

* Sat Nov 21 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.27-2
- Add nginx_upload_module-2.0.10-compat-nginx-0.8.27.patch patch

* Wed Nov 18 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.27-1
- update to 0.8.27

* Fri Nov 13 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.24-1
- update to 0.8.24

* Wed Nov 05 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.22-1
- update to 0.8.22

* Thu Oct 27 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.21-1
- update to 0.8.21

* Thu Oct 15 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.20-1
- update to 0.8.20

* Wed Oct 07 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.19-1
- update to 0.8.19

* Mon Sep 28 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.17-1
- update to 0.8.17

* Wed Sep 23 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.16-1
- update to 0.8.16

* Thu Sep 15 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.15-3
- update nginx-upstream-fair

* Thu Sep 15 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.15-2
- update nginx-upload-progress-module to 0.6
- update mod_zip to 1.1.5

* Thu Sep 15 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.15-1
- update to 0.8.15

* Mon Sep 07 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.14-1
- update to 0.8.14

* Thu Sep 03 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.13-4
- add nginx_uploadprogress_module
- add mod_zip

* Thu Sep 03 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.13-3
- add --with-http_secure_link_module in configure
- add --with-http_random_index_module in configure

* Thu Sep 03 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.13-2
- add --with-file-aio in configure
- add nginx_upload_module

* Tue Sep 01 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.13-1
- update to 0.8.13

* Mon Aug 31 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.11-2
- add memcached patch

* Mon Aug 31 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.11-1
- rebuild to 0.8.11

* Thu Aug 25 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.10-1
- rebuild to 0.8.10

* Thu Aug 18 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.9-1
- rebuild to 0.8.9

* Mon Aug 10 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.8-1
- rebuild to 0.8.8

* Thu Jul 28 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.7-1
- rebuild to 0.8.7

* Mon Jul 20 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.6-1
- rebuild to 0.8.6

* Wed Jul 15 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.5-1
- rebuild to 0.8.5

* Mon Jun 22 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.4-1
- rebuild to 0.8.4

* Sat Jun 19 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.3-1
- rebuild to 0.8.3

* Wed Jun 16 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.2-1
- rebuild to 0.8.2

* Sat Jun 13 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.8.1-1
- rebuild to 0.8.1

* Sat Jun 13 2009 Denis Frolov <d.frolov81 at mail dot ru> - 0.7.59-1
- rebuild to 0.7.59

* Thu Feb 19 2009 Jeremy Hinegardner <jeremy at hinegardner dot org> - 0.6.35-2
- rebuild

* Thu Feb 19 2009 Jeremy Hinegardner <jeremy at hinegardner dot org> - 0.6.35-1
- update to 0.6.35

* Tue Dec 30 2008 Jeremy Hinegardner <jeremy at hinegardner dot org> - 0.6.34-1
- update to 0.6.34
- Fix inclusion of /usr/share/nginx tree => no unowned directories [mschwendt]

* Sun Nov 23 2008 Jeremy Hinegardner <jeremy at hinegardner dot org> - 0.6.33-1
- update to 0.6.33

* Sun Jul 27 2008 Jeremy Hinegardner <jeremy at hinegardner dot org> - 0.6.32-1
- update to 0.6.32
- nginx now supports DESTDIR so removed the patches that enabled it

* Mon May 26 2008 Jeremy Hinegardner <jeremy at hinegardner dot org> - 0.6.31-3
- update init script
- remove 'default' listen parameter

* Tue May 13 2008 Jeremy Hinegardner <jeremy at hinegardner dot org> - 0.6.31-2
- added missing Source files

* Mon May 12 2008 Jeremy Hinegardner <jeremy at hinegardner dot org> - 0.6.31-1
- update to new upstream stable branch 0.6
- added 3rd party module nginx-upstream-fair
- add /etc/nginx/conf.d support [#443280]
- use /etc/sysconfig/nginx to determine nginx.conf [#442708]
- added default webpages
- add Requires for versioned perl (libperl.so) (via Tom "spot" Callaway)
- drop silly file Requires (via Tom "spot" Callaway)

* Sat Jan 19 2008 Jeremy Hinegardner <jeremy at hinegardner dot org> - 0.5.35-1
- update to 0.5.35

* Sun Dec 16 2007 Jeremy Hinegardner <jeremy at hinegardner dot org> - 0.5.34-1
- update to 0.5.34

* Mon Nov 12 2007 Jeremy Hinegardner <jeremy@hinegardner.org> - 0.5.33-2
- bump build number - source wasn't update

* Mon Nov 12 2007 Jeremy Hinegardner <jeremy@hinegardner.org> - 0.5.33-1
* update to 0.5.33

* Mon Sep 24 2007 Jeremy Hinegardner <jeremy@hinegardner.org> - 0.5.32-1
- updated to 0.5.32
- fixed rpmlint UTF-8 complaints.

* Sat Aug 18 2007 Jeremy Hinegardner <jeremy@hinegardner.org> - 0.5.31-3
- added --with-http_stub_status_module build option.
- added --with-http_sub_module build option.
- add in pcre-config --cflags

* Sat Aug 18 2007 Jeremy Hinegardner <jeremy@hinegardner.org> - 0.5.31-2
- remove BuildRequires: perl-devel

* Fri Aug 17 2007 Jeremy Hinegardner <jeremy@hinegardner.org> - 0.5.31-1
- Update to 0.5.31
- specify license is BSD

* Sat Aug 11 2007 Jeremy Hinegardner <jeremy@hinegardner.org> - 0.5.30-2
- Add BuildRequires: perl-devel - fixing rawhide build

* Mon Jul 30 2007 Jeremy Hinegardner <jeremy@hinegardner.org> - 0.5.30-1
- Update to 0.5.30

* Tue Jul 24 2007 Jeremy Hinegardner <jeremy@hinegardner.org> - 0.5.29-1
- Update to 0.5.29

* Wed Jul 18 2007 Jeremy Hinegardner <jeremy@hinegardner.org> - 0.5.28-1
- Update to 0.5.28

* Mon Jul 09 2007 Jeremy Hinegardner <jeremy@hinegardner.org> - 0.5.27-1
- Update to 0.5.27

* Mon Jun 18 2007 Jeremy Hinegardner <jeremy@hinegardner.org> - 0.5.26-1
- Update to 0.5.26

* Sat Apr 28 2007 Jeremy Hinegardner <jeremy@hinegardner.org> - 0.5.19-1
- Update to 0.5.19

* Mon Apr 02 2007 Jeremy Hinegardner <jeremy@hinegardner.org> - 0.5.17-1
- Update to 0.5.17

* Mon Mar 26 2007 Jeremy Hinegardner <jeremy@hinegardner.org> - 0.5.16-1
- Update to 0.5.16
- add ownership of /usr/share/nginx/html (#233950)

* Fri Mar 23 2007 Jeremy Hinegardner <jeremy@hinegardner.org> - 0.5.15-3
- fixed package review bugs (#235222) given by ruben@rubenkerkhof.com

* Thu Mar 22 2007 Jeremy Hinegardner <jeremy@hinegardner.org> - 0.5.15-2
- fixed package review bugs (#233522) given by kevin@tummy.com

* Thu Mar 22 2007 Jeremy Hinegardner <jeremy@hinegardner.org> - 0.5.15-1
- create patches to assist with building for Fedora
- initial packaging for Fedora
