if [ "$1" == "--help" ] || [ "$1" == "-h" ] ; then
	echo
	echo " ------------------------------------------------------------------------------"
	echo "  format: sh $0 [--force|-y]"
	echo " ------------------------------------------------------------------------------"
	echo "  --force or -y"
	echo "      - running '/etc/init.d/httpd restart' instead custom 'restart' declare"
	echo "        inside /etc/local/lxlabs/kloxo/etc/process"
	echo "      - use it if trouble restart without '--force' or '-y'"
	echo
	exit;
else
	for item in ${list[*]} ; do
		if [ -f /etc/init.d/${item} ] ; then
			for num in 0 1 2 3 4 5 6 ; do
				if $(dir /etc/rc.d/rc${num}.d|grep -E 'S[0-9]{2}'${item} >/dev/null 2>&1) ; then
					echo ""
					if [ "$1" == "--force" ] || [ "$1" == "-y" ] ; then
						/etc/init.d/${item} restart
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

					break
				fi
			done
		fi
	done

	echo
	echo "- For help, type '$0 --help' or '$0 -h'"
fi

