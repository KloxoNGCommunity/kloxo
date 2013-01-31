if [ -z "`/usr/bin/id -g vchkpw 2>/dev/null`" ]; then
	/usr/sbin/groupadd -g 89 -r vchkpw >/dev/null 2>&1 || :
fi

if [ -z "`/usr/bin/id -u vpopmail 2>/dev/null`" ]; then
	/usr/sbin/useradd -u 89 -r -M -d /home/vpopmail/  -s /sbin/nologin -c "Vpopmail User" -g vchkpw vpopmail 2>&1 || :
fi
