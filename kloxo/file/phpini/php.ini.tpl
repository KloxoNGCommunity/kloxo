<?php
    exec("php -r 'echo phpversion();'", $out, $ret);

    $phpver = $out[0];

    if (compare_version($phpver, "5.4.0", ">=")) {
        $php54mark = '';
    } else {
        $php54mark = ';';
    }

    if (compare_version($phpver, "5.3.0", ">=")) {
        $php53mark = '';
    } else {
        $php53mark = ';';
    }

    if ($sendmail_from) {
        $sendmailmark = '';
    } else {
        $sendmailmark = ';';
    }
?>

[PHP]
;### MR -- generic (the same on all php 5.x version)
engine = On
short_open_tag = On
asp_tags = Off
precision = 14
y2k_compliance = On
unserialize_callback_func=
allow_call_time_pass_reference = Off
safe_mode_gid = Off
safe_mode_include_dir =
safe_mode_exec_dir =
safe_mode_allowed_env_vars = PHP_
safe_mode_protected_env_vars = LD_LIBRARY_PATH
disable_classes =
expose_php = On
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
display_startup_errors = On
log_errors_max_len = 1024
ignore_repeated_errors = Off
report_memleaks = On
track_errors = Off
variables_order = "EGPCS"
gpc_order = "GPC"
include_path = ".:/usr/share/pear/"
doc_root =
user_dir =
extension_dir = /usr/lib/php/modules
upload_tmp_dir = /tmp/
default_socket_timeout = 60
date.timezone = "Europe/London"

;### MR -- specific for php 5.3+

<?php echo $php53mark; ?>auto_globals_jit = On
<?php echo $php53mark; ?>zlib.output_compression = Off
<?php echo $php53mark; ?>implicit_flush = Off
<?php echo $php53mark; ?>unserialize_callback_func =
<?php echo $php53mark; ?>serialize_precision = 17
<?php echo $php53mark; ?>zend.enable_gc = On
<?php echo $php53mark; ?>expose_php = On
<?php echo $php53mark; ?>max_execution_time = 30
<?php echo $php53mark; ?>max_input_time = 60
<?php echo $php53mark; ?>unserialize_callback_func =
<?php echo $php53mark; ?>output_buffering = 4096
<?php echo $php53mark; ?>ignore_repeated_source = Off
<?php echo $php53mark; ?>report_memleaks = On
<?php echo $php53mark; ?>track_errors = Off
<?php echo $php53mark; ?>html_errors = On
<?php echo $php53mark; ?>auto_append_file =

;### MR -- custom setting (handle by kloxo)
disable_functions = <?php echo $disable_functions; ?>

register_globals = <?php echo $register_globals; ?>

display_errors = <?php echo $display_errors; ?>

file_uploads = <?php echo $file_uploads; ?>

upload_max_filesize = <?php echo $upload_max_filesize; ?>

log_errors = <?php echo $log_errors; ?>

output_buffering = <?php echo $output_buffering; ?>

register_argc_argv = <?php echo $register_argc_argv; ?>

magic_quotes_gpc = <?php echo $magic_quotes_gpc; ?>

post_max_size = <?php echo $post_max_size; ?>

magic_quotes_runtime = <?php echo $magic_quotes_runtime; ?>

magic_quotes_sybase = <?php echo $magic_quotes_sybase; ?>

mysql.allow_persistent = <?php echo $mysql_allow_persistent; ?>

max_execution_time = <?php echo $max_execution_time; ?>

max_input_time = <?php echo $max_input_time; ?>

memory_limit = <?php echo $memory_limit; ?>

post_max_size = <?php echo $post_max_size; ?>

allow_url_fopen = <?php echo $allow_url_fopen; ?>

allow_url_include = <?php echo $allow_url_include; ?>

session.save_path = <?php echo $session_save_path; ?>

cgi.force_redirect = <?php echo $cgi_force_redirect; ?>

<?php echo $sendmailmark; ?>sendmail_from = <?php echo $sendmail_from; ?>

safe_mode = <?php echo $safe_mode; ?>

enable_dl = <?php echo $enable_dl; ?>

disable_functions = <?php echo $disable_functions; ?>

;### MR -- not exist on php 5.4
<?php echo $php54mark; ?>register_long_arrays = <?php echo $register_long_arrays; ?>


[Syslog]
define_syslog_variables = Off

[mail function]
SMTP = localhost
smtp_port = 25
sendmail_path = /usr/sbin/sendmail -t -i

[Java]

[SQL]
sql.safe_mode = Off

[ODBC]
odbc.allow_persistent = On
odbc.check_persistent = On
odbc.max_persistent = -1
odbc.max_links = -1
odbc.defaultlrl = 4096
odbc.defaultbinmode = 1 

