import os,sys,shutil

shutil.move(sys.argv[1],sys.argv[2])
os.chmod(sys.argv[2], 0o666)
if (sys.argv[3] == 'LCD16'):
   os.system("service lcd-piforce restart")