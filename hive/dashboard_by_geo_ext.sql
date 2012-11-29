USE client_logging;
DROP TABLE IF EXISTS dashboard_loc;
CREATE EXTERNAL TABLE dashboard_loc (actionDate STRING, api STRING, locationId STRING, cnt STRING)
      STORED BY 'org.apache.hadoop.hive.cassandra.CassandraStorageHandler'
      WITH SERDEPROPERTIES ("cassandra.columns.mapping"=":key,:column,:value","cassandra.host"="10.5.14.179") TBLPROPERTIES ( "cassandra.ks.name" = "client_logging" );

INSERT OVERWRITE TABLE dashboard_loc
SELECT from_unixtime(l.ts, 'yyyy-MM-dd'), get_json_object(l.event, '$.pageName'), c.city,count(*) FROM device_log l JOIN city_geo c ON get_json_object(l.event, '$.latest_latitude') = c.latitude WHERE  get_json_object(l.event, '$.latest_longtitude') IS NOT NULL AND get_json_object(l.event, '$.latest_latitude') IS NOT NULL GROUP BY get_json_object(l.event, '$.pageName'), c.city, from_unixtime(l.ts, 'yyyy-MM-dd');