[MySQL]
mysql.default_port =
mysql.default_socket =
mysql.default_host =
mysql.default_user =
mysql.default_password =
mysql.connect_timeout = 60
mysql.trace_mode = Off

[mSQL]
msql.max_persistent = -1
msql.max_links = -1

[PostgresSQL]
pgsql.allow_persistent = On
pgsql.auto_reset_persistent = Off
pgsql.max_persistent = -1
pgsql.max_links = -1
pgsql.ignore_notice = 0
pgsql.log_notice = 0

[Sybase]
sybase.allow_persistent = On
sybase.max_persistent = -1
sybase.max_links = -1
sybase.min_error_severity = 10
sybase.min_message_severity = 10
sybase.compatability_mode = Off

[Sybase-CT]
sybct.allow_persistent = On
sybct.max_links = -1
sybct.min_server_severity = 10
sybct.min_client_severity = 10

[dbx]
dbx.colnames_case = "lowercase"

[bcmath]
bcmath.scale = 0

[browscap]
;browscap = extra/browscap.ini

[Informix]
ifx.default_host =
ifx.default_user =
ifx.default_password =
ifx.allow_persistent = On
ifx.max_persistent = -1
ifx.max_links = -1
ifx.textasvarchar = 0
ifx.byteasvarchar = 0
ifx.charasvarchar = 0
ifx.blobinfile = 0
ifx.nullformat = 0

[Session]
session.save_handler = files
session.save_path = /var/lib/php/session
session.use_cookies = 1
session.name = PHPSESSID
session.auto_start = 0
session.cookie_lifetime = 0
session.cookie_path = /
session.cookie_domain =
session.serialize_handler = php
session.gc_probability = 1
session.gc_divisor = 1000
session.gc_maxlifetime = 1440
session.bug_compat_42 = 0
session.bug_compat_warn = 1
session.referer_check =
session.entropy_length = 0
session.entropy_file =
session.cache_limiter = nocache
session.cache_expire = 180
session.use_trans_sid = 0
url_rewriter.tags = "a=href,area=href,frame=src,input=src,form=fakeentry"

[MSSQL]
mssql.allow_persistent = On
mssql.max_persistent = -1
mssql.max_links = -1
mssql.min_error_severity = 10
mssql.min_message_severity = 10
mssql.compatability_mode = Off
mssql.secure_connection = Off

[Assertion]

[Ingres II]
ingres.allow_persistent = On
ingres.max_persistent = -1
ingres.max_links = -1
ingres.default_database =
ingres.default_user =
ingres.default_password =

[Verisign Payflow Pro]
pfpro.defaulthost = "test-payflow.verisign.com"
pfpro.defaultport = 443
pfpro.defaulttimeout = 30

[Sockets]
sockets.use_system_read = On

[com]

[Printer]

[mbstring]

[FrontBase]

[Crack]

[exif]

;### MR -- exist on php 5.3+
[CLI Server]
cli_server.color = On

[Date]

[filter]

[iconv]

[intl]

[sqlite]

[sqlite3]

[Pcre]

[Pdo]

[Pdo_mysql]
pdo_mysql.cache_size = 2000
pdo_mysql.default_socket=

[Phar]

[Interbase]
ibase.allow_persistent = 1
ibase.max_persistent = -1
ibase.max_links = -1
ibase.timestampformat = "%Y-%m-%d %H:%M:%S"
ibase.dateformat = "%Y-%m-%d"
ibase.timeformat = "%H:%M:%S"

[MySQL]
mysql.allow_local_infile = On
mysql.allow_persistent = On
mysql.cache_size = 2000
mysql.max_persistent = -1
mysql.max_links = -1
mysql.default_port =
mysql.default_socket =
mysql.default_host =
mysql.default_user =
mysql.default_password =
mysql.connect_timeout = 60
mysql.trace_mode = Off

[MySQLi]
mysqli.max_persistent = -1
mysqli.allow_persistent = On
mysqli.max_links = -1
mysqli.cache_size = 2000
mysqli.default_port = 3306
mysqli.default_socket =
mysqli.default_host =
mysqli.default_user =
mysqli.default_pw =
mysqli.reconnect = Off

[mysqlnd]
mysqlnd.collect_statistics = On
mysqlnd.collect_memory_statistics = Off

[OCI8]

[Tidy]
tidy.clean_output = Off

[soap]
soap.wsdl_cache_enabled=1
soap.wsdl_cache_dir="/tmp"
soap.wsdl_cache_ttl=86400
soap.wsdl_cache_limit = 5

[sysvshm]

[ldap]
ldap.max_links = -1

[mcrypt]

[dba]


