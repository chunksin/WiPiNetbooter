import os, collections, signal, sys, subprocess, socket
import triforcetools
import RPi.GPIO as GPIO
from time import sleep

lastpidfile = open('/sbin/piforce/pid.txt')
lastpid = file.readline(lastpidfile)
lastpidfile.close

openjvsfile = open('/sbin/piforce/openmode.txt')
openjvs = file.readline(openjvsfile)
openjvsfile.close

bootfile = open('/sbin/piforce/bootfile.txt')
singlemode = file.readline(bootfile)
bootfile.close

ffbfile = open('/sbin/piforce/ffbmode.txt')
ffbmode = file.readline(ffbfile)
ffbfile.close

if (singlemode == 'single'):
  sleep(5)

activedimm = sys.argv[2]

try:
  os.kill(int(lastpid), signal.SIGKILL)
except:
  pass

currentpid = os.getpid()
print(currentpid)

if (openjvs == 'openon'):
  openjvsCommand1 = 'killall -9 openjvs'
  os.system(openjvsCommand1)
  openjvsCommand2 = 'sudo openjvs '+sys.argv[5]+' &'
  os.system(openjvsCommand2)

if (ffbmode == 'ffbon'):
  ffbCommand1 = 'killall -9 openffb'
  os.system(ffbCommand1)
  ffbCommand2 = 'sudo openffb -h=0 -gp='+sys.argv[6]+' &'
  os.system(ffbCommand2)

bashCommand1 = 'sudo echo -n '+str(currentpid)+' | tee /sbin/piforce/pid.txt'
os.system(bashCommand1)
bashCommand2 = 'sudo echo -n '+sys.argv[1]+' '+sys.argv[2]+' | tee /var/www/logs/log.txt'
os.system(bashCommand2)

rom_dir = '/boot/roms/'
romfile = rom_dir+sys.argv[1]

while True:
                # relay mode
                if (sys.argv[3] == 'relayon'):
                   GPIO.setmode(GPIO.BOARD)
                   GPIO.setup(40, GPIO.OUT)
                   GPIO.output(40,1)
                   sleep(0.4)
                   GPIO.output(40,0)
                   sleep(2.0)
                try:
                    triforcetools.connect(activedimm, 10703)
                except:
                    continue
                triforcetools.HOST_SetMode(0, 1)
                triforcetools.SECURITY_SetKeycode("\x00" * 8)
                triforcetools.DIMM_CheckOff()
                triforcetools.DIMM_UploadFile(romfile)
                triforcetools.HOST_Restart()
                triforcetools.TIME_SetLimit(10*60*1000)
                # time hack mode
                if (sys.argv[4] == 'hackon'):
                    while 1:
                       triforcetools.TIME_SetLimit(10*60*1000)
                       sleep(5)
                sleep(5)
                triforcetools.disconnect()
		exit()
				

