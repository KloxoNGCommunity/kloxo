#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <string.h>

int main(int argc, char **argv)
{
	int i;

	for(i = 3; i< 1024; i++) {
		close(i);
	}

	if (argc > 1) {
		system(argv[1]);
	}
}
