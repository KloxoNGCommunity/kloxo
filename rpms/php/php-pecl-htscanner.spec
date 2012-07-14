# $Id: php-pecl-htscanner.spec 137 2007-05-28 10:50:29Z matthias $

%global php_zendabiver %((echo 0; php -i 2>/dev/null | sed -n 's/^PHP Extension => //p') | tail -1)
%global php_extdir     %(php-config --extension-dir 2>/dev/null || echo "undefined")

Summary: PECL package to use per-directory PHP configuration
Name: php-pecl-htscanner
Version: 1.0.1
Release: 1%{?dist}
License: PHP
Group: Development/Languages
URL: http://pecl.php.net/package/htscanner
Source: http://pecl.php.net/get/htscanner-%{version}.tgz
BuildRoot: %{_tmppath}/%{name}-%{version}-%{release}-root
Requires: php-zend-abi = %{php_zendabiver}
BuildRequires: php, php-devel
# Required by phpize
BuildRequires: autoconf, automake, libtool

%description
Allow one to use htaccess-like file to configure PHP per directory, just like
apache's htaccess. It is especially useful with fastcgi.


%prep
%setup -q -c
%{__mv} htscanner-%{version}/* .


%build
# Workaround for broken old phpize on 64 bits
%{__cat} %{_bindir}/phpize | sed 's|/lib/|/%{_lib}/|g' > phpize && sh phpize
%configure
%{__make} %{?_smp_mflags}


%install
%{__rm} -rf %{buildroot}
%{__make} install INSTALL_ROOT=%{buildroot}

# Drop in the bit of configuration
%{__install} -D -p -m 0644 docs/htscanner.ini \
    %{buildroot}%{_sysconfdir}/php.d/htscanner.ini


%clean
%{__rm} -rf %{buildroot}


%files
%defattr(-,root,root,-)
%doc CREDITS README
%config(noreplace) %{_sysconfdir}/php.d/htscanner.ini
%{php_extdir}/htscanner.so


%changelog
* Mon May 28 2007 Matthias Saou <http://freshrpms.net/> 0.8.1-1
- Initial RPM release.

