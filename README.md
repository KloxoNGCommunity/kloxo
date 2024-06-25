# kloxong
Kloxo Next Generation - Building upon Kloxo-MR, KloxoNG aims to address the short falls of this control panel

To install

# remove old rpm
    rm -f kloxo*

# install rpm (read Warning)
    rpm -ivh https://github.com/KloxoNGCommunity/kloxo/raw/initial-rpm/kloxo-release.rpm

# move to /
    cd /

# update
    yum clean all

    yum update kloxo-* -y

    yum install kloxo -y

    sh /script/upcp
