### begin - web of '<?php echo $domainname; ?>' - do not remove/modify this line

<?php

if ($reverseproxy) {
    $ports[] = '30080';
    $ports[] = '30443';
} else {
    $ports[] = '80';
    $ports[] = '443';
}

if ($reverseproxy) {
    $tmp_ip = '*';

    foreach ($certnamelist as $ip => $certname) {
        $tmp_certname = $certname;
        break;
    }

    $certnamelist = null;

    $certnamelist[$tmp_ip] = $tmp_certname;
}

$statsapp = $stats['app'];
$statsprotect = ($stats['protect']) ? true : false;

$serveralias = "www.{$domainname}";

if ($wildcards) {
    $serveralias .= ", *.{$domainname}";
}

if ($serveraliases) {
    foreach ($serveraliases as &$sa) {
        $serveralias .= ", {$sa}";
    }
}

if ($parkdomains) {
    foreach ($parkdomains as $pk) {
        $pa = $pk['parkdomain'];
        $serveralias .= ", {$pa} www.{$pa}";
    }
}

if ($webmailapp === $webmailappdefault) {
    $webmaildocroot = "/home/kloxo/httpd/webmail/{$webmailapp}";

    if ($wildcards) {
        $webmailapp = "*";
    } else {
        $webmailapp = null;
    }
} else {
    if ($webmailapp) {
        if ($webmailapp === '--Disabled--') {
            $webmaildocroot = "/home/kloxo/httpd/disable";
        } else {
            $webmaildocroot = "/home/kloxo/httpd/webmail/{$webmailapp}";
        }
    } else {
        $webmaildocroot = "/home/kloxo/httpd/webmail";
    }
}

$webmailremote = str_replace("http://", "", $webmailremote);
$webmailremote = str_replace("https://", "", $webmailremote);

if ($indexorder) {
    $indexorder = implode(', ', $indexorder);
}

if ($blockips) {
    $biptemp = array();
    foreach ($blockips as &$bip) {
        if (strpos($bip, ".*.*.*") !== false) {
            $bip = str_replace(".*.*.*", ".0.0/8", $bip);
        }
        if (strpos($bip, ".*.*") !== false) {
            $bip = str_replace(".*.*", ".0.0/16", $bip);
        }
        if (strpos($bip, ".*") !== false) {
            $bip = str_replace(".*", ".0/24", $bip);
        }
        $biptemp[] = 'deny ' . $bip;
    }
    $blockips = $biptemp;

    $blockips = implode(', ', $blockips);
}

$userinfo = posix_getpwnam($user);

if ($userinfo) {
    $fpmport = (50000 + $userinfo['uid']);
} else {
    return false;
}

// MR -- for future purpose, apache user have uid 50000
// $userinfoapache = posix_getpwnam('apache');
// $fpmportapache = (50000 + $userinfoapache['uid']);
$fpmportapache = 50000;

$disablepath = "/home/kloxo/httpd/disable";

if (!$reverseproxy) {
    foreach ($certnamelist as $ip => $certname) {
        if ($ip !== '*') {
?>
Binding {
    BindingId = port_nonssl_<?php echo $certname; ?>

    Port = <?php echo $ports[0]; ?>

    Interface = <?php echo $ip; ?>

    MaxKeepAlive = 3600
    TimeForRequest = 3600
    MaxRequestSize = 102400
    ## not able more than 100MB
    MaxUploadSize = 100
}

Binding {
    BindingId = port_ssl_<?php echo $certname; ?>

    Port = <?php echo $ports[1]; ?>

    Interface = <?php echo $ip; ?>

    MaxKeepAlive = 3600
    TimeForRequest = 3600
    MaxRequestSize = 102400
    ## not able more than 100MB
    MaxUploadSize = 100
    #RequiredCA = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.ca
    SSLcertFile = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.pem
}

<?php
        }
    }
}

