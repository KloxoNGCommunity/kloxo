%define repohost repo.kloxong.org
%define mirrorhost raw.githubusercontent.com/KloxoNGCommunity/KloxoNG-rpms/dev/kloxong/mirror
Summary: KloxoNG release file and package configuration
Name: kloxong-release
Version: 0.1.1
Release: 1
License: AGPLV3
Group: System Environment/Base
URL: http://kloxong.org/

BuildArch: noarch
Packager: John Parnell Pierce <john@luckytanuki.com>
Vendor: Kloxo Next Generation Repository, http://%{repohost}/
#BuildRequires: redhat-rpm-config
Obsoletes: mratwork-release > 0 , mratwork-testing > 0

%description
Kloxo Next Generation rpm release. This package contains yum configuration for the Kloxo Next Generation RPM Repository.

%prep

%build

cat > kloxong.repo << _EOF_
[kloxong-copr]
name=kloxong master Copr repo 
baseurl=https://copr-be.cloud.fedoraproject.org/results/kloxong/kloxong/epel-\$releasever-\$basearch/
type=rpm-md
skip_if_unavailable=True
gpgcheck=1
gpgkey=https://copr-be.cloud.fedoraproject.org/results/kloxong/kloxong/pubkey.gpg
repo_gpgcheck=0
enabled=1
enabled_metadata=1

[kloxong-copr-httpd24]
name=kloxong httpd24 Copr repo 
baseurl=https://copr-be.cloud.fedoraproject.org/results/kloxong/httpd24/epel-\$releasever-\$basearch/
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
install -m 755 kloxong.repo %{buildroot}%{_sysconfdir}/yum.repos.d/kloxong.repo


%{__rm} -rf %{_sysconfdir}/yum.repos.d/kloxo.repo
%{__rm} -rf %{_sysconfdir}/yum.repos.d/KloxoNG.repo
%{__rm} -rf %{_sysconfdir}/yum.repos.d/kloxo-custom.repo
%{__rm} -rf %{_sysconfdir}/yum.repos.d/lxcenter.repo
%{__rm} -rf %{_sysconfdir}/yum.repos.d/lxlabs.repo


%clean

%post

%files
%defattr(-, root, root, 0755)
%dir %{_sysconfdir}/yum.repos.d/
%{_sysconfdir}/yum.repos.d/kloxong.repo

%changelog

* Thu Apr 2 2020  John Parnell Pierce <john@luckytanuki.com> - 0.1.1-1
- starter repo for installing kloxong