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

      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawCombo);

      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawCombo() {

        // Create the data table.
		var data = google.visualization.arrayToDataTable([
		    ['Name', 'Height', 'Smokes'],
		    ['Tong Ning mu', 174, true],
		    ['Huang Ang fa', 523, false],
		    ['Teng nu', 86, true]
		  ]);
		  
        // Set chart options
        // var options = {'title':'How Much Pizza I Ate Last Night',
        //                        'width':400,
        //                        'height':300};
	   var options = {
	     'legend':'left',
	     'title':'My Big Pie Chart',
	     'is3D':true,
	     'width':400,
	     'height':300
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
			print "['Key', 'OfferId', 'Offer Category', 'Offer Title']";
			?>
		  ]);
		
  	  	var dataTable = new google.visualization.Table(document.getElementById('table_div'));
  	  	dataTable.draw(tableData, null);
	  	
      }
	  
    </script>
  </head>

  <body>
    <!--Div that will hold the pie chart-->
    <div id="chart_div"></div>
	<div id="table_div"></div>
  </body>
</html>