import os, collections, signal, sys, subprocess, socket
from time import sleep

bootfile = open('/sbin/piforce/bootfile.txt')
bootmode = file.readline(bootfile)
bootfile.close
bootromfile = open('/var/www/logs/log.txt')
bootrom = file.readline(bootromfile)
bootromfile.close
powerfile = open('/sbin/piforce/powerfile.txt')
powermode = file.readline(powerfile)
powerfile.close
nfcfile = open('/sbin/piforce/nfcmode.txt')
nfcmode = file.readline(nfcfile)
nfcfile.close

if (nfcmode == 'nfcon'):
  cp = subprocess.Popen(['python3', '/sbin/piforce/card_emulator/nfcread.py'])

if os.path.exists('/boot/wifi.txt') == True:
  wififile = open('/boot/wifi.txt')
  wifi = file.readline(wififile)
  wififile.close
  if (wifi != ''):
    homewifi = 'sudo python /sbin/piforce/homewifi.py '+wifi
    os.system(homewifi)
if os.path.exists('/boot/reset.txt') == True:
  os.remove('/boot/reset.txt')
  hotspot = 'sudo python /sbin/piforce/hotspotrestore.py'
  os.system(hotspot)
  reboot = 'sudo reboot'
  os.system(reboot)
relayfile = open('/sbin/piforce/relaymode.txt')
relaymode = file.readline(relayfile)
relayfile.close
zerofile = open('/sbin/piforce/zeromode.txt')
zeromode = file.readline(zerofile)
zerofile.close

if (bootmode == 'single'):
  cmd = 'sudo python /sbin/piforce/webforce.py '+bootrom+' '+relaymode+' '+zeromode
  scriptfile = open('/var/www/logs/scriptlog.txt', 'w')
  scriptfile.write('Last command run - '+cmd)
  scriptfile.close()
  os.system(cmd)
  
if (powermode == 'auto-off'):
  shutdwn = 'sudo shutdown -h -t 600'
  os.system(shutdwn)

