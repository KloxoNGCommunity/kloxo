<?php

	// exec("sh /script/clearcache3");

	$factor = 1;

	$total = (int)shell_exec("free -m | grep Mem: | awk '{print $2}'");
	$spare = ($spare) ? $spare : ($total * 0.25);

	if (getServiceType() === 'systemd') {
		$apps  = (int)shell_exec("free -m | grep 'Mem:' | awk '{print $7}'");
	} else {
		$apps  = (int)shell_exec("free -m | grep 'buffers/cache:' | awk '{print $3}'");
	}

	$avail = $total - $spare - $apps;

	if ($select === 'low') {
		$maxpar_p = (int)($avail / 30 * $factor / 4);
		$minpar_p = (int)($maxpar_p / 2);

		$maxpar_w = (int)($avail / 35 * $factor / 4);
		$minpar_w = (int)($maxpar_w / 2);
	} elseif ($select === 'medium') {
		$maxpar_p = (int)($avail / 30 * $factor / 3);
		$minpar_p = (int)($maxpar_p / 2);

		$maxpar_w = (int)($avail / 35 * $factor / 3);
		$minpar_w = (int)($maxpar_w / 2);
	} elseif ($select === 'high') {
		$maxpar_p = (int)($avail / 30 * $factor / 2);
		$minpar_p = (int)($maxpar_p / 2);

		$maxpar_w = (int)($avail / 35 * $factor / 2);
		$minpar_w = (int)($maxpar_w / 2);
	} else {
		$maxpar_p = 4;
		$minpar_p = 2;
		$maxpar_w = 4;
		$minpar_w = 2;
	}

	// correction
	if ($maxpar_p < 4) { $maxpar_p = 4; }
	if ($minpar_p < 2) { $minpar_p = 2; }
	if ($maxpar_w < 4) { $maxpar_w = 4; }
	if ($minpar_w < 2) { $minpar_w = 2; }
/*
	if (!isset($keepalive)) {
		$keepalive = 'off';
	} else {
		$keepalive = 'on';
	}
*/	
	// MR -- default is 25
	$mcfactor = 25;
?>

Timeout 150
KeepAlive <?=$keepalive;?>

MaxKeepAliveRequests 100
KeepAliveTimeout 15

<IfModule prefork.c>
    StartServers 2
	MinSpareServers <?=$minpar_p;?>

	MaxSpareServers <?=$maxpar_p;?>

	ServerLimit <?=$maxpar_p;?>

    <IfVersion >= 2.4>
		MaxRequestWorkers <?=$maxpar_p;?>

        MaxConnectionsPerChild 4000
    </IfVersion>
    <IfVersion < 2.4>
		MaxClients <?=$maxpar_p;?>

        MaxRequestsPerChild 4000
    </IfVersion>
    MaxMemFree 2
    SendBufferSize 65536
    ReceiveBufferSize 65536
</IfModule>

<IfModule itk.c>
    StartServers 2
	MinSpareServers <?=$minpar_p;?>

	MaxSpareServers <?=$maxpar_p;?>

	ServerLimit <?=$maxpar_p;?>

    <IfVersion >= 2.4>
		MaxRequestWorkers <?=$maxpar_p;?>

        MaxConnectionsPerChild 4000
    </IfVersion>
    <IfVersion < 2.4>
		MaxClients <?=$maxpar_p;?>

        MaxRequestsPerChild 4000
    </IfVersion>
    MaxMemFree 2
    SendBufferSize 65536
    ReceiveBufferSize 65536
</IfModule>

<IfModule worker.c>
    StartServers 2
	MinSpareThreads <?=$minpar_w;?>

	MaxSpareThreads <?=$maxpar_w;?>

	ThreadsPerChild <?=$mcfactor;?>

	ServerLimit <?=$maxpar_w;?>

    <IfVersion >= 2.4>
		MaxRequestWorkers <?=$maxpar_w * $mcfactor;?>

        MaxConnectionsPerChild 0
    </IfVersion>
    <IfVersion < 2.4>
		MaxClients <?=$maxpar_w * $mcfactor;?>

        MaxRequestsPerChild 0
    </IfVersion>

    SendBufferSize 65536
    ReceiveBufferSize 65536
</IfModule>

<IfModule event.c>
    StartServers 2
	MinSpareThreads <?=$minpar_w;?>

	MaxSpareThreads <?=$maxpar_w;?>

	ThreadsPerChild <?=$mcfactor;?>

	ServerLimit <?=$maxpar_w;?>

    MaxRequestsPerChild 0
    <IfVersion >= 2.4>
		MaxRequestWorkers <?=$maxpar_w * $mcfactor;?>

        MaxConnectionsPerChild 0
    </IfVersion>
    <IfVersion < 2.4>
		MaxClients <?=$maxpar_w * $mcfactor;?>

        MaxRequestsPerChild 0
    </IfVersion>

    SendBufferSize 65536
    ReceiveBufferSize 65536
</IfModule>

<IfVersion >= 2.4>
	IncludeOptional /opt/configs/apache/conf/defaults/*.conf
	IncludeOptional /opt/configs/apache/conf/domains/*.conf
	IncludeOptional /opt/configs/apache/conf/customs/*.conf
</IfVersion>

<IfVersion < 2.4>
Include /opt/configs/apache/conf/defaults/*.conf
Include /opt/configs/apache/conf/domains/*.conf
	Include /opt/configs/apache/conf/customs/*.conf
</IfVersion>


### selected: <?=$select;?> ###

