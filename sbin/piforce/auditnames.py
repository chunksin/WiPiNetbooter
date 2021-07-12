#!/usr/bin/python -u

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

from os import listdir, rename
from os.path import isfile, join
import sys
import mmap
import csv
import gzip
from filehash import FileHash

sys.stdout = Unbuffered(sys.stdout)

print('Scanning - Please Wait<br><br>')

rom_path = "/boot/roms/"

onlyfiles = [f for f in listdir(rom_path) if isfile(join(rom_path,f))]
cleanup = ['in','japan','usa','(usa)','(jp)','/usa','-','.','version','ver','u.s.a','(',')','jpn','jap']
gamename = ''
success = 0
unknown = []
goodfiles = []
failures = []
dupes = []
gameinfo = {}
mappings = {}
crc32hasher = FileHash('md5')

with open('/var/www/html/csv/romsinfo.csv', mode='r') as infile:
    reader = csv.reader(infile)
    gameinfo = {rows[17]:(rows[4]) for rows in reader}
infile.close()

for i in range(0, len(onlyfiles)):

	offset = 0

	def NaomiAudit(offset):
		s.seek(offset)
		if s.read(5).upper() == b'NAOMI':
			s.seek(offset + 5)
			if s.read(1) == b'2':
				type = 'n2-'
			else:
				type = 'n1-'
			s.seek(offset + 48)
			name = (s.read(32).decode("utf-8")).replace('\x00','')
			s.seek(offset + 308)
			code = (s.read(4).decode("utf-8")).replace('\x00','')
			gamename = ''
			name = "".join( x for x in name if (x.isalnum() or x in " ")).lower()
			name = name.split()
			for element in name:
				if element not in cleanup:
					gamename += element + ' '
			filename = type+((gamename.lower()).strip()+'_'+code+'.bin').replace(' ','_')
			gamename = ''
			if 'AW' in code:
				filename = filename.replace('n1-','aw-')
			return(filename)

	def ChihiroAudit(offset):
		s.seek(offset)
		pos = s.find(b'BTID')
		if (pos != -1):
			pos = pos+32
			s.seek(pos)
			if s.read(4) == b'XBAM':
				type = 'ch-'
			else:
				type = 'tf-'
			pos = pos+16
			s.seek(pos)
			code = (s.read(4).decode("utf-8")).replace('\x00','')
			pos = pos+80
			s.seek(pos)
			name = (s.read(32).decode("utf-8")).replace('\x00','')
			name = "".join( x for x in name if (x.isalnum() or x in " "))
			name = (name.lower().strip()).replace(" ","")
			name = name.replace('demo','')
			filename = type+name+'_'+code+'.bin'
			return(filename)
		

	with gzip.open(rom_path + onlyfiles[i], 'rb', 0) as file, \
	mmap.mmap(file.fileno(), 0, access=mmap.ACCESS_READ) as s:
		result = NaomiAudit(0)
		if result == None:
			result = NaomiAudit(4194304)
			if result == None:
				result = ChihiroAudit(0)
		if result != None:
			if result in goodfiles:
				dupes.append(onlyfiles[i])
			else:	
				if result in gameinfo:
					success = success + 1
					game = gameinfo[result]
					goodfiles.append(result)
					filehash = crc32hasher.hash_file(rom_path + onlyfiles[i])
					mappings[result] = [game, onlyfiles[i], filehash]
				else:
					game = 'Unknown Title'
					unknown.append(onlyfiles[i])
		if result == None:
			failures.append(onlyfiles[i])
		print('*')


print('<br><br>Scanning Complete<br>')
print('<br>')
print('<b>Scan Result Summary</b><br><br>')
print(str(i+1)+' files scanned')
print('<br>')
print(str(success)+' file(s) passed audit')
print('<br>')
print(str(len(failures))+' file(s) failed audit')
print('<br>')
print(str(len(dupes))+' duplicate rom(s) detected')
print('<br>')
print(str(len(unknown))+' file(s) not recognised')
print('<br>')

scriptfile = open('/var/www/html/auditscanresults.php', 'w+')
sorted = {k: v for k, v in sorted(mappings.items(), key=lambda item: item[1])}

i = 0
scriptfile.write('<?php\n')
for key, value in sorted.items():
	i = i + 1
	scriptfile.write('$auditname'+str(i)+' = \"'+key+'\";\n')
	scriptfile.write('$filename'+str(i)+' = \"'+value[1]+'\";\n')
	scriptfile.write('$gamename'+str(i)+' = \"'+value[0]+'\";\n')
	scriptfile.write('$filehash'+str(i)+' = \"'+value[2]+'\";\n')
i = 0
for duplicates in dupes:
	i = i + 1
	scriptfile.write('$duplicate'+str(i)+' = \''+dupes[i-1]+'\';\n')
i = 0
for fails in failures:
	i = i + 1
	scriptfile.write('$failure'+str(i)+' = \''+failures[i-1]+'\';\n')

i = 0
for files in unknown:
	i = i + 1
	scriptfile.write('$unknown'+str(i)+' = \''+unknown[i-1]+'\';\n')

scriptfile.write('$successes = '+str(success)+';\n')
scriptfile.write('$failures = '+str(len(failures))+';\n')
scriptfile.write('$duplicates = '+str(len(dupes))+';\n')
scriptfile.write('$unknowns = '+str(len(unknown))+';\n')
scriptfile.write('?>')