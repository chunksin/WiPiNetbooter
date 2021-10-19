#!/usr/bin/python -u
import subprocess
import time
import sys

class Unbuffered(object):
   def __init__(self, stream):
       self.stream = stream
   def write(self, data):
       self.stream.write(data)
       self.stream.flush()
   def writelines(self, datas):
       self.stream.writelines(datas)
       self.stream.flush()
   def __getattr__(self, attr):
       return getattr(self.stream, attr)

sys.stdout = Unbuffered(sys.stdout)

proc = subprocess.Popen(['script', '-q', '-c', '/sbin/piforce/bluetoothscan.sh', '-f', '/sbin/piforce/bluetoothscan.txt'], shell=False)
time.sleep(15)
proc.terminate()

# OLD METHOD
#
#scriptfile = open('/var/www/html/btlist.php', 'w+')
#scriptfile.write('<?php\n')
#i = 1
#with open('/sbin/piforce/bluetoothscan.txt') as f:
#    lines = f.readlines()
#for line in lines:
#    linelist = line.split(' ')
#    if 'NEW' in linelist[0] and linelist[1] == 'Device':
#        scriptfile.write('$mac'+str(i)+' = \''+linelist[2]+'\';\n')
#        device = ' '.join(linelist[3:len(linelist)])
#        name = device.rstrip('\n')
#        scriptfile.write('$name'+str(i)+' = \''+name+'\';\n')
#        i = i+1
#
#scriptfile.write('$devices'+' = '+str(i-1)+';\n')
#scriptfile.write('?>')
#scriptfile.close()