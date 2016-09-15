<?php
	ini_set("display_errors","1");

	if (file_exists("./custom-index.php")) {
		include_once "./custom-index.php";
	} elseif (file_exists("./custom.index.php")) {
		include_once "./custom.index.php";
	} else {

		if (file_exists("./custom-inc.php")) {
			$incfile = "./custom-inc.php";
		} elseif (file_exists("./custom.inc.php")) {
			$incfile = "./custom.inc.php";
		} else {
			if (file_exists("./inc.php")) {
				$incfile = "./inc.php";
			}
		}

		if (file_exists("./custom-inc2.php")) {
			$incfile2 = "./custom-inc2.php";
		} elseif (file_exists("./custom.inc2.php")) {
			$incfile = "./custom.inc2.php";
		} else {
			if (file_exists("./inc2.php")) {
				$incfile2 = "./inc2.php";
			}
		}

		if (file_exists("./images/user-logo.png")) {
			$logo_url = "./images/user-logo.png";
		} else {
			$logo_url = "./images/logo.png";
		}

		session_start();

		if (!file_exists('./no_need_token')) {
			if (((isset($_GET['frm_emessage'])) && ($_GET['frm_emessage'] === 'token_not_match')) ||
					((isset($_SESSION['no_token'])) && ($_SESSION['no_token'] == 1))) {
				print('<div align="center">*** Token not match. No permit for remote login ***</div>');
				exit;
			}
		}
?>
<html>

<head>
	<meta http-equiv="Content-Language" content="en-us" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php

		if(isset($incfile2)) { include_once $incfile2 ; }

		if (isset($page)) {
			$title = "Kloxo-MR {$page} page";
		} else {
			$title = "Kloxo-MR Page";
		}

		$bckgrnd = "\tbackground-image: url(./images/abstract.jpg);";

		if (basename(getcwd()) === 'login') {

			$path = "../theme/background";

			// MR -- trick to make random background for login
			if ((file_exists($path)) && (!file_exists("./.norandomimage"))) {
				try {
					$dirs = glob("{$path}/*", GLOB_MARK);

					if ($dirs) {
						$count = count($dirs);
						$selnum = rand(0, ($count - 1));

						if ((isset($_SESSION['last_login_time'])) && (isset($_SESSION['num_login_fail']))) {
							if ($_SESSION['num_login_fail'] == 5) {
								$selimg = "{$path}/abstract_003.jpg";
							} else {
								$selimg = $dirs[$selnum];
							}
						} else {
							$selimg = $dirs[$selnum];
						}

						$bckgrnd = "\tbackground-image: url({$selimg});\n".
							"\tbackground-size: cover;\n".
							"\tbackground-attachment: fixed;";
					}
				} catch (Exception $e) {
					$bckgrnd = $bckgrnd;
				}
			}
		}
?>
	<title><?= $title; ?></title>
<style>
body {
	font-family: Tahoma, Verdana, Arial, Helvertica, sans-serif;
	font-size: 8pt;
	font-weight: 100;
<?= $bckgrnd; ?>

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
		<td width="100%"><img style="margin:5px; padding:5px; height:50px" class="logo" src="<?php echo $logo_url; ?>" alt="hosting-logo"></td>
		<td><a href="//mratwork.com/work/" title="Go to Kloxo-MR website"><img style="margin:5px; padding:5px; height:50px" class="logo" src="./images/kloxo-mr.png" alt="kloxo-mr-logo"></a></td>
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
?>
