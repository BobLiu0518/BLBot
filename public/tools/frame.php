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
function config(string $key, string $defaultValue = NULL):string{
    global $Config;

    if(array_key_exists($key, $Config)){
        return $Config[$key];
    }else{
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
function sendPM(string $msg, bool $auto_escape = false, bool $async = false):Message{
    global $Event;

    return new Message($msg, $Event['user_id'], false, $auto_escape, $async);
}

/**
 * 消息从哪来发到哪
 * @param string $msg 消息内容
 * @param bool $auto_escape 是否发送纯文本
 * @param bool $async 是否异步
 * @return kjBot\Frame\Message
 */
function sendBack(string $msg, bool $auto_escape = false, bool $async = false):Message{
    global $Event;

    return new Message($msg, isset($Event['group_id'])?$Event['group_id']:$Event['user_id'], isset($Event['group_id']), $auto_escape, $async);
}

/**
 * 发送给 Master
 * @param string $msg 消息内容
 * @param bool $auto_escape 是否发送纯文本
 * @param bool $async 是否异步
 * @return kjBot\Frame\Message
 */
function sendMaster(string $msg, bool $auto_escape = false, bool $async = false):Message{
    return new Message($msg, config('master'), false, $auto_escape, $async);
}

function sendDevGroup(string $msg, bool $auto_escape = false, bool $async = false):Message{
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
function setData(string $filePath, $data, bool $pending = false){
    return file_put_contents('../storage/data/'.$filePath, $data, $pending?(FILE_APPEND | LOCK_EX):LOCK_EX);
}

function delData(string $filePath){
    return unlink('../storage/data/'.$filePath);
}

/**
 * 读取数据
 * @param $filePath 相对于 storage/data/ 的路径
 * @return mixed string|false
 */
function getData(string $filePath){
    return file_get_contents('../storage/data/'.$filePath);
}

/**
 * 缓存
 * @param string $cacheFileName 缓存文件名
 * @param $cache 要缓存的数据内容
 * @return mixed string|false
 */
function setCache(string $cacheFileName, $cache){
    return file_put_contents('../storage/cache/'.$cacheFileName, $cache, LOCK_EX);
}

/**
 * 取得缓存
 * @param $cacheFileName 缓存文件名
 * @return mixed string|false
 */
function getCache($cacheFileName){
    return file_get_contents('../storage/cache/'.$cacheFileName);
}

function getImg(string $filePath){
    return file_get_contents('../storage/img/'.$filePath);
}

/**
 * 清理缓存
 */
function clearCache(){
    $cacheDir = opendir('../storage/cache/');
    while (false !== ($file = readdir($cacheDir))) {
        if ($file != "." && $file != "..") {
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
function sendImg($str):string{
    return CQCode::Image('base64://'.base64_encode($str));
}

/**
 * 发送录音
 * @param string $str 录音（字符串形式）
 * @return string 录音对应的 base64 格式 CQ码
 */
function sendRec($str):string{
    return CQCode::Record('base64://'.base64_encode($str));
}

/**
 * 装载模块
 * @param string $module 模块名
 */
function loadModule(string $module){
    if($Event['user_id'] == "80000000")
        leave('请不要使用匿名！');
    if('.' === $module[0]){
        leave('非法命令！');
    }
    $moduleFile = str_replace('.', '/', $module, $count);
    if(0 === $count){
        $moduleFile.='/main.php';
    }else{
        $moduleFile.='.php';
    }

    if(file_exists('../module/'.$moduleFile)){
        if(config('recordStat', 'true')){
            if(strpos($module, '.tools')===false && strpos($module, 'recordStat')===false){ //防止记录工具类模块
                global $Event;
                addCommandCount($Event['user_id'], $module);
            }
        }
        require('../module/'.$moduleFile);
    }else{
        leave('没有该命令：#'.$module);
    }
}

/**
 * 解析命令
 * @param string $str 命令字符串
 * @return mixed array|bool 解析结果数组 失败返回false
 */
function parseCommand(string $str){
    // 正则表达式
    $regEx = '#(?:(?<s>[\'"])?(?<v>.+?)?(?:(?<!\\\\)\k<s>)|(?<u>[^\'"\s]+))#';
    // 匹配所有
    if(!preg_match_all($regEx, $str, $exp_list)) return false;
    // 遍历所有结果
    $cmd = array();
    foreach ($exp_list['s'] as $id => $s) {
        // 判断匹配到的值
        $cmd[] = empty($s) ? $exp_list['u'][$id] : $exp_list['v'][$id];
    }
    return $cmd;
}

function pd(){
    throw new UnauthorizedException();
}

/**
 * 继续执行脚本需要指定等级
 * 是就继续，不是就抛出异常，返回权限不足
 */
function requireLvl($lvl){
    global $Event;
    loadModule('exp.tools');
    if(getLvl($Event['user_id']) < $lvl){
         throw new LvlLowException($lvl, getLvl($Event['user_id']));
    }
}

/**
 * 判断是否是机器人主人
 * @param bool 是就return true，不是return false
 */
function isMaster(){
    global $Event;
    return $Event['user_id']==config('master');
}

/**
 * 继续执行脚本需要机器人主人权限
 * 是就继续，不是就抛出异常，返回权限不足
 */
function requireMaster(){
    if(!isMaster()){
        throw new UnauthorizedException();
    }
}

/**
 * 判断是否是机器人主人管理
 * @param bool 是就return true，不是return false
 */
function isSeniorAdmin(){
    if(isMaster()){
        return true;
    }
    global $Event;
    $qq = $Event['user_id'];
    $usertype = getData('usertype.json');
    if($usertype === false)return false; //无法打开黑名单时不再抛异常
    $usertype = json_decode($usertype)->SeniorAdmin;
    foreach($usertype as $person){
        if($qq == $person){
            return true;
        }
    }
    return false;
}

/**
 * 继续执行脚本需要管理权限
 * 是就继续，不是就抛出异常，返回权限不足
 */
function requireSeniorAdmin(){
    if(!isSeniorAdmin()){
        throw new UnauthorizedException();
    }
}

/**
 * 判断是否是机器人主人低管
 * @param bool 是就return true，不是return false
 */
function isAdmin(){
    if(isSeniorAdmin()){
        return true;
    }
    global $Event;
    $qq = $Event['user_id'];
    $usertype = getData('usertype.json');
    if($usertype === false)return false; //无法打开黑名单时不再抛异常
    $usertype = json_decode($usertype)->Admin;
    foreach($usertype as $person){
        if($qq == $person){
            return true;
        }
    }
    return false;
}

/**
 * 继续执行脚本需要低管权限
 * 是就继续，不是就抛出异常，返回权限不足
 */
function requireAdmin(){
    if(!isAdmin()){
        throw new UnauthorizedException();
    }
}

/**
 * 判断是否是Insider
 * @param bool 是就return true，不是return false
 */
function isInsider(){
    if(isSeniorAdmin()){
        return true;
    }
    global $Event;
    $qq = $Event['user_id'];
    $usertype = getData('usertype.json');
    if($usertype === false)return false; //无法打开黑名单时不再抛异常
    $usertype = json_decode($usertype)->Insider;
    foreach($usertype as $person){
        if($qq == $person){
            return true;
        }
    }
    return false;
}

/**
 * 继续执行脚本需要机器人主人权限
 * 是就继续，不是就抛出异常，返回权限不足
 */
function requireInsider(){
    if(!isInsider()){
        throw new InsiderRequiredException();
    }
}

function nextArg(){
    global $Command;
    static $index=0;

    return $Command[$index++];
}

/**
 * 冷却
 * 不指定冷却时间时将返回与冷却完成时间的距离，大于0表示已经冷却完成
 * @param string $name 冷却文件名称，对指定用户冷却需带上Q号
 * @param int $time 冷却时间
 */
function coolDown(string $name, $time = NULL):int{
    global $Event;
    if(NULL === $time){
        clearstatcache();
        return time() - filemtime("../storage/data/coolDown/{$name}")-(int)getData("coolDown/{$name}");
    }else{
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
function fromGroup($group = NULL):bool{
    global $Event;
    if($group == NULL){
        return isset($Event['group_id']);
    }else{
        return ($Event['group_id'] == $group);
    }
}

/**
 * 退出模块
 * @param string $msg 返回信息
 * @param int $code 指定返回码
 * @throws Exception 用于退出模块
 */
function leave($msg = '', $code = 0){
    throw new \Exception($msg, $code);
}

/**
 * 检查是否在黑名单中
 * @return bool
 */
function inBlackList($qq):bool{
    $usertype = getData('usertype.json');
    if($usertype === false)return false; //无法打开黑名单时不再抛异常
    $usertype = json_decode($usertype)->Blacklist;
    foreach($usertype as $person){
        if($qq == $person){
            return true;
        }
    }
    return false;
}

function block($qq){
    if($qq)if(inBlackList($qq))throw new UnauthorizedException();
}
