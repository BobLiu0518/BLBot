<?php

class Horse{
	private $distance;
	private $maxDistance;
	private $dead;
	private $disappeared;
	private $nb;
//	private const normalHorse = "[CQ:emoji,id=128052]";
//	private const nbHorse = "🦄"; //[CQ:emoji,id=129412]
//	private const deadHorse = "👻";
	private $normalHorse;
	private $nbHorse;
	private $deadHorse;

	function __construct($n = 10, $m = 13, $h = "🐴", $nh = "🦄", $dh = "👻"){
		$this->maxDistance = $m;
		$this->distance = $n;
		$this->dead = false;
		$this->disappeared = false;
		$this->normalHorse = $h;
		$this->nbHorse = $nh;
		$this->deadHorse = $dh;
	}
	private function str_suffix($str, $n=1, $char=" "){
		for ($x=0;$x<$n;$x++){$str = $str.$char;}
		return $str;
	}
	public function getChar(){
		if($this->dead && (!$this->disappeared))
			return $this->deadHorse;
		else if($this->dead && $this->disappeared)
			return '　';
		else if($this->nb)
			return $this->nbHorse;
		else
			return $this->normalHorse;
	}
	public function display(){
		$str = $this->str_suffix("", $this->distance);
		$str .= $this->getChar();
		$str = $this->str_suffix($str, $this->maxDistance - $this->distance);
		return $str;
	}
	public function goAhead($n){
		$this->distance -= $n;
		if($this->distance < 0)
			$this->distance = 0;
		return;
	}
	public function goBack($n){
		$this->distance += $n;
		if($this->distance > $this->maxDistance)
			$this->distance = $this->maxDistance;
		return;
	}
	public function goTo($n){
		$this->distance = $n;
		return;
	}
	public function kill($disappeared = false){
		$this->dead = true;
		$this->disappeared = $disappeared;
		return;
	}
	public function makeAlive(){
		$this->dead = false;
		$this->disappeared = false;
		return;
	}
	public function nbIfy(){
		$this->nb = true;
		return;
	}
	public function sbIfy(){
		$this->nb = false;
		return;
	}
	public function isNb(){
		return $this->nb;
	}
	public function isDead(){
		return $this->dead;
	}
	public function isDisappeared(){
		return $this->disappeared;
	}
	public function isWin(){
		return (!$this->dead)&&(!$this->distance);
	}
	public function isFinished(){
		return !$this->distance;
	}
}

?>
