#!/usr/bin/python -u
from smartcard.CardRequest import CardRequest
from smartcard.Exceptions import CardRequestTimeoutException
from smartcard.CardType import AnyCardType
from smartcard import util
from time import sleep
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

if __name__ == '__main__':
    # respond to the insertion of any type of smart card

    card_type = AnyCardType()
    cardstart = 0x04
    cardend = 0x3A
    printstart = 0x3B
    printend = 0x80
    cardresult = []
    printresult = []
    cardtype = 'none'

    print('<br><b>Card Data Check</b><br>')
    print('<br>Setting Control File To Checking Mode<br>')
    controlfile = open('/sbin/piforce/nfccontrol.txt', 'w')
    controlfile.write('checking')
    controlfile.flush()
    controlfile.close
    print('Control File Set To Checking Mode<br>')

    # create the request. Wait for up to x seconds for a card to be attached
    request = CardRequest(timeout=10, cardType=card_type)

    # listen for the card
    while True:
        service = None
        try:
            service = request.waitforcard()
        except CardRequestTimeoutException:
            print("ERROR: No card detected<br>")
            break

        sleep(0.1)
        conn = service.connection
        conn.connect()
        for x in range(cardstart, cardend):
            xhex = hex(x).lstrip('0x')
            if len(xhex) == 1:
                xhex = "0"+str(xhex)
            get_data = util.toBytes("FF B0 00 "+xhex+" 04")
            cardsave_data, sw1, sw2 = conn.transmit(get_data)
            cardresult = [*cardresult, *cardsave_data]
            status = util.toHexString([sw1, sw2])

        if (status == "90 00"):
            for x in range(printstart, printend):
                xhex = hex(x).lstrip('0x')
                if len(xhex) == 1:
                    xhex = "0"+str(xhex)
                get_data = util.toBytes("FF B0 00 "+xhex+" 04")
                cardprint_data, sw1, sw2 = conn.transmit(get_data)
                printresult = [*printresult, *cardprint_data]
                status = util.toHexString([sw1, sw2])
                
        if (status == "90 00"):
            print('Data Read Successful<br>')
            lightbuzz = util.toBytes("FF 00 40 71 04 03 03 01 02")
            writelightbuzz, sw1, sw2 = conn.transmit(lightbuzz)
            cardprinttext = util.toASCIIString(printresult)
            cardsavehex = util.toHexString(cardresult)
            cardvaluelist = []
            for i in range(215):
                cardpartvalue = int("0x" + cardsavehex[i*3 : i*3+2],0)
                cardvaluelist.append(cardpartvalue)
            nullcheck = cardvaluelist.count(cardvaluelist[0]) == len(cardvaluelist)
            if nullcheck:
                print('All values are the same - card is blank<br>')
            elif ('?>' in cardprinttext):
                if ('53 45 47 41 42 46 46 37' in cardsavehex):
                    cardtype = 'idas'
                if ('53 45 47 41 42 46 53 30' in cardsavehex):
                    cardtype = 'id2'
                if ('53 45 47 41 42 48 52 33' in cardsavehex):
                    cardtype = 'id3'
                file = open('/var/www/html/cards/'+cardtype+'/NFC_Check.printdata.php', 'w')
                file.write(cardprinttext)
                file.flush()
                file.close()
                print("Valid Card Save Data Found<br>")
                #print("Copying Card Data to Web Folder<br>")
            else:
                print('Data Found But Not Valid Card Data<br>')
                lightbuzz = util.toBytes("FF 00 40 71 04 03 03 01 02")
                writelightbuzz, sw1, sw2 = conn.transmit(lightbuzz)
        else:
            print("Data Read Failed<br>")
            lightbuzz = util.toBytes("FF 00 40 71 04 03 03 03 02")
            writelightbuzz, sw1, sw2 = conn.transmit(lightbuzz)
        break

    file = open('/var/log/cardcheck/NFC_Check', 'w')
    file.write(cardtype)
    file.flush()
    file.close()
    print('Setting Control File Back To Reading Mode<br>')
    print('Card Check Complete<br>')
    controlfile = open('/sbin/piforce/nfccontrol.txt', 'w')
    controlfile.write('reading')
    controlfile.close
