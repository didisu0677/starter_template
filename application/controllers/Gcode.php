<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gcode extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function barcode($code='') {
		$this->load->library('zend');
		$this->zend->load('Zend/Barcode');
		Zend_Barcode::render('code128', 'image', ['text'=>$code, 'barHeight'=>74, 'factor'=>3.98, 'drawText'=>false], []);
	}

	function qrcode($code='',$save=false) {
		$this->load->library('ciqrcode');
		$params['data']		= $code;
		$params['level']	= 'H';
		$params['size']		= 10;
		if($save) {
			$assets_path 		= FCPATH . 'assets';
			if(!is_dir($assets_path)) {
				$oldmask = umask(0);
				mkdir($assets_path);
				umask($oldmask);
			}
			$save_path 			= $assets_path . '/qrcode';
			if(!is_dir($save_path)) {
				$oldmask = umask(0);
				mkdir($save_path);
				umask($oldmask);
			}
			$code_name		= preg_replace('/[^a-z0-9\s\-]/i', '', $code);
			$code_name		= preg_replace('/\s/', '-', $code_name);
			$code_name		= preg_replace('/\-\-+/', '-', $code_name);
			$code_name		= strtolower(trim($code_name, '-'));
			$params['savename'] = $save_path.'/'.$code_name.'.png';
		} else {
			header("Content-Type: image/png");
		}
		$this->ciqrcode->generate($params);
	}
}