<?php

requireLvl(6);

// Download http://appqiniuyun.hhhtmetro.com/HS_latest.apk
// And simply extract it. Data will be found in assets/ folder.
$files = getCacheFolderContents('toilet/huhhot/');
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['呼和浩特地铁'] = [];

foreach($files as $fileName){
	if(preg_match('/^line\d+_zdxx.json$/', $fileName)){
		$stations = json_decode(getCache('toilet/huhhot/'.$fileName), true)['List'];
		foreach($stations as $station){
			if($data['呼和浩特地铁'][$station['stationName']]) continue;
			$toilets = explode('、', $station['stationwc']);
			foreach($toilets as $id => $toilet){
				$toilets[$id] = '［卫生间］'.$toilet;
			}
			$data['呼和浩特地铁'][$station['stationName']] = implode("\n", $toilets);
		}
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['呼和浩特地铁']).' 条数据');

?>
