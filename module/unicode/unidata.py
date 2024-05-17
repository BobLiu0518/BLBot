#!/bin/python3

import sys, re
import unicodedata as ucd

char = sys.argv[1]
match = re.match(r'^(?:U\+|\\\\u)([0-9A-F]+)$', char, re.I)
if match:
	char = chr(int(match.group()[2:], 16))
else:
	try:
		ucd.name(char)
	except TypeError:
		try:
			char = ucd.lookup(char)
		except KeyError:
			print('未找到', sys.argv[1], '…')
			exit()

try:
	name = ucd.name(char)
except ValueError:
	name = '(No name)'

codepoint = 'U+' + hex(ord(char))[2:].upper()
category = ucd.category(char)

print('「' + char + '」', codepoint)
print('‣', name)
print('‣', 'Category:', category)
