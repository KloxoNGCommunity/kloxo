%define repohost repo.kloxong.org
%define mirrorhost raw.githubusercontent.com/KloxoNGCommunity/KloxoNG-rpms/dev/kloxong/mirror
Summary: KloxoNG release file and package configuration
Name: kloxong-testing
Version: 0.1.1
Release: 1
License: AGPLV3
Group: System Environment/Base
URL: http://kloxong.org/

BuildArch: noarch
Packager: John Parnell Pierce <john@luckytanuki.com>
Vendor: Kloxo Next Generation Repository, http://%{repohost}/
#BuildRequires: redhat-rpm-config

%description
Kloxo Next Generation rpm testing. This package contains yum configuration for the Kloxo Next Generation test RPM Repository.

%prep

%build

cat > kloxong-test.repo << _EOF_

[kloxong-copr-testing]
name=kloxong testing Copr repo 
baseurl=https://copr-be.cloud.fedoraproject.org/results/kloxong/Testing/epel-\$releasever-\$basearch/
type=rpm-md
skip_if_unavailable=True
gpgcheck=1
gpgkey=https://copr-be.cloud.fedoraproject.org/results/kloxong/httpd24/pubkey.gpg
repo_gpgcheck=0
enabled=1
enabled_metadata=1

_EOF_

%install
%{__rm} -rf %{buildroot}
%{__mkdir} -p %{buildroot}%{_sysconfdir}/yum.repos.d/
install -m 755 kloxong.repo %{buildroot}%{_sysconfdir}/yum.repos.d/kloxong-test.repo


%clean

%post

%files
%defattr(-, root, root, 0755)
%dir %{_sysconfdir}/yum.repos.d/
%{_sysconfdir}/yum.repos.d/kloxong-test.repo

%changelog
* Fri Jun 26 2020 John Parnell Pierce <john@luckytanuki.com> 
- updating way testing repo is installed. This file now only contains yum entry for test repo

* Thu Apr 2 2020  John Parnell Pierce <john@luckytanuki.com> - 0.1.1-1
- updated changelog for changes over 12 months
- merge updates from Kloxo-MR
- add kloxong repo to release spec
- clean up spec file
- add gpg key for varnish repo (Thanks DK)
- update test repos
- rebrand to Kloxo Next Generation
- remove old kloxong neutral and no arch repos


* Mon Jan 29 2018 John Parnell Pierce <john@luckytanuki.com> 
- change product name to kloxong
- add obsolete for kloxomr 

* Mon Dec 16 2013 Mustafa Ramadhan <mustafa@bigraf.com> - 0.0.1-1
- first release
