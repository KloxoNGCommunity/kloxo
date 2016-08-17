if [ "$(lxphp.exe -v|grep 'PHP')" == "" ] ; then
	echo
	echo "Need install Kloxo-MR 7.0 perfectly with 'sh /script/upcp -y'"
	echo
fi

T="$(date +%s%N)"