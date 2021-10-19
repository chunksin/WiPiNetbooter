#!/usr/bin/python

# Triforce Netfirm Toolbox, put into the public domain. 
# Please attribute properly, but only if you want.

# Written by debugmode
# Trimmed to be exportable by Capane.us

import struct, sys
import socket
import time
import gzip
import os

#from Adafruit_CharLCDPlate import Adafruit_CharLCDPlate

s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)

def connect(ip, port):
	global s
        s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
	s.connect((ip, port))

def disconnect():
	global s
	s.close()

# a function to receive a number of bytes with hard blocking
def readsocket(n):
	res = ""
	while len(res) < n:
		res += s.recv(n - len(res))
	return res

# Peeks 16 bytes from Host (gamecube) memory
def HOST_Read16(addr):
	s.send(struct.pack("<II", 0xf0000004, addr))
	data = readsocket(0x20)
	res = ""
	for d in xrange(0x10):
		res += data[4 + (d ^ 3)]
	return res

# same, but 4 bytes.
def HOST_Read4(addr, type = 0):
	s.send(struct.pack("<III", 0x10000008, addr, type))
	return s.recv(0xc)[8:]

def HOST_Poke4(addr, data):
	s.send(struct.pack("<IIII", 0x1100000C, addr, 0, data))

def HOST_Restart():
	s.send(struct.pack("<I", 0x0A000000))

def DIMM_CheckOff():
        s.send(struct.pack(">IIIIIIIIH", 0x00000001, 0x1a008104, 0x01000000, 0xf0fffe3f, 0x0000ffff, 0xffffffff, 0xffff0000, 0x00000000, 0x0000))


# Read a number of bytes (up to 32k) from DIMM memory (i.e. where the game is). Probably doesn't work for NAND-based games.
def DIMM_Read(addr, size):
	s.send(struct.pack("<III", 0x05000008, addr, size))
	return readsocket(size + 0xE)[0xE:]

def DIMM_GetInformation():
	s.send(struct.pack("<I", 0x18000000))
	return readsocket(0x10)

def DIMM_SetInformation(crc, length):
	s.send(struct.pack("<IIII", 0x1900000C, crc & 0xFFFFFFFF, length, 0))

def DIMM_Upload(addr, data, mark):
	s.send(struct.pack("<IIIH", 0x04800000 | (len(data) + 0xA) | (mark << 16), 0, addr, 0) + data)

def NETFIRM_GetInformation():
	s.send(struct.pack("<I", 0x1e000000))
	return s.recv(0x404)

def CONTROL_Read(addr):
	s.send(struct.pack("<II", 0xf2000004, addr))
	return s.recv(0xC)

def SECURITY_SetKeycode(data):
	assert len(data) == 8
	s.send(struct.pack("<I", 0x7F000008) + data)

def HOST_SetMode(v_and, v_or):
	s.send(struct.pack("<II", 0x07000004, (v_and << 8) | v_or))
	return readsocket(0x8)

def DIMM_SetMode(v_and, v_or):
	s.send(struct.pack("<II", 0x08000004, (v_and << 8) | v_or))
	return readsocket(0x8)

def DIMM22(data):
	assert len(data) >= 8
	s.send(struct.pack("<I", 0x22000000 | len(data)) + data)

def MEDIA_SetInformation(data):
	assert len(data) >= 8
	s.send(struct.pack("<I",	0x25000000 | len(data)) + data)

def MEDIA_Format(data):
	s.send(struct.pack("<II", 0x21000004, data))

def TIME_SetLimit(data):
	s.send(struct.pack("<II", 0x17000004, data))

def DIMM_DumpToFile(file):
	for x in xrange(0, 0x20000, 1):
		file.write(DIMM_Read(x * 0x8000, 0x8000))
		sys.stderr.write("%08x\r" % x)

def HOST_DumpToFile(file, addr, len):
	for x in range(addr, addr + len, 0x10):
