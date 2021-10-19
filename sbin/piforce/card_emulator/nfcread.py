from time import sleep
from smartcard.CardMonitoring import CardMonitor, CardObserver
from smartcard.util import *
from smartcard import util
import glob

class transmitobserver(CardObserver):

    def __init__(self):
        self.cards = []

    def update(self, observable, actions):
        (addedcards, removedcards) = actions
        controlfile = open('/sbin/piforce/nfccontrol.txt', 'r')
        nfcstate = controlfile.read()
        controlfile.close
        if nfcstate != 'reading':
            self.cards = []
        for card in addedcards:
            if card not in self.cards:
                cardstart = 0x04
                cardend = 0x3A
                printstart = 0x3B
                printend = 0x80
                DropFolderGlobPath = "/var/log/activecard/*"
                status = ''
                cardresult = []
                printresult = []
                self.cards += [card]
                print("+Inserted: ", toHexString(card.atr))
                if nfcstate == 'reading':
                    card.connection = card.createConnection()
                    card.connection.connect()
                    for x in range(cardstart, cardend):
                        xhex = hex(x).lstrip('0x')
                        if len(xhex) == 1:
                            xhex = "0"+str(xhex)
                        get_data = util.toBytes("FF B0 00 "+xhex+" 04")
                        cardsave_data, sw1, sw2 = card.connection.transmit(get_data)
                        cardresult = [*cardresult, *cardsave_data]
                        status = util.toHexString([sw1, sw2])

                    if (status == "90 00"):
                        for x in range(printstart, printend):
                            xhex = hex(x).lstrip('0x')
                            if len(xhex) == 1:
                                xhex = "0"+str(xhex)
                            get_data = util.toBytes("FF B0 00 "+xhex+" 04")
                            cardprint_data, sw1, sw2 = card.connection.transmit(get_data)
                            printresult = [*printresult, *cardprint_data]
                            status = util.toHexString([sw1, sw2])
                
                    if (status == "90 00"):
                        print('Data Read Successful')
                        lightbuzz = util.toBytes("FF 00 40 71 04 03 03 01 02")
                        writelightbuzz, sw1, sw2 = card.connection.transmit(lightbuzz)
                        cardprinttext = util.toASCIIString(printresult)
                        cardsavehex = util.toHexString(cardresult)
                        cardvaluelist = []
                        for i in range(215):
                            cardpartvalue = int("0x" + cardsavehex[i*3 : i*3+2],0)
                            cardvaluelist.append(cardpartvalue)
                        nullcheck = cardvaluelist.count(cardvaluelist[0]) == len(cardvaluelist)
                        dropfoldercheck = glob.glob(DropFolderGlobPath)
                        if nullcheck:
                            print('All values are the same - card is blank')
                            if not dropfoldercheck:
                                print('Creating blank file for save data')
                                with open('/var/log/activecard/NFC_Card', 'w') as empty:
                                    pass
                            else:
                                print("Card Already Present in Drop Folder")
                        elif ('?>' in cardprinttext):
                            cardbytes = b""
                            cardbytes = bytearray(cardvaluelist)
                            if not dropfoldercheck:
                                file = open('/var/log/activecard/NFC_Card', 'wb')
                                file.write(cardbytes)
                                file.flush()
                                file.close()
                                file = open('/var/log/printdata/NFC_Card.printdata.php', 'w')
                                file.write(cardprinttext)
                                file.flush()
                                file.close()
                                file = open('/sbin/piforce/nfcwriteback.txt', 'w')
                                file.write('yes')
                                file.flush()
                                file.close()
                                print("Valid Card Save Data Found")
                                print("No Card Present in Drop Folder")
                                print("Copying Card Data to Drop Folder")
                                print("Card Data Follows:")
                                print("")
                                print(cardsavehex)
                                print("")
                                print(cardprinttext)
                            else:
                                print("Valid Card Save Data Found")
                                print("Card Already Present in Drop Folder")
                                print("Card Data Follows:")
                                print("")
                                print(cardsavehex)
                                print("")
                                print(cardprinttext)
                        else:
                            print('Data Found But Not Valid Card Data')
                            lightbuzz = util.toBytes("FF 00 40 71 04 03 03 01 02")
                            writelightbuzz, sw1, sw2 = card.connection.transmit(lightbuzz)
                    else:
                        print("Data Read Failed")
                        lightbuzz = util.toBytes("FF 00 40 71 04 03 03 03 02")
                        writelightbuzz, sw1, sw2 = card.connection.transmit(lightbuzz)

        for card in removedcards:
            print("-Removed: ", toHexString(card.atr))
            if card in self.cards:
                self.cards.remove(card)

if __name__ == '__main__':
    print("Present your NFC Smart Card")
    cardmonitor = CardMonitor()
    cardobserver = transmitobserver()
    cardmonitor.addObserver(cardobserver)
    x = False
    while not x:
        sleep(100)