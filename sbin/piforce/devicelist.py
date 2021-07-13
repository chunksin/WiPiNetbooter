import evdev, os

scriptfile = open('/var/www/html/devicelist.php', 'w+')

i = 0
j = 0
scriptfile.write('<?php\n')
devices = [evdev.InputDevice(path) for path in evdev.list_devices()]
for device in devices:
   i = i+1
   path = device.path
   name = device.name
   file = name.replace(' ','-').lower()
   filepath = '/etc/openjvs/devices/'+file
   print(device.path, device.name)
   scriptfile.write('$path'+str(i)+' = \''+path+'\';\n')
   scriptfile.write('$name'+str(i)+' = \''+name+'\';\n')
   scriptfile.write('$file'+str(i)+' = \''+filepath+'\';\n')
   if os.path.exists(filepath) == True:
     j = j+1
scriptfile.write('$devices = '+str(i)+';\n')
scriptfile.write('$enableddevices = '+str(j)+';\n')
scriptfile.write('?>')