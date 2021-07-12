import os, collections, signal, sys, subprocess, socket
from time import sleep

cardfile = open('/sbin/piforce/card_emulator/currentcard.txt', 'w')
cardfile.write(sys.argv[1])
file.close

lastpidfile = open('/sbin/piforce/card_emulator/pid.txt')
lastpid = file.readline(lastpidfile)
lastpidfile.close

try:
  os.kill(int(lastpid), signal.SIGKILL)
except:
  pass