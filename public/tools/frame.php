<?php

use kjBot\SDK\CQCode;
use kjBot\Frame\Message;
use kjBot\Frame\UnauthorizedException;
use kjBot\Frame\LvlLowException;

error_reporting(E_ALL ^ E_WARNING);

/**
 * 读取配置文件
 * @param string $kay 键值
 * @param string $defaultValue 默认值
 * @return string|null
 */
function config(string $key, string $defaultValue = null): string {
    global $Config;

    if($Config && array_key_exists($key, $Config)) {
        return $Config[$key];
    } else {
        return $defaultValue;
    }
}

/**
 * 给事件产生者发送私聊
 * @param string $msg 消息内容
 * @param bool $auto_escape 是否发送纯文本
 * @param bool $async 是否异步
 * @return kjBot\Frame\Message
 */
function sendPM(string $msg, bool $auto_escape = false, bool $async = false): Message {
    global $Event;

    return new Message($msg, $Event['user_id'], false, $auto_escape, $async);
}

function sendBackImmediately(string $msg, bool $auto_escape = false): mixed {
    global $Event, $CQ;
    if(fromGroup()) {
        return $CQ->sendGroupMsg($Event['group_id'], $msg, $auto_escape)->message_id;
    } else {
        return $CQ->sendPrivateMsg($Event['user_id'], $msg, $auto_escape)->message_id;
    }
}

/**
 * 消息从哪来发到哪
 * @param string $msg 消息内容
 * @param bool $auto_escape 是否发送纯文本
 * @param bool $async 是否异步
 * @return kjBot\Frame\Message
 */
function sendBack(string $msg, bool $auto_escape = false, bool $async = false): Message {
    global $Event;

    return new Message($msg, isset($Event['group_id']) ? $Event['group_id'] : $Event['user_id'], isset($Event['group_id']), $auto_escape, $async);
}

function replyMessage(string $msg, bool $auto_escape = false, bool $async = false): Message {
    global $Event;
    $msg = '[CQ:reply,id='.$Event['message_id'].']'.$msg;
    // $msg = '[CQ:at,qq='.$Event['user_id']."]\n".$msg;
    if(!rand(0, 15)) {
        $msg = str_replace("哦～", "喵～", $msg);
    }
    return sendBack($msg, $auto_escape, $async);
}

function pokeBack(int $user_id = 0) {
    global $Event, $CQ;
    if(!$user_id) $user_id = $Event['user_id'];
    if($user_id == Config('bot')) return;
    if(fromGroup()) {
        $CQ->groupPoke($Event['group_id'], $user_id);
    } else {
        $CQ->friendPoke($user_id);
    }
    return;
}

/**
 * 发送给 Master
 * @param string $msg 消息内容
 * @param bool $auto_escape 是否发送纯文本
 * @param bool $async 是否异步
 * @return kjBot\Frame\Message
 */
function sendMaster(string $msg, bool $auto_escape = false, bool $async = false): Message {
    return new Message($msg, config('master'), false, $auto_escape, $async);
}

function sendDevGroup(string $msg, bool $auto_escape = false, bool $async = false): ?Message {
    if(config('devgroup'))
        return new Message($msg, config('devgroup'), true, $auto_escape, $async);
}
/**
 * 记录数据
 * @param string $filePath 相对于 storage/data/ 的路径
 * @param $data 要存储的数据内容
 * @param bool $pending 是否追加写入（默认不追加）
 * @return mixed string|false
 */
function setData(string $filePath, $data, bool $pending = false) {
    return file_put_contents('../storage/data/'.$filePath, $data, $pending ? (FILE_APPEND | LOCK_EX) : LOCK_EX);
}

function delData(string $filePath) {
    return unlink('../storage/data/'.$filePath);
}

/**
 * 读取数据
 * @param $filePath 相对于 storage/data/ 的路径
 * @return mixed string|false
 */
function getData(string $filePath) {
    return file_get_contents('../storage/data/'.$filePath);
}

function getDataPath(string $filePath) {
    return '../storage/data/'.$filePath;
}

function getDataFolderContents(string $folderPath) {
    $contents = scandir('../storage/data/'.$folderPath);
    return array_diff($contents, ['.', '..']);
}

/**
 * 缓存
 * @param string $cacheFileName 缓存文件名
 * @param $cache 要缓存的数据内容
 * @return mixed string|false
 */
function setCache(string $cacheFileName, $cache) {
    return file_put_contents('../storage/cache/'.$cacheFileName, $cache, LOCK_EX);
}

function delCache(string $filePath) {
    return unlink('../storage/cache/'.$filePath);
}

/**
 * 取得缓存
 * @param $cacheFileName 缓存文件名
 * @return mixed string|false
 */
function getCache($cacheFileName) {
    return file_get_contents('../storage/cache/'.$cacheFileName);
}

function getCachePath($cacheFileName) {
    return '../storage/cache/'.$cacheFileName;
}

function getCacheTime($cacheFileName) {
    return filemtime(getCachePath($cacheFileName));
}

