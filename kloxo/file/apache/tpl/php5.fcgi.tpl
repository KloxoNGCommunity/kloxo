<?php
    $maxchildren = '5';
    $maxrequests = '1000';
?>
#!/bin/sh
export PHPRC="<?php echo $phpinipath; ?>"
export PHP_FCGI_CHILDREN=<?php echo $maxchildren; ?>

export PHP_FCGI_MAX_REQUESTS=<?php echo $maxrequests; ?>

exec /usr/bin/<?php echo $phpcginame; ?>