<?php
    require('phpcassa/lib/autoload.php');

    use phpcassa\ColumnSlice;
    use phpcassa\Connection\ConnectionPool;
    use phpcassa\ColumnFamily;
    use phpcassa\SystemManager;
    use phpcassa\Schema\StrategyClass;

    $servers = array("10.5.14.58:9160");
    $pool = new ConnectionPool("client_logging", $servers);

    $column_family = new ColumnFamily($pool, 'device_log');
    $column_names = array('1353361570', '1353361571');
    // $rows = $column_family->get('9004002004606', $column_names=$column_names);
    $rows = $column_family->get('9004002004606');
    // $rows = $column_family->get($rowkey=0, new ColumnSlide("", "", 5000));
	// $rows = $column_family->get_range("", "", 100000, NULL, "", "", true, 0);
    // print_r($rows);
	
    echo "<table>";
    echo "<tr><td>Offer Id</td><td>Offer Category</td><td>Offer Title</td></tr>";
    foreach($rows as $key => $columns) {
        // echo "Key:$key; Value: $columns";
        $obj = json_decode($columns);
        $offer_id = $obj->{'offer_id'};
        $offer_category = $obj->{'offer_category'};
        $offer_title = $obj->{'offer_title'};
        print "<tr><td>$offer_id</td><td>$offer_category</td><td>$offer_title</td></tr>";
        // echo "<tr><td>" + $obj["offer_id"] + "</td><td>" + $obj["offer_category"] + "</td><td>" + $obj["offer_title"] + "</td></tr>";
    }
    echo "</table>";
?>


