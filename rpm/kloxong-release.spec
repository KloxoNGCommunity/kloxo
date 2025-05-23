%define repohost repo.kloxong.org
%define mirrorhost raw.githubusercontent.com/KloxoNGCommunity/KloxoNG-rpms/dev/kloxong/mirror
Summary: Kloxo release file and package configuration
Name: kloxo-release
Version: 8.1.1
Release: 8
License: AGPLV3
Group: System Environment/Base
URL: http://kloxong.org/

BuildArch: noarch
Packager: John Parnell Pierce <john@luckytanuki.com>
Vendor: Kloxo Next Generation Repository, http://%{repohost}/
#BuildRequires: redhat-rpm-config
Obsoletes: mratwork-release > 0 , mratwork-testing > 0, kloxong-release > 0

%if 0%{?fedora} >= 27 || 0%{?rhel} >= 8
# switch to add and exclude repos for EL8
%global with_pre8_repos  0
%else
%global with_pre8_repos  1
%endif



%description
Kloxo Next Generation rpm release. This package contains yum configuration for the Kloxo Next Generation RPM Repository.

%prep

%build

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


[kloxo-copr-qmail]
name=kloxong qmail repo 
baseurl=https://download.copr.fedorainfracloud.org/results/kloxong/kloxo-qmail/epel-\$releasever-\$basearch/
type=rpm-md
skip_if_unavailable=True
gpgcheck=1
gpgkey=https://download.copr.fedorainfracloud.org/results/kloxong/kloxo-qmail/pubkey.gpg
repo_gpgcheck=0
enabled=1
enabled_metadata=1



# ==================================

[kloxo-remi]
name=Kloxo - Les RPM de remi pour Enterprise Linux $releasever
#baseurl=http://rpms.remirepo.net/enterprise/\$releasever/remi/\$basearch/
mirrorlist=http://cdn.remirepo.net/enterprise/\$releasever/remi/\$basearch/mirror
gpgcheck=0
enabled=1
#includepkgs=php-ffmpeg php-ioncube-loader
exclude= php-* mysql5* mysql56*  mariadb* postfix32u*


# ==================================

[kloxo-epel]
name=Kloxo - Extra Packages for EL \$releasever
#baseurl=http://download.fedoraproject.org/pub/epel/\$releasever/\$basearch
metalink=https://mirrors.fedoraproject.org/metalink?repo=epel-\$releasever&arch=\$basearch&infra=\$infra&content=\$contentdir
enabled=1
gpgcheck=0
exclude=postfix* exim* ssmtp* pdns*

# ==================================

# for nginx
[kloxo-nginx]
name=Kloxo - nginx repo
baseurl=http://nginx.org/packages/mainline/centos/\$releasever/\$basearch/
module_hotfixes = 1
enabled=1
gpgcheck=0

# for nginx-stable
[kloxo-nginx-stable]
name=Kloxo - nginx-stable repo
baseurl=http://nginx.org/packages/centos/\$releasever/\$basearch/
module_hotfixes = 1
enabled=1
gpgcheck=0

# ==================================

# for mariadb
[kloxo-mariadb]
name=Kloxo - mariadb repo
baseurl=https://rpm.mariadb.org/10.6/rhel/\$releasever/\$basearch
gpgkey = https://rpm.mariadb.org/RPM-GPG-KEY-MariaDB
module_hotfixes = 1
enabled=1
gpgcheck=0

# ==================================

# for litespeed
[kloxo-litespeed]
name=Kloxo - LiteSpeed Tech Repository for CentOS \$releasever - \$basearch
baseurl=http://rpms.litespeedtech.com/centos/\$releasever/\$basearch/
#failovermethod=priority
enabled=0
gpgcheck=0

[kloxo-litespeed-update]
name=Kloxo - LiteSpeed Tech Repository for CentOS \$releasever - \$basearch
baseurl=http://rpms.litespeedtech.com/centos/\$releasever/update/\$basearch/
#failovermethod=priority
enabled=0
gpgcheck=0

