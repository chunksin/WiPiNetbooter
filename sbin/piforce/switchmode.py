import os, collections, signal, sys, subprocess, socket
from time import sleep

if (sys.argv[1] == 'multi') or (sys.argv[1] == 'single'):
   bootfile = open('/sbin/piforce/bootfile.txt', 'w')
   bootfile.write(sys.argv[1])
   bootfile.close
   
if (sys.argv[1] == 'auto-off') or (sys.argv[1] == 'always-on'):
   bootfile = open('/sbin/piforce/powerfile.txt', 'w')
   bootfile.write(sys.argv[1])
   bootfile.close

if (sys.argv[1] == 'simple') or (sys.argv[1] == 'advanced'):
   bootfile = open('/sbin/piforce/menumode.txt', 'w')
   bootfile.write(sys.argv[1])
   bootfile.close

if (sys.argv[1] == 'relayon') or (sys.argv[1] == 'relayoff'):
   bootfile = open('/sbin/piforce/relaymode.txt', 'w')
   bootfile.write(sys.argv[1])
   bootfile.close

if (sys.argv[1] == 'hackon') or (sys.argv[1] == 'hackoff'):
   bootfile = open('/sbin/piforce/zeromode.txt', 'w')
   bootfile.write(sys.argv[1])
   bootfile.close

if (sys.argv[1] == 'openon') or (sys.argv[1] == 'openoff'):
   bootfile = open('/sbin/piforce/openmode.txt', 'w')
   bootfile.write(sys.argv[1])
   bootfile.close

if (sys.argv[1] == 'ffbon') or (sys.argv[1] == 'ffboff'):
   bootfile = open('/sbin/piforce/ffbmode.txt', 'w')
   bootfile.write(sys.argv[1])
   bootfile.close

if (sys.argv[1] == 'soundon') or (sys.argv[1] == 'soundoff'):
   bootfile = open('/sbin/piforce/soundmode.txt', 'w')
   bootfile.write(sys.argv[1])
   bootfile.close

if (sys.argv[1] == 'navon') or (sys.argv[1] == 'navoff'):
   bootfile = open('/sbin/piforce/navmode.txt', 'w')
   bootfile.write(sys.argv[1])
   bootfile.close

if (sys.argv[1] == 'manual') or (sys.argv[1] == 'auto'):
   bootfile = open('/sbin/piforce/emumode.txt', 'w')
   bootfile.write(sys.argv[1])
   bootfile.close

if (sys.argv[1] == 'nfcon') or (sys.argv[1] == 'nfcoff'):
   bootfile = open('/sbin/piforce/nfcmode.txt', 'w')
   bootfile.write(sys.argv[1])
   bootfile.close

if (sys.argv[1] == 'LCD16'):
   bashCommand1 = 'sudo echo -n LCD16 | tee /sbin/piforce/lcdmode.txt'
   os.system(bashCommand1)
   bashCommand2 = 'sudo systemctl enable lcd-piforce'
   os.system(bashCommand2)
   bashCommand3 = 'sudo cp /boot/config.txt.lcd16 /boot/config.txt'
   os.system(bashCommand3)
   sleep(5)
   shutdwn = 'sudo shutdown now'
   os.system(shutdwn)

if (sys.argv[1] == 'LCD35'):
   bashCommand1 = 'sudo echo -n LCD35 | tee /sbin/piforce/lcdmode.txt'
   os.system(bashCommand1)
   bashCommand2 = 'sudo systemctl disable lcd-piforce'
   os.system(bashCommand2)
   bashCommand3 = 'sudo cp /boot/config.txt.lcd35 /boot/config.txt'
   os.system(bashCommand3)
   sleep(5)
   shutdwn = 'sudo shutdown now'
   os.system(shutdwn)