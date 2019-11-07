regex_map http://(.*):80/ http://127.0.0.1:8080/
regex_map http://(.*):443/ http://127.0.0.1:8080/

<?php

foreach ($domains as $k => $v) {
?>
map http://<?=$v;?>:80/ http://127.0.0.1:8080/
map http://<?=$v;?>:443/ http://127.0.0.1:8080/
<?php
}
?>