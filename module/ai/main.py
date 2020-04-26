import sys
from chatterbot import ChatBot
Chinese_bot = ChatBot("BLBot")
print(Chinese_bot.get_response(sys.argv[1]))
