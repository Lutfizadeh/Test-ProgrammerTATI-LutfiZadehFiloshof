<?php
$i;
$j;
$n;

function helloworld($n) {
  for ($i = 1; $i <= $n; $i++) {
    echo "helloworld(" . $i . ") => ";
    for ($j = 1; $j <= $i; $j++) {
      if ($j % 4 == 0 && $j % 5 == 0) {
        echo "helloworld ";
        continue;
      } else if ($j % 4 == 0) {
        echo "hello ";
        continue;
      } else if ($j % 5 == 0) {
        echo "world ";
        continue;
      }
      echo $j . " ";
    }
    echo "\n";
  }
}

helloworld(22);
?>
