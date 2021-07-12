#!/usr/bin/env python3

"""
Wangan Midnight Maximum Tune - Card Reader Emulator v1.0
Programmed by: winteriscoming
Special thanks to: MetalliC

Card command constants referenced from Dolphin emulator Triforce branch.

Serial interface referenced from jpnevulator.py script.

jpnevulator info:

jpnevulator.py version 2.1.3
Copyright (C) 2015 Philipp Klaus
This is free software.  You may redistribute copies of it under the terms of
the GNU General Public License <http://www.gnu.org/licenses/gpl.html>.
There is NO WARRANTY, to the extent permitted by law.

"""

## WMMT Card Command Constants ##
CARD_INIT	        = "10"
CARD_GET_CARD_STATE	= "20"
CARD_IS_PRESENT		= "40"
CARD_LOAD_CARD		= "B0"
CARD_CLEAN_CARD		= "A0"
CARD_READ		= "33"
CARD_WRITE		= "53"
CARD_WRITE_TEXT		= "7C"
CARD_78			= "78"
CARD_7A			= "7A"
CARD_7D			= "7D"
CARD_D0			= "D0"
CARD_80			= "80"

import argparse
import sys
import time
import textwrap
from datetime import datetime as dt
import binascii
import serial
import os

bashCommand1 = 'sudo echo -n '+str(currentpid)+' | tee /sbin/piforce/card_emulator/pid.txt'
os.system(bashCommand1)

try:
    clock = time.perf_counter
except AttributeError:
    clock = time.time

def port_def(string):
    port, alias = string, None
    return {'port': port, 'alias': alias}
    
def hex_format(chunk):
    try:
        return ' '.join('{:02X}'.format(byte) for byte in chunk)
    except ValueError:
        return ' '.join('{:02X}'.format(ord(byte)) for byte in chunk)

def CommandCode(CommandString):
    CurrentCommand = CommandString[CommandString.find("02")+ 6: CommandString.find("02")+ 8]
    return CurrentCommand

def CommandLookup(CommandString):
    if CommandString == "10": return "CARD_INIT(10)"
    if CommandString == "20": return "CARD_GET_CARD_STATE(20)"
    if CommandString == "40": return "CARD_IS_PRESENT(40)"
    if CommandString == "B0": return "CARD_LOAD_CARD(B0)"
    if CommandString == "A0": return "CARD_CLEAN_CARD(A0)"
    if CommandString == "53": return "CARD_WRITE(53)"
    if CommandString == "33": return "CARD_READ(33)"
    if CommandString == "7C": return "CARD_WRITE_TEXT(7C)"
    if CommandString == "78": return "CARD_78(78)"
    if CommandString == "7A": return "CARD_7A(7A)"
    if CommandString == "7D": return "CARD_7D(7D)"
    if CommandString == "D0": return "CARD_D0(D0)"
    if CommandString == "80": return "CARD_80(80)"
    return "UNKNOWN COMMAND!("+CommandString+")"

def ValidCommand(CommandString):
    if CommandString == "10": return 1
    if CommandString == "20": return 1
    if CommandString == "40": return 1
    if CommandString == "B0": return 1
    if CommandString == "A0": return 1
    if CommandString == "53": return 1
    if CommandString == "33": return 1
    if CommandString == "7C": return 1
    if CommandString == "78": return 1
    if CommandString == "7A": return 1
    if CommandString == "7D": return 1
    if CommandString == "D0": return 1
    if CommandString == "80": return 1
    return 0
    
def CardTranslationWMMT(ChihiroString,CardFileName):
    CARDString = ChihiroString[ChihiroString.find("02 4E 53 00 00 00 30 31 30")+27: ChihiroString.find("02 4E 53 00 00 00 30 31 30") +27 + 207]
    CardCRC = 0
    CARDString = "4B 33 31 30 30 " + CARDString + "03"
    i=0
    #print (CARDString)
    
    for i in range(75):
        CardPartValue = int("0x" + CARDString[i*3 : i*3+2],0)
        CardCRC = CardCRC^CardPartValue
    CardCRCText = hex(CardCRC)
    ConvertedCardValue = "02 " + CARDString + " " + CardCRCText[2:]
    print ("Converted Card Value: " + ConvertedCardValue)
    CardValueList = []
    
    for i in range(77):
        CardPartValue = int("0x" + ConvertedCardValue[i*3 : i*3+2],0)
        CardValueList.append(CardPartValue)
        
    CardBytes = b""
    CardBytes = bytearray(CardValueList)

    with open(CardFileName, "wb") as out_file:
        out_file.write(CardBytes)
    print ("Saved Card Data to " + CardFileName)
    
    with open(CardFileName, "rb") as in_file:
        CardBytes = in_file.read()
    print ("Read in Card Data from " + CardFileName)
    
    return CardBytes

