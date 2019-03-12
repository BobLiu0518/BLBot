<?php

class RCNB {

	// char
	private $cr = ['r', 'R', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ʀ', 'Ȑ', 'ȑ', 'Ȓ', 'ȓ', 'Ɍ', 'ɍ'];
	private $cc = ['c', 'C', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ƈ', 'ƈ', 'Ç', 'Ȼ', 'ȼ'];
	private $cn = ['n', 'N', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'Ɲ', 'ƞ', 'Ñ', 'Ǹ', 'ǹ', 'Ƞ', 'ȵ'];
	private $cb = ['b', 'B', 'ƀ', 'Ɓ', 'ƃ', 'Ƅ', 'ƅ', 'ß', 'Þ', 'þ'];

	// reverse char
	private $rcr;
	private $rcc;
	private $rcn;
	private $rcb;

	// size
	private $sr;
	private $sc;
	private $sn;
	private $sb;
	private $src;
	private $snb;
	private $scnb;

	function __construct() {
		$this->rcr = array_flip($this->cr);
		$this->rcc = array_flip($this->cc);
		$this->rcn = array_flip($this->cn);
		$this->rcb = array_flip($this->cb);

		$this->sr = count($this->cr);
		$this->sc = count($this->cc);
		$this->sn = count($this->cn);
		$this->sb = count($this->cb);
		$this->src = $this->sr * $this->sc;
		$this->snb = $this->sn * $this->sb;
		$this->scnb = $this->sc * $this->snb;
	}

	private function _encodeByte(int $i) : string {
		if ($i > 0xFF) throw new Exception('rc/nb overflow');
		if ($i > 0x7F) {
			$i = $i & 0x7F;
			return $this->cn[intdiv($i, $this->sb)].$this->cb[$i % $this->sb];
		}
		return $this->cr[intdiv($i, $this->sc)].$this->cc[$i % $this->sc];
	}

	private function _encodeShort(int $i) : string {
		if ($i > 0xFFFF) throw new Exception('rcnb overflow');
		$reverse = false;
		if ($i > 0x7FFF) {
			$reverse = true;
			$i = $i & 0x7FFF;
		}
		$char = [
			intdiv($i, $this->scnb),
			intdiv($i % $this->scnb, $this->snb),
			intdiv($i % $this->snb, $this->sb),
			$i % $this->sb
		];
		$char = [
			$this->cr[$char[0]],
			$this->cc[$char[1]],
			$this->cn[$char[2]],
			$this->cb[$char[3]]
		];
		if ($reverse) {
			return $char[2].$char[3].$char[0].$char[1];
		}
		return implode('', $char);
	}

	private function _decodeByte(string $c) : int {
		$nb = false;
		$idx = [
			$this->rcr[mb_substr($c, 0, 1)] ?? false,
			$this->rcc[mb_substr($c, 1, 1)] ?? false
		];
		if ($idx[0] === false || $idx[1] === false) {
			$idx = [
				$this->rcn[mb_substr($c, 0, 1)] ?? false,
				$this->rcb[mb_substr($c, 1, 1)] ?? false
			];
			$nb = true;
		}
		if ($idx[0] === false || $idx[1] === false) throw new Exception('not rc/nb');
		$result = $nb ? $idx[0] * $this->sb + $idx[1] : $idx[0] * $this->sc + $idx[1];
		if ($result > 0x7F) throw new Exception('rc/nb overflow');
		return $nb ? $result | 0x80 : $result;
	}

	private function _decodeShort(string $c) : int {
		$idx = [];
		$reverse = !isset($this->rcr[mb_substr($c, 0, 1)]);
		if (!$reverse) {
			$idx = [
				$this->rcr[mb_substr($c, 0, 1)] ?? false,
				$this->rcc[mb_substr($c, 1, 1)] ?? false,
				$this->rcn[mb_substr($c, 2, 1)] ?? false,
				$this->rcb[mb_substr($c, 3, 1)] ?? false
			];
		} else {
			$idx = [
				$this->rcr[mb_substr($c, 2, 1)] ?? false,
				$this->rcc[mb_substr($c, 3, 1)] ?? false,
				$this->rcn[mb_substr($c, 0, 1)] ?? false,
				$this->rcb[mb_substr($c, 1, 1)] ?? false
			];
		}
		if ($idx[0] === false || $idx[1] === false || $idx[2] === false || $idx[3] === false) throw new Exception('not rcnb');
		$result = $idx[0] * $this->scnb + $idx[1] * $this->snb + $idx[2] * $this->sb + $idx[3];
		if ($result > 0x7FFF) throw new Exception('rcnb overflow');
		return $reverse ? $result | 0x8000 : $result;
	}

	public function encode(string $data) : string {
		$len = strlen($data);
		$output = '';
		// encode every 2 bytes
		for ($i = 0; $i < ($len >> 1); $i++) {
			$output .= $this->_encodeShort((ord($data{$i * 2}) << 8) | ord($data{$i * 2 + 1}));
		}
		// encode tailing byte
		if ($len & 1) $output .= $this->_encodeByte(ord(substr($data, -1)));
		return $output;
	}

	public function decode(string $str) : string {
		$len = mb_strlen($str);
		if ($len & 1) throw new Exception('invalid length');
		$output = '';
		// decode every 2 bytes (1 rcnb = 2 bytes)
		for ($i = 0; $i < ($len >> 2); $i++) {
			$short = $this->_decodeShort(mb_substr($str, $i * 4, 4));
			$output .= chr($short >> 8);
			$output .= chr($short & 0xFF);
		}
		// decode tailing byte (1 rc / 1 nb = 1 byte)
		if ($len & 2) $output .= chr($this->_decodeByte(mb_substr($str, -2, 2)));
		return $output;
	}
}
