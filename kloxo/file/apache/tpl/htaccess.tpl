### begin content - please not remove this line

### MR -- attention
### 1. Move '#<Ifmodule !mod_php5.c>' until '#</Ifmodule>' on
###    above '###Start Kloxo PHP config Area'
### 2. Remove # in front of '#<Ifmodule !mod_php5.c>' and '#</Ifmodule>'
###    on point (1)
### 3. Remove # in front of 'AddHandler x-httpd-php52' to activate php 5.2 as
###    secondary-php on point (1)
### 4. Or Remove # in front of 'AddHandler x-httpd-php' to activate php branch as
###    secondary-php on point (1)
### 5. Copy '#<Ifmodule !mod_php5.c>' until '#</Ifmodule>' to subdirectory and then
###    follow point (1)-(4) if want certain php version active in subdirectory

#<Ifmodule !mod_php5.c>
    #AddHandler x-httpd-php .php
    #AddHandler x-httpd-php52 .php
    #AddHandler x-httpd-php53 .php
    #AddHandler x-httpd-php54 .php
    #AddHandler x-httpd-php55 .php
    #AddHandler x-httpd-php56 .php
#</Ifmodule>

### end content - please not remove this line
