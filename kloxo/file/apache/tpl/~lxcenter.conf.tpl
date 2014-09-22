Timeout 150
KeepAlive <?php echo $keepalive; ?>

MaxKeepAliveRequests 100
KeepAliveTimeout 15

<IfModule prefork.c>
    StartServers 2
    MinSpareServers <?php echo $minspareservers; ?>

    MaxSpareServers <?php echo $maxspareservers; ?>

    ServerLimit <?php echo $maxspareservers; ?>

    <IfVersion >= 2.4>
        MaxRequestWorkers <?php echo $maxspareservers; ?>

        MaxConnectionsPerChild <?php echo $maxrequestsperchild; ?>

    </IfVersion>
    <IfVersion < 2.4>
        MaxClients <?php echo $maxspareservers; ?>

        MaxRequestsPerChild <?php echo $maxrequestsperchild; ?>

    </IfVersion>
    MaxMemFree 2
    SendBufferSize 65536
    ReceiveBufferSize 65536
</IfModule>

<IfModule itk.c>
    StartServers 2
    MinSpareServers <?php echo $minspareservers; ?>

    MaxSpareServers <?php echo $maxspareservers; ?>

    ServerLimit <?php echo $maxspareservers; ?>

    <IfVersion >= 2.4>
        MaxRequestWorkers <?php echo $maxspareservers; ?>

        MaxConnectionsPerChild <?php echo $maxrequestsperchild; ?>

    </IfVersion>
    <IfVersion < 2.4>
        MaxClients <?php echo $maxspareservers; ?>

        MaxRequestsPerChild <?php echo $maxrequestsperchild; ?>

    </IfVersion>
    MaxMemFree 2
    SendBufferSize 65536
    ReceiveBufferSize 65536
</IfModule>

<IfModule worker.c>
    StartServers 2
    MinSpareThreads <?php echo $minsparethreads; ?>

    MaxSpareThreads <?php echo $maxsparethreads; ?>

    ThreadsPerChild 25
    <IfVersion >= 2.4>
        MaxRequestWorkers 400
        MaxConnectionsPerChild 0
    </IfVersion>
    <IfVersion < 2.4>
        MaxClients 400
        MaxRequestsPerChild 0
    </IfVersion>

    ThreadStackSize 8196
    MaxMemFree 2
    SendBufferSize 65536
    ReceiveBufferSize 65536
</IfModule>

<IfModule event.c>
    StartServers 2
    MinSpareThreads <?php echo $minsparethreads; ?>

    MaxSpareThreads <?php echo $maxsparethreads; ?>

    ThreadsPerChild 25
    MaxRequestsPerChild 0
    ThreadStackSize 8196
    <IfVersion >= 2.4>
        MaxRequestWorkers 400
        MaxConnectionsPerChild 0
    </IfVersion>
    <IfVersion < 2.4>
        MaxClients 400
        MaxRequestsPerChild 0
    </IfVersion>

    SendBufferSize 65536
    ReceiveBufferSize 65536
</IfModule>

Include /opt/configs/apache/conf/defaults/*.conf
Include /opt/configs/apache/conf/domains/*.conf

