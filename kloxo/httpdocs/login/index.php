<?php
	ini_set("display_errors","1");

	// use default index.php
	if (!file_exists("./custom-index.php")) {
?>

<?php
	if (file_exists("./custom-inc.php")) {
		// use user-define inc.php -- no override when kloxo update
		$incfile = "./custom-inc.php";
		if (file_exists("./custom-inc2.php")) {
			$incfile2 = "./custom-inc2.php";
		}
	}
	else {
		// use default inc.php
		$incfile = "./inc.php";
		if (file_exists("./inc2.php")) {
			$incfile2 = "./inc2.php";
		}
	}
?>

<html>

<head>
	<title>Kloxo-MR Page</title>
	<meta http-equiv="Content-Language" content="en-us" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<?php if(isset($incfile2)) { include_once $incfile2 ; } ?>

<style>
body {
	font-family: Tahoma, Verdana, Arial, Helvertica, sans-serif;
	font-size: 8pt;
	font-weight: 100;
	background-image:url('./images/abstract.jpg');
	background-color:#cccccc;
	margin: 0;
}
a {
	text-decoration: none;
}
img {
	border: 0;
}

img.logo {
	margin: 5px;
	padding: 0;
}

table.header {
	border-collapse: collapse;
	border-spacing: 0;
	width: 100%;
	font-family: Tahoma, Verdana, Arial, Helvertica, sans-serif;
	font-size: 8pt;
	font-weight: 100;
}

table.content {
	border-collapse: collapse;
	border-spacing: 0;
	width: 100%;
	/* height: 100%; */
	font-family: Tahoma, Verdana, Arial, Helvertica, sans-serif;
	font-size: 8pt;
	font-weight: 100%;
}

table.content_body td {
	border-collapse: collapse;
	border: 1px dashed #cccccc;
	font-family: Tahoma, Verdana, Arial, Helvertica, sans-serif;
	font-size: 10pt;
	color: #444444;
	padding:10px;
	spacing:0;
}

table.content_title td {
	border-collapse: collapse;
	border: 0;
	font-family: Tahoma, Verdana, Arial, Helvertica, sans-serif;
	font-size: 12pt;
	color: #336699;
}

</style>

</head>

<body>

<table class="header">
	<tr>
		<td width="100%"><img class="logo" src="./images/logo.png" height="75" alt="hosting-logo"></td>
		<td><a href="http://mratwork.com/work/" title="Go to Kloxo-MR website"><img class="logo" src="./images/kloxo-mr.png" alt="kloxo-mr-logo" height="75"></a></td>
	</tr>
</table>
<table class="content">
	<tr>
		<td width="50">&nbsp;</td>
		<td valign="middle" width="100%"><?php include_once $incfile; ?></td>
		<td width="50">&nbsp;</td>
	</tr>
</table>
</body>

</html>

<?php
	}
	else {
		// use user-define index.php -- no override when kloxo update
		include_once "./custom-index.php";
	}
?>
