<?PHP
/*
*
*
* PowerDNS driver for Kloxo.
* 10:31 AM 8/24/2007 Ahmet YAZICI ahmet.yazici@pusula.net.tr
*
*  Usage : get and install powerdns from www.powerdns.com
*  Create your database and import powerdns schema..
*  Let kloxo to use powerdns as default dns driver via
*  cd /usr/local/lxlabs/kloxo/httpdocs/
*  lxphp.exe ../bin/common/setdriver.php --server=localhost --class=dns --driver=powerdns
*
*  Changelog :
*  01:07 AM 8/26/2007 Ahmet 
*     Moved sql variables to secure location 
*	
*/

class dns__powerdns extends lxDriverClass
{
	function dbactionUpdate($subaction)
	{
		$this->dbactionDelete();
		$this->dbactionAdd();
	}

	function dbConnect()
	{
		include_once "/usr/local/lxlabs/kloxo/etc/powerdns.conf.inc";

		$ret = new mysqli($power_sql_host, $power_sql_user, $power_sql_pwd, $power_sql_db);

		return $ret;
	}

	function dbClose($conn)
	{
		$conn->close();
	}

	function dbactionAdd()
	{
		$conn = $this->dbConnect();

		$domainname = $this->main->nname;

		$query = $conn->query("INSERT INTO domains (name,type) values('$domainname','NATIVE')");

		if ($conn->affected_rows()) {
			$this_domain_id = $conn->insert_id();

			foreach ($this->main->dns_record_a as $k => $o) {
				switch ($o->ttype) {
					case "ns":
						$conn->query("INSERT INTO records (domain_id, name, content, type,ttl,prio) VALUES ('$this_domain_id','$domainname','$o->param','NS','3600','NULL')");

						break;
					case "mx":
						$v = $o->priority;
						$conn->query("INSERT INTO records (domain_id, name, content, type,ttl,prio) VALUES ('$this_domain_id','$domainname','$o->param','MX','3600','$v')");

						break;
					case "a":
						$key = $o->hostname;
						$value = $o->param;
						
						if ($key === '*') {
							$starvalue = "* IN A $value";
							break;
						}
						if ($key !== "__base__") {
							$key = "$key.$domainname";
						} else {
							$key = "$domainname";
						}

						$conn->query("INSERT INTO records (domain_id, name, content, type,ttl,prio) VALUES ('$this_domain_id','$key','$value','A','3600','NULL')");

						break;
					case "cn":
					case "cname":
						$key = $o->hostname;
						$value = $o->param;
						$key .= ".$domainname";

						if ($value !== "__base__") {
							$value = "$value.$domainname";
						} else {
							$value = "$domainname";
						}

						if ($key === '*') {
							$starvalue = "*		IN CNAME $value\n";
							break;
						}
					
						$conn->query("INSERT INTO records (domain_id, name, content, type,ttl,prio) VALUES ('$this_domain_id','$key','$value','CNAME','3600','NULL')");
	
						break;
					case "fcname":
						$key = $o->hostname;
						$value = $o->param;
						$key .= ".$domainname";

						if ($value !== "__base__") {
							if (!cse($value, ".")) {
								$value = "$value.";
							}
						} else {
							$value = "$domainname";
						}

						$conn->query("INSERT INTO records (domain_id, name, content, type,ttl,prio) VALUES ('$this_domain_id','$key','$value','CNAME','3600','NULL')");
	
						break;
					case "txt":
						$key = $o->hostname;
						$value = $o->param;

						if ($o->param === null) {
							continue;
						}

						if ($key !== "__base__") {
							$key = "$key.$domainname.";
						} else {
							$key = "$domainname.";
						}

						$value = str_replace("<%domain>", $domainname, $value);

						$conn->query("INSERT INTO records (domain_id, name, content, type,ttl,prio) VALUES ('$this_domain_id','$key','$value','TXT','3600','NULL')");

						break;
				}
			}
		}

		$this->dbClose($conn);
	}

	function dbactionDelete()
	{
		$conn = $this->dbConnect();

		$this_domain = $this->main->nname;

		$my_query = $conn->query("SELECT * FROM domains WHERE name='" . $this_domain . "'");
		
		if ($my_query->num_rows()) {
			$this_row = $my_query->fetch_object();

			$this_domain_id = $this_row->id;

			$conn->query("DELETE FROM domains WHERE id='" . $this_domain_id . "'");
			$conn->query("DELETE FROM records WHERE domain_id='" . $this_domain_id . "'");
		}

		$this->dbClose($conn);
	}

	function dosyncToSystemPost()
	{
		global $sgbl;
	}
}