function getCacheFolderContents(string $folderPath) {
    $contents = scandir('../storage/cache/'.$folderPath);
    return array_diff($contents, ['.', '..']);
}

function getAvatar($user_id, $large = false) {
    global $Config;
    $refreshInverval = $Config['avatarCacheTime'] ?? 86400;
    $size = $large ? 640 : 100;
    $cacheFile = "avatar/{$size}/{$user_id}";
    $avatar = getCache($cacheFile);
    if(!$avatar || time() - filemtime(getCachePath($cacheFile)) > $refreshInverval) {
        $avatar = file_get_contents("https://q1.qlogo.cn/g?b=qq&s={$size}&nk={$user_id}");
        $img = new Imagick();
        $img->readImageBlob($avatar);
        $img->setImageFormat('png');
        $img->setImageAlphaChannel(Imagick::ALPHACHANNEL_ACTIVATE);
        $avatar = $img->getImagesBlob();
        $img->clear();
        $img->destroy();
        setCache($cacheFile, $avatar);
    }
    return $avatar;
}

function getImg(string $filePath) {
    return file_get_contents('../storage/img/'.$filePath);
}

function getFontPath(string $fontName) {
    return '../storage/font/'.$fontName;
}

function getConfig($group_id) {
    $json = getData('config/'.$group_id.'.json');
    if(!$json) {
        $json = '{"mode":"blacklist","commands":[],"silence":false}';
    }
    return json_decode($json, true);
}

function setConfig($group_id, $data) {
    setData('config/'.$group_id.'.json', json_encode($data));
}

/**
 * 清理缓存
 */
function clearCache() {
    $cacheDir = opendir('../storage/cache/');
    while(false !== ($file = readdir($cacheDir))) {
        if($file != "." && $file != "..") {
            unlink('../storage/cache/'.$file);
        }
    }
    closedir($cacheDir);
}

/**
 * 发送图片
 * @param string $str 图片（字符串形式）
 * @return string 图片对应的 base64 格式 CQ码
 */
function sendImg($str): string {
    return CQCode::Image('base64://'.base64_encode($str));
}

/**
 * 发送录音
 * @param string $str 录音（字符串形式）
 * @return string 录音对应的 base64 格式 CQ码
 */
function sendRec($str): string {
    return CQCode::Record('base64://'.base64_encode($str));
}

/**
 * 装载模块
 * @param string $module 模块名
 */
function loadModule(string $module) {
    global $Event;
    static $config;
    if(fromGroup()) {
        if(!$config) {
            $config = getConfig($Event['group_id']);
        }
        $baseCommand = explode('.', $module)[0];
        $moduleInList = in_array($baseCommand, $config['commands']);
        if(!in_array($baseCommand, ['config']) && !preg_match('/\.tools$/', $module)) {
            if($moduleInList && $config['mode'] == 'blacklist' || !$moduleInList && $config['mode'] == 'whitelist') {
                if(!$config['silence']) {
                    replyAndLeave('该指令已被群指令配置禁用。');
                } else {
                    leave();
                }
            }
        }
    }

    if($Event['user_id'] == "80000000") {
        // $Queue[]= replyMessage('请不要使用匿名！');
        leave();
    }
    if('.' === $module[0]) {
        $Queue[] = replyMessage('非法命令！');
        leave();
    }
    $moduleFile = str_replace('.', '/', strtolower($module), $count);
    if(0 === $count) {
        $moduleFile .= '/main';
    }
    $moduleFile .= '.php';

    if(file_exists('../module/'.$moduleFile)) {
        require_once('../module/'.$moduleFile);
    } else if(strlen($module) <= 15 && $module != '接龙') {
        $prefix = config('prefix', '/');
        replyAndLeave("指令 {$prefix}{$module} 不存在哦…不知道怎么使用 Bot ？发送 {$prefix}help 即可查看帮助～");
    }
}

function checkModule(string $module) {
    global $Event;
    if('.' === $module[0]) {
        $Queue[] = replyMessage('非法命令！');
        leave();
    }
    $moduleFile = str_replace('.', '/', $module, $count);
    if(0 === $count) {
        $moduleFile .= '/main';
    }
    $moduleFile .= '.php';
    return file_exists('../module/'.$moduleFile);
}

/**
 * 解析命令
 * @param string $str 命令字符串
 * @return mixed array|bool 解析结果数组 失败返回false
 */
function parseCommand(string $str) {
    // 正则表达式
    $regEx = '#(?:(?<s>[\'"])?(?<v>.+?)?(?:(?<!\\\\)\k<s>)|(?<u>[^\'"\s]+))#';
    // 匹配所有
    if(!preg_match_all($regEx, $str, $exp_list)) return false;
    // 遍历所有结果
    $cmd = array();
    foreach($exp_list['s'] as $id => $s) {
        // 判断匹配到的值
        $cmd[] = empty($s) ? $exp_list['u'][$id] : $exp_list['v'][$id];
    }
    return $cmd;
}

