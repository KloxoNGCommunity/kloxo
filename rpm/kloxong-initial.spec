%define repohost repo.kloxong.org
%define mirrorhost raw.githubusercontent.com/KloxoNGCommunity/KloxoNG-rpms/dev/kloxong/mirror
Summary: Kloxo release file and package configuration
Name: kloxo-release
Version: 8.0.0
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


_EOF_




%install
%{__rm} -rf %{buildroot}
%{__mkdir} -p %{buildroot}%{_sysconfdir}/yum.repos.d/
install -m 755 kloxo.repo %{buildroot}%{_sysconfdir}/yum.repos.d/kloxo.repo


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
%{_sysconfdir}/yum.repos.d/kloxo.repo

%changelog

* Tue Jun 18 2024  John Parnell Pierce <john@luckytanuki.com> - 8.0.0-1
- starter repo for installing kloxo 8

* Thu Apr 2 2020  John Parnell Pierce <john@luckytanuki.com> - 0.1.1-1
- starter repo for installing kloxong