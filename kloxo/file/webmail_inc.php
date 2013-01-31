<br /> <br />
<div align="center">
	<table style="border-left: 1px solid #cccccc; spacing: 0; padding: 5px; width: 200px">
		<tr>
			<td colspan="3" style="border-bottom: 1px solid #cccccc; text-align: center"><b>Webmail List</b></td>
		</tr>
<?php
$dirs = glob("*");
$count = 1;
foreach ($dirs as $dir) {
//	if ($dir != "." && $dir != "..") {
		if ($dir != "img" && $dir != "images" && $dir != "disabled"  && is_dir($dir)) {
?>
		<tr>
			<td><?php echo $count; ?></td><td>-</td><td style="border-bottom: 1px solid #cccccc; width: 100%"><a href="/<?php echo $dir; ?>"><?php echo ucfirst("$dir"); ?></a></td>
		</tr>
<?php
			$count++;
		}
//	}
}
?>
	</table>
</div>