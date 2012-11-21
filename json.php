<?php
// $json = '{"a":1,"b":2,"c":3,"d":4,"e":5}';
$json = '{"a":"Alice", "b":"Bob", "c":"Cindy", "d":"David", "e":"Ella"}';
var_dump(json_decode($json));
// var_dump(json_decode($json, true));
$text = "text";
echo "abcd $text";
$obj = json_decode($json);
echo $obj->{'a'};
print $obj->{'a'};
$bob = $obj->{'b'};
print "bob = $bob";
// print "($obj->{'a'})";
// echo "foo is " + (string)$obj->{'a'} + "---" + (string)$obj->{'b'};
// print "foo is $obj->{'a'} --- $obj->{'b'}";
?>

