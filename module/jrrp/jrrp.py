# Reference:
# https://github.com/SkyDynamic/nonebot_plugin_jrrp/blob/main/nonebot_plugin_jrrp/__init__.py

import sys

def rol(num: int, k: int, bits: int = 64):
    b1 = bin(num << k)[2:]
    if len(b1) <= bits:
        return int(b1, 2)
    return int(b1[-bits:], 2)

def get_hash(string: str):
    num = 5381
    num2 = len(string) - 1
    for i in range(num2 + 1):
        num = rol(num, 5) ^ num ^ ord(string[i])
    return num ^ 12218072394304324399

def get_jrrp():
    num = round(abs((get_hash("".join([
        "asdfgbn",
        sys.argv[4], # YDay
        "12#3$45",
        sys.argv[2], # Year
        "IUY"
    ])) / 3 + get_hash("".join([
        "QWERTY",
        sys.argv[1], # User ID
        "0*8&6",
        sys.argv[3], # MDay
        "kjhg"
    ])) / 3) / 527) % 1001)
    if num >= 970:
        num2 = 100
    else:
        num2 = round(num / 969 * 99)
    return num2

print(get_jrrp(), end='')
