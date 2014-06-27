<?php
    $maxchildren = (isset($maxchildren)) ? $maxchildren : '6';
    $maxrequests = (isset($maxrequests)) ? $maxrequests : '1000';

    $php_rc_path = (isset($php_rc_path)) ? $php_rc_path : "/etc";
    $php_scan_path = (isset($php_scan_path)) ? $php_scan_path : "/etc/php.d";

    $php_prog_type = (isset($php_prog_type)) ? $php_prog_type : 'php-cli';
    $php_prog = (isset($php_prog)) ? $php_prog : '/usr/bin/php';
?>
#!/bin/sh

php_rc='<?=$php_rc_path;?>'
php_scan='<?=$php_scan_path;?>'
php_prog='<?=$php_prog;?>'

php_max_children='<?=$maxchildren;?>'
php_max_requests='<?=$maxrequests;?>'

export PHPRC=$php_rc
export PHP_INI_SCAN_DIR=$php_scan
<?php
    if ($php_program_type === 'php-ls') {
?>
export PHP_LSAPI_CHILDREN=$php_max_children
export PHP_LSAPI_MAX_REQUESTS=$php_max_requests
export LSAPI_MAX_PROCESS_TIME=300

exec <?=$php_prog;?> -c $php_rc -b <?=$php_rc;?>/<?=$user;?>.sock \
	-d extension_dir=<?=$extension_dir_path;?> \
	-d zlib.output_compression=<?php echo $output_compression_flag; ?> \
	-d disable_functions=<?php echo $disable_functions; ?> \
	-d display_errors=<?php echo $display_error_flag; ?> \
	-d file_uploads=<?php echo $file_uploads_flag; ?> \
	-d upload_max_filesize=<?php echo $upload_max_filesize; ?> \
	-d log_errors=<?php echo $log_errors_flag; ?> \
	-d output_buffering=<?php echo $output_buffering_flag; ?> \
	-d register_argc_argv=<?php echo $register_argc_argv_flag; ?> \
	-d mysql.allow_persistent=<?php echo $mysql_allow_persistent_flag; ?> \
	-d max_execution_time=<?php echo $max_execution_time_flag; ?> \
	-d max_input_time=<?php echo $max_input_time_flag; ?> \
	-d memory_limit=<?php echo $memory_limit_flag; ?> \
	-d post_max_size=<?php echo $post_max_size_flag; ?> \
	-d allow_url_fopen=<?php echo $allow_url_fopen_flag; ?> \
	-d allow_url_include=<?php echo $allow_url_include_flag; ?> \
	-d session.save_path=<?php echo $session_save_path_flag; ?> \
	-d cgi.force_redirect=<?php echo $cgi_force_redirect_flag; ?> \
	-d enable_dl=<?php echo $enable_dl_flag; ?> \
	-d open_basedir=<?php echo $openbasedir; ?> \
	-d max_input_vars=<?php echo $max_input_vars_flag; ?> \
	$*
<?php
    } else {
        if ($php_program_type === 'php-cgi') {
?>
export PHP_FCGI_CHILDREN=$php_max_children
export PHP_FCGI_MAX_REQUESTS=$php_max_requests
<?php
        }
?>
exec $php_prog -c $php_rc \
	-d extension_dir=<?=$extension_dir_path;?> \
	-d zlib.output_compression=<?php echo $output_compression_flag; ?> \
	-d disable_functions=<?php echo $disable_functions; ?> \
	-d display_errors=<?php echo $display_error_flag; ?> \
	-d file_uploads=<?php echo $file_uploads_flag; ?> \
	-d upload_max_filesize=<?php echo $upload_max_filesize; ?> \
	-d log_errors=<?php echo $log_errors_flag; ?> \
	-d output_buffering=<?php echo $output_buffering_flag; ?> \
	-d register_argc_argv=<?php echo $register_argc_argv_flag; ?> \
	-d mysql.allow_persistent=<?php echo $mysql_allow_persistent_flag; ?> \
	-d max_execution_time=<?php echo $max_execution_time_flag; ?> \
	-d max_input_time=<?php echo $max_input_time_flag; ?> \
	-d memory_limit=<?php echo $memory_limit_flag; ?> \
	-d post_max_size=<?php echo $post_max_size_flag; ?> \
	-d allow_url_fopen=<?php echo $allow_url_fopen_flag; ?> \
	-d allow_url_include=<?php echo $allow_url_include_flag; ?> \
	-d session.save_path=<?php echo $session_save_path_flag; ?> \
	-d cgi.force_redirect=<?php echo $cgi_force_redirect_flag; ?> \
	-d enable_dl=<?php echo $enable_dl_flag; ?> \
	-d open_basedir=<?php echo $openbasedir; ?> \
	-d max_input_vars=<?php echo $max_input_vars_flag; ?> \
	$*
<?php
     }
?>