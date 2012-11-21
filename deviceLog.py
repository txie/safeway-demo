import time, sys
import pycassa
import json
from random import randint
from pycassa.pool import ConnectionPool

gKeyspace = 'client_logging'
gColumnFamily = 'device_log'
gSleepSeconds = 1;

# Tao and Wanchun's iPhone UDID
deviceIdList = [
    '272cf69ab0364686a6779caac9980e02a7447446',     # Tao
    'cda388dad47a8f75886443e901226622deea95cb'      # Wanchun
]
# 2e5adcab0006a507440c6b075740c38ab14e3e5d #device id of SWY iPad

geoDicts = [
    {'latitude': '37.696796', 'longitude': '-121.939036'},      # San Ramon
    {'latitude': '37.490767', 'longitude': '-121.929392'},      # Fremont
    {'latitude': '39.099727', 'longitude': '-94.578567'},       # Kansas City
    {'latitude': '36.139558', 'longitude': '-115.175986'},      # Las Vegas
    {'latitude': '34.052234', 'longitude': '-118.243685'},      # Los Angeles
    {'latitude': '49.261226', 'longitude': '-123.113927'},      # Vancouver, BC
    {'latitude': '27.950575', 'longitude': '-82.457178'},       # Tampa
    {'latitude': '42.358431', 'longitude': '-71.059773'},       # Boston
    {'latitude': '43.883081', 'longitude': '-85.791094'},       # Chicago
    {'latitude': '40.760779', 'longitude': '-111.891047'},      # Salt Lake City
    {'latitude': '39.737567', 'longitude': '-104.984718'},      # Denver
    {'latitude': '32.802955', 'longitude': '-96.769923'},       # Dallas
    {'latitude': '33.448377', 'longitude': '-112.074037'}       # Phoenix
]

def usage():
    print ''
    print ''
    print 'python' + sys.argv[0] + '[select|insert]'
    print 'pump data to ' + gKeyspace + '/' + gColumnFamily + ' cassandra'
    print 'OR'
    print 'return data from device_log CF'
    print ''

# Print object of given row_key (timestamp)
def deviceData(row_key):
    print '[row_key]: ' + row_key
    entries = deviceLogCF.get(row_key)

    for key in entries:
        print "[entry key]: " + key;
        try:
            obj = json.loads(entries[key])   # json.loads(entries['1352933021']) # json format of string
            offerText = ""
            metaText = ""
            geoText = ""
            if 'offer_id' in obj and 'offer_category' in obj:
                offerText = "{" + obj['offer_id'] + ", " + obj['offer_category'] + ", " + obj['offer_title'] + "}"
            if 'channel' in obj and 'pageName' in obj:
                metaText = "{" + obj['channel'] + ", " + obj['pageName'] + "}"
            if 'latest_latitude' in obj and 'latest_longitude' in obj:
                geoText =  "(" + obj['latest_latitude'] + "," + obj['latest_longitude'] + ")"
            print obj['deviceId'] + metaText + offerText + geoText
        except ValueError as ve:
            print entries[key] + " Not the JSON data I expected."
    print ''
    print 'Subtotal entries: ' + str(len(entries))
            
# Insert data to device_log CF
# Sample:
# (u'1352923327', u'{"latest_longitude":"-121.939036","pageName":"mobile:safeway:savings:couponctr","latest_latitude":"37.696796","offer_id":"864644","userId":"300-367-1003007885","level":"1","offer_category":"Baby Care","deviceId":"272cf69ab0364686a6779caac9980e02a7447446","offer_title":"O Organics","ts":"1352923327","visitorID":"272cf69ab0364686a6779caac9980e02a7447446","channel":"mobile:savings"}')

