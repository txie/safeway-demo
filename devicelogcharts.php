<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
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

		$column_family = new ColumnFamily($pool, 'device_log');
		$rows = $column_family->get_range("", "", 100, NULL);
		?>
		// Load the Visualization API and the piechart package.
		google.load('visualization', '1.0', {'packages':['corechart', 'table']});

		google.setOnLoadCallback(drawCombo);

		function drawCombo() {
		  drawPieChart();
		  drawDataTable();
		}

		function drawPieChart() {
			<?php
				$category2NumArray = array();
				foreach($rows as $key => $columns) {
					foreach($columns as $k => $cs) {
						$obj = json_decode($cs);
						$offer_category = $obj->{'offer_category'};
                        if (array_key_exists($offer_category, $category2NumArray)) {
                            $category2NumArray[$offer_category] = (int)$category2NumArray[$offer_category] + 1;
                        }
                        else {
                            $category2NumArray[$offer_category] = 1;
                        }
					}
				}
			?>
			
			// Create the data table.
			var data = google.visualization.arrayToDataTable([
				['Category', 'Number'],
                <?php
                    foreach($category2NumArray as $key => $value) {
						$displayKey = ($key == '') ? 'Unknown' : $key;
                        print "['$displayKey', $value],";
                    }
                ?>
			  ]);
		  
			var options = {
			 'legend' : 'right',
			 'title' : 'Users Action Based on Offer Category',
			 'is3D' : true,
			 'width' : 800,
			 'height' : 500
			}
			// Instantiate and draw our chart, passing in some options.
			var chart = new google.visualization.PieChart(document.getElementById('chart_div'));

			function selectHandler() {
			  var selectedItem = chart.getSelection()[0];
			  if (selectedItem) {
				var topping = data.getValue(selectedItem.row, 0);
				alert('The user selected ' + topping);
			  }
			}
			google.visualization.events.addListener(chart, 'select', selectHandler);    
			chart.draw(data, options);
		}

		function drawDataTable() {
			var tableData = google.visualization.arrayToDataTable([
				['Key', 'OfferId', 'Offer Category', 'Offer Title'],
				<?php
					foreach($rows as $key => $columns) {
						foreach($columns as $k => $cs) {
							$obj = json_decode($cs);
							$offer_id = $obj->{'offer_id'};
							$offer_category = $obj->{'offer_category'};
							$offer_title = $obj->{'offer_title'};
							print "['$key', '$offer_id' , '$offer_category', \"$offer_title\"],";
						}
					}
				?>
			  ]);

			var dataTable = new google.visualization.Table(document.getElementById('table_div'));
			dataTable.draw(tableData, null);
		}
		</script>
		<title></title>
	</head>
	<body>
		<div id="chart_div"></div>
		<div id="table_div"></div>
	</body>
</html>
