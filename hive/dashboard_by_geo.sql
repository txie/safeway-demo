use client_logging;
DROP TABLE IF EXISTS dashboard_loc;
create EXTERNAL table dashboard_loc (api string, locationId string, cnt string)
      STORED BY 'org.apache.hadoop.hive.cassandra.CassandraStorageHandler'
      with SERDEPROPERTIES ("cassandra.columns.mapping"=":key,:column,:value","cassandra.host"="10.5.14.179") TBLPROPERTIES ( "cassandra.ks.name" = "client_logging" );

insert overwrite table dashboard_loc
select get_json_object(l.event, '$.pageName'), c.city,count(*) from device_log l join city_geo c on get_json_object(l.event, '$.latest_latitude') = c.latitude where  get_json_object(l.event, '$.latest_longtitude') is not null and get_json_object(l.event, '$.latest_latitude') is not null group by get_json_object(l.event, '$.pageName'), c.city ;

