<?php
	$ha = str_replace('cp.', '', $_SERVER["HTTP_HOST"]);
?>
		<br><br>
		<div align="center">
			<table width="300" style="border-collapse: collapse" border="1" bordercolor="#AAAAAA">
				<tr>
					<td nowrap bgcolor="#CCCCCC">&nbsp;</td>
					<td colspan="2" nowrap align="center" bgcolor="#EEEEEE">Panel</td>
				</tr>
				<tr>
					<td nowrap bgcolor="#eeeeee">&nbsp; Kloxo</td>
					<td nowrap align="center" bgcolor="#FFFFCC"><a href="http://<?php echo $ha; ?>:7778/">http</a></td>
					<td nowrap align="center" bgcolor="#FFFFCC"><a href="https://<?php echo $ha; ?>:7777/">https</a></td>
				</tr>
				<tr>
					<td nowrap bgcolor="#eeeeee">&nbsp; Webmail</td>
					<td nowrap align="center" bgcolor="#FFFFCC"><a href="http://webmail.<?php echo $ha; ?>/">http</a></td>
					<td nowrap align="center" bgcolor="#FFFFCC"><a href="https://webmail.<?php echo $ha; ?>/">https</a></td>
				</tr>
				<tr>
					<td nowrap bgcolor="#eeeeee" width="140">&nbsp; PHPMyAdmin</td>
					<td nowrap align="center" width="80" bgcolor="#FFFFCC">
					<a href="http://<?php echo $ha; ?>:7778/thirdparty/phpMyAdmin/">
					http</a></td>
					<td nowrap align="center" width="80" bgcolor="#FFFFCC">
					<a href="https://<?php echo $ha; ?>:7777/thirdparty/phpMyAdmin/">
					https</a></td>
				</tr>
			</table>
		</div>

<br /> <br />
<div align="center">
	<table style="border-left: 1px solid #cccccc; spacing: 0; padding: 5px; width: 250px">
		<tr>
			<td colspan="3" style="border-bottom: 1px solid #cccccc; text-align: center"><b>Admins on CP</b></td>
		</tr>
<?php
$dirs = glob("*");
$count = 1;
foreach ($dirs as $dir) {
//	if ($dir != "." && $dir != "..") {
		if ($dir != "img" && $dir != "images" && $dir != "disabled"  && is_dir($dir)) {
			if (file_exists("{$dir}/index.php")) {
?>
		<tr>
			<td><?php echo $count; ?></td><td>-</td><td style="border-bottom: 1px solid #cccccc; width: 100%"><a href="/<?php echo $dir; ?>"><?php echo ucfirst("$dir"); ?></a></td>
		</tr>
<?php
			}

			$count++;
		}
//	}
}
?>
	</table>
</div>
