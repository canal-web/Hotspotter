<?php

class Anny_Hotspotter_Block_Hotspot extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface {
	protected function _toHtml() {
		$this->setTemplate('hotspotter/hotspot.phtml');
		return parent::_toHtml();
	}

	public function getOriginalDimensions() {
		return Mage::helper('hotspotter/image')->getDimensions($this->getData('image'));
	}

	public function getEmbedDimensions() {
		if ($size = $this->getData('imagesize')) {
			preg_match_all('/\d+/', $size, $matches);
			if (count($matches[0])) {
				$w = reset($matches[0]);
				$h = next($matches[0]);
				return array($w, $h);
			}
		}
		return $this->getOriginalDimensions();
	}

	public function resizeImage($constrain=false, $aspect=true, $frame=false) {
		list($w, $h) = $this->getEmbedDimensions();
		return Mage::helper('hotspotter/image')->resizeImage(
			$this->getData('image'),
			$w, $h,
			$constrain,
			$aspect,
			$frame
		);
	}

	protected $_spot;
	protected $_spotFields = array('spot%dtype', 'spot%dvalue', 'spot%dxy');

	protected function _spotField($f) {
		return sprintf($f, $this->_spot);
	}

	protected function getSpot($i) {
		$this->_spot = $i;
		$fields = array_map(array($this, '_spotField'), $this->_spotFields);
		$data = array();
		foreach ($fields as $field) {
			$nf = preg_replace('/^spot\d+/', '', $field);
			$d = $this->getData($field);
			if ($nf == 'xy') {
				preg_match_all('/\d+/', $d, $m);
				$d = $m[0];
				if (2 > count($d)) {
					$d[] = end($d);
				}
			}
			$data[$nf] = $d;
		}
		return $data;
	}

	const SPOTS_MAX = 3;

	public function getSpots() {
		$spots = array();
		for ($i = 0; $i < self::SPOTS_MAX; $i++) {
			$spot = $this->getSpot($i+1);
			if ($spot['type'] == Anny_Hotspotter_Model_System_Config_Source_Hotspot_Type::DISABLED) {
				continue;
			}
			$spots[$i+1] = $spot;
		}
		return $spots;
	}

	protected function getLeft($spot) {
		list($w, $h) = $this->getOriginalDimensions();
		$p = 100*($spot['xy'][0]/$w);
		return sprintf('%.1f%%', $p);
	}

	protected function getTop($spot) {
		list($w, $h) = $this->getOriginalDimensions();
		$p = 100*($spot['xy'][1]/$h);
		return sprintf('%.1f%%', $p);
	}

	protected function getContent($spot) {
		return $spot['value'];
	}
}
