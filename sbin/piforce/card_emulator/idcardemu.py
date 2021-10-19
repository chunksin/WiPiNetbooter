#!/usr/bin/env python3

"""
Initial D3 Card SANWA v1.0
Programmed by: winteriscoming
Special thanks to: Metallic

edited by: SaturnNiGHTS and Chunksin

Serial interface referenced from jpnevulator.py script.

jpnevulator info:

jpnevulator.py version 2.1.3
Copyright (C) 2015 Philipp Klaus
This is free software.  You may redistribute copies of it under the terms of
the GNU General Public License <http://www.gnu.org/licenses/gpl.html>.
There is NO WARRANTY, to the extent permitted by law.

"""

import argparse
import sys
import time
import textwrap
from datetime import datetime as dt
import binascii
import serial
import os
import subprocess
import threading
import glob
from queue import Queue
from pathlib import Path
from time import sleep
import filecmp
import shutil
import shortuuid

currentpid = os.getpid()
bashCommand1 = 'sudo echo -n '+str(currentpid)+' | tee /sbin/piforce/card_emulator/pid.txt'
os.system(bashCommand1)

Sentinel = "FF"
nocard = True
stop_thread = False
if not os.path.exists('/var/log/activecard'):
    print('---> Creating temporary drop folder as /var/log/activecard')
    os.makedirs('/var/log/activecard')
if not os.path.exists('/var/log/printdata'):
    print('---> Creating temporary data folder as /var/log/printdata')
    os.makedirs('/var/log/printdata')
if not os.path.exists('/var/log/cardcheck'):
    print('---> Creating temporary data folder as /var/log/cardcheck')
    os.makedirs('/var/log/cardcheck')

try:
    clock = time.perf_counter
except AttributeError:
    clock = time.time

def autogen():
    BasePath = "/var/log/activecard/"
    BaseName = BasePath+"card"
    Suffix = shortuuid.ShortUUID().random(length=6)
    NewCardFileName = "_".join([BaseName, Suffix]) # Temp card file name generated
    return NewCardFileName

def emptydropfolder():
    DropFolderGlobPath = "/var/log/activecard/*"
    CardFiles = glob.glob(DropFolderGlobPath) # Empty the drop folder and printdata folder
    for f in CardFiles:
        os.remove(f)

def cardpoll(threadname, q): # Card polling thread checks drop folder for card file
    global nocard
    global stop_thread
    DropFolderGlobPath = "/var/log/activecard/*" # Drop folder to monitor
    while True:
        while nocard is True:
            if glob.glob(DropFolderGlobPath):
                DropFile = glob.glob(DropFolderGlobPath)
                CardFileName = DropFile[0] # Update CardFileName to be original card file
                print('---> File detected in drop folder:', CardFileName)
                with open(CardFileName, "rb") as in_file: # Open original card file and read contents
                    CardBytes = in_file.read()
                    q.put(CardBytes) # Send card contents to the cardpoll queue
                nocard = False
            sleep(0.1)
            if stop_thread:
                break
        while nocard is False:
            if not glob.glob(DropFolderGlobPath):
                print("---> Drop folder is empty")
                nocard = True
            sleep(1)
            if stop_thread:
                break

def port_def(string):
    port, alias = string, None
    return {'port': port, 'alias': alias}

def hex_format(chunk):
    try:
        return ' '.join('{:02X}'.format(byte) for byte in chunk)
    except ValueError:
        return ' '.join('{:02X}'.format(ord(byte)) for byte in chunk)

def CardTranslation(NAOMIString,CardFileName,IDMode):
    CARDString = NAOMIString[NAOMIString.find("02 D8 53 00 00 00 30 31 36")+27: NAOMIString.find("02 D8 53 00 00 00 30 31 36") +27 + 620]
    CardCRC = 0

    # 0x02, preamble; 0xD5, length [213 bytes]; 0x33, CRWReadDataL
    # 0x78, RESULT1 bitfield 0b01111000 - shutter closed, dispenser full, card inside reader
    # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; CARDString, magnetic payload; 0x03, end of command; CardCRCText, CRC
    CARDString = "D5 33 78 30 30 " + CARDString + " 03"

    i=0
    for i in range(213):
        CardPartValue = int("0x" + CARDString[i*3 : i*3+2],0)
        CardCRC = CardCRC^CardPartValue
        #print ("Card Part: " + hex(CardPartValue))
    CardCRCText = hex(CardCRC)
    global Sentinel
    Sentinel = CardCRCText[2:]
    print (Sentinel)
    ConvertedCardValue = "02 " + CARDString + " " + CardCRCText[2:]
    print ("Converted Card Value: " + ConvertedCardValue)
    CardValueList = []

    for i in range(215):
        CardPartValue = int("0x" + ConvertedCardValue[i*3 : i*3+2],0)
        #print (str(i) + ":Converting " + ConvertedCardValue[i*3 : i*3+2] + " to HEX:" + hex(CardPartValue) + " : DEC:"+ str(CardPartValue))
        CardValueList.append(CardPartValue)

    CardBytes = b""
    CardBytes = bytearray(CardValueList)
    
    if CardBytes.find(b'SEGABFF7') > -1:
        CardFileName = '/boot/config/cards/idas/'+os.path.basename(CardFileName)
        print('ID card type detected: IDAS')
        IDMode = 'idas'
    if CardBytes.find(b'SEGABFS0') > -1:
        CardFileName = '/boot/config/cards/id2/'+os.path.basename(CardFileName)
        print('ID card type detected: ID2')
        IDMode = 'id2'
    if CardBytes.find(b'SEGABHR3') > -1:
        CardFileName = '/boot/config/cards/id3/'+os.path.basename(CardFileName)
        print('ID card type detected: ID3')
        IDMode = 'id3'

    with open(CardFileName, "wb") as out_file:
        out_file.write(CardBytes)
    print ("---> Saved Card Data to " + CardFileName)

    with open(CardFileName, "rb") as in_file:
        CardBytes = in_file.read()
    print ("---> Read in Card Data from " + CardFileName)

    return CardBytes, IDMode, CardFileName

