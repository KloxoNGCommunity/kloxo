for item in ${list[*]} ; do
	if [ -f /etc/init.d/${item} ] ; then
		for num in 0 1 2 3 4 5 6 ; do
			if $(dir /etc/rc.d/rc${num}.d|grep -E 'S[0-9]{2}'${item} >/dev/null 2>&1) ; then
				if [ -f /usr/local/lxlabs/kloxo/etc/process/custom.${item} ] ; then
					sh /usr/local/lxlabs/kloxo/etc/process/custom.${item}	
				elif [ -f /usr/local/lxlabs/kloxo/etc/process/${item} ] ; then
					sh /usr/local/lxlabs/kloxo/etc/process/${item}
				else
					/etc/init.d/${item} restart
				fi

				break
			fi
		done
	fi
done