#		if not (x & 0xFFF):
		sys.stderr.write("%08x\r" % x)
		file.write(HOST_Read16(x))

def getuncompressedsize(filename):
	with open(filename, 'rb') as f:
		f.seek(-4, 2)
		return struct.unpack('I', f.read(4))[0]

def getPercent(first, second, integer = False):
	percent = first / second * 100
	if integer:
		return int(percent)
	return percent

# upload a file into DIMM memory, and optionally encrypt for the given key.
# note that the re-encryption is obsoleted by just setting a zero-key, which
# is a magic to disable the decryption.
def DIMM_UploadFile(name, key = None):
	import zlib
	crc = 0
	if name.endswith(".gz"):
		a = gzip.open(name, 'rb')
	else:
		a = open(name, "rb")
	addr = 0
	f = getuncompressedsize(name)
	sys.stderr.write("Filesize: ")
	sys.stderr.write(str(f))
	sys.stderr.write("\n")
        progressfile = open("/var/log/progress.txt", "w")
	last = 0
	if key:
		d = DES.new(key[::-1], DES.MODE_ECB)
	while True:
		i = int("%08x\r" % addr, 16)
		progress = str(i)+"/"+str(f)
		percentage = str(getPercent(float(i),f,True))
		status = str(progress)+" "+str(percentage)+"%"+"\r"
		sys.stderr.write(status)
		sys.stderr.flush()
		progressfile.write(percentage)
		progressfile.write("\n")
		progressfile.flush()
		data = a.read(0x8000)
		if not len(data):
			break
		if key:
			data = d.encrypt(data[::-1])[::-1]
		DIMM_Upload(addr, data, 0)
		crc = zlib.crc32(data, crc)
		addr += len(data)
	crc = ~crc
	DIMM_Upload(addr, "12345678", 1)
	DIMM_SetInformation(crc, addr)
	time.sleep(0.2)
        progressfile.write("COMPLETE")
	progressfile.close()

# obsolete
def PATCH_MakeProgressCode(x):
	#addr = 0x80066ed8 # 2.03
	#addr = 0x8005a9c0 # 1.07
	#addr = 0x80068304 # 2.15
	addr = 0x80068e0c # 3.01
	HOST_Poke4(addr + 0, 0x4e800020)
	HOST_Poke4(addr + 4, 0x38a00000 | x)
	HOST_Poke4(addr + 8, 0x90a30000)
	HOST_Poke4(addr + 12, 0x38a00000)
	HOST_Poke4(addr + 16, 0x60000000)
	HOST_Poke4(addr + 20, 0x4e800020)
	HOST_Poke4(addr + 0, 0x60000000)

#obsolete
def PATCH_MakeContentError(x):
	#addr = 0x80066b30 # 2.03
	#addr = 0x8005a72c # 1.07
	#addr = 0x80067f5c # 2.15
	addr = 0x8005a72c # 3.01
	HOST_Poke4(addr + 0, 0x4e800020)
	HOST_Poke4(addr + 4, 0x38a00000 | x)
	HOST_Poke4(addr + 8, 0x90a30000)
	HOST_Poke4(addr + 12, 0x38a00000)
	HOST_Poke4(addr + 16, 0x60000000)
	HOST_Poke4(addr + 20, 0x4e800020)
	HOST_Poke4(addr + 0, 0x60000000)

# this essentially removes a region check, and is triforce-specific; It's also segaboot-version specific.
# - look for string: "CLogo::CheckBootId: skipped."
# - binary-search for lower 16bit of address
def PATCH_CheckBootID():

	# 3.01
	addr = 0x8000dc5c
	HOST_Poke4(addr + 0, 0x4800001C)
	return

	addr = 0x8000CC6C # 2.03, 2.15
	#addr = 0x8000d8a0 # 1.07
	HOST_Poke4(addr + 0, 0x4e800020)
	HOST_Poke4(addr + 4, 0x38600000)
	HOST_Poke4(addr + 8, 0x4e800020)
	HOST_Poke4(addr + 0, 0x60000000)