def main():
    WaitingForCard = 0
    CardInserted = 0
    CardFromDispenser = 0
    CardCleanCheck = 0
    CardWait = 0
    CardBusy = 0
    CardCleaning = 0
    CardBytes = b""
    ReadInput = ""
    NewCard = 0
    CardData = ""
    ShutterOpen = 1
    NewCardFileName = autogen()
    DropFolderGlobPath = "/var/log/activecard/*"
    PrintFolderGlobPath = "/var/log/printdata/*"
    DropFolderPath = "/var/log/activecard/"
    CardFolderRoot = "/boot/config/cards"
    NFCCardFound = False
    NFCCardMatch = False
    NFCCardWrite = ""
    CopyNFCphp = False
    rawfilename = ""
    parser = argparse.ArgumentParser()
    parser.add_argument('-cp', '--comport', type=port_def, metavar='COMPORT', help="The serial device. Example: -t COM1")
    parser.add_argument('-f', '--cardfile', help='Name of card file to load - optional. Example: -f CardHexFile')
    parser.add_argument('-m', '--mode', help='Mode of ID card emulator. Example: -m id2')

    args = parser.parse_args()

    if not args.comport:
        parser.error('Please specify a serial port! Example: -cp COM1')
        sys.exit(0)

    if not args.cardfile:
        print('---> No Card file name provided, using autogen and switching to real time mode') # If no card file name specified default to autogen
        CardFileName = NewCardFileName
    else:
        CardFileName = args.cardfile

    if not args.mode:
        parser.error('Please specify an ID mode! Example: -m id2')
        sys.exit(0)

    CardFiles = glob.glob(DropFolderGlobPath) # Clear out any existing files from the drop folder before the polling queue starts up
    for f in CardFiles:
        os.remove(f)

    PrintFiles = glob.glob(PrintFolderGlobPath) # Clear out any existing files from the printdata folder
    for f in PrintFiles:
        os.remove(f)
    
    IDMode = args.mode
    comport = args.comport
    comport['alias'] = "NAOMI"
    comport['buffer'] = b''
    comport['ser'] = serial.Serial(comport['port'], baudrate=9600, timeout=0, parity=serial.PARITY_EVEN, bytesize = serial.EIGHTBITS, stopbits=serial.STOPBITS_ONE)
    comport['last_byte'] = clock()
    comport['ser'].setRTS(True)
    print ("COM port opened and named:", comport['alias'])
    print("CTS:", comport['ser'].getCTS())
    cardpollq = Queue() # Card polling queue defined and started
    cardpollthread = threading.Thread(target=cardpoll, args=("CardPollingThread", cardpollq))
    cardpollthread.daemon = True
    cardpollthread.start()

    try:
        with open(CardFileName, "rb") as in_file:
            CardBytes = in_file.read()
        print ("---> Read in Card Data from " + CardFileName)

        if CardBytes == b"":
            print ("--->" + CardFileName + " card file is empty.  You will have to select to create a new card in the game.")
            NewCard=1
        else:
            print ("--->" + CardFileName + " appears to contain data.  This card data will be loaded when a game is started.")
    except:
        print ("---> New random filename " + CardFileName + " generated. It will be available for purchase as a new card in the game.")
        NewCard = 1

    try:
        while True:
            sleep(0.001)
            new_data = comport['ser'].read()
            if len(new_data) > 0:
                comport['buffer'] += new_data
                comport['last_byte'] = clock()

            if comport['buffer'] and (clock() - comport['last_byte']) > 100000/1E6:
                line = '{0}\nWaitingForCard={1}, CardInserted={2}, Sentinel={3}, CardFromDispenser={4}, CardWait={5}\nCardBusy={6}, ShutterOpen={7}, NewCard={8}, CardBytes={9}, CardCleaning={10}, CardCleanCheck={11}\n{12}: '.format(dt.now().isoformat(' '), WaitingForCard, CardInserted, Sentinel, CardFromDispenser, CardWait, CardBusy, ShutterOpen, NewCard, len(CardBytes), CardCleaning, CardCleanCheck, comport['alias'])

                sys.stdout.write(line)
                while comport['buffer']:
                    chunk = comport['buffer'][:250]
                    comport['buffer'] = comport['buffer'][250:]
                    fmt = "{{hex:{0}s}}".format(250*3)
                    line = fmt.format(hex=hex_format(chunk))
                    line = line.strip()
                    ReadInput += " " + line
                    line += '\n'
                    sys.stdout.write(line)

                    if not cardpollq.empty(): # Check cardpoll queue for data
                        CardBytes = cardpollq.get_nowait()
                        CardData = CardBytes
                        DropFile = glob.glob(DropFolderGlobPath)
                        if (os.path.basename(DropFile[0]) == 'NFC_Card'): # If the dropped file is called NFC_Card search for existing copy
                            NFCCardFound = True
                            print('---> NFC Data file detected, checking for match')
                            for root, subdirectories, files in os.walk(CardFolderRoot):
                                for file in files:
                                    if not NFCCardMatch:
                                        if filecmp.cmp(DropFile[0], os.path.join(root, file), shallow=False) and not NFCCardMatch:
                                            CardFileName = DropFolderPath+file
                                            shutil.move(DropFile[0], CardFileName)
                                            NFCCardMatch = True
                                            print('---> Match found locally for NFC data:', CardFileName)
                                            
                            if NFCCardFound and not NFCCardMatch:
                                CardFileName = NewCardFileName # No match found for the NFC data so treat as a new card - php data to be handled at card save
                                shutil.move(DropFile[0], NewCardFileName)
                                print('---> No match found locally for NFC data')
                                print('---> Card will be treated as autogen with existing PHP data')
                                CopyNFCphp = True # Set a flag for the php data to be copied out at save time                                
                        else:
                            CardFileName = DropFile[0] # Update CardFileName

                        NFCCardFound = False # Reset the NFCCard variables for the next card swipe
                        NFCCardMatch = False
                        print('---> Card file detected:', CardFileName)
                        print('---> Card file read into memory')
                        print('---> Card file processed from drop folder')
                        CardInserted = 0
                        NewCard = 0
                        WaitingForCard = 1

                    # 0x40 - CRWCancel - cancel card wait state
                    if "02 06 40 00 00 00 03 45" in ReadInput:
                        # 0x06, ack; 0x02, preamble; 0x06, length [6 bytes]; 0x40, CRWCancel
                        if ShutterOpen:
                            if CardInserted:
                                if CardBusy:
                                    output =b"\x06\x02\x06\x40\xB8\x30\x30\x03\xFD"
                                    # 0xB8, RESULT1 bitfield 0b10111000 - shutter open, dispenser full, card fully pulled inside reader
                                    # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; 0x03, end of command; 0xFD, CRC
                                    print ("SANWA: 06 02 06 40 B8 30 30 03 FD [CRWCancel (reply to 0x40, shutter open, not busy, card pulled inside)]")
                                    CardBusy = 0
                                    CardWait = 0
                                    WaitingForCard = 1

                                elif CardWait:
                                    output = b"\x02\x06\x40\xA0\x30\x30\x03\xE7"
                                    # 0xA0, RESULT1 bitfield 0b10111000, shutter open, full dispenser, no card
                                    # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum WAIT; 0x03, end of command; 0xE5, CRC
                                    print ("SANWA: 02 06 40 A0 30 32 03 E7 [CRWCancel (Reply to 0x40, shutter open, CARD WAIT, no card)]")
                                    CardBusy = 0

                                else:
                                    output =b"\x02\x06\x40\xB8\x30\x30\x03\x3F"
                                    # 0xB8, RESULT1 bitfield 0b10111000 - shutter open, dispenser full, card fully pulled inside reader
                                    # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; 0x03, end of command; 0x3F, CRC
                                    print ("SANWA: 02 06 40 B8 30 30 03 FD [CRWCancel (reply to 0x40, shutter open, not busy, card pulled inside)]")
                                    CardWait = 0

                            else:
                                if CardBusy:
                                    output = b"\x06\x02\x06\x40\xA0\x30\x30\x03\xE5"
                                    # 0xA0, RESULT1 bitfield 0b10111000, shutter open, full dispenser, no card
                                    # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; 0x03, end of command; 0xE5, CRC
                                    print ("SANWA: 06 02 06 40 A0 30 30 03 E5 [CRWCancel (Reply to 0x40, shutter open, not busy, no card)]")
                                    CardBusy = 0
                                    CardWait = 0
                                else:
                                    output = b"\x06\x02\x06\x40\xA0\x30\x30\x03\xE5"
                                    # 0xA0, RESULT1 bitfield 0b10111000, shutter open, full dispenser, no card
                                    # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; 0x03, end of command; 0xE5, CRC
                                    print ("SANWA: 06 02 06 40 A0 30 30 03 E5 [CRWCancel (Reply to 0x40, shutter open, not busy, no card)]")
                                    CardWait = 0

                        else:
                            if CardInserted:
                                if CardBusy:
                                    output =b"\x06\x02\x06\x40\x78\x30\x30\x03\x3D"
                                    # 0x78, RESULT1 bitfield 0b01111000 - shutter closed, dispenser full, card inside reader
                                    # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; 0x03, end of command; 0x3D, CRC
                                    print ("SANWA: 06 02 06 40 78 30 30 03 3D [CRWCancel (reply to 0x40, shutter closed, not busy, card present)]")
                                    CardBusy = 0
                                    CardWait = 0

                                else:
                                    output =b"\x06\x02\x06\x40\x78\x30\x30\x03\x3D"
                                    # 0x78, RESULT1 bitfield 0b01111000 - shutter closed, dispenser full, card inside reader
                                    # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; 0x03, end of command; 0x3D, CRC
                                    print ("SANWA: 06 02 06 40 78 30 30 03 3D [CRWCancel (reply to 0x40, shutter closed, not busy, card present)]")
                                    CardWait = 0

                            else:
                                if CardBusy:
                                    output =b"\x06\x02\x06\x40\x60\x30\x30\x03\x25"
                                    # 0x60, RESULT1 bitfield 0b01100000 - shutter closed, dispenser full, no card inserted
                                    # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; 0x03, end of command; 0x25, CRC
                                    print ("SANWA: 06 02 06 40 60 30 30 03 25 [CRWCancel (reply to 0x40, shutter closed, not busy, no card)]")
                                    CardBusy = 0
                                    CardWait = 0
                                    
                                elif CardFromDispenser:
                                    output =b"\x06\x02\x06\x40\x7C\x30\x32\x03\x3B"
                                    # 0x7C, RESULT1 bitfield 0b01111100 - shutter closed, dispenser full, card coming from dispenser, maybe?
                                    # 0x30, RESULT2 enum OK; 0x32, RESULT3 enum BUSY; 0x03, end of command; 0x3B, CRC
                                    print ("SANWA: 06 02 06 40 7C 30 32 03 3B [CRWCancel (Reply to 0x40, shutter closed, busy, card coming from dispenser)]")
                                    CardBusy = 1
                                    CardWait = 0
                                    
                                else:
                                    output =b"\x06\x02\x06\x40\x60\x30\x30\x03\x25"
                                    # 0x60, RESULT1 bitfield 0b01100000 - shutter closed, dispenser full, no card inserted
                                    # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; 0x03, end of command; 0x25, CRC
                                    print ("SANWA: 06 02 06 40 60 30 30 03 25 [CRWCancel (reply to 0x40, shutter closed, not busy, no card)]")
                                    CardWait = 0
                                    
                                    #output =b"\x06\x02\x06\x40\x60\x30\x32\x03\x27"
                                    ## 0x60, RESULT1 bitfield 0b10111000, shutter closed, full dispenser, no card
                                    ## 0x30, RESULT2 enum OK; 0x32, RESULT3 enum BUSY; 0x03, end of command; 0x27, CRC
                                    #print ("SANWA: 06 02 06 40 60 30 32 03 27 [CRWCancel (Reply to 0x40, Shutter closed, busy, no card)]")
                                    #CardBusy = 1
                                    #CardWait = 0
                                
                        comport['ser'].write(output)
                        ReadInput=""


                    # 0xD0 - Actuate shutter [somehow it knows to close the shutter, maybe 0x30?]
                    # why send card data in reply to this?
                    if WaitingForCard and len(CardData)>0 and "02 07 D0 00 00 00 30 03 E4" in ReadInput:
                        # 0x06, ack; 0x02, preamble; 0xD5, length [213 bytes]; 0x33, CRWReadDataL
                        # 0x78, RESULT1 bitfield 0b01111000 - shutter closed, dispenser full, card inside reader
                        # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; CARDString, magnetic payload; 0x03, end of command; CardCRCText, CRC
                        output =b'\x06'
                        comport['ser'].write(output)
                        output = CardBytes
                        comport['ser'].write(output)
                        print ("SANWA: 06 02 D5 33 78 30 30 [CRWReadDataL - Sending magnetic payload (shutter closed, card inside reader)]")
                        ReadInput=""
                        WaitingForCard = 0
                        ShutterOpen = 0

                    # 0xD0 - Actuate shutter [close, maybe, due to 0x30 in data]
                    if WaitingForCard and "02 07 D0 00 00 00 30 03 E4" in ReadInput:
                        output = b"\x06\x02\x06\xD0\xB8\x30\x33\x03\x6E"
                        # 0x06, ack; 0x02, preamble; 0x06, length [6 bytes]; 0xD0, Actuate shutter
                        # 0xB8, RESULT1 bitfield 0b10111000, shutter open, full dispenser, card fully pulled inside reader
                        # 0x30, RESULT2 enum OK; 0x33, RESULT3 enum CARD_WAIT; 0x03, end of command; 0x6E, CRC
                        print ("SANWA: 06 02 06 D0 B8 30 33 03 6E [Actuate shutter (reply to 0xD0, shutter open, CARD WAIT, pulling in card, closing shutter?]")
                        comport['ser'].write(output)
                        ReadInput=""
                        WaitingForCard = 1
                        CardWait = 1
                        CardBusy = 0
                        ShutterOpen = 0
                        CardInserted = 0

                    # 0xD0 - Actuate shutter [close, maybe, due to 0x30 in data]
                    if not CardWait and "02 07 D0 00 00 00 30 03 E4" in ReadInput:
                        output =b"\x06\x02\x06\xD0\xA0\x30\x33\x03\x76"
                        # 0x06, ack; 0x02, preamble; 0x06, length [6 bytes]; 0xD0, Actuate shutter
                        # 0xA0, RESULT1 bitfield 0b10111000, shutter open, full dispenser, no card
                        # 0x30, RESULT2 enum OK; 0x33, RESULT3 enum CARD_WAIT; 0x03, end of command; 0x76, CRC
                        print ("SANWA: 06 02 06 D0 A0 30 33 03 76 [Actuate shutter (reply to 0xD0, shutter open, CARD WAIT, no card, closing shutter?)]")
                        ReadInput=""
                        CardWait = 1
                        CardBusy = 0
                        ShutterOpen = 0
                        CardInserted = 0

                    # 0x20 - CRWReadStatus
                    if ShutterOpen and not CardFromDispenser and "02 06 20 00 00 00 03 25" in ReadInput:
                        output =b"\x06\x02\x06\x20\xA0\x30\x30\x03\x85"
                        # 0x06, ack; 0x02, preamble; 0x06, length [6 bytes]; 0x20, CRWReadStatus
                        # 0xA0, RESULT1 bitfield 0b10111000, shutter open, full dispenser, no card
                        # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; 0x03, end of command; 0x85, CRC
                        print ("SANWA: 06 02 06 20 A0 30 30 03 85 [CRWReadStatus (shutter open, not busy, no card)]")
                        comport['ser'].write(output)
                        ReadInput=""
                        CardWait = 0
                        CardBusy = 0
                        CardInserted = 0

                    # 0xD0 - Actuate shutter [close, maybe, due to 0x30 in data]
                    if CardWait and "02 07 D0 00 00 00 30 03 E4" in ReadInput:
                        output =b"\x06\x02\x06\xD0\x60\x30\x30\x03\xB5"
                        # 0x06, ack; 0x02, preamble; 0x06, length [6 bytes]; 0xD0, Actuate shutter
                        # 0x60, RESULT1 bitfield 0b01100000, shutter closed, full dispenser, no card
                        # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; 0x03, end of command; 0xB5, CRC
                        print ("SANWA: 02 06 D0 60 30 30 03 B5 [Actuate shutter (Reply to 0xD0, shutter closed, not busy, no card)]")
                        comport['ser'].write(output)
                        ReadInput=""
                        ShutterOpen = 0
                        CardWait = 0
                        CardBusy = 0
                        CardInserted = 0

                    # 0x20 - CRWReadStatus
                    if not CardFromDispenser and not ShutterOpen and not CardInserted and "02 06 20 00 00 00 03 25" in ReadInput:
                        output =b"\x06\x02\x06\x20\x60\x30\x30\x03\x45"
                        # 0x06, ack; 0x02, preamble; 0x06, length [6 bytes]; 0x20, CRWReadStatus
                        # 0x60, RESULT1 bitfield 0b01100000, shutter closed, full dispenser, no card
                        # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; 0x03, end of command; 0x45, CRC
                        print ("SANWA: 06 02 06 20 60 30 30 03 45 [CRWReadStatus (shutter closed, not busy, no card)]")
                        comport['ser'].write(output)
                        ReadInput=""
                        CardBusy = 0
                        CardWait = 0

                    # 0x20 - CRWReadStatus
                    if CardFromDispenser and not ShutterOpen and "02 06 20 00 00 00 03 25" in ReadInput:
                        output =b"\x06\x02\x06\x20\x7C\x30\x30\x03\x59"
                        # 0x06, ack; 0x02, preamble; 0x06, length [6 bytes]; 0x20, CRWReadStatus
                        # 0x7C, RESULT1 bitfield 0b01111100 - shutter closed, dispenser full, card coming from dispenser, maybe?
                        # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; 0x03, end of command; 0x59, CRC
                        print ("SANWA: 06 02 06 20 7C 30 30 03 59 [CRWReadStatus (shutter closed, not busy, card coming from dispenser)]")
                        comport['ser'].write(output)
                        ReadInput=""
                        CardBusy = 0
                        CardWait = 0
                        CardFromDispenser = 0
                        
                    # 0xB0 - CRWTakenCardDispenser - request blank card from dispenser [0x31?]
                    if "02 07 B0 00 00 00 31 03 85" in ReadInput:
                        output =b"\06\x02\x06\xB0\x7C\x30\x30\x03\xC9"
                        # 0x06, ack; 0x02, preamble; 0x06, length [6 bytes]; 0xB0, CRWTakenCardDispenser
                        # 0x7C, RESULT1 bitfield 0b01111100 - shutter closed, dispenser full, card coming from dispenser, maybe?
                        # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; 0x03, end of command; 0xC9, CRC
                        print ("SANWA: 06 02 06 B0 7C 30 30 03 C9 [CRWTakenCardDispenser (shutter closed, not busy, requested blank)]")
                        comport['ser'].write(output)
                        ReadInput=""
                        CardFromDispenser = 1
                        CardInserted = 1
                        CardBusy = 0
                        CardWait = 0
                        NewCard = 1

                    # 0x20 - CRWReadStatus
                    if not ShutterOpen and CardInserted and "02 06 20 00 00 00 03 25" in ReadInput:
                        output =b"\x06\x02\x06\x20\x78\x30\x30\x03\x5D"
                        # 0x06, ack; 0x02, preamble; 0x06, length [6 bytes]; 0x20, CRWReadStatus
                        # 0x78, RESULT1 bitfield 0b01111000 - shutter closed, dispenser full, card inside reader
                        # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; 0x03, end of command; 0x5D, CRC
                        print ("SANWA: 06 02 06 20 78 30 30 03 5D [CRWReadStatus (shutter closed, not busy, card present)]")
                        comport['ser'].write(output)
                        ReadInput=""
                        CardBusy = 0
                        CardWait = 0

                    # 0x10 - CRWInitialize
                    if "02 07 10 00 00 00 31 03 25" in ReadInput:
                        output =b"\x06\x02\x06\x10\xA0\x30\x30\x03\xB5"
                        # 0x06, ack; 0x02, preamble; 0x06, length [6 bytes]; 0x10, CRWInitialize
                        # 0xA0, RESULT1 bitfield 0b10100000 - shutter open, dispenser full, no card
                        # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; 0x03, end of command; 0xB5, CRC
                        comport['ser'].write(output)
                        print ("SANWA: 06 02 06 10 A0 30 30 03 B5 [CRWInitialize (shutter open, not busy, no card)]")
                        ReadInput=""
                        CardBusy = 0
                        CardWait = 0
                        ShutterOpen = 1
                        CardInserted = 0

                    # 0x80 - CRWDischargeCard - eject card from reader
                    if "02 06 80 00 00 00 03 85" in ReadInput:
                        output =b"\x06\x02\x06\x20\xA0\x30\x30\x03\x85"
                        # 0x06, ack; 0x02, preamble; 0x06, length [6 bytes]; 0x20, CRWReadStatus
                        # 0xA0, RESULT1 bitfield 0b10100000 - shutter open, dispenser full, no card
                        # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; 0x03, end of command; 0x85, CRC
                        print ("SANWA: 06 02 06 20 A0 30 30 03 85 [CRWDischargeCard (shutter open, not busy, no card)]")
                        comport['ser'].write(output)
                        ReadInput=""
                        CardWait = 0
                        CardBusy = 0
                        ShutterOpen = 1
                        CardInserted = 0
                        if len(CardData)>0:
                            NewCard = 1
                        emptydropfolder() # Empty the drop folder as card has been ejected
                        CardBytes = b"" # Reset CardBytes
                        #CardData = CardBytes
                        CardFileName = autogen() # Set CardFileName to new autogen name

                    # 0xD0 - Actuate shutter [close, maybe, due to 0x30 in data]
                    if "02 07 D0 00 00 00 30 03 E4" in ReadInput:
                        output =b"\x06\x02\x06\xD0\x60\x30\x30\x03\xB5"
                        # 0x06, ack; 0x02, preamble; 0x06, length [6 bytes]; 0xD0, Actuate shutter
                        # 0x60 RESULT1 bitfield 0b01100000 - shutter closed, dispenser full, no card
                        # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; 0x03, end of command; 0xB5, CRC
                        print ("SANWA: 06 02 06 D0 60 30 30 03 B5 [Actuate shutter (shutter closed, no card, ejected card?)]")
                        comport['ser'].write(output)
                        ReadInput=""
                        CardInserted = 0
                        CardWait = 0
                        CardBusy = 0
                        ShutterOpen = 0
                        emptydropfolder() # Empty the drop folder as card has been ejected
                        CardBytes = b"" # Reset CardBytes
                        #CardData = CardBytes
                        CardFileName = autogen() # Set CardFileName to new autogen name

                    # 0xD0 - Actuate shutter [open, maybe, due to 0x31 in data]
                    if "02 07 D0 00 00 00 31 03 E5" in ReadInput:
                        output =b"\x06\x02\x06\xD0\xA0\x30\x30\x03\x75"
                        # 0x06, ack; 0x02, preamble; 0x06, length [6 bytes]; 0xD0, Actuate shutter
                        # 0xA0 RESULT1 bitfield 0b10100000 - shutter open, dispenser full, no card
                        # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; 0x03, end of command; 0x75, CRC
                        print ("SANWA: 06 02 06 D0 A0 30 30 03 75 [Actuate shutter (shutter open, no card, ejected card?)]")
                        comport['ser'].write(output)
                        ReadInput=""
                        CardInserted = 0
                        ShutterOpen = 1
                        NewCard = 0
                        emptydropfolder() # Empty the drop folder as card has been ejected
                        CardBytes = b"" # Reset CardBytes
                        #CardData = CardBytes
                        CardFileName = autogen() # Set CardFileName to new autogen name

                    # 0x53 - CRWWriteDataL - write magnetic track data
                    if "02 D8 53 00 00 00" in ReadInput and len(ReadInput)>150:
                        CardData = ReadInput
                        CardBytes, IDMode, CardFileName = CardTranslation(CardData,CardFileName,IDMode)
                        output =b"\x06\x02\x06\x53\x78\x30\x30\x03\x2E"
                        # 0x06, ack; 0x02, preamble; 0x06, length [6 bytes]; 0x53, CRWWriteDataL
                        # 0x78, RESULT1 bitfield 0b01111000 - shutter closed, dispenser full, card inside reader
                        # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; 0x03, end of command; 0x2E, CRC
                        print ("SANWA: 06 02 06 53 78 30 30 03 2E [CRWWriteDataL - Receive magnetic data (shutter closed, not busy, card inserted)]")
                        comport['ser'].write(output)
                        PrintDataFile = "/var/www/html/cards/"+IDMode+"/"+os.path.basename(CardFileName)+".printdata.php"
                        WriteBackFile = open('/sbin/piforce/nfcwriteback.txt') # Check NFC writeback setting
                        NFCCardWrite = WriteBackFile.readline()
                        WriteBackFile.close()
                        if CopyNFCphp: # If the data has come from a new unknown NFC card - move its associated printdata php file to the correct location
                            print('---> Moving NFC Card print data file')
                            shutil.move("/var/log/printdata/NFC_Card.printdata.php", PrintDataFile)
                            CopyNFCphp = False # Reset CopyNFCphp variable for next card swipe
                        if (NFCCardWrite == 'yes'):
                            print('---> Writing card data to NFC card')
                            print('---> NFC data file:', CardFileName)
                            print('---> NFC print file:', PrintDataFile)
                            cp = subprocess.Popen(["python3","/sbin/piforce/card_emulator/nfcwrite.py", CardFileName, PrintDataFile])
                        ReadInput=""
                        CardInserted = 1
                        CardFromDispenser = 0
                        WaitingForCard = 0
                        CardBusy = 0
                        CardWait = 0
                        NewCard = 0
                        ShutterOpen = 0

                    # 0x78 - CRWSetPrint, Set print parameters
                    if ReadInput[ReadInput.find("78")-6:].find("02")==0:
                        # 0x06, ack; 0x02, preamble; 0x06, length [6 bytes]; 0x78, CRWPrintL
                        # 0x67, RESULT1 bitfield 0b1100111, shutter closed, full dispenser, position close to print head
                        # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; 0x03, end of command; 0x1E, CRC
                        output =b"\x06\x02\x06\x78\x67\x30\x30\x03\x1A"
                        print ("SANWA: 06 02 06 78 67 30 30 03 1A [CRWSetPrint (shutter closed, not busy, position close to print head?)]")
                        comport['ser'].write(output)
                        ReadInput=""
                        CardInserted = 1
                        ShutterOpen = 0
                    
                    # 0x7A - CRWRegisterFont - configure font for thermal printing [expects font payload]
                    if len(ReadInput)>241 and "02 4F 7A 00 00 00" in ReadInput:
                        if CardInserted:
                            output =b"\x06\x02\x06\x7A\x78\x30\x30\x03\x07"
                            # 0x78, RESULT1 bitfield 0b01111000 - shutter closed, dispenser full, card inside reader
                            # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; 0x03, end of command; 0x07, CRC
                            print ("SANWA: 06 02 06 7A 78 30 30 03 07 [CRWRegisterFont (shutter closed, not busy, card present)]")
                            CardBusy = 0
                            CardWait = 0
                            ShutterOpen = 0
                        
                        else:
                            output =b"\x06\x02\x06\x7A\xA0\x30\x30\x03\xDF"
                            # 0xA0, RESULT1 bitfield 0b10100000 - shutter open, dispenser full, no card
                            # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; 0x03, end of command; 0xDF, CRC
                            print ("SANWA: 06 02 06 7A A0 30 30 03 DF [CRWRegisterFont (shutter open, not busy, no card)]")
                            CardBusy = 0
                            CardWait = 0
                            ShutterOpen = 1

                        comport['ser'].write(output)
                        ReadInput=""
                    
                    # 0x7C - CRWPrintL, Print on the card [print payload ignored, and this feels hacky.  needs to be parsed better.]
                    if ReadInput[ReadInput.find("7C")-6:].find("02")==0 or "02 7C 7C 00 00 00 30 30 00" in ReadInput:
                        # 0x06, ack; 0x02, preamble; 0x06, length [6 bytes]; 0x7C, CRWPrintL
                        # 0x67, RESULT1 bitfield 0b1100111, shutter closed, full dispenser, position close to print head
                        # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; 0x03, end of command; 0x1E, CRC
                        rawprintpath = '/var/log/printdata/'
                        rawfilename = rawprintpath+os.path.basename(CardFileName)+".printdata"
                        rawfile = open(rawfilename, "w")
                        rawprintdata = ReadInput.replace(' ','')
                        rawfile.write(rawprintdata)
                        rawfile.close()
                        print('---> Print data detected and exported for processing')
                        if (rawfilename != ""):
                            idscript = '/sbin/piforce/card_emulator/'+IDMode+'_card_data.py'
                            cp = subprocess.run(["python3", idscript, rawfilename])
                            rawfilename = ""
                            print('---> Card print data processed')
                        output =b"\x06\x02\x06\x7C\x67\x30\x30\x03\x1E"
                        print ("SANWA: 06 02 06 7C 67 30 30 03 1E [CRWPrintL (shutter closed, not busy, position close to print head?)]")
                        comport['ser'].write(output)
                        ReadInput=""
                        CardInserted = 1
                        ShutterOpen = 0
                        CardPrint = 0

                    # NO CARD DATA - a random request packet is the time to simulate a card?
                    if WaitingForCard and "05" in ReadInput and len(ReadInput)<4 and len(CardBytes) == 0:
                        output =b"\x02\x06\x33\xA0\x30\x34\x03\x92"
                        # 0x06, ack; 0x02, preamble; 0x06, length [6 bytes]; 0x33, CRWReadDataL
                        # 0xA0, RESULT1 bitfield 0b10100000 - shutter open, dispenser full, no card
                        # 0x30, RESULT2 enum OK; 0x34, RESULT3 enum NO CARD IN BOX; 0x03, end of command; 0x95, CRC
                        print ("SANWA: 02 06 33 A0 30 34 03 92 [CRWReadDataL (shutter open, NO CARD IN BOX, no card)]")
                        comport['ser'].write(output)
                        ReadInput=""
                        NewCard = 1
                        WaitingForCard = 0

                    # CARD DATA - a random request packet is the time to simulate a card?
                    if WaitingForCard and "05" in ReadInput and len(ReadInput)<4 and len(CardBytes)>0:
                        output =b"\x02\x06\x33\xB8\x30\x30\x03\x8E"
                        # 0x06, ack; 0x02, preamble; 0x06, length [6 bytes]; 0x33, CRWReadDataL
                        # 0xB8, RESULT1 bitfield 0b10111000 - shutter open, dispenser full, card fully pulled inside reader
                        # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; 0x03, end of command; 0x95, CRC
                        print ("SANWA: 02 06 33 B8 30 30 03 8E [CRWReadDataL (shutter open, not busy, card fully pulled)]")
                        comport['ser'].write(output)
                        ReadInput=""
                        CardInserted = 1
                        if not CardWait:
                            WaitingForCard = 0
                            
                    # 0x33 - CRWReadDataL - read magnetic track data [0x32...?]
                    if "02 09 33 00 00 00 32 31 36 03 0C" in ReadInput:
                        output =b"\x06\x02\x06\x33\xA0\x30\x33\x03\x95"
                        # 0x06, ack; 0x02, preamble; 0x06, length [6 bytes]; 0x33, CRWReadDataL
                        # 0xA0, RESULT1 bitfield 0b10100000 - shutter open, dispenser full, no card
                        # 0x30, RESULT2 enum OK; 0x33, RESULT3 enum CARD WAIT; 0x03, end of command; 0x95, CRC
                        print ("SANWA: 06 02 06 33 A0 30 33 03 95 [CRWReadDataL (shutter open, CARD WAIT, no card)]")
                        comport['ser'].write(output)
                        ReadInput=""
                        WaitingForCard = 1
                        CardInserted = 0
                        CardWait = 1
                        #if len(CardData)>0:
                        #    CardInserted = 1
                        #    ShutterOpen = 0                               

                    # 0x33 - CRWReadDataL - read magnetic track data [0x30...?]
                    if CardInserted and len(CardBytes)>0 and "02 09 33 00 00 00 30 31 36 03 0E" in ReadInput:
                        # 0x06, ack; 0x02, preamble; 0xD5, length [213 bytes]; 0x33, CRWReadDataL
                        # 0x78, RESULT1 bitfield 0b01111000 - shutter closed, dispenser full, card inside reader
                        # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; CARDString, magnetic payload; 0x03, end of command; CardCRCText, CRC
                        output =b'\x06'
                        comport['ser'].write(output)
                        output = CardBytes
                        comport['ser'].write(output)
                        print ("SANWA: 06 02 D5 33 78 30 30 [CRWReadDataL - Sending magnetic payload (shutter closed, not busy, card inside reader)]")
                        ReadInput=""
                        CardBusy = 0
                        CardWait = 0

                    # ping pong
                    if "05" in ReadInput and len(ReadInput)<8:
                        if CardCleaning > 0:
                            print ("SANWA: CRWCleaning - flagging to enter clean loop")
                            CardCleanCheck = 1
                        elif not WaitingForCard and not CardFromDispenser and not CardBusy:
                            print ("SANWA: chomp")
                        else:
                            output =b"\x06"
                            print ("SANWA: 06 [Ping pong]")
                            comport['ser'].write(output)

                        ReadInput=""

                    # 0xA0 - CRWCleaning, Clean CRW mechanism
                    if ReadInput[ReadInput.find("A0")-6:].find("02")==0 or CardCleanCheck:
                        # 0x06, ack; 0x02, preamble; 0x06, length [6 bytes]; 0xA0, CRWCleaning
                        if CardCleaning == 0:
                            # 0xA1, RESULT1 bitfield 0b10100001, shutter open, dispenser full, card inserted in front of reader
                            # 0x30, RESULT2 enum OK; 0x33, RESULT3 enum BUSY; 0x03, end of command; 0x07, CRC
                            output =b"\x06\x02\x06\xA0\xA1\x30\x33\x03\x07"
                            print ("SANWA: 06 02 06 A0 A1 30 33 03 07 [CRWCleaning - Step 1 (shutter open, busy, card inserted into reader)]")
                            comport['ser'].write(output)
                            ReadInput=""
                            CardCleaning = 1
                            CardCleanCheck = 0
                            ShutterOpen = 1
                            CardBusy = 1
                            CardInserted = 1
                            break

                        if CardCleaning == 1:
                            # 0xB8, RESULT1 bitfield 0b10111000, shutter open, dispenser full, card fully pulled inside reader
                            # 0x30, RESULT2 enum OK; 0x33, RESULT3 enum BUSY; 0x03, end of command; 0x1E, CRC
                            output =b"\x06\x02\x06\xA0\xB8\x30\x30\x03\x1E"
                            print ("SANWA: 06 02 06 A0 B8 30 33 03 1E [CRWCleaning - Step 2 (shutter open, busy, card fully pulled into reader)]")
                            comport['ser'].write(output)
                            ReadInput=""
                            CardCleaning = 2
                            CardCleanCheck = 0
                            ShutterOpen = 1
                            CardBusy = 1
                            CardInserted = 1
                            break

                        if CardCleaning == 2:
                            # 0x78, RESULT1 bitfield 0b01111000, shutter closed, dispenser full, card inside reader
                            # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; 0x03, end of command; 0x0D, CRC
                            output =b"\x06\x02\x06\xA0\x78\x30\x30\x03\x0D"
                            print ("SANWA: 06 02 06 A0 78 30 30 03 0D [CRWCleaning - Step 3 (shutter closed, not busy, card inside reader)]")
                            comport['ser'].write(output)
                            ReadInput=""
                            CardCleaning = 3
                            CardCleanCheck = 0
                            ShutterOpen = 0
                            CardBusy = 0
                            CardInserted = 1
                            break

                        if CardCleaning == 3:
                            # 0xA0, RESULT1 bitfield 0b10100000, shutter open, dispenser full, no card inserted
                            # 0x30, RESULT2 enum OK; 0x30, RESULT3 enum OK; 0x03, end of command; 0x05, CRC
                            output =b"\x06\x02\x06\xA0\xA0\x30\x30\x03\x05"
                            print ("SANWA: 06 02 06 A0 A0 30 30 03 05 [CRWCleaning - Step 4 (shutter open, not busy, no card)]")
                            comport['ser'].write(output)
                            ReadInput=""
                            CardCleaning = 0
                            CardCleanCheck = 0
                            ShutterOpen = 1
                            CardBusy = 0
                            CardInserted = 0

                    if ReadInput:
                        print ("ERROR: Emulator does not know how to respond to: " + ReadInput )
                        ReadInput=""

                sys.stdout.flush()
    except KeyboardInterrupt:
        sys.exit(1)

if __name__ == "__main__": main()
