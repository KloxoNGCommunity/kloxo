<?php

include_once "lib/html/driver_definecore.php";

$gl_class_array['autoresponder__qmail'] = "driver/mmail/autoresponder__qmaillib.php";
//--// $gl_class_array['autoresponder__mailenable'] = "driver/mmail/autoresponder__mailenablelib.php";

//--// $gl_class_array['domaintraffichistory__apache'] = "driver/web/domaintraffichistory__apachelib.php";

$gl_class_array['mailinglist__mailman'] = "driver/mmail/mailinglist__mailmanlib.php";
$gl_class_array['listsubscribe__mailman'] = "driver/mmail/listsubscribe__mailmanlib.php";

$gl_class_array['web__apache'] = "driver/web/web__apachelib.php";
$gl_class_array['web__lighttpd'] = "driver/web/web__lighttpdlib.php";
$gl_class_array['web__nginx'] = "driver/web/web__nginxlib.php";
$gl_class_array['web__hiawatha'] = "driver/web/web__hiawathalib.php";
$gl_class_array['web__openlitespeed'] = "driver/web/web__openlitespeedlib.php";
$gl_class_array['web__gwan'] = "driver/web/web__gwanlib.php";

$gl_class_array['web__lighttpdproxy'] = "driver/web/web__lighttpdproxylib.php";
$gl_class_array['web__nginxproxy'] = "driver/web/web__nginxproxylib.php";
$gl_class_array['web__hiawathaproxy'] = "driver/web/web__hiawathaproxylib.php";
$gl_class_array['web__openlitespeedproxy'] = "driver/web/web__openlitespeedproxylib.php";

$gl_class_array['webcache__varnish'] =  "driver/web/webcache__varnishlib.php";
$gl_class_array['webcache__trafficserver'] =  "driver/web/webcache__trafficserverlib.php";
$gl_class_array['webcache__none'] =  "driver/web/webcache__nonelib.php";
$gl_class_array['webcache__squid'] =  "driver/web/webcache__squidlib.php";

$gl_class_array['webtraffic__apache'] = "driver/web/webtraffic__apachelib.php";
$gl_class_array['webtraffic__lighttpd'] = "driver/web/webtraffic__lighttpdlib.php";
$gl_class_array['webtraffic__nginx'] = "driver/web/webtraffic__nginxlib.php";

$gl_class_array['mailtraffic__qmail'] = "driver/mmail/mailtraffic__qmaillib.php";

$gl_class_array['installappsnapshot__sync'] = "driver/web/installappsnapshot__synclib.php";
$gl_class_array['davuser__lighttpd'] = "driver/web/davuser__lighttpdlib.php";

$gl_class_array['dirprotect__apache'] = "driver/web/dirprotect__apachelib.php";
$gl_class_array['dirprotect__lighttpd'] = "driver/web/dirprotect__lighttpdlib.php";
//--// $gl_class_array['dirprotect__iis'] = "driver/web/dirprotect__iislib.php";
$gl_class_array['dirprotect__nginx'] = "driver/web/dirprotect__nginxlib.php";
$gl_class_array['dirprotect__lighttpdproxy'] = "driver/web/dirprotect__lighttpdproxylib.php";
$gl_class_array['dirprotect__nginxproxy'] = "driver/web/dirprotect__nginxproxylib.php";
$gl_class_array['dirprotect__openlitespeed'] = "driver/web/dirprotect__openlitespeedlib.php";
$gl_class_array['dirprotect__openlitespeedproxy'] = "driver/web/dirprotect__openlitespeedproxylib.php";
$gl_class_array['dirprotect__gwan'] = "driver/web/dirprotect__gwanlib.php";

$gl_class_array['mmail__qmail'] = "driver/mmail/mmail__qmaillib.php";
//--// $gl_class_array['mmail__mailenable'] = "driver/mmail/mmail__mailenablelib.php";

$gl_class_array['webmimetype__apache'] = "driver/web/mimehandler__apachelib.php";
$gl_class_array['webhandler__apache'] = "driver/web/mimehandler__apachelib.php";

$gl_class_array['mailaccount__qmail'] = "driver/mmail/mailaccount__qmaillib.php";
$gl_class_array['mailforward__qmail'] = "driver/mmail/mailforward__qmaillib.php";

$gl_class_array['installapp__linux'] = "driver/web/installapp__linuxlib.php";
$gl_class_array['allinstallapp__linux'] = "driver/web/allinstallapp__linuxlib.php";
//--// $gl_class_array['autoresponder__mailenable'] = "driver/mmail/autoresponder__mailenable.php";
//--// $gl_class_array['mailinglist__mailenable'] = "driver/mmail/mailinglist__mailenablelib.php";
//--// $gl_class_array['mailaccount__mailenable'] = "driver/mmail/mailaccount__mailenablelib.php";
$gl_class_array['mailinglist__ezmlm'] = "driver/mmail/mailinglist__ezmlmlib.php";
$gl_class_array['listsubscribe__ezmlm'] = "driver/mmail/listsubscribe__ezmlmlib.php";
//--// $gl_class_array['listsubscribe__mailenable'] = "driver/mmail/listsubscribe__mailenablelib.php";
$gl_class_array['spam__spamassassin'] = "driver/mmail/spam__spamassassinlib.php";
$gl_class_array['spam__bogofilter'] = "driver/mmail/spam__bogofilterlib.php";
$gl_class_array['mailcontent__qmail'] = "driver/mmail/mailcontent__qmaillib.php";
$gl_class_array['mailqueue__qmail'] = "driver/pserver/mailqueue__qmaillib.php";

$gl_class_array['serverweb__apache'] = "driver/web/serverweb__apachelib.php";
$gl_class_array['serverweb__lighttpd'] = "driver/web/serverweb__lighttpdlib.php";
$gl_class_array['serverweb__nginx'] = "driver/web/serverweb__nginxlib.php";
$gl_class_array['serverweb__hiawatha'] = "driver/web/serverweb__hiawathalib.php";
$gl_class_array['serverweb__openlitespeed'] = "driver/web/serverweb__openlitespeedlib.php";
$gl_class_array['serverweb__gwan'] = "driver/web/serverweb__gwanlib.php";

$gl_class_array['serverweb__lighttpdproxy'] = "driver/web/serverweb__lighttpdproxylib.php";
$gl_class_array['serverweb__nginxproxy'] = "driver/web/serverweb__nginxproxylib.php";
$gl_class_array['serverweb__hiawathaproxy'] = "driver/web/serverweb__hiawathaproxylib.php";
$gl_class_array['serverweb__openlitespeedproxy'] = "driver/web/serverweb__openlitespeedproxylib.php";

$gl_class_array['rubyrails__linux'] = "driver/web/rubyrails__linuxlib.php";

