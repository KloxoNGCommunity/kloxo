# kloxong
Kloxo Next Generation - Building upon Kloxo-MR, KloxoNG aims to address the short falls of this control panel

To install

# remove old rpm
    rm -f kloxong*

# install rpm (read Warning)
    rpm -ivh https://github.com/KloxoNGCommunity/kloxong/raw/initial-rpm/kloxong-release.rpm

# move to /
    cd /

# update
    yum clean all

    yum update kloxong-* -y

    yum install kloxong -y

    sh /script/upcp