function pd() {
    throw new UnauthorizedException();
}

/**
 * 继续执行脚本需要指定等级
 * 是就继续，不是就抛出异常，返回权限不足
 */
function requireLvl($lvl = 0, $msg = '本指令', $resolve = null) {
    global $Event;
    loadModule('exp.tools');
    if(intval(getLvl($Event['user_id'])) < $lvl) {
        throw new LvlLowException($lvl, getLvl($Event['user_id']), $msg, $resolve);
    }
}

/**
 * 判断是否是机器人主人
 * @param bool 是就return true，不是return false
 */
function isMaster() {
    global $Event;
    return $Event['user_id'] == config('master');
}

/**
 * 继续执行脚本需要机器人主人权限
 * 是就继续，不是就抛出异常，返回权限不足
 */
function requireMaster() {
    if(!isMaster()) {
        throw new UnauthorizedException();
    }
}

/**
 * 判断是否是机器人主人管理
 * @param bool 是就return true，不是return false
 */
function isSeniorAdmin() {
    if(isMaster()) {
        return true;
    }
    global $Event;
    $qq = $Event['user_id'];
    $usertype = getData('usertype.json');
    if($usertype === false) return false; //无法打开黑名单时不再抛异常
    $usertype = json_decode($usertype)->SeniorAdmin;
    foreach($usertype as $person) {
        if($qq == $person) {
            return true;
        }
    }
    return false;
}

/**
 * 继续执行脚本需要管理权限
 * 是就继续，不是就抛出异常，返回权限不足
 */
function requireSeniorAdmin() {
    if(!isSeniorAdmin()) {
        throw new UnauthorizedException();
    }
}

/**
 * 判断是否是机器人主人低管
 * @param bool 是就return true，不是return false
 */
function isAdmin() {
    if(isSeniorAdmin()) {
        return true;
    }
    global $Event;
    $qq = $Event['user_id'];
    $usertype = getData('usertype.json');
    if($usertype === false) return false; //无法打开黑名单时不再抛异常
    $usertype = json_decode($usertype)->Admin;
    foreach($usertype as $person) {
        if($qq == $person) {
            return true;
        }
    }
    return false;
}

/**
 * 继续执行脚本需要低管权限
 * 是就继续，不是就抛出异常，返回权限不足
 */
function requireAdmin() {
    if(!isAdmin()) {
        throw new UnauthorizedException();
    }
}

/**
 * 判断是否是Insider
 * @param bool 是就return true，不是return false
 */
function isInsider() {
    if(isSeniorAdmin()) {
        return true;
    }
    global $Event;
    $qq = $Event['user_id'];
    $usertype = getData('usertype.json');
    if($usertype === false) return false; //无法打开黑名单时不再抛异常
    $usertype = json_decode($usertype)->Insider;
    foreach($usertype as $person) {
        if($qq == $person) {
            return true;
        }
    }
    return false;
}

function nextArg(bool $getRemaining = false) {
    global $Command;
    static $index = 0;

    return $getRemaining ? implode(' ', array_slice($Command, $index)) : $Command[$index++];
}

/**
 * 冷却
 * 不指定冷却时间时将返回与冷却完成时间的距离，大于0表示已经冷却完成
 * @param string $name 冷却文件名称，对指定用户冷却需带上Q号
 * @param int $time 冷却时间
 */
function coolDown(string $name, $time = null): int {
    global $Event;
    if(null === $time) {
        clearstatcache();
        return time() - filemtime("../storage/data/coolDown/{$name}") - (int)getData("coolDown/{$name}");
    } else {
        setData("coolDown/{$name}", $time);
        return -$time;
    }
}

/**
 * 消息是否来自(指定)群
 * 指定参数时将判定是否来自该群
 * 不指定时将判定是否来自群聊
 * @param mixed $group=NULL 群号
 * @return bool
 */
function fromGroup($group = null): bool {
    global $Event;
    if($group == null) {
        return isset($Event['group_id']);
    } else {
        return ($Event['group_id'] == $group);
    }
}

/**
 * 退出模块
 * @param string $msg 返回信息
 * @param int $code 指定返回码
 * @throws Exception 用于退出模块
 */
function leave($msg = '', $code = 0): never {
    throw new \Exception($msg, $code);
}

function replyAndLeave($msg = '', $code = 0): never {
    global $Event;
    if($msg) {
        $msg = "[CQ:reply,id=".$Event['message_id']."]".$msg;
        // $msg = '[CQ:at,qq='.$Event['user_id']."]\n".$msg;
    }
    throw new \Exception($msg, $code);
}

/**
 * 检查是否在黑名单中
 * @return bool
 */
function inBlackList($qq): bool {
    $usertype = getData('usertype.json');
    if($usertype === false) return false; //无法打开黑名单时不再抛异常
    $usertype = json_decode($usertype)->Blacklist;
    foreach($usertype as $person) {
        if($qq == $person) {
            return true;
        }
    }
    return false;
}

function block($qq) {
    if($qq) if(inBlackList($qq)) exit;
}
