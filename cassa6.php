<?php
    require('phpcassa/lib/autoload.php');

    use phpcassa\ColumnSlice;
    use phpcassa\Connection\ConnectionPool;
    use phpcassa\ColumnFamily;
    use phpcassa\SystemManager;
    use phpcassa\Schema\StrategyClass;

    $servers = array("10.5.14.58:9160");
    $pool = new ConnectionPool("client_logging", $servers);

    $column_family = new ColumnFamily($pool, 'dashboard_loc');
    $rows = $column_family->get_range("", "", 100, NULL);
    
    echo "<table>";
    echo "<tr><td>Offer Path</td><td>City</td><td>Number</td></tr>";
	$offerTypeArray = array();
    foreach($rows as $key => $columns) {
        foreach($columns as $k => $cs) {
			if (array_key_exists($key, $offerTypeArray)) {
				$offerTypeArray[$key] = (int)$offerTypeArray[$key] + (int)$cs;
			}
			else {
				$offerTypeArray[$key] = (int)$cs;
			}
        	print "<tr><td>$key</td><td>$k</td><td>$cs</td></tr>";
        }
    }
	print_r($offerTypeArray);
?>
