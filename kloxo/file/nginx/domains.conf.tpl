### begin content - please not remove this line

<?php

$port = '80';
$portssl = '443';

if (!$ipssllist) {
    $ipssllist = $iplist;
}

$statsapp = $stats['app'];
$statsprotect = ($stats['protect']) ? true : false;

$serveralias = "{$domainname} www.{$domainname}";

$excludedomains = array(
        "cp",
        "disable",
        "default",
        "webmail"
);

$excludealias = implode("|", $excludedomains);

if ($wildcards) {
    $serveralias .= "\n        *.{$domainname}";
}

if ($serveraliases) {
    foreach ($serveraliases as &$sa) {
        $serveralias .= "\n        {$sa}";
    }
}

if ($parkdomains) {
    foreach ($parkdomains as $pk) {
        $pa = $pk['parkdomain'];
        $serveralias .= "\n        {$pa} www.{$pa}";
    }
}

if ($webmailapp) {
    $webmaildocroot = "/home/kloxo/httpd/webmail/{$webmailapp}";
} else {
    $webmaildocroot = "/home/kloxo/httpd/webmail";
}

if ($indexorder) {
    $indexorder = implode(' ', $indexorder);
}

if ($blockips) {
    $blockips = str_replace(' ', ', ', $blockips);
}

$userinfo = posix_getpwnam($user);
$fpmport = (50000 + $userinfo['uid']);

$disablepath = "/home/kloxo/httpd/disable";

$globalspath = "/home/nginx/conf/globals";

?>

## web for '<?php echo $domainname; ?>'
server {
<?php
/*
    foreach ($ipssllist as &$ipssl) {
?>
    listen <?php echo $ipssl ?>:<?php echo $port ?>;
    listen <?php echo $ipssl ?>:<?php echo $portssl ?>;

<?php
    }
*/
?>
    listen *:<?php echo $port ?>;
    listen *:<?php echo $portssl ?>;

    server_name <?php echo $serveralias; ?>;

    index <?php echo $indexorder; ?>;

    set $domain '<?php echo $domainname; ?>';
<?php
    if ($wwwredirect) {
?>

    if ($host != 'www.<?php echo $domainname; ?>') {
        rewrite ^/(.*) 'http://www.<?php echo $domainname; ?>/$1' permanent;
    }
<?php
    }

    if ($disabled) {
?>

    set $rootdir '<?php echo $disablepath; ?>';
<?php
    } else {
        if ($wildcards) {
?>

    set $rootdir '<?php echo $rootpath; ?>';
<?php
            foreach ($excludedomains as &$ed) {
?>

    if ($host ~* ^(<?php echo $ed; ?>.<?php echo $domainname; ?>)$) {
<?php
                if ($ed !== 'webmail') {
?>
        set $rootdir '/home/kloxo/httpd/<?php echo $ed; ?>';
<?php
                } else {
                    if($webmailremote) {
?>
        rewrite ^/(.*) 'http://<?php echo $webmailremote; ?>/$1' permanent;
<?php
                    } else {
?>
        set $rootdir '<?php echo $webmaildocroot; ?>';
<?php
                    }
                }
?>
    }
<?php
            }
        } else {
?>

    set $rootdir '<?php echo $rootpath; ?>';
<?php
        }
    }
?>

    root $rootdir;

    set $user '<?php echo $user; ?>';

    include '<?php echo $globalspath; ?>/generic.conf';
<?php
    if (!$reverseproxy) {
?>

    access_log /home/httpd/<?php echo $domainname; ?>/stats/<?php echo $domainname; ?>-custom_log main;
    error_log  /home/httpd/<?php echo $domainname; ?>/stats/<?php echo $domainname; ?>-error_log;
<?php
        if ($statsapp === 'awstats') {
?>

    set $statstype 'awstats';

    include '<?php echo $globalspath; ?>/awstats.conf';
<?php
            if ($statsprotect) {
?>

    set $protectpath     'awstats';
    set $protectauthname 'Awstats';
    set $protectfile     '__stats';

    include '<?php echo $globalspath; ?>/dirprotect.conf';
<?php
            }
        } elseif ($statsapp === 'webalizer') {
?>

    set $statstype 'stats';

    include '<?php echo $globalspath; ?>/webalizer.conf';
<?php
            if ($statsprotect) {
?>

    set $protectpath     'stats';
    set $protectauthname 'stats';
    set $protectfile     '__stats';

    include '<?php echo $globalspath; ?>/dirprotect.conf';
<?php
            }
        }
    }

    if ($nginxextratext) {
?>

    # Extra Tags - begin
<?php echo $nginxextratext; ?>

    # Extra Tags - end
<?php
    }

    if (!$disablephp) {
        if ($reverseproxy) {
?>

    include '<?php echo $globalspath; ?>/proxy.conf';
<?php
        } else {
            if ($wildcards) {
?>

#    if ($host !~* ^((<?php echo $excludealias; ?>).<?php echo $domainname; ?>)$) {
        set $fpmport '<?php echo $fpmport; ?>';
#    }

    if ($host ~* ^((<?php echo $excludealias; ?>).<?php echo $domainname; ?>)$) {
        set $fpmport '50000';
    }
<?php
            } else {
?>

    set $fpmport '<?php echo $fpmport; ?>';
<?php
            }
?>

    include '<?php echo $globalspath; ?>/php-fpm.conf';

    include '<?php echo $globalspath; ?>/perl.conf';
<?php
        }
    }

    if ($dirprotect) {
        foreach ($dirprotect as $k) {
            $protectpath = $k['path'];
            $protectauthname = $k['authname'];
            $protectfile = str_replace('/', '_', $protectpath) . '_';
?>

    location /<?php echo $protectpath; ?>/(.*)$ {
        satisfy any;
        auth_basic '<?php echo $protectauthname; ?>';
        auth_basic_user_file '/home/httpd/<?php echo $domainname; ?>/__dirprotect/<?php echo $protectfile; ?>';
    }
<?php
        }
    }

    if ($blockips) {
?>

    location ^~ /(.*) {
        deny   <?php echo $blockips; ?>;
        allow  all;
    }
<?php
    }
?>
}

