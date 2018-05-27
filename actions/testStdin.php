#!/usr/bin/php
<?php
system("stty -icanon");
echo "input# ";
while ($c = fread(STDIN, 1)) {
    echo "Read from STDIN: " . $c . "\ninput# ";
    if($c == 'a') break;
}
exit('=====[ end ]=====');