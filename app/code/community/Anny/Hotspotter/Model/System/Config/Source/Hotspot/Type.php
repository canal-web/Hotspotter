<?php

class Anny_Hotspotter_Model_System_Config_Source_Hotspot_Type {
	const DISABLED = '';
	const STATIC_BLOCK = 'cms_block';
	const PRODUCT_SKU = 'product_sku';

	public function toArray() {
		$helper = Mage::helper('hotspotter');
		return array(
			self::DISABLED => $helper->__('Disabled'),
			self::STATIC_BLOCK => $helper->__('CMS Static Block'),
			self::PRODUCT_SKU => $helper->__('Product SKU')
		);
	}

	public function toOptionArray() {
		$opt = array();
		foreach ($this->toArray() as $v => $l) {
			$opt[] = array('value' => $v, 'label' => $l);
		}
		return $opt;
	}
}
