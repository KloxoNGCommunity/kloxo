### begin content - please not remove this line

### MR -- attention
### 1. Move '#<Ifmodule !mod_php5.c>' until '#</Ifmodule>' on
###    above '###Start Kloxo PHP config Area'
### 2. Remove # in front of '#<Ifmodule !mod_php5.c>' and '#</Ifmodule>'
###    on point (1)
### 3. Remove # in front of 'AddHandler x-httpd-php52' to activate secondary-php
###    on point (1)
### 4. Or Remove # in front of 'AddHandler x-httpd-php' to activate primary-php
###    on point (1) if select suphp_worker/_event for primary-php

#<Ifmodule !mod_php5.c>
    #AddHandler x-httpd-php52 .php
    #AddHandler x-httpd-php .php
#</Ifmodule>

### end content - please not remove this line
