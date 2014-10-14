<?php
    // can use $user and $domain vars

    exec("php -r 'echo phpversion();'", $out, $ret);

    $phpver = $out[0];

    if (version_compare($phpver, "5.4.0", ">=")) {
        $php54mark = '';
        $php54disable = '#';
    } else {
        $php54mark = '#';
        $php54disable = '';
    }

    if (version_compare($phpver, "5.3.0", ">=")) {
        $php53mark = '';
    } else {
        $php53mark = '#';
    }

    if ($sendmail_from) {
        $sendmailmark = '';
    } else {
        $sendmailmark = '#';
        $sendmail_from = '';
    }

    if (!$max_input_vars_flag) {
        $max_input_vars_flag = '3000';
    }

    if (!isset($date_timezone_flag)) {
        $date_timezone_flag = 'Europe/London';
    }
?>
php_admin_value upload_max_filesize <?php echo $upload_max_filesize; ?>

php_admin_value max_execution_time <?php echo $max_execution_time_flag; ?>

php_admin_value max_input_time <?php echo $max_input_time_flag; ?>

php_admin_value memory_limit <?php echo $memory_limit_flag; ?>

php_admin_value post_max_size <?php echo $post_max_size_flag; ?>

php_flag display_errors <?php echo $display_error_flag; ?>

php_flag file_uploads <?php echo $file_uploads_flag; ?>

php_flag log_errors <?php echo $log_errors_flag; ?>

php_flag output_buffering <?php echo $output_buffering_flag; ?>

php_flag mysql.allow_persistent <?php echo $mysql_allow_persistent_flag; ?>

php_flag allow_url_fopen <?php echo $allow_url_fopen_flag; ?>

php_flag cgi.force_redirect <?php echo $cgi_force_redirect_flag; ?>

php_flag enable_dl <?php echo $enable_dl_flag; ?>

php_admin_value max_input_vars <?php echo $max_input_vars_flag; ?>

php_admin_value date.timezone <?php echo $date_timezone_flag; ?>

