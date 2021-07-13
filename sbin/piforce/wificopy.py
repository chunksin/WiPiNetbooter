import os,sys

wififile = open('/boot/wifi.txt', 'w')
wifi = '"'+sys.argv[1]+'" "'+sys.argv[2]+'"'
clean = wifi.replace("\\", "")
wififile.write(clean)
wififile.close