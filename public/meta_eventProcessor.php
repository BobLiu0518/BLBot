<?php

global $Queue, $CQ;

$cd = 2*60; // 2分钟cd
$now = time();
$lastUpdated = intval(trim(getCache("biliDynamicLastUpdated")));
$dynamicApi = "https://api.bilibili.com/x/polymer/web-dynamic/v1/feed/space?host_mid=";
$updating = intval(trim(getCache("biliDynamicUpdating")));

ini_set('user_agent', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36');
$context = stream_context_create(['http' => ['header' => 'Cookie: SESSDATA='.getData('bili/api/sessdata')]]);

if($updating != 1 && (!$lastUpdated || ($now > $lastUpdated + $cd))) { // 过cd更新内容
	// setCache("biliDynamicUpdating", 1);
	setCache("biliDynamicLastUpdated", $now);

	// 整理要爬的up的列表
	$groups = getDataFolderContents('bili/subscription/config/');
	$ups = array();
	foreach($groups as $group){
		$subs = json_decode(getData('bili/subscription/config/'.$group), true)['sub'];
		foreach($subs as $sub){
			$ups[$sub][] = $group;
		}
	}

	// 按up挨个爬取
	foreach($ups as $up => $groups){
		$dynamics = json_decode(file_get_contents($dynamicApi.$up, false, $context), true)['data']['items'];
		$lastDynamic = getData('bili/subscription/data/'.$up);
		$latestDynamic = $lastDynamic;

		foreach($dynamics as $dynamic){
			if(!$lastDynamic || $dynamic['modules']['module_author']['pub_ts'] > $lastDynamic){

				// 查查最后一个动态是哪个
				if($dynamic['modules']['module_author']['pub_ts'] > $latestDynamic){
					$latestDynamic = $dynamic['modules']['module_author']['pub_ts'];
				}
				if(!$lastDynamic){
					continue;
				}

				try{
					// 准备消息
					$name = $dynamic['modules']['module_author']['name'];
					$action = $dynamic['modules']['module_author']['pub_action'];
					if(!$action)
						$action = '发布了动态';
					$id = $dynamic['id_str'];
					$time = date('Y/m/d H:i:s', $dynamic['modules']['module_author']['pub_ts']);
					$text = trim($dynamic['modules']['module_dynamic']['desc']['text']);
					if($dynamic["type"] == "DYNAMIC_TYPE_FORWARD"){
						$text .= '//@'.$dynamic["orig"]["modules"]["module_author"]["name"].":";
						$text .= $dynamic["orig"]["modules"]["module_dynamic"]["desc"]["rich_text_nodes"][0]["type"] == "RICH_TEXT_NODE_TYPE_LOTTERY"?
							'[互动抽奖]': $dynamic["orig"]["modules"]["module_dynamic"]["desc"]["text"];
						$text = rtrim($text);
						switch($dynamic['orig']['modules']['module_dynamic']['major']['type']){
							case "MAJOR_TYPE_DRAW":
								foreach($dynamic['orig']['modules']['module_dynamic']['major']['draw']['items'] as $pic){
									$text .= "[图片]";
								}
								break;
							case "MAJOR_TYPE_ARCHIVE":
								$text .= "[视频]".$dynamic['orig']['modules']['module_dynamic']['major']['archive']['title'];
								$text .= " (https://b23.tv/av".$dynamic['orig']['modules']['module_dynamic']['major']['archive']['aid'].")";
								break;
							default:
								break;
						}
					}
					$msg = <<<EOT
群订阅的UP {$name} {$action}：
{$time}
https://t.bilibili.com/{$id}


EOT;
					if($text){
						$msg .= $text;
					}
					switch($dynamic['modules']['module_dynamic']['major']['type']){
						case "MAJOR_TYPE_DRAW":
							foreach($dynamic['modules']['module_dynamic']['major']['draw']['items'] as $pic){
								$msg .= "\n[CQ:image,file=".$pic['src']."]";
							}
							break;
						case "MAJOR_TYPE_ARCHIVE":
							$msg .= $dynamic['modules']['module_dynamic']['major']['archive']['title'];
							$msg .= " (https://b23.tv/av".$dynamic['modules']['module_dynamic']['major']['archive']['aid'].")";
							$msg .= "\n[CQ:image,file=".$dynamic['modules']['module_dynamic']['major']['archive']['cover']."]";
							break;
						case "MAJOR_TYPE_LIVE_RCMD":
							$liveData = json_decode($dynamic['modules']['module_dynamic']['major']['live_rcmd']['content'], true);
							$msg .= $liveData['live_play_info']['title'];
							$msg .= "\nhttps:".$liveData['live_play_info']['link'];
							$msg .= "\n[CQ:image,file=".$liveData['live_play_info']['cover']."]";
							$msg .= "\n注：标注时间为B站发布直播推荐动态的时间，并非直播开始时间";
							break;
						default:
							break;
					}
				}catch(Exception $e){
					$Queue[]= sendMaster('刷新B站订阅时发生了错误：'.$e);
				}finally{
					// 发消息
					foreach($groups as $group){
						$CQ->sendGroupMsg($group, trim($msg));
						sleep(5);
					}
				}
			}else if($dynamic['modules']['module_tag']['text'] != "置顶"){
				// 不是置顶且是之前发过的，直接跑路节省时间
				break;
			}
		}
		setData('bili/subscription/data/'.$up, $latestDynamic);
		sleep(5);
	}
	setCache("biliDynamicUpdating", 0);
}

?>
