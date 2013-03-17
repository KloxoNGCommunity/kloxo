all:
#	rm -f closeinput
	rm -f kloxo.exe
	rm -f kloxo.o

	cc -static -o closeinput closeinput.c

#	chmod 755 closeinput
#	chmod ug+s closeinput

	gcc -o kloxo.exe kloxo.c -lssl
	cp kloxo.exe ../sbin/kloxo.exe
	cp kloxo.exe ../sbin/kloxo.exe.core

	rm -f kloxo.exe
	rm -f kloxo.o


#test:
#	./closeinput "ps ax > test.q"
