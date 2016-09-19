<?php

class Anny_Hotspotter_Model_Observer {
	protected function _getWidgetInstance() {
		return Mage::registry('current_widget_instance');
	}

	public function prepareAdminForm($observer) {
		$block = $observer->getEvent()->getBlock();
		if (! $block instanceof Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Form) {
			return $this;
		}
		$data = $block->getForm()->getData();
		$data = array_merge($data, $this->_formAttributes());
		$form = new Varien_Data_Form($data);
		$block->setForm($form);
		return $this;
	}

	protected function _formAttributes() {
		return array(
			'enctype' => 'multipart/form-data'
		);
	}

	const INSTANCE_TYPE = 'hotspotter/hotspot';

	public function uploadImage($observer) {
		$instance = $observer->getDataObject();
		if ($instance->getInstanceType() != self::INSTANCE_TYPE) {
			return $this;
		}
		$helper = Mage::helper('hotspotter/image');
		if (array_key_exists('delete', $_POST['parameters']['image'])) {
			$this->_setImagePath('', $instance);
		}
		else {
			$path = $helper->saveUploadedFile('parameters[image]');
			if ($path) {
				$rel = $helper->getRelativePath($path);
				$this->_setImagePath($rel, $instance);
			}
			else {
				// prevents image value overwriting itself with "Array"
				$this->_setImagePath(null, $instance);
			}
		}
		return $this;
	}

	protected function _setImagePath($path, $instance) {
		$params = $instance->getWidgetParameters();
		if ($path === null) {
			$params['image'] = $params['image']['value'];
		}
		else {
			$params['image'] = $path;
		}
		$instance->setWidgetParameters(serialize($params));
		return $this;
	}
}
