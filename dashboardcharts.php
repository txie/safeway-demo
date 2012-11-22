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

        $column_family = new ColumnFamily($pool, 'dashboard_loc');
        $rows = $column_family->get_range("", "", 100, NULL);
    ?>
      google.load('visualization', '1.0', {'packages':['corechart', 'geochart', 'table']});

      google.setOnLoadCallback(drawCombo);

      function drawCombo() {
          drawPieChart();
          drawGeoMap();
          drawDataTable();
      }
      
      function drawPieChart() {
            // Create the data table.
            <?php
                $type2NumArray = array();
                $city2NumArray = array();
                
                foreach($rows as $key => $columns) {
                    foreach($columns as $k => $cs) {
                        if (array_key_exists($key, $offerTypeArray)) {
                            $type2NumArray[$key] = (int)$type2NumArray[$key] + (int)$cs;
                        }
                        else {
                            $type2NumArray[$key] = (int)$cs;
                        }
                        
                        if (array_key_exists($k, $city2NumArray)) {
                            $city2NumArray[$k] = (int)$city2NumArray[$k] + (int)$cs;
                        }
                        else {
                            $city2NumArray[$k] = (int)$cs;
                        }
                    }
                }
            ?>
            var type2NumData = google.visualization.arrayToDataTable([
                ['Offer Type', 'Number'],
                <?php
                    foreach($type2NumArray as $key => $value) {
                        print "['$key', $value],";
                    }
                ?>
              ]);
              
             // Set chart options
            var type2NumOptions = {
             'legend.position' : 'right',
             'title' : 'Distribution By Offer Type',
             'is3D' : true,
             'width' : 800,
             'height' : 500
           }

              // Instantiate and draw our chart, passing in some options.
              var type2NumChart = new google.visualization.PieChart(document.getElementById('chartByType_div'));

              function selectHandler() {
                var selectedItem = type2NumChart.getSelection()[0];
                if (selectedItem) {
                  var topping = data.getValue(selectedItem.row, 0);
                  alert(' ' + topping);
                }
              }
             google.visualization.events.addListener(type2NumChart, 'select', selectHandler);    
             type2NumChart.draw(type2NumData, type2NumOptions);      
             

             // city2Num pie chart
             var city2NumData = google.visualization.arrayToDataTable([
                 ['City', 'Number'],
                <?php
                    foreach($city2NumArray as $key => $value) {
                        print "['$key', $value],";
                    }
                ?>
               ]);
              
              // Set chart options
             var city2NumOptions = {
              'legend.position' : 'right',
              'title' : 'Distribution By City',
              'is3D' : true,
              'width' : 800,
              'height' : 500
            }

               // Instantiate and draw our chart, passing in some options.
               var city2NumChart = new google.visualization.PieChart(document.getElementById('chartByCity_div'));

               function selectHandler() {
                 var selectedItem = type2NumChart.getSelection()[0];
                 if (selectedItem) {
                   var topping = data.getValue(selectedItem.row, 0);
                   alert(' ' + topping);
                 }
               }
              google.visualization.events.addListener(city2NumChart, 'select', selectHandler);    
              city2NumChart.draw(city2NumData, city2NumOptions);                 
    
      }
      
      function drawGeoMap() {
          var geoData = google.visualization.arrayToDataTable([
              ['City', 'Offer Numbers'],
                <?php
                    foreach($rows as $key => $columns) {
                        foreach($columns as $k => $cs) {
                            print "['$k', $cs],";
                        }
                    }
                    print "['Seattle', 100]";
                ?>            
              ]);
          var options = {
                region: 'US',
                displayMode: 'markers',
                colorAxis: {colors: ['green', 'blue']}
          };

          var geoMap = new google.visualization.GeoChart(document.getElementById('map_div'));
          geoMap.draw(geoData, options);
      }
      
      function drawDataTable() {       
            // data table
            var tableData = google.visualization.arrayToDataTable([
                ['Offer Path', 'City', 'Number'],
                <?php
                    foreach($rows as $key => $columns) {
                        foreach($columns as $k => $cs) {
                            print "['$key', '$k' , '$cs'],";
                        }
                    }
                    print "['', '', '']";
                ?>
          ]);
        
        var dataTable = new google.visualization.Table(document.getElementById('table_div'));
        dataTable.draw(tableData, null);        
      }
      
    </script>
  </head>

  <body>
    <div id="chartByType_div"></div>
    <div id="chartByCity_div"></div>
    <div id="map_div"></div>
    <div id="table_div"></div>
  </body>
</html>