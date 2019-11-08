#!/bin/sh

service $prog stop
if [ "$(pgrep ^$prog)" != "" ] ; then
	killall -9 $prog
fi
service $prog start