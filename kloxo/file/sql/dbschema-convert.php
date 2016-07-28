<?php


dbschema_to_phpvar();

function dbschema_to_phpvar()
{
	$p = "/usr/local/lxlabs/kloxo/file/sql/";

	$c = file_get_contents("$p/.db_schema");
	$u = unserialize($c);
/*
	$s = '<' . '?php' . "\n";

	foreach ($u as $k1 => $v1) {
		if (!is_array($v1)) {
			$s .= "\$var['$k1'] = $v1;\n";
		} else {
			foreach($v1 as $k2 => $v2) {
				if (!is_array($v2)) {
					$s .= "\$var['$k1']['$k2'] = $v2;\n";
				} else {
					foreach($v2 as $k3 => $v3) {
						if (!is_array($v3)) {
							$s .= "\$var['$k1']['$k2']['$k3'] = $v3;\n";
						}	
					}
				}	
			}
		}
	}
*/
	$v = var_export($u, true);

	file_put_contents("$p/db_schema.php", '<' . '?php' . "\n" . "\$var = " . $v . ';');
}




