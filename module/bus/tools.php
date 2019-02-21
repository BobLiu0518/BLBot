<?php

function unicode_encode($name)
{
    $name = iconv('UTF-8', 'UCS-2', $name);
    $len = strlen($name);
    $str = '';
    for ($i = 0; $i < $len - 1; $i = $i + 2)
    {
        $c = $name[$i];
        $c2 = $name[$i + 1];
        if (ord($c) > 0)
        {    // 两个字节的文字
            $str .= '%u'.base_convert(ord($c2), 10, 16).base_convert(ord($c), 10, 16);
        }
        else
        {
            $str .= $c2;
        }
    }
    return $str;
}

?>