def insertData(deviceId, channelName, pageName, offerId, offerCategory, offerTitle, latitude, longitude):
    ts = str(int(round(time.time())))
    # col_fam.insert('row_key', {'col_name': 'col_val'})
    # col_fam.insert(deviceId_string, {timestamp_string:json_string})
    offerDict = {'offer_id' : offerId, 'offer_category' : offerCategory, 'offer_title' : offerTitle}
    metaDict = {'level' : 1, 'deviceId' : deviceId, 'channel' : channelName, 'pageName' : pageName}
    geoDict = {'latest_latitude' : latitude, 'latest_longtitude' : longitude}
    completeDict = dict(offerDict.items() + metaDict.items() + geoDict.items())
    valueStr = json.dumps(completeDict)
    deviceLogCF.insert(deviceId, {ts: valueStr})
    print 'insert ' + deviceId + ' ...'

# cookie monster in Fremont
def cookieMonster(deviceId=deviceIdList[1], num=10, latitude=geoDicts[1]['latitude'], longitude=geoDicts[1]['longitude']):
    for i in range (0, num):
        for j in range (0, len(cookieMonsterDict)):
            time.sleep(gSleepSeconds)
            print "[CookieMonster] inserting data to cassandra " + gKeyspace + '/' + gColumnFamily + ' ...'
            insertData(deviceId, channelList[1], pageNameList[2], cookieMonsterDict[j]['offer_id'], cookieMonsterDict[j]['offer_category'], cookieMonsterDict[j]['offer_title'], latitude, longitude)
        
# meat lover in San Ramon
def meatLover(deviceId=deviceIdList[0], num=10, latitude=geoDicts[0]['latitude'], longitude=geoDicts[0]['longitude']):
    for i in range (0, num):
        for j in range (0, len(meatLoverDict)):
            time.sleep(gSleepSeconds)
            print "[MeatLover] inserting data to cassandra " + gKeyspace + '/' + gColumnFamily + '...'
            insertData(deviceId, channelList[i%len(channelList)], pageNameList[i%len(pageNameList)], meatLoverDict[j]['offer_id'], meatLoverDict[j]['offer_category'], meatLoverDict[j]['offer_title'], latitude, longitude);
        
# drink junkie
def drinkJunkie(deviceId, num, latitude, longitude):
    for i in range (0, num):
        for j in range (0, len(drinkJunkieDict)):
            time.sleep(gSleepSeconds)
            print "[DrinkJunkie] inserting data to cassandra " + gKeyspace + '/' + gColumnFamily + "..."
            insertData(deviceId, channelList[1], pageNameList[2], drinkJunkieDict[j]['offer_id'], drinkJunkieDict[j]['offer_category'], drinkJunkieDict[j]['offer_title'], latitude, longitude)
        
def profile1():
    meatLover(num=100)
    cookieMonster(num=100)

def profile2():
    meatLover(num=200)
    cookieMonster(num=200)
    drinkJunkie(deviceId=deviceIdList[0], num=10, latitude=geoDicts[0].latitude, longitude=geoDicts[0].longitude)
    drinkJunkie(deviceId=deviceIdList[1], num=10, latitude=geoDicts[1].latitude, longitude=geoDicts[1].longitude)

# by default, 10 deviceId per geo location.
def profileGeoDistribute(numPerGeo=10):
    deviceIdStart = 9000
    for i in range (0, len(geoDicts)):
        for j in range(0, numPerGeo): 
            _deviceId = str(deviceIdStart + i) + "00" + str(j) + "00" + str(randint(1001, 9999))
            _latitude = geoDicts[i]['latitude']
            _longitude = geoDicts[i]['longitude']
        
            print ""
            print "deviceId:" + _deviceId + ", location: (" + _latitude + ", " + _longitude + ")"
            print "-----------------------------------------------------------------------------"
            meatLover(deviceId=_deviceId, num=1, latitude=_latitude, longitude=_longitude)
            cookieMonster(deviceId=_deviceId, num=1, latitude=_latitude, longitude=_longitude)
            drinkJunkie(deviceId=_deviceId, num=1, latitude=_latitude, longitude=_longitude)
        
# Main Program #

# sample feed data

