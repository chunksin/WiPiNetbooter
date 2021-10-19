import os, collections, signal, sys, subprocess, socket
import triforcetools
import psutil
import RPi.GPIO as GPIO
import glob
from time import sleep

def checkprocess(process):
    for proc in psutil.process_iter():
        try:
            if process.lower() in proc.name().lower():
                return True
        except (psutil.NoSuchProcess, psutil.AccessDenied, psutil.ZombieProcess):
            pass
    return False

def exists(path):
    try:
        os.stat(path)
    except OSError:
        return False
    return True

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

emufile = open('/sbin/piforce/emumode.txt')
emumode = file.readline(emufile)
emufile.close

if (singlemode == 'single'):
    sleep(5)

activedimm = sys.argv[2]

try:
    os.kill(int(lastpid), signal.SIGKILL)
except:
    pass

currentpid = os.getpid()

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
print
bashCommand2 = 'sudo echo -n '+sys.argv[1]+' '+sys.argv[2]+' | tee /var/www/logs/log.txt'
os.system(bashCommand2)
print
bashCommand3 = 'sudo echo Sending Game ...'
os.system(bashCommand3)

rom_dir = '/boot/roms/'
romfile = rom_dir+sys.argv[1]

if ('monster_ride' in romfile or 'cycraft' in romfile):
    cycraftCommand = 'sudo /usr/lib/cycraft &'
    os.system(cycraftCommand)

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

                if (emumode == 'auto' and 'initial_d' in romfile and not checkprocess('cardemu')):
                    if exists('/dev/COM1'):
                        compath = os.readlink('/dev/COM1')
                        devices = glob.glob('/dev/ttyUSB*')
                        for device in devices:
                            if (device != compath):
                                emuport = device
                                break
                    else:
                        emuport = '/dev/ttyUSB0'

                    if ('initial_d_3' in romfile):
                        IDMode = 'id3'
                    elif ('initial_d_2' in romfile):
                        IDMode = 'id2'
                    else:
                        IDMode = 'idas'
                    emuCommand = 'sudo python3 /sbin/piforce/card_emulator/idcardemu.py -cp '+emuport+' -m '+IDMode+' &'
                    os.system(emuCommand)

                # time hack mode
                if (sys.argv[4] == 'hackon'):
                    while 1:
                       triforcetools.TIME_SetLimit(10*60*1000)
                       sleep(5)
                sleep(5)
                triforcetools.disconnect()
		exit()