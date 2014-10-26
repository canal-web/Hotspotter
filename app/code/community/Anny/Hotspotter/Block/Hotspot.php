<?php

class Anny_Hotspotter_Block_Hotspot extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface {
	protected function _toHtml() {
		$this->setTemplate('hotspotter/hotspot.phtml');
		return parent::_toHtml();
	}

	public function getOriginalDimensions() {
		return Mage::helper('hotspotter/image')->getDimensions($this->getData('image'));
	}

	protected function parseDimensions($size, $allowPercent=true) {
		if (strpos($size, ',') !== false) {
			$d = explode(',', $size);
			array_walk($d, 'trim');
			list($w, $h) = $d;
		}
		else {
			$w = $h = $size;
		}
		$u = $allowPercent ? '/^\d+px|\d+(.\d+)?\%$/' : '/^\d+px$/';
		$wOk = preg_match($u, $w);
		$hOk = preg_match($u, $h);
		if (! $wOk) {
			preg_match('/\d+/', $w, $m);
			if (count($m)) {
				$w = array_shift($m).'px';
			}
		}
		if (! $hOk) {
			preg_match('/\d+/', $h, $m);
			if (count($m)) {
				$h = array_shift($m).'px';
			}
		}
		return array($w, $h);
	}

	public function getEmbedDimensions() {
		if ($size = $this->getData('imagesize')) {
			return $this->parseDimensions($size, false);
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
				$d = $this->parseDimensions($d);
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

	public function getSpotPosition($spot) {
		return $spot['xy'];
	}

	protected function getContent($spot) {
		if ($spot['type'] == Anny_Hotspotter_Model_System_Config_Source_Hotspot_Type::STATIC_BLOCK) {
			return $this->_getStaticBlockContent($spot);
		}
		else if($spot['type'] == Anny_Hotspotter_Model_System_Config_Source_Hotspot_Type::PRODUCT_SKU) {
			return $this->_getProductContent($spot);
		}
		return $spot['value'];
	}

	protected function _getStaticBlockContent($spot) {
		$blockId = $spot['value'];
		$block = $this->getLayout()->createBlock('cms/block');
		$block->setBlockId($blockId);
		return $block->toHtml();
	}

	protected function _getProductContent($spot) {
		$sku = $spot['value'];
		$product = Mage::getModel('catalog/product');
		$id = $product->getIdBySku($sku);
		if ($id) {
			$product->load($id);
			$block = $this->getLayout()->createBlock('hotspotter/hotspot_product');
			$block->setProduct($product);
			return $block->toHtml();
		}
		return '';
	}
}
