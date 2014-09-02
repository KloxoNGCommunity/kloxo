### begin content - please not remove this line

<?php
    if (!$phpdesc) {
        $phpdesc = '5.3.0';
    }

    if (version_compare($phpdesc, "5.3.0", ">=")) {
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
?>
### MR -- attention
### 1. Move '#<Ifmodule !mod_php5.c>' until '#</Ifmodule>' on
###    above '###Start Kloxo PHP config Area'
### 2. Remove # in front of '#<Ifmodule !mod_php5.c>' and '#</Ifmodule>'
###    on point (1)
### 3. Remove # in front of 'AddHandler x-httpd-php52' to activate secondary-php
###    on point (1)
### 4. Or Remove # in front of 'AddHandler x-httpd-php' to activate primary-php
###    on point (1) if select suphp_worker/_event for primary-php

#<Ifmodule !mod_php5.c>
    #AddHandler x-httpd-php55 .php
    #AddHandler x-httpd-php54 .php
    #AddHandler x-httpd-php53 .php
    #AddHandler x-httpd-php52 .php
    #AddHandler x-httpd-php .php
#</Ifmodule>

<Ifmodule mod_php5.c>
    php_value upload_max_filesize <?php echo $upload_max_filesize; ?>

    php_value max_execution_time <?php echo $max_execution_time_flag; ?>

    php_value max_input_time <?php echo $max_input_time_flag; ?>

    php_value memory_limit <?php echo $memory_limit_flag; ?>

    php_value post_max_size <?php echo $post_max_size_flag; ?>

    php_flag register_globals <?php echo $register_global_flag; ?>

    php_flag display_errors <?php echo $display_error_flag; ?>

    php_flag file_uploads <?php echo $file_uploads_flag; ?>

    php_flag log_errors <?php echo $log_errors_flag; ?>

    php_flag output_buffering <?php echo $output_buffering_flag; ?>

    php_flag register_argc_argv <?php echo $register_argc_argv_flag; ?>

    php_flag magic_quotes_gpc <?php echo $magic_quotes_gpc_flag; ?>

    php_flag magic_quotes_runtime <?php echo $magic_quotes_runtime_flag; ?>

    php_flag magic_quotes_sybase <?php echo $magic_quotes_sybase_flag; ?>

    php_flag mysql.allow_persistent <?php echo $mysql_allow_persistent_flag; ?>

    php_flag allow_url_fopen <?php echo $allow_url_fopen_flag; ?>

    php_flag cgi.force_redirect <?php echo $cgi_force_redirect_flag; ?>

    php_flag enable_dl <?php echo $enable_dl_flag; ?>

    php_flag max_input_vars <?php echo $max_input_vars_flag; ?>

</Ifmodule>

### end content - please not remove this line