foreach ($certnamelist as $ip => $certname) {
    $count = 0;

    foreach ($ports as &$port) {
        $protocol = ($count === 0) ? "http://" : "https://";

        if ($disabled) {
?>

## webmail for '<?php echo $domainname; ?>'
VirtualHost {
    Hostname = webmail.<?php echo $domainname; ?>

    WebsiteRoot = <?php echo $disablepath; ?>

    #StartFile = <?php echo $indexorder; ?>

    StartFile = index.php
<?php
            if ($count !== 0) {
?>

    #RequiredCA = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.ca
    SSLcertFile = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.pem
<?php
            }
?>
            }

<?php
        } else {
            if ($webmailremote) {
?>

## webmail for '<?php echo $domainname; ?>'
VirtualHost {
    Hostname = webmail.<?php echo $domainname; ?>

    #Match ^/(.*) Redirect <?php echo $protocol; ?><?php echo $webmailremote; ?>/$1

<?php
                if ($count !== 0) {
?>

    #RequiredCA = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.ca
    SSLcertFile = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.pem
<?php
                }
?>
    UseFastCGI = php_for_apache
    TimeForCGI = 3600
}
<?php
            } elseif ($webmailapp) {
?>

## webmail for '<?php echo $domainname; ?>'
VirtualHost {
    Hostname = webmail.<?php echo $domainname; ?>

    WebsiteRoot = <?php echo $webmaildocroot; ?>

    #StartFile = <?php echo $indexorder; ?>

    StartFile = index.php
<?php
                if ($count !== 0) {
?>

    #RequiredCA = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.ca
    SSLcertFile = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.pem
<?php
                }
?>
    UseFastCGI = php_for_apache
    TimeForCGI = 3600
}

<?php
            } else {
?>

## webmail for '<?php echo $domainname; ?>' handled by ../defaults/webmail.conf

<?php
            }
        }
?>

## web for '<?php echo $domainname; ?>'
VirtualHost {
    Hostname = <?php echo $domainname; ?>, <?php echo $serveralias; ?>

<?php
        if ($count !== 0) {
            if ($ip !== '*') {
?>

    RequiredBinding = port_ssl_<?php echo $certname; ?>

<?php
            } else {
?>

    #RequiredCA = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.ca
    SSLcertFile = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.pem
<?php
            }
        } else {
            if ($ip !== '*') {
?>

    #RequiredCA = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.ca
    SSLcertFile = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.pem
<?php
            }
        }

        if ($wwwredirect) {
?>

    #Match ^/(.*) Redirect <?php echo $protocol; ?>www.<?php echo $domainname; ?>/$1
<?php
        }

        if ($disabled) {
            $rootpath = $disablepath;
        }
?>

    WebsiteRoot = <?php echo $rootpath; ?>

    #StartFile = <?php echo $indexorder; ?>

    StartFile = index.php

    Alias = /__kloxo:/home/<?php echo $user; ?>/kloxoscript

    #Match ^/kloxo/(.*) Redirect https://cp.<?php echo $domainname; ?>:7777/$1
    #Match ^/kloxononssl/(.*) Redirect http://cp.<?php echo $domainname; ?>:7778/$1

    #Match ^/webmail/(.*) Redirect <?php echo $protocol; ?>webmail.<?php echo $domainname; ?>/$1

    Alias = /cgi-bin:/home/<?php echo $user; ?>/<?php echo $domainname; ?>/cgi-bin
<?php
        if ($redirectionlocal) {
            foreach ($redirectionlocal as $rl) {
?>

    Alias = <?php echo $rl[0]; ?>:<?php echo $rootpath; ?><?php echo $rl[1]; ?>

<?php
            }
        }

        if ($redirectionremote) {
            foreach ($redirectionremote as $rr) {
                if ($rr[2] === 'both') {
?>

    #Match /^<?php echo $rr[0]; ?>/(.*) Redirect <?php echo $protocol; ?><?php echo $rr[1]; ?>/$1

<?php
                } else {
                    $protocol2 = ($rr[2] === 'https') ? "https://" : "http://";
?>

    #Match ^/<?php echo $rr[0]; ?>/(.*) Redirect <?php echo $protocol2; ?><?php echo $rr[1]; ?>/$1

<?php
                }
            }
        }
?>

    AccessLogfile = /home/httpd/<?php echo $domainname ?>/stats/<?php echo $domainname ?>-custom_log
    AccessLogfile = /home/httpd/<?php echo $domainname ?>/stats/<?php echo $domainname ?>-error_log
<?php
        if ($statsapp === 'awstats') {
?>

    Alias = /awstats:/home/kloxo/httpd/awstats/wwwroot/cgi-bin

    Alias = /awstatscss:/home/kloxo/httpd/awstats/wwwroot/css
    Alias = /awstatsicons:/home/kloxo/httpd/awstats/wwwroot/icon

    #Match ^/stats/(.*) Redirect <?php echo $protocol; ?><?php echo $domainname; ?>/awstats/awstats.pl
<?php
            if ($statsprotect) {
?>
    Directory {
        Path = /awstats
        PasswordFile = /home/httpd/<?php echo $domainname ?>/__dirprotect/__stats
    }
<?php
            }
        } elseif ($statsapp === 'webalizer') {
?>

    Alias = /stats:/home/httpd/<?php echo $domainname; ?>/webstats

    Directory {
        Path = /stats
        ShowIndex = yes
    }
<?php
            if ($statsprotect) {
?>

    Directory {
        Path = /stats
        PasswordFile = /home/httpd/<?php echo $domainname ?>/__dirprotect/__stats
    }
<?php
            }
        }

        if ($hiawathaextratext) {
?>

    # Extra Tags - begin
    # <?php echo $hiawathaextratext; ?>

    # Extra Tags - end
<?php
        }

        if ($disablephp) {
?>
    # AddType application/x-httpd-php-source .php
<?php
        }

        if ($dirprotect) {
            foreach ($dirprotect as $k) {
                $protectpath = $k['path'];
                $protectauthname = $k['authname'];
                $protectfile = str_replace('/', '_', $protectpath) . '_';
?>

    Directory {
        Path = /<?php echo $protectpath; ?>

        PasswordFile = /home/httpd/<?php echo $domainname; ?>/__dirprotect/<?php echo $protectfile; ?>
    }
<?php
            }
        }

        if ($blockips) {
?>
    # BanlistMask = <?php echo $blockips; ?>

    AccessList = <?php echo $blockips; ?>

<?php
        }
?>

    UserWebsites = yes
    UseFastCGI = php_for_<?php echo $user; ?>

    TimeForCGI = 3600
}

<?php
        if ($domainredirect) {
            foreach ($domainredirect as $domredir) {
                $redirdomainname = $domredir['redirdomain'];
                $redirpath = ($domredir['redirpath']) ? $domredir['redirpath'] : null;
                $webmailmap = ($domredir['mailflag'] === 'on') ? true : false;

                $randnum = rand(0, 32767);

                if ($redirpath) {
                    if ($disabled) {
                        $$redirfullpath = $disablepath;
                    } else {
                        $redirfullpath = str_replace('//', '/', $rootpath . '/' . $redirpath);
                    }
?>

## web for redirect '<?php echo $redirdomainname; ?>'
VirtualHost {
    Hostname = <?php echo $redirdomainname; ?>, www.<?php echo $redirdomainname; ?>

    WebsiteRoot = <?php echo $redirfullpath; ?>

    #StartFile = <?php echo $indexorder; ?>

    StartFile = index.php
<?php
                    if ($count !== 0) {
                        if ($ip !== '*') {
?>

    RequiredBinding = port_ssl_<?php echo $certname; ?>

<?php
                        } else {
?>

    #RequiredCA = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.ca
    SSLcertFile = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.pem
<?php
                        }
                    } else {
                        if ($ip !== '*') {
?>

    RequiredBinding = port_nonssl_<?php echo $certname; ?>

<?php
                        } else {
?>

    #RequiredCA = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.ca
    SSLcertFile = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.pem
<?php
                        }
                    }
?>

    UserWebsites = yes
    UseFastCGI = php_for_<?php echo $user; ?>

    TimeForCGI = 3600
}

<?php
                } else {
?>

## web for redirect '<?php echo $redirdomainname; ?>'
VirtualHost {
    Hostname = <?php echo $redirdomainname; ?>, www.<?php echo $redirdomainname; ?>

    #Match ^/(.*) Redirect <?php echo $protocol; ?><?php echo $domainname; ?>/$1

<?php
                    if ($count !== 0) {
                        if ($ip !== '*') {
?>

    RequiredBinding = port_ssl_<?php echo $certname; ?>

<?php
                        } else {
?>

    #RequiredCA = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.ca
    SSLcertFile = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.pem
<?php
                        }
                    } else {
                        if ($ip !== '*') {
?>

    #RequiredCA = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.ca
    SSLcertFile = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.pem
<?php
                        }
                    }
?>

    UserWebsites = yes
    UseFastCGI = php_for_<?php echo $user; ?>

    TimeForCGI = 3600
}

<?php
                }
            }
        }

        if ($parkdomains) {
            foreach ($parkdomains as $dompark) {
                $parkdomainname = $dompark['parkdomain'];
                $webmailmap = ($dompark['mailflag'] === 'on') ? true : false;

                if ($disabled) {
?>

## webmail for parked '<?php echo $parkdomainname; ?>'
VirtualHost {
    Hostname = webmail.<?php echo $parkdomainname; ?>

    WebsiteRoot = <?php echo $disablepath; ?>

    #StartFile = <?php echo $indexorder; ?>

    StartFile = index.php
<?php
                    if ($count !== 0) {
?>

    #RequiredCA = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.ca
    SSLcertFile = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.pem
<?php
                    }
?>
}

<?php
                } else {
                    if ($webmailremote) {
?>

## webmail for parked '<?php echo $parkdomainname; ?>'
VirtualHost {
    Hostname = webmail.<?php echo $parkdomainname; ?>

    #Match ^/(.*) Redirect <?php echo $protocol; ?><?php echo $webmailremote; ?>/$1

<?php
                        if ($count !== 0) {
?>

    #RequiredCA = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.ca
    SSLcertFile = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.pem
<?php
                        }
?>
    UseFastCGI = php_for_apache
    TimeForCGI = 3600
}

<?php
                    } elseif ($webmailmap) {
                        if ($webmailapp) {
?>

## webmail for parked '<?php echo $parkdomainname; ?>'
VirtualHost {
    Hostname = webmail.<?php echo $parkdomainname; ?>

    WebsiteRoot = <?php echo $webmaildocroot; ?>

    #StartFile = <?php echo $indexorder; ?>

    StartFile = index.php
<?php
                            if ($count !== 0) {
?>

    #RequiredCA = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.ca
    SSLcertFile = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.pem
<?php
                            }
?>
}

<?php
                        } else {
?>

## webmail for parked '<?php echo $parkdomainname; ?>' handled by ../defaults/webmail.conf

<?php
                        }
                    } else {
?>

## No mail map for parked '<?php echo $parkdomainname; ?>'

<?php
                    }
                }
            }
        }

        if ($domainredirect) {
            foreach ($domainredirect as $domredir) {
                $redirdomainname = $domredir['redirdomain'];
                $webmailmap = ($domredir['mailflag'] === 'on') ? true : false;

                if ($disabled) {
?>

## webmail for redirect '<?php echo $redirdomainname; ?>'
VirtualHost {
    Hostname = webmail.<?php echo $redirdomainname; ?>

    WebsiteRoot = <?php echo $disablepath; ?>

    #StartFile = <?php echo $indexorder; ?>

    StartFile = index.php
<?php
                    if ($count !== 0) {
?>

    #RequiredCA = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.ca
    SSLcertFile = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.pem
<?php
                    }
?>
    UseFastCGI = php_for_apache
    TimeForCGI = 3600
}

<?php
                } else {
                    if ($webmailremote) {
?>

## webmail for redirect '<?php echo $redirdomainname; ?>'
VirtualHost {
    Hostname = webmail.<?php echo $redirdomainname; ?>

    #Match ^/(.*) Redirect <?php echo $protocol; ?><?php echo $webmailremote; ?>/$1

<?php
                        if ($count !== 0) {
?>

    #RequiredCA = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.ca
    SSLcertFile = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.pem
<?php
                        }
?>
    UseFastCGI = php_for_apache
    TimeForCGI = 3600
}

<?php
                    } elseif ($webmailmap) {
                        if ($webmailapp) {
?>

## webmail for redirect '<?php echo $redirdomainname; ?>'
VirtualHost {
    Hostname = webmail.<?php echo $redirdomainname; ?>

    WebsiteRoot = <?php echo $webmaildocroot; ?>

    #StartFile = <?php echo $indexorder; ?>

    StartFile = index.php
<?php
                            if ($count !== 0) {
?>

    #RequiredCA = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.ca
    SSLcertFile = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.pem
<?php
                            }
?>
    UseFastCGI = php_for_apache
    TimeForCGI = 3600
}

<?php
                        } else {
?>

## webmail for redirect '<?php echo $redirdomainname; ?>' handled by ../defaults/webmail.conf

<?php
                        }
                    } else {
?>

## No mail map for redirect '<?php echo $redirdomainname; ?>'

<?php
                    }
                }
            }
        }

        $count++;
    }
}
?>

### end - web of '<?php echo $domainname; ?>' - do not remove/modify this line
