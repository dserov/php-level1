<?php

$a = 1;
$b = 2;

echo "\$a=$a, \$b=$b" . PHP_EOL;

$a = ($a + $b); // 3
$b = ($a - $b); // 3 - 2
$a = ($a - $b); // 3 - 1

echo "\$a=$a, \$b=$b" . PHP_EOL;
