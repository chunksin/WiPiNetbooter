from wifi import Cell, Scheme
ssids = Cell.all('wlan0')
scriptfile = open('/var/www/html/wifilist.php', 'w+')
i = 0
scriptfile.write('<?php\n')
for ssid in ssids:
   i = i+1
   name = ssid.ssid
   scriptfile.write('$name'+str(i)+' = \''+name+'\';\n')
scriptfile.write('$ssids = '+str(i)+';\n')
scriptfile.write('?>')