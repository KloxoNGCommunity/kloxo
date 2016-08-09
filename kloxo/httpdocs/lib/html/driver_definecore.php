<?php 
$gl_class_array['dns__bind'] =  "driver/dns/dns__bindlib.php";
$gl_class_array['dns__djbdns'] =  "driver/dns/dns__djbdnslib.php";
$gl_class_array['dns__pdns'] =  "driver/dns/dns__pdnslib.php";
//--// $gl_class_array['dns__maradns'] =  "driver/dns/dns__maradnslib.php";
$gl_class_array['dns__nsd'] =  "driver/dns/dns__nsdlib.php";
$gl_class_array['dns__none'] =  "driver/dns/dns__nonelib.php";
//--// $gl_class_array['dns__msdns'] =  "driver/dns/dns__msdnslib.php";
$gl_class_array['dns__mydns'] =  "driver/dns/dns__mydnslib.php";
$gl_class_array['dns__yadifa'] =  "driver/dns/dns__yadifalib.php";

$gl_class_array['anonftpipaddress__pureftp'] =  "driver/ftp/anonftpipaddress__pureftplib.php";
$gl_class_array['service__linux'] =  "driver/pserver/service__linuxlib.php";
$gl_class_array['service__redhat'] =  "driver/pserver/service__redhatlib.php";
$gl_class_array['service__debian'] =  "driver/pserver/service__debianlib.php";
//--// $gl_class_array['service__windows'] =  "driver/pserver/service__windowslib.php";
$gl_class_array['package__yum'] = "driver/pserver/package__yumlib.php";
$gl_class_array['package__up2date'] = "driver/pserver/package__up2datelib.php";
//$gl_class_array['package__lxupdate'] = "driver/pserver/package__lxupdate.php";
$gl_class_array['process__linux'] = "driver/pserver/process__linuxlib.php";
//--// $gl_class_array['process__windows'] = "driver/pserver/process__windowslib.php";
//--// $gl_class_array['odbc__windows'] = "driver/pserver/odbc__windowslib.php";
//$gl_class_array['odbc__linux'] = "driver/pserver/odbc__linuxlib.php";
$gl_class_array['component__rpm'] = "driver/pserver/component__rpmlib.php";
//--// $gl_class_array['component__windows'] = "driver/pserver/component__windowslib.php";
$gl_class_array['uuser__linux'] =  "driver/pserver/uuser__linuxlib.php";
//--// $gl_class_array['uuser__windows'] =  "driver/pserver/uuser__windowslib.php";
$gl_class_array['dirlocation__linux'] =  "driver/pserver/dirlocation__linuxlib.php";

$gl_class_array['ipaddress__redhat'] =  "driver/pserver/ipaddress__redhatlib.php";
//--// $gl_class_array['ipaddress__windows'] =  "driver/pserver/ipaddress__windowslib.php";
$gl_class_array['ipaddress__debian'] =  "driver/pserver/ipaddress__debianlib.php";
$gl_class_array['diskusage__linux'] =  "driver/pserver/diskusage__linuxlib.php";
//--// $gl_class_array['diskusage__windows'] =  "driver/pserver/diskusage__windowslib.php";

$gl_class_array['dbadmin__sync'] =  "driver/pserver/dbadmin__synclib.php";
$gl_class_array['mysqldb__mysql'] =  "driver/pserver/mysqldb__mysqllib.php";
$gl_class_array['mysqldbuser__mysql'] =  "driver/pserver/mysqldbuser__mysqllib.php";
//--// $gl_class_array['mssqldb__mssql'] =  "driver/pserver/mssqldb__mssqllib.php";
//--// $gl_class_array['mssqldbuser__mssql'] =  "driver/pserver/mssqldbuser__mssqllib.php";

$gl_class_array['reversedns__bind'] =  "driver/pserver/reversedns__bindlib.php";

$gl_class_array["mimetype__apache"] = "driver/web/mimetype__apachelib.php";
$gl_class_array["mimetype__lighttpd"] = "driver/web/mimetype__lighttpdlib.php";

$gl_class_array['cron__linux'] = "driver/pserver/cron__linuxlib.php";
//--// $gl_class_array['cron__windows'] = "driver/pserver/cron__windowslib.php";
$gl_class_array['ddatabase__sync'] = "driver/pserver/ddatabase__synclib.php";
$gl_class_array['hostdeny__linux'] = "driver/pserver/hostdeny__linuxlib.php";
//--// $gl_class_array['hostdeny__windows'] = "driver/pserver/hostdeny__windowslib.php";
//--// $gl_class_array["aspnet__windows"] = "driver/pserver/aspnet__windowslib.php";
$gl_class_array['llog__linux'] = "driver/pserver/llog__linuxlib.php";
//$gl_class_array['llog__windows'] = "driver/pserver/llog__windowslib.php";
$gl_class_array['sslcert__sync'] = "driver/pserver/sslcert__synclib.php";
$gl_class_array['sslipaddress__sync'] = "driver/pserver/sslipaddress__synclib.php";
$gl_class_array['servermail__qmail'] =  "driver/mmail/servermail__qmaillib.php";
$gl_class_array['serverftp__pureftp'] =  "driver/ftp/serverftp__pureftplib.php";
//--// $gl_class_array['servermail__mailenable'] =  "driver/msil/servermail__mailenablelib.php";
$gl_class_array['pserver__linux'] =  "driver/pserver/pserver__linuxlib.php";
//--// $gl_class_array['pserver__windows'] =  "driver/pserver/pserver__windowslib.php";
$gl_class_array['ffile__linux'] =  "driver/pserver/ffile__linuxlib.php";
//--// $gl_class_array['ffile__windows'] =  "driver/pserver/ffile__windowslib.php";
$gl_class_array['ftpuser__pureftp'] = "driver/ftp/ftpuser__pureftplib.php";
$gl_class_array['ftpusertraffic__pureftp'] = "driver/ftp/ftpusertraffic__pureftplib.php";
//--// $gl_class_array['ftpuser__iisftp'] = "driver/ftp/ftpuser__iisftplib.php";
$gl_class_array['ftpsession__pureftp'] = "driver/ftp/ftpsession__pureftplib.php";
$gl_class_array['dhcp__dhcpd'] = "driver/pserver/dhcp__dhcpdlib.php";
$gl_class_array['watchdog__sync'] = "driver/pserver/watchdog__synclib.php";
$gl_class_array['lxguard__sync'] = "driver/pserver/lxguard__synclib.php";
$gl_class_array['lxguardwhitelist__sync'] = "driver/pserver/lxguardwhitelist__synclib.php";
$gl_class_array['sshconfig__linux'] = "driver/pserver/sshconfig__linuxlib.php";

$gl_class_array['phpmodule__linux'] = "driver/pserver/phpmodule__linuxlib.php";
