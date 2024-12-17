<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bahasa extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function save() {
		$bahasa 	= strtolower(str_replace(' ','',post('bahasa')));
		$response 	= [
			'status'	=> 'failed',
			'message'	=> lang('data_gagal_disimpan')
		];
		if($bahasa && strlen($bahasa) == 2 && get_access('bahasa')['access_input']) {
			$this->load->helper('directory');
			$srcdir = rtrim(getcwd().'/assets/lang/id','/');
			$dstdir = rtrim(getcwd().'/assets/lang/'.$bahasa,'/');

			if( !is_dir($dstdir) ) mkdir($dstdir, 0777, true);

			$dir_map=directory_map($srcdir);

			foreach($dir_map as $object_key=>$object_value){
				if(is_numeric($object_key)) {
					@copy($srcdir.'/'.$object_value,$dstdir.'/'.$object_value);
					$oldmask = umask(0);
					chmod($dstdir.'/'.$object_value, 0777);
					umask($oldmask);
				} else {
					@copy($srcdir.'/'.$object_key,$dstdir.'/'.$object_key);
					$oldmask = umask(0);
					chmod($dstdir.'/'.$object_key, 0777);
					umask($oldmask);
				}
			}
			@copy(FCPATH . 'assets/js/_id.js', FCPATH . 'assets/js/_'.$bahasa.'.js');
			$oldmask = umask(0);
			chmod(FCPATH . 'assets/js/_'.$bahasa.'.js', 0777);
			umask($oldmask);
			if( post('flag') ){
				$dest 		= FCPATH . 'assets/lang/'.$bahasa.'/_flag.png';
				$img        = basename(post('flag'));
				$temp_dir   = str_replace($img, '', post('flag'));
				if(@copy(post('flag'),$dest)) {
					delete_dir(FCPATH . $temp_dir);
				}
			}

			$response = [
				'status'	=> 'success',
				'message'	=> lang('data_berhasil_disimpan')
			];
		}
		render($response,'json');
	}

	function delete() {
		$bahasa = post('bahasa');
		$response 	= [
			'status'	=> 'failed',
			'message'	=> lang('izin_ditolak')
		];
		if($bahasa != 'id' && get_access('bahasa')['access_delete']) {
			delete_dir(FCPATH . 'assets/lang/'.$bahasa.'/');
			@unlink(FCPATH . 'assets/js/_'.$bahasa.'.js');
			$response = [
				'status'	=> 'success',
				'message'	=> lang('data_berhasil_dihapus')
			];
		}
		render($response,'json');
	}

	function d($bahasa) {
		if(is_dir(FCPATH . 'assets/lang/'.$bahasa) && get_access('bahasa')['access_edit']) {
			$data['title'] 	= strtoupper($bahasa);
			$data['file']	= scandir(FCPATH . 'assets/lang/id/');
			foreach($data['file'] as $k => $f) {
				if(substr($f,0,1) == '.') unset($data['file'][$k]);
			}
			render($data);
		} else {
			flash_message('error',lang('izin_ditolak'));
			redirect('settings/bahasa');
		}
	}

	function get_directory() {
		$bahasa = post('bahasa');
		$file 	= post('file');
		if($file == '_js') {
			$read_id 		= file_get_contents(FCPATH . 'assets/js/_id.js');
			$normalisasi1	= explode('{',$read_id);
			$normalisasi2	= explode('}',$normalisasi1[1]);
			$read_id 		= '{'.$normalisasi2[0].'}';
			$data 			= json_decode($read_id,true);
			if($bahasa != 'id' && file_exists(FCPATH . 'assets/js/_'.$bahasa.'.js')) {
				$read_bahasa 	= file_get_contents(FCPATH . 'assets/js/_'.$bahasa.'.js');
				$normalisasi1	= explode('{',$read_bahasa);
				$normalisasi2	= explode('}',$normalisasi1[1]);
				$read_bahasa 	= '{'.$normalisasi2[0].'}';
				$data_b 		= json_decode($read_bahasa,true);
				foreach($data as $k => $v) {
					$data[$k] = isset($data_b[$k]) ? $data_b[$k] : $v;
				}
			}
		} else {
			$read_id 	= file_get_contents(FCPATH . 'assets/lang/id/'.$file.'.json');
			$data 		= json_decode($read_id,true);
			if($bahasa != 'id' && file_exists(FCPATH . 'assets/lang/'.$bahasa.'/'.$file.'.json')) {
				$read_bahasa 	= file_get_contents(FCPATH . 'assets/lang/'.$bahasa.'/'.$file.'.json');
				$data_b 		= json_decode($read_bahasa,true);
				foreach($data as $k => $v) {
					$data[$k] = isset($data_b[$k]) ? $data_b[$k] : $v;
				}
			}
		}
		render($data,'json');
	}

	function update() {
		$bahasa = post('bahasa');
		$file 	= post('file');
		$key 	= post('key');
		$value 	= post('value');
		$data 	= [];
		foreach($key as $k => $v) {
			$data[$v]	= $value[$k];
		}
		$json = json_encode($data,JSON_PRETTY_PRINT);
		if($file == '_js') {
			$filename 	= FCPATH . 'assets/js/_'.$bahasa.'.js';
			$json 		= 'var lang = '. $json .';';
		} else {
			$filename = FCPATH . 'assets/lang/'.$bahasa.'/'.$file.'.json';
		}
		@unlink($filename);
		$handle = fopen ($filename, "wb");
		if($handle) {
			fwrite ( $handle, $json );
		}
		fclose($handle);
		$oldmask = umask(0);
		chmod($filename, 0777);
		umask($oldmask);
		$response = [
			'status'	=> 'success',
			'message'	=> lang('data_berhasil_diperbaharui')
		];
		render($response,'json');
	}

}