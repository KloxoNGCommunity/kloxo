<?php 

include_once "lib/html/displayinclude.php";

initProgram('admin');

$list = parse_opt($argv);

$port_ssl = (isset($list['port-ssl'])) ? $list['portssl'] : '7777';
$port_nonssl = (isset($list['port-nonssl'])) ? $list['port-nonssl'] : '7778';
$disable_nonssl  = (isset($list['disable_nonssl'])) ? $list['disable_nonssl'] : null;
$redirect_to_ssl  = (isset($list['redirect-to-ssl'])) ? $list['redirect-to-ssl'] : null;

$gen = $login->getObject('general');

$gen->portconfig_b->sslport = $port_ssl;
$gen->portconfig_b->nonsslport = $port_nonssl;
$gen->portconfig_b->nonsslportdisable_flag = $redirect_to_ssl;
$gen->portconfig_b->redirectnonssl_flag = $disable_nonssl;

$gen->setUpdateSubaction();
$gen->write();
