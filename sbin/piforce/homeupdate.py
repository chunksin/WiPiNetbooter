import subprocess
import os,sys,shutil
from time import sleep

shutil.copy('/etc/wpa_supplicant/wpa_supplicant.conf.new','/etc/wpa_supplicant/wpa_supplicant.conf')

ssid = sys.argv[1]
passkey = sys.argv[2]

p1 = subprocess.Popen(
    ["wpa_passphrase", ssid, passkey], 
    stdout=subprocess.PIPE
)

p2 = subprocess.Popen(
    ["sudo", "tee", "-a", "/etc/wpa_supplicant/wpa_supplicant.conf"], 
    stdin=p1.stdout, 
    stdout=subprocess.PIPE
)

p1.stdout.close()

output,err = p2.communicate()