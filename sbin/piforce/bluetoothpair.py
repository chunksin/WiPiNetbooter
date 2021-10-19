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

command = sys.argv[1]
mac = sys.argv[2]
if command == 'add':
    proc1 = subprocess.run(['bluetoothctl', 'pair', mac], shell=False)
    time.sleep(3)
    proc2 = subprocess.run(['bluetoothctl', 'connect', mac], shell=False)
    time.sleep(3)
    proc3 = subprocess.Popen(['bluetoothctl', 'trust', mac], shell=False)
    time.sleep(3)
    proc3.terminate()
if command == 'remove':
    proc1 = subprocess.run(['bluetoothctl', 'untrust', mac], shell=False)
    time.sleep(3)
    proc2 = subprocess.run(['bluetoothctl', 'disconnect', mac], shell=False)
    time.sleep(3)
    proc3 = subprocess.Popen(['bluetoothctl', 'remove', mac], shell=False)
    time.sleep(3)
    proc3.terminate()
