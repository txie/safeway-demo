<?php
	$bytes = array(255, 0, 55, 42, 17);
	$string = implode(array_map("chr", $bytes));
	echo "string:".$string."\n";
	
	$var = 0xf2;
	$str = strval($var);
	echo "str:".$str."\n";
?>