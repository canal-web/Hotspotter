<?php

class Anny_Hotspotter_Block_Saved extends Mage_Core_Block_Abstract {
	protected function _widget() {
		$id = $this->getData('id');
		if ($id) {
			$widget = Mage::getModel('widget/widget_instance')->load($id);
			if ($widget->getId() && $widget->getType() == 'hotspotter/hotspot') {
				return $widget;
			}
		}
		return null;
	}

	protected function _toHtml() {
		$widget = $this->_widget();
		if (! $widget) {
			return '';
		}
		$block = $this->getLayout()->createBlock(
			$widget->getType(),
			$widget->getTitle(),
			$widget->getWidgetParameters()
		);
		return $block->toHtml();
	}
}
