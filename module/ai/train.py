import os
from chatterbot import ChatBot
from chatterbot.trainers import ListTrainer
Chinese_bot = ChatBot("BLBot")
Chinese_boter = ListTrainer(Chinese_bot)
lines = open("data.txt","r",encoding='utf-8').readlines()
sec = [ line.strip() for line in lines]
Chinese_boter.train(sec)
