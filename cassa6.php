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
    foreach($rows as $key => $columns) {
        foreach($columns as $k => $cs) {
			var_dump($cs);
			$num = "$cs";
            // print "<tr><td>$key</td><td>$k</td><td>$num</td></tr>";
        }
    }
?>
