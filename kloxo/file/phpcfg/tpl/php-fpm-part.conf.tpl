<?php
    if (strpos($fpm_type, '52') !== false) {
        if ($fpm_section === 'global') {
?>
    <section name="global_options">
        <value name="pid_file">/var/run/php-fpm.pid</value>
        <value name="error_log">/var/log/php-fpm/error.log</value>
        <value name="log_level">error</value>
        <value name="emergency_restart_threshold">10s</value>
        <value name="emergency_restart_interval">1m</value>
        <value name="process_control_timeout">10s</value>
        <value name="daemonize">yes</value>
    </section>

<?php
        } else {
            if ($maxchildren === 0) {
?>
        <!-- no pool for '<?php echo $user; ?>' user -->
<?php
    
            } else {
?>
        <section name="pool">
            <value name="name"><?php echo $pool; ?></value>
            <value name="listen_address"><?php echo $fpm_type; ?>-<?php echo $user; ?>.sock</value>
            <value name="listen_options">
                <value name="backlog">65536</value>
                <value name="owner"></value>
                <value name="group"></value>
                <value name="mode">0666</value>
            </value>
            <value name="user"><?php echo $user; ?></value>                
            <value name="group"><?php echo $user; ?></value>        
            <value name="pm">
                <value name="style">static</value>
                <value name="max_children"><?php echo $maxchildren; ?></value>
            </value>
            <value name="request_terminate_timeout">120s</value>
            <value name="request_slowlog_timeout">30s</value>
            <value name="slowlog">/var/log/php-fpm/slow.log</value>
            <value name="rlimit_files">1024</value>
            <value name="rlimit_core">0</value>
            <value name="chroot"></value>
            <value name="chdir"></value>
            <value name="catch_workers_output">yes</value>
            <value name="max_requests">1000</value>
            <value name="allowed_clients">127.0.0.1</value>
            <value name="environment">
                <value name="HOSTNAME">$HOSTNAME</value>
                <value name="PATH">/usr/local/bin:/usr/bin:/bin</value>
                <value name="TMP">/tmp</value>
                <value name="TMPDIR">/tmp</value>
                <value name="TEMP">/tmp</value>
                <value name="OSTYPE">$OSTYPE</value>
                <value name="MACHTYPE">$MACHTYPE</value>
                <value name="MALLOC_CHECK_">2</value>
            </value>
        </section>

<?php
            }
        }
    } else {
        if ($fpm_section === 'global') {
?>
[global]
pid=/var/run/php-fpm/php-fpm.pid
error_log=/var/log/php-fpm/error.log
log_level=error

;emergency_restart_threshold=0
;emergency_restart_interval=0
;process_control_timeout=0

emergency_restart_threshold=10s
emergency_restart_interval=1m
process_control_timeout=10s

daemonize=yes

;include=/home/phpcfg/fpm/pool/<?php echo $fpm_type; ?>*.conf

<?php
        } else {
            if ($maxchildren === 0) {
?>
        ; no pool for '<?php echo $user; ?>' user
<?php
    
            } else {
?>
[<?php echo $pool; ?>]
;catch_workers_output = yes

listen = /home/php-fpm/sock/<?php echo $fpm_type; ?>-<?php echo $user; ?>.sock
listen.backlog = 65536
listen.allowed_clients = 127.0.0.1
listen.owner = <?php echo $user; ?>

listen.group = <?php echo $user; ?>

listen.mode = 0666
user = <?php echo $user; ?>

group = <?php echo $user; ?>

;pm = dynamic
pm = ondemand
pm.max_children = <?php echo $maxchildren; ?>

;pm.start_servers = <?php echo $startservers; ?>

;pm.min_spare_servers = <?php echo $minspareservers; ?>

;pm.max_spare_servers = <?php echo $maxspareservers; ?>

pm.max_requests = 1000

;pm.status_path = /status
;ping.path = /ping
;ping.response = pong
request_terminate_timeout = 120s
request_slowlog_timeout = 30s
slowlog = /var/log/php-fpm/slow.log
rlimit_files = 1024
rlimit_core = 0
;chroot = 
;chdir = /var/www
catch_workers_output = yes
security.limit_extensions = .php .php3 .php4 .php5

env[HOSTNAME] = $HOSTNAME
env[PATH] = /usr/local/bin:/usr/bin:/bin
env[TMP] = /tmp
env[TMPDIR] = /tmp
env[TEMP] = /tmp
env[OSTYPE] = $OSTYPE
env[MACHTYPE] = $MACHTYPE
env[MALLOC_CHECK_] = 2

php_admin_value[extension_dir] = <?php echo $extension_dir_path; ?>

php_flag[zlib.output_compression] = <?php echo $output_compression_flag; ?>

php_admin_value[disable_functions] = <?php echo $disable_functions; ?>

php_flag[display_errors] = <?php echo $display_error_flag; ?>

php_flag[file_uploads] = <?php echo $file_uploads_flag; ?>

php_admin_value[upload_max_filesize] = <?php echo $upload_max_filesize; ?>

php_flag[log_errors] = <?php echo $log_errors_flag; ?>

php_flag[output_buffering] = <?php echo $output_buffering_flag; ?>

php_flag[register_argc_argv] = <?php echo $register_argc_argv_flag; ?>

php_flag[mysql.allow_persistent] = <?php echo $mysql_allow_persistent_flag; ?>

php_admin_value[max_execution_time] = <?php echo $max_execution_time_flag; ?>

php_admin_value[max_input_time] = <?php echo $max_input_time_flag; ?>

php_admin_value[memory_limit] = <?php echo $memory_limit_flag; ?>

php_admin_value[post_max_size] = <?php echo $post_max_size_flag; ?>

php_flag[allow_url_fopen] = <?php echo $allow_url_fopen_flag; ?>

php_flag[allow_url_include] = <?php echo $allow_url_include_flag; ?>

php_admin_value[session.save_path] = <?php echo $session_save_path_flag; ?>

php_flag[cgi.force_redirect] = <?php echo $cgi_force_redirect_flag; ?>

php_flag[enable_dl] = <?php echo $enable_dl_flag; ?>

php_admin_value[open_basedir] = <?php echo $openbasedir; ?>

php_admin_value[max_input_vars] = <?php echo $max_input_vars_flag; ?>

<?php
        }
    }
?>

