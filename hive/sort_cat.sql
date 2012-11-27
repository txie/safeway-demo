use client_logging;
DROP TABLE IF EXISTS category_by_device;
create EXTERNAL table category_by_device 
     (row_key string, category string, view_cnt int)
      STORED BY 'org.apache.hadoop.hive.cassandra.CassandraStorageHandler'
      with SERDEPROPERTIES ("cassandra.columns.mapping"=":key,:column,:value","cassandra.host"="10.5.14.179") TBLPROPERTIES ( "cassandra.ks.name" = "client_logging" );
INSERT OVERWRITE TABLE client_logging.category_by_device  select deviceid, get_json_object(event, '$.offer_category'), count(*) as cnt from client_logging.device_log where ts > (UNIX_TIMESTAMP() - 3600 ) and get_json_object(event, '$.offer_category') is not null group by deviceid, get_json_object(event, '$.offer_category');

