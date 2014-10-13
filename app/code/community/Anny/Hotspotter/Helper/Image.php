<?php

class Anny_Hotspotter_Helper_Image extends Mage_Core_Helper_Abstract {
	protected $_dirBase;
	protected $_dirCache;

	public function __construct() {
		$this->_dirBase = Mage::getBaseDir('media').DS.'hotspot';
		$this->_dirCache = $this->_dirBase.DS.'cache';
	}

	protected function isUpload($param) {
		preg_match_all('/[a-z\_]+/', $param, $match);
		$params = $match[0];
		if (count($params) > 1) {
			$a = reset($params);
			$b = next($params);
			return isset($_FILES[$a])
				&& isset($_FILES[$a]['name'][$b])
				&& strlen($_FILES[$a]['name'][$b]);
		}
		return isset($_FILES[$param])
			&& isset($_FILES[$param]['name'])
			&& strlen($_FILES[$param]['name']);
	}

	public function saveUploadedFile($param) {
		if (! $this->isUpload($param)) {
			return null;
		}
		try {
			$uploader = new Varien_File_Uploader($param);
			$uploader->setAllowedExtensions(array('gif','jpg','jpeg','png'));
			$uploader->setAllowCreateFolders(true);
			$uploader->setAllowRenameFiles(true);
			$uploader->setFilesDispersion(false);
			$result = $uploader->save($this->_dirBase.DS);
			return $this->_dirBase.DS.$result['file'];
		}
		catch (Exception $e) {
			Mage::logException($e);
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		}
		return null;
	}

	public function getUrl($path) {
		return Mage::getBaseUrl('media').$this->getRelativePath($path);
	}

	public function getPath($path) {
		return Mage::getBaseDir('media').DS.$this->getRelativePath($path);
	}

	public function getCachePath($path, $w, $h) {
		$file = basename($path);
		return $this->_dirCache.DS.$w.'x'.$h.'.'.$file;
	}

	public function getRelativePath($abs) {
		return str_replace(Mage::getBaseDir('media').DS, '', $abs);
	}

	public function getDimensions($path) {
		$path = $this->getPath($path);
		$img = new Varien_Image($path);
		return array($img->getOriginalWidth(), $img->getOriginalHeight());
	}

	public function resizeImage($path, $w, $h, $constrain, $aspect, $frame) {
		$path = $this->getPath($path);
		$cachePath = $this->getCachePath($path, $w, $h);
		if (file_exists($cachePath)) {
			return $this->getUrl($cachePath);
		}
		try {
			$img = new Varien_Image($path);
			$img->constrainOnly($constrain);
			$img->keepAspectRatio($aspect);
			$img->keepFrame($frame);
			$img->resize($w, $h);
			$img->save($cachePath);
			return $this->getUrl($cachePath);
		}
		catch (Exception $e) {
			Mage::logException($e);
			return $this->getUrl($path);
		}
	}
}
