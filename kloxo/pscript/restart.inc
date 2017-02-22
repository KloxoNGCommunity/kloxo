if [ "$1" == "--help" ] || [ "$1" == "-h" ] ; then
	echo ""
	echo " ------------------------------------------------------------------------------"
	echo "  format: sh $0 [--force|-y]"
	echo " ------------------------------------------------------------------------------"
	echo "  --force or -y"
	echo "      - example: running 'service httpd restart' instead custom 'restart'"
	echo "        declare inside /etc/local/lxlabs/kloxo/etc/process"
	echo "      - use it if trouble restart without '--force' or '-y'"
	echo ""
	exit
else
	ppath="/usr/local/lxlabs/kloxo/etc/process"
	fpath="/usr/local/lxlabs/kloxo/etc/flag"
	
	ASQMAIL=""

	for item in ${list[*]} ; do
		svc_ok='no'
		if [ "$(chkconfig --list --type=sysv 2>/dev/null|grep ^${item})" != "" ] ; then
			svc_ok='yes'
		fi
		if [ "$(command -v systemctl)" != "" ] ; then
			if [ "$(systemctl list-unit-files --type=service|grep ^${item})" != "" ] ; then

				svc_ok='yes'
			fi
		fi

		if [ "${svc_ok}" == 'yes' ] ; then
			if [ "${item}" == "phpm-fpm" ] ; then
				if [ -f ${fpath}/enablemultiplephp.flg ] ; then
					chkconfig phpm-fpm on 2>/dev/null
					chkconfig php-fpm on 2>/dev/null
				else
					chkconfig php-fpm on 2>/dev/null
					chkconfig phpm-fpm off 2>/dev/null
					if [ "$(pgrep ^${item})" != "" ] ; then
						service phpm-fpm stop >/dev/null 2>&1
					fi
				fi
			fi
			svc_on='no'
			if [ "$(chkconfig --list --type=sysv 2>/dev/null|grep ^${item}|grep ':on')" != "" ] ; then
				svc_on='yes'
			fi
			if [ "$(command -v systemctl)" != "" ] ; then
				if [ "$(systemctl list-unit-files --type=service|grep ^${item}|grep 'enabled')" != "" ] ; then
					svc_on='yes'
				fi
			fi

			if [ "${svc_on}" == 'yes' ] ; then
					echo ""
					if [ "$1" == "--force" ] || [ "$1" == "-y" ] ; then
						if [ "${item}" == "qmail" ] ; then
							ASQMAIL="qmail"
							sh /script/restart-mail
						elif [ "${item}" == "php-fpm" ] ; then
							service ${item} stop
							'rm' -f /opt/configs/php-fpm/sock/php-*.sock
							service ${item} start
						elif [ "${item}" == "nsd" ] ; then
							service ${item} restart
						elif [ "${item}" == "spawn-fcgi" ] ; then
							'rm' -f /tmp/fcgiwrap.sock
							service ${item} restart
						else
							service ${item} restart
						fi
					else
						if [ -f ${ppath}/custom.${item} ] ; then
							sh ${ppath}/custom.${item}
						elif [ -f ${ppath}/${item} ] ; then
							sh ${ppath}/${item}
						else
							service ${item} restart
						fi
					fi

					echo "-------------------------------------------------------------------"
			else
				if [ "$(pgrep ^${item})" != "" ] ; then
					service ${item} stop
				fi
			fi
		fi
	done

	if [ "${ASQMAIL}" != "qmail" ] ; then
		echo ""
		echo "- For help, type '$0 [--help|-h]'"
	fi
fi