<?php
if (!$wildcards) {
    if ($disabled) {
?>

## webmail for '<?php echo $domainname; ?>'
server {
<?php
/*
        foreach ($iplist as &$ip) {
?>
    listen <?php echo $ip ?>:<?php echo $port ?>;
    listen <?php echo $ip ?>:<?php echo $portssl ?>;

<?php
        }
*/
?>
    listen *:<?php echo $port ?>;
    listen *:<?php echo $portssl ?>;

    server_name webmail.<?php echo $domainname; ?>;

    index <?php echo $indexorder; ?>;

    set $rootdir '<?php echo $disablepath; ?>';

    root $rootdir;

<?php
        if ($reverseproxy) {
?>

    include '<?php echo $globalspath; ?>/proxy.conf';
<?php
        } else {
?>

    set $fpmport '50000';

    include '<?php echo $globalspath; ?>/php-fpm.conf';
<?php
        }
?>
}

<?php
    } else {
        if ($webmailremote) {
?>

## webmail for '<?php echo $domainname; ?>'
server {
<?php
/*
            foreach ($iplist as &$ip) {
?>
    listen <?php echo $ip ?>:<?php echo $port ?>;
    listen <?php echo $ip ?>:<?php echo $portssl ?>;

<?php
            }
*/
?>
    listen *:<?php echo $port ?>;
    listen *:<?php echo $portssl ?>;

    server_name webmail.<?php echo $domainname; ?>;

    if ($host != '<?php echo $webmailremote; ?>') {
        rewrite ^/(.*) 'http://<?php echo $webmailremote; ?>/$1' permanent;
    }
}
<?php
        } elseif ($webmailapp) {
?>

## webmail for '<?php echo $domainname; ?>'
server {
<?php
/*
            foreach ($iplist as &$ip) {
?>
    listen <?php echo $ip ?>:<?php echo $port ?>;
    listen <?php echo $ip ?>:<?php echo $portssl ?>;

<?php
            }
*/
?>
    listen *:<?php echo $port ?>;
    listen *:<?php echo $portssl ?>;

    server_name webmail.<?php echo $domainname; ?>;

    index <?php echo $indexorder; ?>;

    set $rootdir '<?php echo $webmaildocroot; ?>';

    root $rootdir;
<?php
            if ($reverseproxy) {
?>

    include '<?php echo $globalspath; ?>/proxy.conf';
<?php
            } else {
?>

    set $fpmport '50000';

    include '<?php echo $globalspath; ?>/php-fpm.conf';
<?php
            }
?>
}

<?php
        } else {
?>

## webmail for '<?php echo $domainname; ?>' handled by ../webmails/webmail.conf

<?php
        }
    }
}

    if ($domainredirect) {
        foreach ($domainredirect as $domredir) {
            $redirdomainname = $domredir['redirdomain'];
            $redirpath = ($domredir['redirpath']) ? $domredir['redirpath'] : null;
            $webmailmap = ($domredir['mailflag'] === 'on') ? true : false;

            if ($redirpath) {
                $redirfullpath = str_replace('//', '/', $rootpath . '/' . $redirpath);
?>

## web for redirect '<?php echo $redirdomainname; ?>'
server {
<?php
/*
                foreach ($iplist as &$ip) {
?>
    listen <?php echo $ip ?>:<?php echo $port ?>;
    listen <?php echo $ip ?>:<?php echo $portssl ?>;

<?php
                }
*/
?>
    listen *:<?php echo $port ?>;
    listen *:<?php echo $portssl ?>;

    server_name '<?php echo $redirdomainname; ?>';

    index <?php echo $indexorder; ?>;

    set $rootdir '<?php echo $redirfullpath; ?>';

    root $rootdir;
<?php
                if ($reverseproxy) {
?>

    include '<?php echo $globalspath; ?>/proxy.conf';
<?php
                } else {
?>

    set $fpmport <?php echo $fpmport; ?>';

    include '<?php echo $globalspath; ?>/php-fpm.conf';

    include '<?php echo $globalspath; ?>/perl.conf';
<?php
                }
?>
}
<?php
            } else {
?>

## web for redirect '<?php echo $redirdomainname; ?>'
server {
<?php
/*
                foreach ($iplist as &$ip) {
?>
    listen <?php echo $ip ?>:<?php echo $port ?>;
    listen <?php echo $ip ?>:<?php echo $portssl ?>;

<?php
                }
*/
?>
    listen *:<?php echo $port ?>;
    listen *:<?php echo $portssl ?>;

    server_name '<?php echo $redirdomainname; ?>';

    if ($host != '<?php echo $domainname; ?>') {
         rewrite ^/(.*) 'http://<?php echo $domainname; ?>/$1';
    }
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
server {
<?php
/*
                foreach ($iplist as &$ip) {
?>
    listen <?php echo $ip ?>:<?php echo $port ?>;
    listen <?php echo $ip ?>:<?php echo $portssl ?>;

<?php
                }
*/
?>
    listen *:<?php echo $port ?>;
    listen *:<?php echo $portssl ?>;

    server_name 'webmail.<?php echo $parkdomainname; ?>';

    index <?php echo $indexorder; ?>;

    set $rootdir '<?php echo $disablepath; ?>';

    root $rootdir;
<?php
                if ($reverseproxy) {
?>

    include '<?php echo $globalspath; ?>/proxy.conf';
<?php
                } else {
?>

    set $fpmport '50000';

    include '<?php echo $globalspath; ?>/php-fpm.conf';
<?php
                }
?>
}

<?php
            } else {
                if ($webmailremote) {
?>

## webmail for parked '<?php echo $parkdomainname; ?>'
server {
<?php
/*
                    foreach ($iplist as &$ip) {
?>
    listen <?php echo $ip ?>:<?php echo $port ?>;
    listen <?php echo $ip ?>:<?php echo $portssl ?>;

<?php
                    }
*/
?>
    server_name 'webmail.<?php echo $parkdomainname; ?>';

    if ($host != '<?php echo $webmailremote; ?>') {
        rewrite ^/(.*) 'http://<?php echo $webmailremote; ?>/$1';
    }
}

<?php

                } elseif ($webmailmap) {
                    if ($webmailapp) {
?>

## webmail for parked '<?php echo $parkdomainname; ?>'
server {
<?php
/*
                        foreach ($iplist as &$ip) {
?>
    listen <?php echo $ip ?>:<?php echo $port ?>;
    listen <?php echo $ip ?>:<?php echo $portssl ?>;

<?php
                        }
*/
?>
    listen *:<?php echo $port ?>;
    listen *:<?php echo $portssl ?>;

    server_name 'webmail.<?php echo $parkdomainname; ?>';

    index <?php echo $indexorder; ?>;

    set $rootdir '<?php echo $webmaildocroot; ?>';

    root $rootdir;
<?php
                        if ($reverseproxy) {
?>

    include '<?php echo $globalspath; ?>/proxy.conf';
<?php
                        } else {
?>

    set $fpmport '50000';

    include '<?php echo $globalspath; ?>/php-fpm.conf';
<?php
                        }
?>
}

<?php
                    } else {
?>

## webmail for parked '<?php echo $parkdomainname; ?>' handled by ../webmails/webmail.conf

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
server {
<?php
/*
                foreach ($iplist as &$ip) {
?>
    listen <?php echo $ip ?>:<?php echo $port ?>;
    listen <?php echo $ip ?>:<?php echo $portssl ?>;

<?php
                }
*/
?>
    listen *:<?php echo $port ?>;
    listen *:<?php echo $portssl ?>;

    server_name 'webmail.<?php echo $redirdomainname; ?>';

    index <?php echo $indexorder; ?>;

    set $rootdir '<?php echo $disablepath; ?>';

    root $rootdir;
<?php
                if ($reverseproxy) {
?>

    include '<?php echo $globalspath; ?>/proxy.conf';
<?php
                } else {
?>

    set $fpmport '50000';

    include '<?php echo $globalspath; ?>/php-fpm.conf';
<?php
                }
?>
}

<?php
            } else {
                if ($webmailremote) {
?>

## webmail for redirect '<?php echo $redirdomainname; ?>'
server {
<?php
/*
                    foreach ($iplist as &$ip) {
?>
    listen <?php echo $ip ?>:<?php echo $port ?>;
    listen <?php echo $ip ?>:<?php echo $portssl ?>;

<?php
                    }
*/
?>
    listen *:<?php echo $port ?>;
    listen *:<?php echo $portssl ?>;

    server_name 'webmail.<?php echo $redirdomainname; ?>';

    if ($host != '<?php echo $webmailremote; ?>') {
        rewrite ^/(.*) 'http://<?php echo $webmailremote; ?>/$1';
    }
}

<?php
               } elseif ($webmailmap) {
                    if ($webmailapp) {
?>

## webmail for redirect '<?php echo $redirdomainname; ?>'
server {
<?php
/*
                        foreach ($iplist as &$ip) {
?>
    listen <?php echo $ip ?>:<?php echo $port ?>;
    listen <?php echo $ip ?>:<?php echo $portssl ?>;

<?php
                        }
*/
?>
    listen *:<?php echo $port ?>;
    listen *:<?php echo $portssl ?>;

    server_name 'webmail.<?php echo $redirdomainname; ?>';

    index <?php echo $indexorder; ?>;

    set $rootdir '<?php echo $webmaildocroot; ?>';

    root $rootdir;
<?php
                        if ($reverseproxy) {
?>

    include '<?php echo $globalspath; ?>/proxy.conf';
<?php
                        } else {
?>

    set $fpmport '50000';

    include '<?php echo $globalspath; ?>/php-fpm.conf';
<?php
                        }
?>
}

<?php
                    } else {
?>

## webmail for redirect '<?php echo $redirdomainname; ?>' handled by ../webmails/webmail.conf

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
?>

### end content - please not remove this line