# ==================================

# for mod-pagespeed
[kloxo-google-mod-pagespeed]
name=Kloxo - google-mod-pagespeed
baseurl=https://dl.google.com/linux/mod-pagespeed/rpm/stable/\$basearch
enabled=1
gpgcheck=0


_EOF_





%install
%{__rm} -rf %{buildroot}
%{__mkdir} -p %{buildroot}%{_sysconfdir}/yum.repos.d/
install -m 755 kloxo.repo %{buildroot}%{_sysconfdir}/yum.repos.d/kloxo.repo

%{__rm} -rf %{_sysconfdir}/yum.repos.d/KloxoNG.repo
%{__rm} -rf %{_sysconfdir}/yum.repos.d/kloxo-custom.repo
%{__rm} -rf %{_sysconfdir}/yum.repos.d/lxcenter.repo
%{__rm} -rf %{_sysconfdir}/yum.repos.d/lxlabs.repo


%clean

%post

%files
%defattr(-, root, root, 0755)
%dir %{_sysconfdir}/yum.repos.d/
%{_sysconfdir}/yum.repos.d/kloxo.repo

%changelog
* Thu Oct 24 2024  John Parnell Pierce <john@luckytanuki.com> - 8.1.1-8
- Module hot fix needs to be one for nginx repos to work in el8

* Sun Sep 29 2024  John Parnell Pierce <john@luckytanuki.com> - 8.1.1-7
- google-mod-pagespeed requires https

* Tue Jun 25 2024  John Parnell Pierce <john@luckytanuki.com> - 8.1.1-1
- Configure for kloxo 8

* Wed Sep 13 2023 John Parnell Pierce <john@luckytanuki.com> - 0.1.1-10
- go back to MariaDB 10.5

* Thu Sep 7 2023 John Parnell Pierce <john@luckytanuki.com> - 0.1.1-9
- add missing php excludes for webtastic

* Tue Sep 5 2023 John Parnell Pierce <john@luckytanuki.com> - 0.1.1-8
- add excludes for conflicting php versions

* Thu Aug 31 2023  John Parnell Pierce <john@luckytanuki.com> - 0.1.1-7
- Fix typo in remi mirror url

* Thu Aug 31 2023  John Parnell Pierce <john@luckytanuki.com> - 0.1.1-6
- Active remi repo for php 8.1 & 8.2

* Mon May 1 2023  John Parnell Pierce <john@luckytanuki.com> - 0.1.1-5
- Move Kloxong Curl to it own copr repo

* Fri Apr 15 2022  John Parnell Pierce <john@luckytanuki.com> - 0.1.1-4
- Add repo includes/excludes for EL8 builds

* Thu Oct 7 2021  John Parnell Pierce <john@luckytanuki.com> - 0.1.1-3
- Exclude IUS from Centos 8 version 

* Sat Jun 5 2021  John Parnell Pierce <john@luckytanuki.com> - 0.1.1-2
- Exclude postfix32u* as it conflicts with toaster packages 

* Fri Jun 26 2020 John Parnell Pierce <john@luckytanuki.com> 
- updating way testing repo is installed. This file has had yum entry for test repo removed

* Thu Apr 2 2020  John Parnell Pierce <john@luckytanuki.com> - 0.1.1-1
- updated changelog for changes over 12 months
- merge updates from Kloxo-MR
- add kloxong repo to release spec
- clean up spec file
- add gpg key for varnish repo (Thanks DK)
- update test repos
- remove old kloxong neutral and no arch repos

* Mon Jan 29 2018 John Parnell Pierce <john@luckytanuki.com> 
- rebrand to Kloxo Next Generation
- change product name to kloxong
- add obsolete for kloxomr 
- change MRatWork to kloxong

* Mon Dec 16 2013 Mustafa Ramadhan <mustafa@bigraf.com> - 0.0.1-1
- first release
