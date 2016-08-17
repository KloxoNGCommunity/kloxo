if [ "$(lxphp.exe -v|grep 'PHP')" == "" ] ; then
	echo
	echo "* No 'lxphp.exe' exists"
	echo "  - Need install Kloxo-MR 7.0 perfectly with 'sh /script/upcp -y'"
	echo
	exit
fi

T="$(date +%s%N)"