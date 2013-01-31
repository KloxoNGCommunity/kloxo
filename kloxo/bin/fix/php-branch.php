<?php
// release by mustafa.ramadhan@lxcenter.org

include_once "htmllib/lib/include.php";

$list = parse_opt($argv);

$select = (isset($list['select'])) ? $list['select'] : '';
$nolog  = (isset($list['nolog']))  ? $list['nolog'] : null;

setPhpBranch($select, $nolog);


