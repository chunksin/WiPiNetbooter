import os, collections, signal, sys, subprocess, socket
import triforcetools
from time import sleep

fw_dir = '/boot/config/firmware/'
activedimm = sys.argv[2]

while True:

                try:
                    triforcetools.connect(activedimm, 10703)
                except:
                    continue

                triforcetools.HOST_SetMode(0, 1)
                triforcetools.SECURITY_SetKeycode("\x00" * 8)
                triforcetools.DIMM_UploadFile(fw_dir+sys.argv[1])
                triforcetools.HOST_Restart()
                triforcetools.TIME_SetLimit(10*60*1000)
                triforcetools.disconnect()

                sleep(5)
		exit()
				

