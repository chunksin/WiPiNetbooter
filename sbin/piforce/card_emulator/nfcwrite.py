#!/usr/bin/python -u
from smartcard.CardRequest import CardRequest
from smartcard.Exceptions import CardRequestTimeoutException
from smartcard.CardType import AnyCardType
from smartcard import util
from time import sleep
import sys
import os

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

if __name__ == '__main__':
    # respond to the insertion of any type of smart card

    card_type = AnyCardType()
    cardfile = sys.argv[1]
    printfile = sys.argv[2]
    cardstart = 0x04
    cardend = 0x3A
    printstart = 0x3B
    printend = 0x80

    print('<br>Setting Control File To Writing Mode<br>')
    controlfile = open('/sbin/piforce/nfccontrol.txt', 'w')
    controlfile.write('writing')
    controlfile.flush()
    controlfile.close
    print('Control File Set To Writing Mode<br>')

    # create the request. Wait for up to x seconds for a card to be attached
    request = CardRequest(timeout=10, cardType=card_type)

    # listen for the card
    while True:
        service = None
        try:
            service = request.waitforcard()
        except CardRequestTimeoutException:
            print("ERROR: No card detected<br>")
            print('Print Data Write Timeout<br>')
            print('Setting Control File Back To Reading Mode<br>')
            print('Card Write Failed<br>')
            controlfile = open('/sbin/piforce/nfccontrol.txt', 'w')
            controlfile.write('reading')
            controlfile.close
            exit(-1)

    # when a card is attached, open a connection
        sleep(0.1)
        conn = service.connection
        conn.connect()

        cardstart = 0x04
        cardend = 0x3A
        printstart = 0x3B
        printend = 0x80

        CHUNK_SIZE = 4
        f = open(cardfile, 'rb')
        chunk = f.read(CHUNK_SIZE)
        while chunk:
            for x in range(cardstart, cardend):
                chunkdata_list = util.toBytes(chunk.hex())
                xhex = hex(x).lstrip('0x')
                if len(xhex) == 1:
                    xhex = "0"+str(xhex)
                if len(chunkdata_list) < 4:
                    padding = 4 - len(chunkdata_list)
                    for p in range(0,padding):
                        chunkdata_list.append(0)
                command_list = (util.toBytes("FF D6 00 "+xhex+" 04 "))
                write_data = [*command_list, *chunkdata_list]
                writestuff, sw1, sw2 = conn.transmit(write_data)
                chunk = f.read(CHUNK_SIZE) #read the next chunk
                if not chunk:
                    break
        f.close()
        status = util.toHexString([sw1, sw2])
        if (status == '90 00'):
            print('Card Data Write Successful<br>')
        else:
            print('Print Data Write Timeout<br>')
            print('Setting Control File Back To Reading Mode<br>')
            print('Card Write Complete<br>')
            controlfile = open('/sbin/piforce/nfccontrol.txt', 'w')
            controlfile.write('reading')
            controlfile.close
            break

        f = open(printfile, 'rb')
        chunk = f.read(CHUNK_SIZE)
        while chunk:
            for x in range(printstart, printend):
                chunkdata_list = util.toBytes(chunk.hex())
                xhex = hex(x).lstrip('0x')
                if len(xhex) == 1:
                    xhex = "0"+str(xhex)
                if len(chunkdata_list) < 4:
                    padding = 4 - len(chunkdata_list)
                    for p in range(0,padding):
                        chunkdata_list.append(0)
                command_list = (util.toBytes("FF D6 00 "+xhex+" 04 "))
                write_data = [*command_list, *chunkdata_list]
                writestuff, sw1, sw2 = conn.transmit(write_data)
                chunk = f.read(CHUNK_SIZE) #read the next chunk
                if not chunk:
                    break
        f.close()
        status = util.toHexString([sw1, sw2])
        if (status == '90 00'):
            lightbuzz = util.toBytes("FF 00 40 41 04 03 03 02 02")
            writelightbuzz, sw1, sw2 = conn.transmit(lightbuzz)
            print('Print Data Write Successful<br>')
            print('Setting Control File Back To Reading Mode<br>')
            print('Card Write Complete<br>')
            controlfile = open('/sbin/piforce/nfccontrol.txt', 'w')
            controlfile.write('reading')
            controlfile.close
        else:
            print('Print Data Write Timeout<br>')
            print('Setting Control File Back To Reading Mode<br>')
            print('Card Write Complete<br>')
            controlfile = open('/sbin/piforce/nfccontrol.txt', 'w')
            controlfile.write('reading')
            controlfile.close
        break