offerDicts = [
    {'offer_id' : '864644', 'offer_category' : 'Baby Care', 'offer_title' : 'O Organics'}, 
    {'offer_id' : '831657', 'offer_category' : 'Beverages', 'offer_title' : 'Ocean Spray Cranberry Juice Cocktail}'},
    {'offer_id' : '836736', 'offer_category' : 'Beverages', 'offer_title' : 'Safeway Orange Juice'},
    {'offer_id' : '805017', 'offer_category' : 'Bread & Bakery', 'offer_title' : 'Pumpkin Pie'},
    {'offer_id' : '804534', 'offer_category' : 'Bread & Bakery', 'offer_title' : 'Artisan French Bread'},
    {'offer_id' : '832206', 'offer_category' : 'Bread & Bakery', 'offer_title' : 'Dave\'s Killer Bread'},
    {'offer_id' : '804685', 'offer_category' : 'Cookies, Snacks & Candy', 'offer_title' : 'Any Potato or Tortilla Chips Purchase'},
    {'offer_id' : '805317', 'offer_category' : 'Meat & Seafood', 'offer_title' : 'Safeway SELECT Fresh Grade A Turkey'}
]

cookieMonsterDict = [
    {'offer_id' : '805017', 'offer_category' : 'Bread & Bakery', 'offer_title' : 'Pumpkin Pie'},
    {'offer_id' : '804534', 'offer_category' : 'Bread & Bakery', 'offer_title' : 'Artisan French Bread'},
    {'offer_id' : '804685', 'offer_category' : 'Cookies, Snacks & Candy', 'offer_title' : 'Any Potato or Tortilla Chips Purchase'}    
]

meatLoverDict = [
    {'offer_id' : '805317', 'offer_category' : 'Meat & Seafood', 'offer_title' : 'Safeway SELECT Fresh Grade A Turkey'},
    {'offer_id' : '32282405', 'offer_category' : 'Meat & Seafood', 'offer_title' : 'Rancher\'s Reserve Boneless New York Strip Carver\'s Tradition'},
    {'offer_id' : '32282425', 'offer_category' : 'Meat & Seafood', 'offer_title' : 'Johnsonville Breakfast Sausage Patties'},
    {'offer_id' : '32282415', 'offer_category' : 'Meat & Seafood', 'offer_title' : 'Safeway Shank Half Ham'}    
]

drinkJunkieDict = [
    {'offer_id' : '831657', 'offer_category' : 'Beverages', 'offer_title' : 'Ocean Spray Cranberry Juice Cocktail}'},
    {'offer_id' : '836736', 'offer_category' : 'Beverages', 'offer_title' : 'Safeway Orange Juice'},    
]

channelList = [
    'mobile:Your Club Specials', 
    'mobile:Weekly Specials', 
    'mobile:savings'
]
    
pageNameList = [
    'mobile:safeway:savings', 
    'mobile:safeway:savings:couponctr', 
    'mobile:safeway:savings:personaldeal', 
    'mobile:safeway:savings:clubspecial', 
    'mobile:safeway:Weekly Specials', 
    'mobile:safeway:Your Club Specials'
]

print 'Connecting to Cassandra ' + gKeyspace + '/' + gColumnFamily + '...'
pool = ConnectionPool(gKeyspace, ['10.5.14.58:9160'])
deviceLogCF = pycassa.ColumnFamily(pool, gColumnFamily) 
print 'Connected to ' + gKeyspace + '/' + gColumnFamily

if len(sys.argv) < 2:
    usage()
    sys.exit()

operation = sys.argv[1]

option = "full"
if (len(sys.argv) >= 3):
    option = sys.argv[2]

if (operation == 'insert'):
    # profile1()
    # profile2()
    profileGeoDistribute()
    
elif (operation == 'select'):    
    # iterate for each row_key
    for value in deviceLogCF.get_range(column_count=0, filter_empty=False):
        print 'row_key: ' + value[0]
        if option != 'concise':
            deviceData(value[0]);
else:
    usage()
    sys.exit()
    
# End #
