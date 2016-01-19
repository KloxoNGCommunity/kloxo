<?php 

class serverftp__pureftp extends lxDriverclass
{
	function dbactionAdd()
	{
	}

	function dbactionUpdate($subaction)
	{
		$this->updateXinConfig();
	}

	function updateXinConfig()
	{
		if ($this->main->isOn('enable_anon_ftp')) {
			$anonval = "";
		} else { 
			$anonval = "-E";
		}

		if ($this->main->isOn('enable_tls')) {
			$tlsval = 1;
		} else { 
			$tlsval = 0;
		}

	/*
		// MR -- xinetd
		$txt = lfile_get_contents("../file/pure-ftpd/etc/xinetd/pureftp");
		$txt = str_replace("%lowport%", $this->main->lowport, $txt);
		$txt = str_replace("%highport%", $this->main->highport, $txt);
		$txt = str_replace("%maxclient%", $this->main->maxclient, $txt);
		$txt = str_replace("%anonymous%", $anonval, $txt);

		lfile_put_contents("/etc/xinetd.d/pureftp", $txt);

		$begincomment[] = "### begin - add by Kloxo-MR";
		$endcomment[] = "### end - add by Kloxo-MR";
		$texttarget = "/etc/services";
		$textcontent  = "ftp {$this->main->defaultport}/tcp\n";
		$textcontent .= "ftp {$this->main->defaultport}/udp fsp fspd\n";
		$nowarning = true;

		file_put_between_comments($this->main->defaultport, $begincomment, $endcomment,
			$begincomment[0], $endcomment[0], $texttarget, $textcontent, $nowarning);
	*/

		// MR -- init.d
		$txt = lfile_get_contents("../file/pure-ftpd/etc/pure-ftpd/pure-ftpd.conf");
		$txt = str_replace("%lowport%", $this->main->lowport, $txt);
		$txt = str_replace("%highport%", $this->main->highport, $txt);
		$txt = str_replace("%maxclient%", $this->main->maxclient, $txt);
		$txt = str_replace("%port%", $this->main->defaultport, $txt);
		$txt = str_replace("%anonymous%", $anonval, $txt);

		$txt = str_replace("%enabletls%", $tlsval, $txt);

		lfile_put_contents("/etc/pure-ftpd/pure-ftpd.conf", $txt);

		createRestartFile('restart-ftp');
	}
}
