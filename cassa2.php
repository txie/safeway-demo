<?php
    require('phpcassa/lib/autoload.php');

    use phpcassa\ColumnSlice;
    use phpcassa\Connection\ConnectionPool;
    use phpcassa\ColumnFamily;
    use phpcassa\SystemManager;
    use phpcassa\Schema\StrategyClass;

    // tutorial code starts here
    $servers = array("10.5.14.58:9160");
    $pool = new ConnectionPool("client_logging", $servers);

    $column_family = new ColumnFamily($pool, 'device_log');
    $rows = $column_family->get('9004002004606', $column_names=array('1353361570', '1353361571'));
	foreach ($rows as $key => $columns) {

	}
?>

