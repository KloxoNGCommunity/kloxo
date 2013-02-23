<?php
$userinfo = posix_getpwnam($user);

if ($userinfo) {
    $fpmport = (50000 + $userinfo['uid']);
} else {
    return false;
}

// MR -- to make easy for watchdog, apache user have uid 50000
//$userinfoapache = posix_getpwnam('apache');
//$fpmportapache = (50000 + $userinfoapache['uid']);
$fpmportapache = 50000;

?>
#!/bin/sh
### Username: <?php echo $user; ?>

export MUID=<?php echo $userid; ?>

export GID=<?php echo $userid; ?>

export PHPRC=/home/httpd/<?php echo $domainname; ?>

export TARGET=/usr/bin/perl
export NON_RESIDENT=1
exec lxsuexec $*