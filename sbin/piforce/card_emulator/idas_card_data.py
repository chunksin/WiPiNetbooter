#!/usr/bin/env python3
import struct
import codecs
import sys
import os
import random
import shutil

def replace_line(file_name, line_num, text):
        lines = open(file_name, 'r').readlines()
        lines[line_num] = text
        out = open(file_name, 'w')
        out.writelines(lines)
        out.close()

rawprintfile = sys.argv[1]
path = '/var/www/html/cards/idas/'
phppath = path+os.path.basename(rawprintfile)+".php"
delta = "false"
namestart = 42
keystart = 70
carstart = 100
deltastart = 16

if os.path.exists(phppath) and os.path.getsize(rawprintfile) < 160:
        print("PHP File found, this is a delta")
        delta = "true"
        phpfile = open(phppath)
        lines = phpfile.readlines()
        areadata = list(''.join(lines[5].split('"')[1::2]))
else:
        cardimage = random.choice(os.listdir("/var/www/html/cardimages/idas"))
        areadata = ['0','0','0','0','0','0','0','0','0']

with open(rawprintfile,"r") as f:

        if f.read(2) == '05':
            namestart = namestart+2
            keystart = keystart+2
            carstart = carstart+2
            deltastart = deltastart+2

        if (delta == "false"):
            f.seek(namestart)
            name = f.read(20)
            f.seek(keystart)
            keystring = f.read(12)
            f.seek(carstart)
            cardtoend = f.read()
        else:
            f.seek(deltastart)
            cardtoend = f.read()
f.close()

carinfo = cardtoend[0:-4]

if ("1B6701" in carinfo):
        carinfo = carinfo.replace("1B6701","20494920")
if ("1B6702" in carinfo):
        carinfo = carinfo.replace("1B6702","2049494920")
if ("1B6703" in carinfo):
        carinfo = carinfo.replace("1B6703","20495620")
if ("1B6704" in carinfo):
        carinfo = carinfo.replace("1B6704","2056494920")
if ("1B6705" in carinfo):
        areadata[0] = '1'
if ("1B6706" in carinfo):
        areadata[1] = '1'
if ("1B6707" in carinfo):
        areadata[2] = '1'
if ("1B6708" in carinfo):
        areadata[3] = '1'
if ("1B6709" in carinfo):
        areadata[4] = '1'
if ("1B670A" in carinfo):
        areadata[5] = '1'
if ("1B670B" in carinfo):
        areadata[6] = '1'
if ("1B670C" in carinfo):
        areadata[7] = '1'
if ("1B670C" in carinfo):
        areadata[8] = '1'
if ("1B670E" in carinfo):
        carinfo = carinfo.replace("1B670E","19")
if ("1B670F" in carinfo):
        carinfo = carinfo.replace("1B670F","1A")
if ("1B6710" in carinfo):
        carinfo = carinfo.replace("1B6710","1B")
if ("8187" in carinfo):
        carinfo = carinfo.replace("8187","1C")

cardata = carinfo.split("0D")
if (delta == "true"):
        areas = ''.join(areadata)
        replace_line(phppath, 5, '$areas="'+areas+'";\n')
else:
        car1 = codecs.decode(cardata[0],'hex').decode()
        car2 = codecs.decode(cardata[1].replace('2020',''),'hex').decode()
        areas = ''.join(areadata)
        keyno = codecs.decode(keystring.replace("1B670E",""),'hex').decode()
        namestring = name.replace('2020','20')
        test = codecs.decode(namestring,'hex')
        driver = codecs.decode(test,'shift-jis')
        drivername = ''
        for c in driver:
	        if (ord(c)>256):
		        newc = chr(ord(c)-65248)
		        drivername+=newc
	        else:
		        drivername+=c
        print ("Driver Name: "+drivername)
        print ("Car Line 1: "+car1)
        print ("Car Line 2: "+car2)
        with open("card_output","w") as fi:
                fi.write('<?php'+'\n')
                fi.write('$drivername="'+drivername+'";\n')
                fi.write('$carline1="'+car1+'";\n')
                fi.write('$carline2="'+car2+'";\n')
                fi.write('$keyno="'+keyno+'";\n')
                fi.write('$areas="'+areas+'";\n')
                fi.write('$card="'+cardimage+'";\n')
                fi.write('?>'+'\n')
        fi.close()
        shutil.move("./card_output", phppath)