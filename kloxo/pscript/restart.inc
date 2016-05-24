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
	ASQMAIL=""

	for item in ${list[*]} ; do
		if [ -f /etc/init.d/${item} ] ; then
			if [ "${item}" == "php-fpm" ] || [ "${item}" == "phpm-fpm" ] ; then
				if [ -f /usr/local/lxlabs/kloxo/etc/flag/enablemultiplephp.flg ] ; then
					chkconfig phpm-fpm on
					chkconfig php-fpm off
					service php-fpm stop >/dev/null 2>&1
				else
					chkconfig php-fpm on
					chkconfig phpm-fpm off
					service phpm-fpm stop >/dev/null 2>&1
				fi
			fi

			if [ "$(chkconfig --list ${item}|grep ':on')" != "" ] ; then
			#if chkconfig --list ${item}|grep ':on' >/dev/null 2>&1 ; then
			#for num in 0 1 2 3 4 5 6 ; do
				#if $(dir /etc/rc.d/rc${num}.d|grep -E 'S[0-9]{2}'${item} >/dev/null 2>&1) ; then
					echo ""
					if [ "$1" == "--force" ] || [ "$1" == "-y" ] ; then
						if [ "${item}" == "qmail" ] ; then
							ASQMAIL="qmail"
							sh /script/restart-mail
						elif [ "${item}" == "php-fpm" ] ; then
							/etc/init.d/${item} stop
							if [ "$(ps aux|grep 'kloxo-php-fpm.conf'|grep -v grep)" == "" ] ; then
								echo "pkill to make sure stopped"
								pkill -9 php-fpm
							fi
							/etc/init.d/${item} start
						else
							/etc/init.d/${item} restart
						fi
					else
						if [ -f /usr/local/lxlabs/kloxo/etc/process/custom.${item} ] ; then
							sh /usr/local/lxlabs/kloxo/etc/process/custom.${item}
						elif [ -f /usr/local/lxlabs/kloxo/etc/process/${item} ] ; then
							sh /usr/local/lxlabs/kloxo/etc/process/${item}
						else
							/etc/init.d/${item} restart
						fi
					fi

					echo "-------------------------------------------------------------------"

					#break
				#fi
			#done
			fi
		fi
	done

	if [ "${ASQMAIL}" != "qmail" ] ; then
		echo ""
		echo "- For help, type '$0 [--help|-h]'"
	fi
fi

