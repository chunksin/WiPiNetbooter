#!/usr/bin/env python3

"""
Initial D3 Card Reader Emulator v1.0
Programmed by: winteriscoming
Special thanks to: Metallic

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

    
def CardTranslation(NAOMIString,CardFileName):
    CARDString = NAOMIString[NAOMIString.find("02 D8 53 00 00 00 30 31 36")+27: NAOMIString.find("02 D8 53 00 00 00 30 31 36") +27 + 620]
    CardCRC = 0
    CARDString = "D5 33 78 30 30 " + CARDString + " 03"
    i=0
    for i in range(213):
        CardPartValue = int("0x" + CARDString[i*3 : i*3+2],0)
        CardCRC = CardCRC^CardPartValue
        #print ("Card Part: " + hex(CardPartValue))
    CardCRCText = hex(CardCRC)
    ConvertedCardValue = "02 " + CARDString + " " + CardCRCText[2:]
    print ("Converted Card Value: " + ConvertedCardValue)
    CardValueList = []
    
    for i in range(215):
        CardPartValue = int("0x" + ConvertedCardValue[i*3 : i*3+2],0)
        #print (str(i) + ":Converting " + ConvertedCardValue[i*3 : i*3+2] + " to HEX:" + hex(CardPartValue) + " : DEC:"+ str(CardPartValue))
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
    WaitingForCard = 0
    CardInserted=0
    CardBytes = b""
    ReadInput = ""
    NewCard = 0
    CardData = ""
    NewGame = 0
    NewGameStep = 0

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
    comport['alias'] = "NAOMI2"
    comport['buffer'] = b''
    comport['ser'] = serial.Serial(comport['port'], baudrate=9600, timeout=0, parity=serial.PARITY_EVEN, bytesize = serial.EIGHTBITS, stopbits=serial.STOPBITS_ONE)
    comport['last_byte'] = clock()
    comport['ser'].setRTS(True)
    print ("COM port opened and named:", comport['alias'])
    print("CTS:", comport['ser'].getCTS())

    try:
        with open(CardFileName, "rb") as in_file:
            CardBytes = in_file.read()
        print ("Read in Card Data from " + CardFileName)
        
        if CardBytes == b"":
            print (CardFileName + " card file is empty.  You will have to select to create a new card in the game.")
            NewCard=1
        else:
            print (CardFileName + " appears to contain data.  This card data will be loaded when a game is started.")
    except:
        with open(CardFileName, "wb") as out_file:
            out_file.write(CardBytes)
        print (CardFileName + " not found.  Created " + CardFileName + ". You will have to select to create a new card in the game.")
        NewCard = 1
        

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

                    if ReadInput[ReadInput.find("7C")-6:].find("02")==0:
                        print ("Received print command!")
                        output =b"\x06\x02\x06\x7C\x67\x30\x30\x03\x1E"
                        print ("Reader Emulator: Sending:  06 02 06 7C 67 30 30 03 1E")
                        comport['ser'].write(output)
                        ReadInput=""
                        WaitingForCard=0
                        CardInserted = 1
                        NewGame=0
                    
                    if WaitingForCard and "02 07 D0 00 00 00 30 03 E4" in ReadInput:
                        output = b"\x06\x02\x06\xD0\xB8\x30\x33\x03\x6E"
                        print ("Reader Emulator: Waiting For Card: Sending:  06 02 06 D0 B8 30 33 03 6E")
                        comport['ser'].write(output)
                        ReadInput=""
                        WaitingForCard=1
                        CardInserted=0

                    if WaitingForCard and "02 06 40 00 00 00 03 45" in ReadInput:
                        output = b"\x06\x02\x06\x40\xA0\x30\x30\x03\xE5"
                        print ("Reader Emulator: NEW GAME: Stop Waiting for Card: Sending:  06 02 06 40 A0 30 30 03 E5")
                        print ("New Game Started!")
                        comport['ser'].write(output)
                        ReadInput=""
                        WaitingForCard=0
                        CardInserted=0
                        NewGame=1

                    if NewGame:

                        if NewGameStep == 0 and "02 06 40 00 00 00 03 45" in ReadInput:
                            output =b"\x06\x02\x06\x40\xA0\x30\x32\x03\xE7"
                            print ("Reader Emulator: NEW GAME0: Sending: 06 02 06 40 A0 30 32 03 E7")
                            comport['ser'].write(output)
                            ReadInput=""
                            NewGameStep = 1

                        if NewGameStep == 1 and "02 06 20 00 00 00 03 25" in ReadInput:
                            output =b"\x06\x02\x06\x20\xA0\x30\x30\x03\x85"
                            print ("Reader Emulator: NEW GAME1: Sending: 06 02 06 20 A0 30 30 03 85")
                            comport['ser'].write(output)
                            ReadInput=""
                            NewGameStep = 2
                                               
                        if NewGameStep == 2 and "02 06 40 00 00 00 03 45" in ReadInput:
                            output =b"\x06\x02\x06\x40\xA0\x30\x32\x03\xE7"
                            print ("Reader Emulator: NEW GAME2: Sending: 06 02 06 40 A0 30 32 03 E7")
                            comport['ser'].write(output)
                            ReadInput=""
                            NewGameStep = 3
                            
                        if NewGameStep == 3 and "02 07 D0 00 00 00 30 03 E4" in ReadInput:
                            output =b"\x06\x02\x06\xD0\xA0\x30\x33\x03\x76"
                            print ("Reader Emulator: NEW GAME3: Sending: 06 02 06 D0 A0 30 33 03 76")
                            ReadInput=""
                            WaitingForCard=0
                            NewGameStep = 4

                        if NewGameStep == 4 and "02 07 D0 00 00 00 30 03 E4" in ReadInput:
                            output =b"\x06\x02\x06\xD0\x60\x30\x30\x03\xB5"
                            print ("Reader Emulator: NEW GAME4: Sending: 02 06 D0 60 30 30 03 B5")
                            comport['ser'].write(output)                            
                            ReadInput=""
                            WaitingForCard=0
                            NewGameStep =5
                            
                        if NewGameStep == 4 and "05" in ReadInput and len(ReadInput)<4:
                            output =b"\x06\x02\x06\xD0\x60\x30\x30\x03\xB5"
                            print ("Reader Emulator: NEW GAME4: Sending: 02 06 D0 60 30 30 03 B5")
                            comport['ser'].write(output)
                            ReadInput=""
                            WaitingForCard=0
                            NewGameStep =5
                            
                        if NewGameStep == 5 and "02 06 40 00 00 00 03 45" in ReadInput:
                            output =b"\x06\x02\x06\x40\x60\x30\x32\x03\x27"
                            print ("Reader Emulator: NEW GAME5: Sending: 06 02 06 40 60 30 32 03 27")
                            comport['ser'].write(output)
                            ReadInput=""
                            WaitingForCard=0
                            NewGameStep =6
                            
                        if NewGameStep == 6 and "02 06 20 00 00 00 03 25" in ReadInput:
                            output =b"\x06\x02\x06\x20\x60\x30\x30\x03\x45"
                            print ("Reader Emulator: NEW GAME6: Sending: 06 02 06 20 60 30 30 03 45")
                            comport['ser'].write(output)
                            ReadInput=""
                            NewGameStep = 7

                        if NewGameStep == 7 and "02 06 40 00 00 00 03 45" in ReadInput:
                            output =b"\x06\x02\x06\x40\x60\x30\x32\x03\x27"
                            print ("Reader Emulator: NEW GAME7: Sending: 06 02 06 40 60 30 32 03 27")
                            comport['ser'].write(output)
                            ReadInput=""
                            NewGameStep = 8

                        if NewGameStep == 8 and "02 07 B0 00 00 00 31 03 85" in ReadInput:
                            output =b"\06\x02\x06\xB0\x7C\x30\x30\x03\xC9"
                            print ("Reader Emulator: NEW GAME8:  Sending: 06 02 06 B0 7C 30 30 03 C9")
                            comport['ser'].write(output)
                            ReadInput=""
                            NewGameStep = 9

                        if NewGameStep == 9 and "02 06 40 00 00 00 03 45" in ReadInput:
                            output =b"\x06\x02\x06\x40\x7C\x30\x32\x03\x3B\x06\x02\x06\x20\x7C\x30\x30\x03\x59\x06\x02\x06\x40\x7C\x30\x32\x03\x3B"
                            print ("Reader Emulator: NEW GAME9: Sending: 06 02 06 40 7C 30 32 03 3B 06 02 06 20 7C 30 30 03 59 06 02 06 40 7C 30 32 03 3B")
                            comport['ser'].write(output)
                            ReadInput=""
                            NewGameStep = 10

                        if NewGameStep == 10 and "02 06 20 00 00 00" in ReadInput and len(ReadInput)>200:
                            output =b"x06\x02\x06\x7C\x67\x30\x30\x03\x1E"
                            print ("Reader Emulator: NEW GAME10: Sending: 06 02 06 7C 67 30 30 03 1E")
                            comport['ser'].write(output)
                            ReadInput=""
                            NewGameStep = 11

                        if NewGameStep == 11 and "02 06 40 00 00 00 03 45" in ReadInput:
                            output =b"\x06\x02\x06\x40\x67\x30\x32\x03\x20"
                            print ("Reader Emulator: NEW GAME11: Sending: 06 02 06 40 67 30 32 03 20")
                            comport['ser'].write(output)
                            ReadInput=""
                            NewGameStep = 12
                            
                        if NewGameStep == 12 and "02 06 20 00 00 00 03 25 05" in ReadInput:
                            output =b"\x06\x02\x06\x20\x67\x30\x30\x03\x42\x06\x02\x06\x40\x67\x30\x32\x03\x20"
                            print ("Reader Emulator: NEW GAME12: Sending: 06 02 06 20 67 30 30 03 42 06 02 06 40 67 30 32 03 20")
                            comport['ser'].write(output)
                            ReadInput=""
                            #NewGameStep = 13
                            WaitingForCard=0
                            NewGame=0
                            NewGameStep = 0

                    if len(CardData)>0 and CardInserted and "02 06 20 00 00 00 03 25" in ReadInput:
                        output =b'\x06'
                        comport['ser'].write(output)
                        output = CardBytes
                        comport['ser'].write(output)
                        print ("Reader Emulator: 5 Sending SIMULATING CARD:  SENDING CARD DATA")
                        ReadInput=""
                        WaitingForCard=0

                    if CardInserted and "02 06 40 00 00 00 03 45" in ReadInput:
                        output =b"\x06\x02\x06\x40\xA0\x30\x32\x03\xE7"
                        print ("Reader Emulator: Card Inserted: Sending: 06 02 06 40 A0 30 32 03 E7")
                        comport['ser'].write(output)
                        ReadInput=""

                    if len(ReadInput)>241 and "02 4F 7A 00 00 00" in ReadInput and "02 06 40 00 00 00 03 45" in ReadInput:
                        output =b"\x06\x02\x06\x7A\xA0\x30\x32\x03\xDD"
                        print ("Reader Emulator: Sending:  06 02 06 7A A0 30 32 03 DD")
                        output =b"\x06\x02\x06\x40\xA0\x30\x32\x03\xE7"
                        print ("Reader Emulator: 2nd Part of Response: Sending: 06 02 06 40 A0 30 32 03 E7")
                        comport['ser'].write(output)
                        ReadInput=""
                        WaitingForCard = 0

                    if len(CardData)>0 and "02 06 40 00 00 00 03 45" in ReadInput:
                        output =b"\x06\x02\x06\x40\xA0\x30\x32\x03\xE7"
                        print ("Reader Emulator: Have Card Data: Sending: 06 02 06 40 A0 30 32 03 E7")
                        comport['ser'].write(output)
                        ReadInput=""
                        WaitingForCard = 0
                        
                    if len(CardData)>0 and "02 06 40 00 00 00 03 45" in ReadInput:
                        output =b"\x06\x02\x06\x40\xA0\x30\x32\x03\xE7"
                        print ("Reader Emulator: Have Card Data: Sending: 06 02 06 40 A0 30 32 03 E7")
                        comport['ser'].write(output)
                        ReadInput=""
                        WaitingForCard = 0

                    if "02 06 40 00 00 00 03 45" in ReadInput:
                        output =b"\x06\x02\x06\x40\xA0\x30\x32\x03\xE7"
                        print ("Reader Emulator: Sending: 06 02 06 40 A0 30 32 03 E7")
                        comport['ser'].write(output)
                        ReadInput=""
                        
                    if CardInserted and"02 06 20 00 00 00 03 25" in ReadInput:
                        output =b"\x06\x02\x06\x20\x78\x30\x30\x03\x5D"
                        print ("Reader Emulator: Card Inserted: Sending: 06 02 06 20 78 30 30 03 5D")
                        comport['ser'].write(output)
                        ReadInput=""
                        WaitingForCard = 0
                        
                    if CardInserted and"02 09 33 00 00 00 30 31 36 03 0E" in ReadInput:
                        output =b'\x06'
                        comport['ser'].write(output)
                        output = CardBytes
                        comport['ser'].write(output)
                        print ("Reader Emulator: Card Inserted: 4 Sending SIMULATING CARD:  SENDING CARD DATA")
                        ReadInput=""
                        WaitingForCard=0
                        CardInserted=1
                        
                    if "02 06 20 00 00 00 03 25" in ReadInput:
                        output =b"\x06\x02\x06\x20\xA0\x30\x30\x03\x85"
                        print ("Reader Emulator: Sending: 06 02 06 20 A0 30 30 03 85")
                        comport['ser'].write(output)
                        ReadInput=""
                        WaitingForCard = 0

                    if "02 07 10 00 00 00 31 03 25" in ReadInput:
                        output =b"\x06\x02\x06\x10\xA0\x30\x30\x03\xB5"
                        comport['ser'].write(output)
                        print ("Reader Emulator: Sending:  06 02 06 10 A0 30 30 03 B5")
                        ReadInput=""
                        WaitingForCard = 0

                    if len(ReadInput)>241 and "02 4F 7A 00 00 00" in ReadInput:
                        output =b"\x06\x02\x06\x7A\xA0\x30\x32\x03\xDD"
                        print ("Reader Emulator: Sending:  06 02 06 7A A0 30 32 03 DD")
                        comport['ser'].write(output)
                        ReadInput=""
                        WaitingForCard = 0

                    if "02 09 33 00 00 00 32 31 36 03 0C" in ReadInput:
                        output =b"\x06\x02\x06\x33\xA0\x30\x33\x03\x95"
                        print ("Reader Emulator: Sending:  WAITING FOR CARD - BEGIN:  06 02 06 33 A0 30 33 03 95")
                        comport['ser'].write(output)
                        ReadInput=""
                        WaitingForCard=1
                        if len(CardData)>0:
                            CardInserted=1
                        
                    if "02 06 20 00 00 00 03 25" in ReadInput:
                        output =b"\x06\x02\x06\x20\xA0\x30\x30\x03\x85"
                        print ("Reader Emulator: Sending:  06 02 06 20 A0 30 30 03 85")
                        comport['ser'].write(output)
                        ReadInput=""
                        WaitingForCard=0

                    if "02 06 80 00 00 00 03 85" in ReadInput:
                        output =b"\x06\x02\x06\x20\xA0\x30\x30\x03\x85"
                        print ("Reader Emulator: Sending:  06 02 06 20 A0 30 30 03 85")
                        comport['ser'].write(output)
                        ReadInput=""
                        WaitingForCard=0
                        
                    if "02 09 33 00 00 00 30 31 36 03 0E" in ReadInput:
                        output =b'\x06'
                        comport['ser'].write(output)
                        output = CardBytes
                        comport['ser'].write(output)
                        print ("Reader Emulator: 3 Sending SIMULATING CARD:  SENDING CARD DATA")
                        ReadInput=""
                        WaitingForCard=0

                    if "02 07 D0 00 00 00 30 03 E4" in ReadInput:
                        output =b'\x06'
                        comport['ser'].write(output)
                        output = CardBytes
                        comport['ser'].write(output)
                        print ("Reader Emulator: 2 Sending SIMULATING CARD:  SENDING CARD DATA")
                        ReadInput=""
                        WaitingForCard=0

                    if WaitingForCard and "05" in ReadInput and len(ReadInput)<4 and not NewCard:
                        output =b"\x02\x06\x33\xB8\x30\x30\x03\x8E"
                        print ("Reader Emulator: Sending: WAITING FOR CARD - SIMULATING CARD NOW:  02 06 33 B8 30 30 03 8E")
                        comport['ser'].write(output)
                        ReadInput=""
                        WaitingForCard=0
                        CardInserted=1

                    if "09 33 00 00 00 30 31 36 03 0E" in ReadInput:
                        output =b'\x06'
                        comport['ser'].write(output)
                        output = CardBytes
                        comport['ser'].write(output)
                        print ("Reader Emulator: 1 Sending SIMULATING CARD:  SENDING CARD DATA")
                        ReadInput=""
                        WaitingForCard=0

                    if "02 07 D0 00 00 00 31 03 E5" in ReadInput:
                        output =b"\x06\x02\x06\xD0\x60\x30\x30\x03\xB5"
                        print ("Reader Emulator: Sending: EJECTED CARD:  06 02 06 D0 60 30 30 03 B5")
                        comport['ser'].write(output)
                        ReadInput=""
                        WaitingForCard=0
                        CardInserted=0

                    if "02 D8 53 00 00 00" in ReadInput and len(ReadInput)>650:
                        print ("Received new card data!")
                        CardData=ReadInput
                        CardBytes =  CardTranslation(CardData,CardFileName)
                        output =b"\x06\x02\x06\x53\x78\x30\x30\x03\x2E"
                        print ("Reader Emulator: Sending:  06 02 06 53 78 30 30 03 2E")
                        comport['ser'].write(output)
                        ReadInput=""
                        WaitingForCard=0
                        NewGame=0
                        NewCard=0
                        NewGameStep=0
                        CardInserted=1
                        
                    if WaitingForCard and "05" in ReadInput and len(ReadInput)<4:
                        output =b"\x02\x06\x33\xA0\x30\x34\x03\x92"
                        print ("Reader Emulator: Sending: WAITING FOR CARD:  02 06 33 A0 30 34 03 92")
                        comport['ser'].write(output)
                        ReadInput=""
                        
                    if "05" in ReadInput and len(ReadInput)<4:
                        output =b"\x06"
                        print ("Reader Emulator: Sending:  06")
                        comport['ser'].write(output)
                        ReadInput=""
                        WaitingForCard = 0

                    if ReadInput:
                        print ("ERROR: Emulator does not know how to respond to: " + ReadInput )
                        ReadInput=""
                    
                sys.stdout.flush()
    except KeyboardInterrupt:
        sys.exit(1)

if __name__ == "__main__": main()
