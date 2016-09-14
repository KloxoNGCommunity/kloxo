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

		if (!file_exists("./no_need_token")) {
			if ((isset($_GET['frm_emessage'])) && ($_GET['frm_emessage'] === 'token_not_match')) {
				print('<div align="center">*** Token not match. No permit for remote login ***</div>');
				exit;
			}
		}
	
		if ((isset($_GET['frm_emessage'])) && ($_GET['frm_emessage'] === 'blocked')) {
			// MR -- js script taken from http://jsfiddle.net/JFYaq/1/
			$msg = '
<div align="center">*** Your address is blocked and need waiting 10 minutes to login again ***</div>
<div id="countdown" align="center"></div>
<script>
var countdown = document.getElementById("countdown");
var totalTime = 600;
function pad(n) {
	return n > 9 ? "" + n : "0" + n;
}
var original = totalTime;
function padMinute(n) {
	return original >= 600 && n <= 9 ? "0" + n : "" + n;
}
var interval = setInterval(function() {
	updateTime();
	if(totalTime == -1) {
		clearInterval(interval);
	//	return;
	//	self.location = self.location.href;
		self.location = "/";
	}
}, 1000);

function displayTime() {
	var minutes = Math.floor(totalTime / 60);
	var seconds = totalTime % 60;
	minutes = "<span>" + padMinute(minutes).split("").join("</span><span>") + "</span>";
	seconds = "<span>" + pad(seconds).split("").join("</span><span>") + "</span>";
	countdown.innerHTML = "Remaining: " + minutes + ":" + seconds;
}
function updateTime() {
	displayTime();
	totalTime--;
}
updateTime();
</script>
';
			print($msg);
			exit;
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

						if ((isset($_GET['frm_emessage'])) && ($_GET['frm_emessage'] === 'login_error')) {
							$selimg = "{$path}/abstract_003.jpg";
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
