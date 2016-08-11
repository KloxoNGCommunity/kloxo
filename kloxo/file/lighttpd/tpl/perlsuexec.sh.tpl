<?php
$userinfo = posix_getpwnam($user);

userid =  $userinfo['uid'];
?>
#!/bin/sh
### Username: <?=$user;?>

export MUID=<?=$userid;?>

export GID=<?=$userid;?>

export PHPRC=/home/httpd/<?=$domainname;?>

export TARGET=/usr/bin/perl
export NON_RESIDENT=1
exec execwrap $*