#!/usr/bin/python -u

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

# import stuff

import evdev, sys, time, csv, select
from evdev import *
import threading
from select import select
from multiprocessing import Queue
from os import system, name

# set unbuffered output to allow real time display in browser

sys.stdout = Unbuffered(sys.stdout)

# open the master mapping file and read into data dictionary

with open('/sbin/piforce/mastermapping.csv', mode='r') as infile:
 reader = csv.reader(infile)
 mappings = {rows[0]:(rows[1],rows[2],rows[3]) for rows in reader}

# define the input device

device = sys.argv[1]
dev = InputDevice(device)
capabilities = dev.capabilities()

# create a dictionary for maximum analogue values from the device capabilities - 3 is equal to analogue

i = 0
maxvalues = {}

# check if device has any analogue inputs and if so, capture the max values

analogcheck = capabilities.get(3, "NA")
if (analogcheck != 'NA'):
 while i < len(capabilities.get(3, "NA")):
  maxvalues[capabilities.get(3)[i][0]] = capabilities.get(3)[i][1].max
  i = i + 1

# define the device file to be written to based on the device name and print device info for debug

devicefile = '/etc/openjvs/devices/'+dev.name.replace(' ', '-').lower()
#print('Configuration file = '+devicefile)
#print(dev.capabilities(verbose=True))
print('<b>Configuring '+dev.name+' -- Press any input to begin</b><br>')
time.sleep(1)

# send a rumble to the controller if it supports it

if ecodes.EV_FF in dev.capabilities():
 rumble = ff.Rumble(strong_magnitude=0xc000, weak_magnitude=0xc000)
 effect_type = ff.EffectType(ff_rumble_effect=rumble)
 duration_ms = 1000
 effect = ff.Effect(
   ecodes.FF_RUMBLE, # type
   -1, # id (set by ioctl)
   0,  # direction
   ff.Trigger(0, 0), # no triggers
   ff.Replay(duration_ms, 0), # length and delay
   ff.EffectType(ff_rumble_effect=rumble)
 )
 effect_id = dev.upload_effect(effect)
 repeat_count = 1
 dev.write(ecodes.EV_FF, effect_id, repeat_count)
 time.sleep(0.5)
 dev.erase_effect(effect_id)

# create new data dictionary for the control mappings

controls = {}
stop_threads = False

# define event polling thread - analogue values need to be at least fifty percent of their max values to register

eventsq = Queue()
def worker():
    global stop_threads 
    for event in dev.read_loop():
        if event.type == ecodes.EV_KEY:
            eventsq.put(event)
        elif event.type == ecodes.EV_ABS:
          qabsevent = categorize(event)
          qcode = ecodes.bytype[qabsevent.event.type][qabsevent.event.code]
          if 'HAT' in qcode:
              eventsq.put(event)
          elif (event.value > (maxvalues[event.code]/2)) or (event.value < (maxvalues[event.code]/-2)):
              eventsq.put(event)
        if stop_threads: 
            break

# start polling thread

t = threading.Thread(target=worker)
t.start()
timeout = 5
skip = 1

# poll for any single input

while eventsq.empty():
   if not eventsq.empty():
     qevent = eventsq.get_nowait()
     time.sleep(0.05)

# flush the event queue

time.sleep(0.1)
while not eventsq.empty():
   eventsq.get()

# main loop to capture inputs with 5 second timeout and 50ms poll

for input in mappings:
   print('<tr><td>Press input for '+mappings[input][1]+' ('+input+')</td>')
   t_end = time.time() + timeout
   while time.time() < t_end:
     if not eventsq.empty():
       qevent = eventsq.get_nowait()
       if (qevent.type == ecodes.EV_KEY or qevent.type == ecodes.EV_ABS) and qevent.value != 1:
        if (qevent.type == ecodes.EV_KEY and mappings[input][2] == 'Digital'):
         keyevent = categorize(qevent)
         code = ecodes.bytype[keyevent.event.type][keyevent.event.code]
         if "BTN_SOUTH" in code:
           code = "BTN_SOUTH"
         elif "BTN_NORTH" in code:
           code = "BTN_NORTH"
         elif "BTN_EAST" in code:
           code = "BTN_EAST"
         elif "BTN_WEST" in code:
           code = "BTN_WEST"
         print('<td><b>'+code+'</b></td></tr>')
         if code in controls:
           controls[code] += ' '+input
         else:
           controls[code] = input
         time.sleep(0.5)
         while not eventsq.empty():
           eventsq.get()
         while dev.read_one() != None:
           pass
         skip = 0
         break
        elif ((qevent.type == ecodes.EV_ABS and mappings[input][2] == 'Analogue') or (qevent.type == ecodes.EV_ABS and qevent.code in range(10, 18))):
         absevent = categorize(qevent)
         code = ecodes.bytype[absevent.event.type][absevent.event.code]
         print('<td><b>'+code+'</b></td></tr>')
         if code in controls:
           controls[code] += ' '+input
         else:
           controls[code] = input
         time.sleep(0.5)
         while not eventsq.empty():
           eventsq.get()
         while dev.read_one() != None:
           pass
         skip = 0
         break
     time.sleep(0.05)
   if skip == 1:
     print('<td>Skipped</td></tr>')
   skip = 1


# print contents of mapped controls dictionary for debug

#print(controls)
print('<tr><td><b>Controller configuration complete, press any input to continue</b></td></tr>')

# stop polling thread and tidy up

stop_threads = True
t.join()
dev.close()

# write out controller mappings to the device file

w = csv.writer(open(devicefile, "w"),delimiter=' ',escapechar=' ',quoting=csv.QUOTE_NONE)
for key, val in controls.items():
    w.writerow([key, val])