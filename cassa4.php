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
    $rows = $column_family->get_range("", "", 100, NULL);
    
    echo "<table>";
    echo "<tr><td>Key</td><td>Offer Id</td><td>Offer Category</td><td>Offer Title</td></tr>";
    foreach($rows as $key => $columns) {
        foreach($columns as $k => $cs) {
            $obj = json_decode($cs);
            $offer_id = $obj->{'offer_id'};
            $offer_category = $obj->{'offer_category'};
            $offer_title = $obj->{'offer_title'};
            print "<tr><td>$key</td><td>$offer_id</td><td>$offer_category</td><td>$offer_title</td></tr>";
        }
    }
?>
