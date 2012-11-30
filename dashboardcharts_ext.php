<html>
  <head>
    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">

    <?php
        require('phpcassa/lib/autoload.php');

        use phpcassa\ColumnSlice;
        use phpcassa\Connection\ConnectionPool;
        use phpcassa\ColumnFamily;
        use phpcassa\SystemManager;
        use phpcassa\Schema\StrategyClass;

        $servers = array("10.5.14.58:9160");
        $pool = new ConnectionPool("client_logging", $servers);

        $column_family = new ColumnFamily($pool, 'dashboard_ext');
        $rows = $column_family->get_range("", "", 100, NULL);
    ?>
 	
	google.load('visualization', '1.0', {'packages':['corechart', 'geochart', 'table', 'motionchart']});
	google.setOnLoadCallback(drawCombo);

      function drawCombo() {
		  drawMotionChart();
      }
      
	  // motion chart
	  function drawMotionChart() {
		  var data = new google.visualization.DataTable();
		  data.addColumn('string', 'Offer Type');
		  data.addColumn('date', 'Date');
		  data.addColumn('number', 'Offer');
		  data.addColumn('number', 'Expense');		  
		  data.addColumn('string', 'City');
		  
		  data.addRows([
			  <?php
			  	foreach($rows as $key => $columns) {
					$datePieces = explode("-", $key);
					$dateConstructString = "new Date(".$datePieces[0].",".$datePieces[1].",".$datePieces[2].")";
			  		foreach($columns as $k => $cs) {
						$ks = explode("|", $k);
			  			print "['$ks[0]', $dateConstructString, $cs, 200, '$ks[1]'],";
			  		}
			  	}
			?>
			]);
		  var motionChart = new google.visualization.MotionChart(document.getElementById('motion_div'));
		  motionChart.draw(data, {'width': 800, 'height': 400});
	  }
      
    </script>
  </head>

  <body>
	<div id="motion_div"></div>
  </body>
</html>