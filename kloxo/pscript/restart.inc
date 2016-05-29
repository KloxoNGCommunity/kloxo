if [ "$1" == "--help" ] || [ "$1" == "-h" ] ; then
	echo ""
	echo " ------------------------------------------------------------------------------"
	echo "  format: sh $0 [--force|-y]"
	echo " ------------------------------------------------------------------------------"
	echo "  --force or -y"
	echo "      - example: running '/etc/init.d/httpd restart' instead custom 'restart'"
	echo "        declare inside /etc/local/lxlabs/kloxo/etc/process"
	echo "      - use it if trouble restart without '--force' or '-y'"
	echo ""
	exit
else
	ppath="/usr/local/lxlabs/kloxo/etc/process"
	ipath="/etc/rc.d/init.d"
	fpath="/usr/local/lxlabs/kloxo/etc/flag"
	
	ASQMAIL=""

	for item in ${list[*]} ; do
		if [ -f ${ipath}/${item} ] ; then
			if [ "${item}" == "php-fpm" ] || [ "${item}" == "phpm-fpm" ] ; then
				if [ -f ${fpath}/enablemultiplephp.flg ] ; then
					chkconfig phpm-fpm on
					chkconfig php-fpm on
				else
					chkconfig php-fpm on
					chkconfig phpm-fpm off
					service phpm-fpm stop >/dev/null 2>&1
				fi
			fi

			if [ "$(chkconfig --list ${item}|grep ':on')" != "" ] ; then
					echo ""
					if [ "$1" == "--force" ] || [ "$1" == "-y" ] ; then
						if [ "${item}" == "qmail" ] ; then
							ASQMAIL="qmail"
							sh /script/restart-mail
						else
							${ipath}/${item} restart
						fi
					else
						if [ -f ${ppath}/custom.${item} ] ; then
							sh ${ppath}/custom.${item}
						elif [ -f ${ppath}/${item} ] ; then
							sh ${ppath}/${item}
						else
							${ipath}/${item} restart
						fi
					fi

					echo "-------------------------------------------------------------------"
			else
				if [ "$(${ipath}/${item} status|grep '(pid')" != "" ] || [ "$(${ipath}/${item} status|grep 'dead but')" != "" ] ; then
					${ipath}/${item} stop
				fi
			fi
		fi
	done

	if [ "${ASQMAIL}" != "qmail" ] ; then
		echo ""
		echo "- For help, type '$0 [--help|-h]'"
	fi
fi