def main():
    CardBytes = b""
    ReadInput = ""
    CurrentCommand = ""
    PriorCommand = ""
    ReadyToEject= 0
    HaveCard = 0
    StepNum = 0
    CleanStep = 0
    
    parser = argparse.ArgumentParser()
    parser.add_argument('-cp', '--comport', type=port_def, metavar='COMPORT', help="The serial device. Example: -t COM1")
    parser.add_argument('-f', '--cardfile', help='Name of card file to load. Example: -f CardHexFile')
    
    args = parser.parse_args()

    if not args.comport:
        parser.error('Please specify a serial port! Example: -cp COM1')
        sys.exit(0)
        
    if not args.cardfile:
        parser.error('Please specify a Card file name!  Example: -f CARDFILE.HEX')
        sys.exit(0)

    CardFileName = ""

    CardFileName = args.cardfile
    
    comport = args.comport
    comport['alias'] = "Triforce"
    comport['buffer'] = b''
    comport['ser'] = serial.Serial(comport['port'], baudrate=9600, timeout=0, parity=serial.PARITY_NONE, bytesize = serial.EIGHTBITS, stopbits=serial.STOPBITS_ONE)
    #comport['ser'] = serial.Serial(comport['port'], baudrate=19200, timeout=0, parity=serial.PARITY_NONE, bytesize = serial.EIGHTBITS, stopbits=serial.STOPBITS_ONE)
    comport['last_byte'] = clock()
    #comport['ser'].setRTS(True)
    
    print ("COM port opened and named:", comport['alias'])
    #print("CTS:", comport['ser'].getCTS())

    try:
        with open(CardFileName, "rb") as in_file:
            CardBytes = in_file.read()
        print ("Read in Card Data from " + CardFileName)
        
        if CardBytes == b"":
            print (CardFileName + " card file is empty.  You will have to select to create a new card in the game.")
            HaveCard=0
        else:
            print (CardFileName + " appears to contain data.  This card data will be loaded when a game is started.")
            HaveCard=1
    except:
        with open(CardFileName, "wb") as out_file:
            out_file.write(CardBytes)
        print (CardFileName + " not found.  Created " + CardFileName + ". You will have to select to create a new card in the game.")
        HaveCard=0
    

    try:
        while True:
            
            new_data = comport['ser'].read()
            if len(new_data) > 0:
                comport['buffer'] += new_data
                comport['last_byte'] = clock()

            if comport['buffer'] and (clock() - comport['last_byte']) > 100000/1E6:
                line = '{0}: {1}\n'.format(dt.now().isoformat(' '), comport['alias'])
                
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

                    if len(ReadInput)>9:
                        CurrentCommand = CommandCode(ReadInput)
                        PriorCommand = ReadInput
                        print ("Command Code is: " + CurrentCommand + ": " +CommandLookup(CurrentCommand))
                        if CurrentCommand == CARD_CLEAN_CARD:
                            CleanStep=0

                    if ValidCommand(CurrentCommand) and len(ReadInput)>9:
                        print ("Reader Emulator: Acknowledging:  06")
                        output =b"\x06"
                        comport['ser'].write(output)
                        ReadInput=""
                        
                    if CurrentCommand == CARD_INIT and "05" in ReadInput:
                        ReadInput=""
                        output =b"\x02\x06\x10\x30\x30\x30\x03\x25"
                        comport['ser'].write(output)
                        print ("Reader Emulator: Sending Init Reply(10):  02 06 10 30 30 30 03 25 - Initializing Variables")
                        ReadInput=""
                        StepNum=0
                        CleanStep=0
                        CleanFinal=0

                    if CurrentCommand == CARD_78 and "05" in ReadInput:
                        output =b"\x02\x06\x78\x30\x30\x30\x03\x4D"
                        print ("Reader Emulator: Sending 78 Reply:  02 06 78 30 30 30 03 4D")
                        comport['ser'].write(output)
                        ReadInput=""
                        
                    if CurrentCommand == CARD_CLEAN_CARD and "05" in ReadInput:
                        if CleanStep == 3:
                            ReadInput=""
                            output =b"\x02\x06\xA0\x30\x30\x30\x03\x95"
                            print ("Reader Emulator: Sending: CARD_CLEAN_CARD(A0) Reply Step 3:  02 06 A0 30 30 30 03 95")
                            comport['ser'].write(output)
                        if CleanStep == 2:
                            ReadInput=""
                            output =b"\x02\x06\xA0\x34\x30\x30\x03\x91"
                            print ("Reader Emulator: Sending: CARD_CLEAN_CARD(A0) Reply Step 2:  02 06 A0 34 30 30 03 91")
                            comport['ser'].write(output)
                            CleanStep=3
                        if CleanStep == 1:
                            ReadInput=""
                            output =b"\x02\x06\xA0\x31\x30\x30\x03\x97"
                            print ("Reader Emulator: Sending: CARD_CLEAN_CARD(A0) Reply Step 1:  02 06 A0 31 30 33 03 97")
                            comport['ser'].write(output)
                            CleanStep=2
                        if CleanStep == 0:
                            ReadInput=""
                            output =b"\x02\x06\xA0\x30\x30\x33\x03\x96"
                            print ("Reader Emulator: Sending: CARD_CLEAN_CARD(A0) Reply Step 0:  02 06 A0 30 30 33 03 96")
                            comport['ser'].write(output)
                            CleanStep=1
                        
                    if CurrentCommand == CARD_LOAD_CARD and "05" in ReadInput:
                        if StepNum == 1:
                            output =b"\x02\x06\xB0\x31\x30\x32\x03\x86"
                            print ("Reader Emulator: Sending: CARD_LOAD_CARD Command(B0) Reply Step 1: 02 06 B0 31 30 32 03 86")
                            comport['ser'].write(output)
                            StepNum=2
                        if StepNum == 0:
                            output =b"\x02\x06\xB0\x31\x30\x30\x03\x84"
                            print ("Reader Emulator: Sending: CARD_LOAD_CARD Command(B0) Reply Step 0: 02 06 B0 31 30 30 03 84")
                            comport['ser'].write(output)
                            StepNum=1
                        ReadInput=""
                        
                    if CurrentCommand == CARD_IS_PRESENT and "05" in ReadInput:
                        output =b"\x02\x06\x40\x30\x30\x30\x03\x75"
                        print ("Reader Emulator: Sending: Card Is Present Command(40) Reply: 02 06 40 30 30 30 03 75")
                        comport['ser'].write(output)
                        ReadInput=""
                        
                    if CurrentCommand == CARD_GET_CARD_STATE and "05" in ReadInput:
                        output =b"\x02\x06\x20\x30\x30\x30\x03\x15"
                        #print ("Reader Emulator: Sending: CARD_GET_CARD_STATE Command(20) Reply: 02 06 20 31 30 30 03 14")
                        print ("Reader Emulator: Sending: CARD_GET_CARD_STATE Command(20) Reply: 02 06 20 30 30 30 03 15")
                        comport['ser'].write(output)
                        ReadInput=""
                        
                    if CurrentCommand == CARD_7D and "05" in ReadInput:
                        output =b"\x02\x06\x7D\x31\x30\x30\x03\x49"
                        print ("Reader Emulator: Sending: CARD_7D Command(7D) Reply: 02 06 7D 31 30 30 03 49")
                        comport['ser'].write(output)
                        ReadInput=""
                        
                    if CurrentCommand == CARD_7A and "05" in ReadInput:
                        output =b"\x02\x06\x7A\x30\x30\x30\x03\x4F"
                        print ("Reader Emulator: Sending: CARD_7A Command(7A) Reply: 02 06 7A 30 30 30 03 4F")
                        comport['ser'].write(output)
                        ReadInput=""
                        
                    if CurrentCommand == CARD_WRITE and "05" in ReadInput:
                        output =b"\x02\x06\x53\x31\x30\x30\x03\x67"
                        print ("Received new card data!")
                        CardBytes= CardTranslationWMMT(PriorCommand,CardFileName)
                        print (CardBytes)
                        print ("Reader Emulator: Sending: CARD_WRITE Command(53) Reply: 02 06 53 31 30 30 03 67")
                        comport['ser'].write(output)
                        ReadInput=""
                        
                    if CurrentCommand == CARD_WRITE_TEXT and "05" in ReadInput:
                        output =b"\x02\x06\x7C\x31\x30\x30\x03\x48"
                        print ("Reader Emulator: Sending: CARD_WRITE_TEXT Command(7C) Reply: 02 06 7C 31 30 30 03 48")
                        comport['ser'].write(output)
                        ReadInput=""
                        
                    if CurrentCommand == CARD_80 and "05" in ReadInput:
                        output =b"\x02\x06\x80\x30\x30\x30\x03\xB5"
                        print ("Reader Emulator: Sending: CARD_80 Command(80) Reply - CARD EJECTED: 02 06 80 30 30 30 03 B5")
                        comport['ser'].write(output)
                        ReadInput=""
                        StepNum=0
                        HaveCard=1

                    if HaveCard==0:
                        if CurrentCommand == CARD_READ and "05" in ReadInput:
                            output =b"\x02\x06\x33\x30\x30\x34\x03\x02"
                            print ("Reader Emulator: Sending: Read Command(33) Reply - No Card:  02 06 33 30 30 34 03 02")
                            comport['ser'].write(output)
                            ReadInput=""
                            
                    if HaveCard==1:
                        if "02 09 33 00 00 00 32 31 30 03 0A" in PriorCommand and CurrentCommand == CARD_READ and "05" in ReadInput:
                            output =b"\x02\x06\x33\x31\x30\x30\x03\x07"
                            print ("Reader Emulator: Sending: Read Command(33) Reply - Have Card:  02 06 33 31 30 30 03 07")
                            comport['ser'].write(output)
                            ReadInput=""
                            
                        if CurrentCommand == CARD_READ and "05" in ReadInput:
                            output = CardBytes
                            print ("Reader Emulator: Sending: Read Command(33) Reply - Have Card - Sending Card Data")
                            print (CardBytes)
                            comport['ser'].write(output)
                            ReadInput=""

                    if ReadInput:
                        print ("ERROR: Emulator is ignoring or does not know how to respond to: " + ReadInput )
                        ReadInput=""
                    
                sys.stdout.flush()
    except KeyboardInterrupt:
        sys.exit(1)

if __name__ == "__main__": main()
